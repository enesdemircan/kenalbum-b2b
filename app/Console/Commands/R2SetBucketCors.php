<?php

namespace App\Console\Commands;

use Aws\S3\S3Client;
use Illuminate\Console\Command;

class R2SetBucketCors extends Command
{
    protected $signature = 'r2:set-cors
        {--origin=* : Allowed origin (defaults to APP_URL or repeat to add multiple)}
        {--show : Yalnızca mevcut CORS yapılandırmasını yazdır, değişiklik yapma}';

    protected $description = 'Cloudflare R2 bucket CORS politikasını checkout direct PUT için ayarlar';

    public function handle(): int
    {
        $client = new S3Client([
            'version' => 'latest',
            'region' => 'auto',
            'endpoint' => (string) env('R2_ENDPOINT'),
            'use_path_style_endpoint' => false,
            'credentials' => [
                'key' => (string) env('R2_ACCESS_KEY_ID'),
                'secret' => (string) env('R2_SECRET_ACCESS_KEY'),
            ],
        ]);

        $bucket = (string) config('filesystems.disks.r2.bucket', env('R2_BUCKET'));
        if ($bucket === '') {
            $this->error('R2_BUCKET tanımlı değil.');
            return self::FAILURE;
        }

        if ($this->option('show')) {
            try {
                $cur = $client->getBucketCors(['Bucket' => $bucket]);
                $this->info('Mevcut CORS:');
                $this->line(json_encode($cur->get('CORSRules'), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            } catch (\Throwable $e) {
                $this->warn('CORS yapılandırması yok veya alınamadı: ' . $e->getMessage());
            }
            return self::SUCCESS;
        }

        $origins = $this->option('origin') ?: [];
        if (empty($origins)) {
            $appUrl = rtrim((string) config('app.url'), '/');
            if ($appUrl !== '') {
                $origins[] = $appUrl;
            }
        }
        if (empty($origins)) {
            $this->error('En az bir origin gerekli (--origin=https://example.com).');
            return self::FAILURE;
        }

        $rules = [[
            'AllowedOrigins' => array_values(array_unique($origins)),
            'AllowedMethods' => ['PUT', 'GET', 'HEAD', 'POST', 'DELETE'],
            'AllowedHeaders' => ['*'],
            'ExposeHeaders'  => ['ETag'],
            'MaxAgeSeconds'  => 3600,
        ]];

        $this->info('Bucket: ' . $bucket);
        $this->info('Uygulanacak CORS kuralı:');
        $this->line(json_encode($rules, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        try {
            $client->putBucketCors([
                'Bucket' => $bucket,
                'CORSConfiguration' => ['CORSRules' => $rules],
            ]);
            $this->info('✓ CORS politikası ayarlandı.');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Hata: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
