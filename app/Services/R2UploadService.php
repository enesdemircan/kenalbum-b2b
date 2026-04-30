<?php

namespace App\Services;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class R2UploadService
{
    private S3Client $client;
    private string $bucket;
    private string $publicBase;

    public function __construct()
    {
        $this->bucket = (string) config('filesystems.disks.r2.bucket', env('R2_BUCKET'));
        $this->publicBase = rtrim((string) env('R2_PUBLIC_LINK', ''), '/');

        $this->client = new S3Client([
            'version' => 'latest',
            'region' => 'auto',
            'endpoint' => (string) env('R2_ENDPOINT'),
            'use_path_style_endpoint' => false,
            'credentials' => [
                'key' => (string) env('R2_ACCESS_KEY_ID'),
                'secret' => (string) env('R2_SECRET_ACCESS_KEY'),
            ],
        ]);
    }

    public function buildKey(int $cartPrimaryId, string $cartSlug, int $fileIndex, string $extension): string
    {
        $ext = ltrim(strtolower($extension), '.');
        if ($ext === '') {
            $ext = 'bin';
        }
        $slug = $this->sanitizeKeySegment($cartSlug ?: (string) $cartPrimaryId);
        $suffix = $fileIndex > 0 ? "-{$fileIndex}" : '';
        return "orders/{$cartPrimaryId}/{$slug}{$suffix}.{$ext}";
    }

    /**
     * Sipariş henüz oluşmadan checkout sırasında geçici upload key'i.
     * Order create olunca buildOrderFinalKey ile final key'e taşınır.
     */
    public function buildOrderTempKey(int $userId, string $extension): string
    {
        $ext = ltrim(strtolower($extension), '.');
        if ($ext === '') {
            $ext = 'bin';
        }
        $token = bin2hex(random_bytes(8));
        $ts = time();
        return "orders/temp/{$userId}/{$ts}-{$token}.{$ext}";
    }

    /**
     * Order yaratılınca temp key buraya taşınır.
     */
    public function buildOrderFinalKey(int $orderId, string $orderNumber, string $extension): string
    {
        $ext = ltrim(strtolower($extension), '.');
        if ($ext === '') {
            $ext = 'bin';
        }
        $slug = $this->sanitizeKeySegment($orderNumber ?: (string) $orderId);
        return "orders/{$orderId}/{$slug}.{$ext}";
    }

    /**
     * Temp upload'u final key'e taşı (CopyObject + DeleteObject).
     */
    public function moveToFinal(string $tempKey, string $finalKey): bool
    {
        if ($tempKey === $finalKey) {
            return true;
        }
        if (!$this->copyObject($tempKey, $finalKey)) {
            return false;
        }
        $this->deleteObject($tempKey);
        return true;
    }

    public function publicUrl(string $key): string
    {
        return $this->publicBase . '/' . ltrim($key, '/');
    }

    public function extractKeyFromUrl(?string $url): ?string
    {
        if (!$url) {
            return null;
        }
        if (!preg_match('#^https?://#i', $url)) {
            return ltrim($url, '/');
        }
        $base = $this->publicBase;
        if ($base !== '' && str_starts_with($url, $base . '/')) {
            return substr($url, strlen($base) + 1);
        }
        $path = parse_url($url, PHP_URL_PATH);
        return $path ? ltrim($path, '/') : null;
    }

    public function initiateMultipart(string $key, ?string $contentType = null): string
    {
        $params = [
            'Bucket' => $this->bucket,
            'Key' => $key,
        ];
        if ($contentType) {
            $params['ContentType'] = $contentType;
        }

        $result = $this->client->createMultipartUpload($params);
        return (string) $result['UploadId'];
    }

    public function presignPartUrls(string $key, string $uploadId, int $partCount, int $ttlSeconds = 3600): array
    {
        $urls = [];
        for ($partNumber = 1; $partNumber <= $partCount; $partNumber++) {
            $cmd = $this->client->getCommand('UploadPart', [
                'Bucket' => $this->bucket,
                'Key' => $key,
                'UploadId' => $uploadId,
                'PartNumber' => $partNumber,
            ]);
            $request = $this->client->createPresignedRequest($cmd, "+{$ttlSeconds} seconds");
            $urls[] = [
                'partNumber' => $partNumber,
                'url' => (string) $request->getUri(),
            ];
        }
        return $urls;
    }

    public function completeMultipart(string $key, string $uploadId, array $parts): void
    {
        usort($parts, fn($a, $b) => ($a['PartNumber'] ?? 0) <=> ($b['PartNumber'] ?? 0));

        $this->client->completeMultipartUpload([
            'Bucket' => $this->bucket,
            'Key' => $key,
            'UploadId' => $uploadId,
            'MultipartUpload' => ['Parts' => $parts],
        ]);
    }

    public function abortMultipart(string $key, string $uploadId): void
    {
        try {
            $this->client->abortMultipartUpload([
                'Bucket' => $this->bucket,
                'Key' => $key,
                'UploadId' => $uploadId,
            ]);
        } catch (AwsException $e) {
            // best-effort — orphan part R2 lifecycle ile temizlenebilir
        }
    }

    public function headObject(string $key): ?array
    {
        try {
            $result = $this->client->headObject([
                'Bucket' => $this->bucket,
                'Key' => $key,
            ]);
            return [
                'size' => (int) $result['ContentLength'],
                'content_type' => (string) ($result['ContentType'] ?? ''),
            ];
        } catch (AwsException $e) {
            return null;
        }
    }

    /**
     * Cart_id slug değişince R2 object key'ini yeniden adlandır (CopyObject + Delete).
     * Returns new key on success, null if rename was a no-op or failed.
     */
    public function renameSlugInKey(string $oldKey, string $oldSlug, string $newSlug, int $cartPrimaryId): ?string
    {
        $oldSanitized = $this->sanitizeKeySegment($oldSlug);
        $newSanitized = $this->sanitizeKeySegment($newSlug);

        if ($oldSanitized === $newSanitized) {
            return null;
        }

        $prefix = "orders/{$cartPrimaryId}/";
        $expectedPrefix = $prefix . $oldSanitized;

        if (!str_starts_with($oldKey, $expectedPrefix)) {
            return null;
        }

        $remainder = substr($oldKey, strlen($expectedPrefix));
        if ($remainder !== '' && !in_array($remainder[0], ['.', '-'], true)) {
            return null;
        }

        $newKey = $prefix . $newSanitized . $remainder;

        if (!$this->copyObject($oldKey, $newKey)) {
            return null;
        }
        $this->deleteObject($oldKey);
        return $newKey;
    }

    public function copyObject(string $sourceKey, string $destKey): bool
    {
        if ($sourceKey === $destKey) {
            return true;
        }
        try {
            $this->client->copyObject([
                'Bucket' => $this->bucket,
                'Key' => $destKey,
                'CopySource' => rawurlencode($this->bucket . '/' . $sourceKey),
            ]);
            return true;
        } catch (AwsException $e) {
            return false;
        }
    }

    public function deleteObject(string $key): void
    {
        try {
            $this->client->deleteObject([
                'Bucket' => $this->bucket,
                'Key' => $key,
            ]);
        } catch (AwsException $e) {
            // best-effort
        }
    }

    private function sanitizeKeySegment(string $segment): string
    {
        $segment = preg_replace('#[^A-Za-z0-9._-]+#', '-', $segment) ?? '';
        $segment = trim($segment, '-.');
        if ($segment === '') {
            return 'file';
        }
        if (strlen($segment) > 200) {
            $segment = substr($segment, 0, 200);
        }
        return $segment;
    }
}
