<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use ZipArchive;
use App\Models\Cart;
use App\Models\CartFile;

class FileProcessingService
{
    /**
     * Dosyaları sıraya göre işle ve ZIP oluştur
     */
    public function processFilesWithOrder($cartId, $files, $fileOrders)
    {
        try {
            $processedFiles = [];
            $zipFiles = [];

            foreach ($files as $file) {
                $categoryId = $file['categoryId'];
                $fileName = $file['original']['name'];
                $fileType = $file['type'];

                // Sıralama bilgisini al
                $order = $fileOrders[$categoryId] ?? [];
                $fileOrder = $this->getFileOrder($fileName, $order);

                if ($fileType === 'files') {
                    // Çoklu dosya - sıraya göre isimlendir
                    $newFileName = $this->generateSequentialFileName($fileName, $fileOrder);
                    $zipFiles[] = [
                        'original_name' => $fileName,
                        'new_name' => $newFileName,
                        's3_path' => $file['s3_path'] ?? null,
                        'category_id' => $categoryId
                    ];
                } else {
                    // Tekli dosya - rastgele isim ver
                    $newFileName = $this->generateRandomFileName($fileName);
                    $zipFiles[] = [
                        'original_name' => $fileName,
                        'new_name' => $newFileName,
                        's3_path' => $file['s3_path'] ?? null,
                        'category_id' => $categoryId
                    ];
                }

                $processedFiles[] = [
                    'category_id' => $categoryId,
                    'original_name' => $fileName,
                    'new_name' => $newFileName,
                    's3_path' => $file['s3_path'] ?? null,
                    'file_type' => $fileType,
                    'order' => $fileOrder
                ];
            }

            // ZIP dosyası oluştur
            $zipPath = $this->createZipFile($cartId, $zipFiles);

            if ($zipPath) {
                // ZIP'i S3'e yükle
                $zipS3Path = $this->uploadZipToS3($zipPath, $cartId);
                
                if ($zipS3Path) {
                    // ZIP dosyasını sil
                    unlink($zipPath);
                    
                    // Cart'ı güncelle
                    $this->updateCartWithZipInfo($cartId, $zipS3Path, $processedFiles);
                    
                    return [
                        'success' => true,
                        'zip_path' => $zipS3Path,
                        'processed_files' => $processedFiles
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Failed to create or upload ZIP file'
            ];

        } catch (\Exception $e) {
            Log::error('File processing error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'File processing failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Dosya sırasını al
     */
    private function getFileOrder($fileName, $order)
    {
        foreach ($order as $item) {
            if ($item['name'] === $fileName) {
                return $item['newIndex'];
            }
        }
        return 0;
    }

    /**
     * Sıralı dosya adı oluştur
     */
    private function generateSequentialFileName($originalName, $order)
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
        
        // Sıra numarası ekle (1'den başla)
        $sequenceNumber = $order + 1;
        
        return "{$sequenceNumber}_{$nameWithoutExt}.{$extension}";
    }

    /**
     * Rastgele dosya adı oluştur
     */
    private function generateRandomFileName($originalName)
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $randomString = uniqid('file_', true);
        
        return "{$randomString}.{$extension}";
    }

    /**
     * ZIP dosyası oluştur
     */
    private function createZipFile($cartId, $zipFiles)
    {
        $zipPath = storage_path("app/temp/zips/cart_{$cartId}_" . uniqid() . ".zip");
        
        if (!file_exists(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        $zip = new ZipArchive();
        
        if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
            Log::error('Failed to create ZIP file: ' . $zipPath);
            return false;
        }

        foreach ($zipFiles as $file) {
            if ($file['s3_path'] && Storage::disk('s3')->exists($file['s3_path'])) {
                // S3'ten dosyayı al
                $fileContent = Storage::disk('s3')->get($file['s3_path']);
                
                // ZIP'e ekle
                $zip->addFromString($file['new_name'], $fileContent);
            }
        }

        $zip->close();
        return $zipPath;
    }

    /**
     * ZIP dosyasını S3'e yükle
     */
    private function uploadZipToS3($zipPath, $cartId)
    {
        try {
            $zipFileName = basename($zipPath);
            $s3Path = "carts/{$cartId}/zips/{$zipFileName}";
            
            if (Storage::disk('s3')->put($s3Path, file_get_contents($zipPath))) {
                return $s3Path;
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('ZIP S3 upload error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cart'ı ZIP bilgileri ile güncelle
     */
    private function updateCartWithZipInfo($cartId, $zipS3Path, $processedFiles)
    {
        try {
            $cart = Cart::find($cartId);
            if ($cart) {
                $notes = json_decode($cart->notes, true) ?: [];
                $notes['zip_file'] = [
                    's3_path' => $zipS3Path,
                    's3_url' => Storage::disk('s3')->url($zipS3Path),
                    'created_at' => now()->toISOString()
                ];
                $notes['processed_files'] = $processedFiles;
                
                $cart->update(['notes' => json_encode($notes)]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to update cart with ZIP info: ' . $e->getMessage());
        }
    }

    /**
     * Dosya sıralama bilgilerini veritabanına kaydet
     */
    public function saveFileOrders($cartId, $fileOrders)
    {
        try {
            foreach ($fileOrders as $categoryId => $order) {
                CartFile::where('cart_id', $cartId)
                    ->where('category_id', $categoryId)
                    ->update(['file_order' => json_encode($order)]);
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to save file orders: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cart için tüm dosya bilgilerini getir
     */
    public function getCartFiles($cartId)
    {
        try {
            $cart = Cart::find($cartId);
            if (!$cart) {
                return [];
            }

            $notes = json_decode($cart->notes, true) ?: [];
            return $notes['processed_files'] ?? [];
        } catch (\Exception $e) {
            Log::error('Failed to get cart files: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ZIP dosyasını indir
     */
    public function downloadZip($cartId)
    {
        try {
            $cart = Cart::find($cartId);
            if (!$cart) {
                return false;
            }

            $notes = json_decode($cart->notes, true) ?: [];
            $zipInfo = $notes['zip_file'] ?? null;

            if (!$zipInfo || !isset($zipInfo['s3_path'])) {
                return false;
            }

            $zipPath = $zipInfo['s3_path'];
            
            if (Storage::disk('s3')->exists($zipPath)) {
                return Storage::disk('s3')->get($zipPath);
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Failed to download ZIP: ' . $e->getMessage());
            return false;
        }
    }
} 