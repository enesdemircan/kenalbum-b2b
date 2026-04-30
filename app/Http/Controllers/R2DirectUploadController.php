<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Services\R2UploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class R2DirectUploadController extends Controller
{
    private const PART_SIZE_BYTES = 10 * 1024 * 1024;
    private const MAX_FILE_BYTES = 524_288_000;
    private const MAX_FILE_BYTES_TOLERANCE = 550_502_400;
    private const MAX_FILE_INDEX = 9;
    private const PRESIGN_TTL_SECONDS = 3600;

    public function __construct(private R2UploadService $r2)
    {
    }

    public function initiate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'cart_id' => 'required|integer',
            'file_index' => 'required|integer|min:0|max:' . self::MAX_FILE_INDEX,
            'file_size' => 'required|integer|min:1|max:' . self::MAX_FILE_BYTES,
            'file_name' => 'required|string|max:255',
            'content_type' => 'nullable|string|max:255',
        ]);

        $cart = $this->authorizedCart((int) $data['cart_id']);

        $extension = pathinfo($data['file_name'], PATHINFO_EXTENSION) ?: 'bin';
        $cartSlug = $cart->cart_id ?: (string) $cart->id;
        $key = $this->r2->buildKey($cart->id, $cartSlug, (int) $data['file_index'], $extension);

        $partCount = (int) ceil($data['file_size'] / self::PART_SIZE_BYTES);
        if ($partCount < 1) {
            $partCount = 1;
        }

        try {
            $uploadId = $this->r2->initiateMultipart($key, $data['content_type'] ?? null);
            $partUrls = $this->r2->presignPartUrls($key, $uploadId, $partCount, self::PRESIGN_TTL_SECONDS);
        } catch (\Throwable $e) {
            Log::error('R2 initiate failed', [
                'cart_id' => $cart->id,
                'file_index' => $data['file_index'],
                'key' => $key,
                'error' => $e->getMessage(),
                'class' => get_class($e),
                'trace_first' => $e->getTraceAsString() ? substr($e->getTraceAsString(), 0, 500) : '',
                'r2_endpoint_set' => !empty(env('R2_ENDPOINT')),
                'r2_bucket_set' => !empty(env('R2_BUCKET')),
                'r2_key_set' => !empty(env('R2_ACCESS_KEY_ID')),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'R2 initiate hatası.',
                'debug' => config('app.debug') ? [
                    'error' => $e->getMessage(),
                    'class' => get_class($e),
                ] : null,
            ], 500);
        }

        return response()->json([
            'success' => true,
            'upload_id' => $uploadId,
            'key' => $key,
            'part_size' => self::PART_SIZE_BYTES,
            'part_count' => $partCount,
            'part_urls' => $partUrls,
            'max_file_size' => self::MAX_FILE_BYTES,
        ]);
    }

    public function complete(Request $request): JsonResponse
    {
        $data = $request->validate([
            'cart_id' => 'required|integer',
            'key' => 'required|string|max:512',
            'upload_id' => 'required|string|max:512',
            'parts' => 'required|array|min:1|max:60',
            'parts.*.PartNumber' => 'required|integer|min:1|max:60',
            'parts.*.ETag' => 'required|string|max:128',
        ]);

        $cart = $this->authorizedCart((int) $data['cart_id']);
        $this->assertKeyBelongsToCart($data['key'], $cart->id);

        try {
            $this->r2->completeMultipart($data['key'], $data['upload_id'], $data['parts']);
        } catch (\Throwable $e) {
            Log::error('R2 complete failed', [
                'cart_id' => $cart->id,
                'key' => $data['key'],
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'R2 complete hatası.',
            ], 500);
        }

        $headInfo = $this->r2->headObject($data['key']);
        if ($headInfo === null) {
            Log::warning('R2 complete: HeadObject failed', ['key' => $data['key']]);
            return response()->json([
                'success' => false,
                'message' => 'Yüklenen dosya doğrulanamadı.',
            ], 500);
        }
        if ($headInfo['size'] > self::MAX_FILE_BYTES_TOLERANCE) {
            Log::warning('R2 complete: file exceeds limit, deleting', [
                'cart_id' => $cart->id,
                'key' => $data['key'],
                'size' => $headInfo['size'],
            ]);
            $this->r2->deleteObject($data['key']);
            return response()->json([
                'success' => false,
                'message' => 'Dosya boyutu 500 MB üst sınırını aşıyor.',
            ], 422);
        }

        $publicUrl = $this->r2->publicUrl($data['key']);
        $this->appendUploadedUrl($cart, $publicUrl);

        return response()->json([
            'success' => true,
            'key' => $data['key'],
            'url' => $publicUrl,
            'size' => $headInfo['size'],
        ]);
    }

    public function abort(Request $request): JsonResponse
    {
        $data = $request->validate([
            'cart_id' => 'required|integer',
            'key' => 'required|string|max:512',
            'upload_id' => 'required|string|max:512',
        ]);

        $cart = $this->authorizedCart((int) $data['cart_id']);
        $this->assertKeyBelongsToCart($data['key'], $cart->id);

        $this->r2->abortMultipart($data['key'], $data['upload_id']);

        return response()->json(['success' => true]);
    }

    private function authorizedCart(int $cartId): Cart
    {
        $cart = Cart::find($cartId);
        if (!$cart) {
            abort(404, 'Cart bulunamadı.');
        }
        $user = auth()->user();
        if (!$user) {
            abort(403, 'Bu sepete dosya yükleme yetkiniz yok.');
        }

        // 1) Cart'ı oluşturan kullanıcının kendisi
        if ((int) $cart->user_id === (int) $user->id) {
            return $cart;
        }

        // 2) Aynı firma altındaki personel (proje OrderController::show pattern'i)
        if ($user->customer_id && $cart->user && (int) $cart->user->customer_id === (int) $user->customer_id) {
            return $cart;
        }

        Log::warning('R2 cart auth denied', [
            'cart_id' => $cart->id,
            'cart_user_id' => $cart->user_id,
            'cart_user_customer_id' => $cart->user?->customer_id,
            'auth_user_id' => $user->id,
            'auth_customer_id' => $user->customer_id,
        ]);
        abort(403, 'Bu sepete dosya yükleme yetkiniz yok.');
    }

    private function assertKeyBelongsToCart(string $key, int $cartId): void
    {
        $expectedPrefix = "orders/{$cartId}/";
        if (!str_starts_with($key, $expectedPrefix) || str_contains($key, '..')) {
            abort(403, 'Geçersiz dosya anahtarı.');
        }
    }

    private function appendUploadedUrl(Cart $cart, string $url): void
    {
        $existing = trim((string) $cart->s3_zip);
        if ($existing === '') {
            $cart->s3_zip = $url;
        } else {
            $existingUrls = array_filter(array_map('trim', explode(',', $existing)));
            if (!in_array($url, $existingUrls, true)) {
                $existingUrls[] = $url;
                $cart->s3_zip = implode(',', $existingUrls);
            }
        }
        $cart->save();
    }
}
