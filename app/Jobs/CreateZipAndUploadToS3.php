<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use ZipArchive;
use App\Models\Cart;
use App\Models\Order;

class CreateZipAndUploadToS3 implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 dakika timeout
    public $tries = 3; // 3 kez dene
    public $backoff = 60; // Retry arasında 60 saniye bekle

    protected $cartId;
    protected $mergedFiles;
 
    /**
     * Create a new job instance.
     */
    public function __construct($cartId, $mergedFiles)
    {
        $this->cartId = $cartId;
        $this->mergedFiles = $mergedFiles;
        
        // Memory limit artır (512MB)
        ini_set('memory_limit', '512M');
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            Log::info("Starting ZIP creation job", [
                'cart_id' => $this->cartId,
                'file_count' => count($this->mergedFiles)
            ]);
            
            // Status'u processing olarak güncelle (confirm)
            $this->updateZipStatus('processing');

            // Dosyayı R2'ye yükle (zaten ZIP, oluşturma yok)
            $s3ZipUrl = $this->uploadZipToR2();

            if ($s3ZipUrl) {
                // Carts tablosunu güncelle
                $this->updateCart($s3ZipUrl);

                // Geçici dosyaları temizle (merged klasörü)
                $this->cleanupTempFiles();
                
                // Status'u completed olarak güncelle
                $this->updateZipStatus('completed');

                Log::info("ZIP job completed successfully", [
                    'cart_id' => $this->cartId,
                    's3_url' => $s3ZipUrl
                ]);
            }

        } catch (\Exception $e) {
            Log::error("ZIP job failed", [
                'error' => $e->getMessage(),
                'cart_id' => $this->cartId,
                'trace' => $e->getTraceAsString(),
                'attempt' => $this->attempts()
            ]);

            // Durumu güncelle
            $this->updateZipStatus('failed', $e->getMessage());

            throw $e; // Job'ı tekrar dene
        }
    }
    
    /**
     * Job başarısız olduğunda çalışır (tüm retry'lar tükendikten sonra)
     */
    public function failed(\Throwable $exception)
    {
        Log::error("ZIP job completely failed after all retries", [
            'error' => $exception->getMessage(),
            'cart_id' => $this->cartId
        ]);

        // Durumu güncelle
        $this->updateZipStatus('failed', $exception->getMessage());
        
        // Kullanıcıya bildirim gönder (opsiyonel)
        // Mail::to($user)->send(new ZipProcessingFailed($this->cartId));
    }
    
    /**
     * ZIP durumunu güncelle
     */
    private function updateZipStatus($status, $error = null)
    {
        try {
            $zipInfo = [
                'cart_id' => $this->cartId,
                'status' => $status,
                'file_count' => count($this->mergedFiles),
                'updated_at' => now()->toISOString()
            ];
            
            if ($error) {
                $zipInfo['error'] = $error;
            }
            
            Storage::disk('public')->put("zips/{$this->cartId}/zip_info.json", json_encode($zipInfo));
            
        } catch (\Exception $e) {
            Log::error("Failed to update ZIP status", [
                'error' => $e->getMessage(),
                'cart_id' => $this->cartId
            ]);
        }
    }

    /**
     * Tüm dosyaları ZIP'e sıkıştır ve R2'ye yükle
     */
    private function uploadZipToR2()
    {
        try {
            // Cart'ı bul ve cart_id al
            $cart = Cart::with(['order', 'product', 'user.customer'])->find($this->cartId);
            if (!$cart || !$cart->cart_id) {
                throw new \Exception("Cart or cart_id not found");
            }

            // Geçici ZIP dosyası oluştur
            $zipFileName = $cart->cart_id . '.zip';
            $zipPath = storage_path("app/public/zips/{$this->cartId}/{$zipFileName}");
            
            // ZIP dizinini oluştur
            $zipDir = dirname($zipPath);
            if (!is_dir($zipDir)) {
                mkdir($zipDir, 0755, true);
            }

            // ZIP arşivi oluştur
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
                throw new \Exception("Failed to create ZIP file");
            }

            // Tüm yüklenen dosyaları ZIP'e ekle
            if (!empty($this->mergedFiles)) {
                foreach ($this->mergedFiles as $index => $mergedFile) {
                    $filePath = storage_path("app/public/" . $mergedFile['stored_path']);
                    
                    if (!file_exists($filePath)) {
                        Log::warning("Merged file not found, skipping", [
                            'file_path' => $filePath,
                            'original_name' => $mergedFile['original_name']
                        ]);
                        continue;
                    }

                    // ZIP'e dosya ekle (orijinal dosya adıyla)
                    $zip->addFile($filePath, $mergedFile['original_name']);
                    
                    Log::info("File added to ZIP", [
                        'file_name' => $mergedFile['original_name'],
                        'file_size' => filesize($filePath)
                    ]);
                }
            }

            // Sipariş detay PDF'ini oluştur ve ZIP'e ekle
            if ($cart->order_id) {
                $pdfPath = $this->generateOrderDetailPdf($cart);
                if ($pdfPath && file_exists($pdfPath)) {
                    $zip->addFile($pdfPath, 'siparis-detay-' . $cart->cart_id . '.pdf');
                    
                    Log::info("Order detail PDF added to ZIP", [
                        'cart_id' => $this->cartId,
                        'pdf_path' => $pdfPath
                    ]);
                }
            }

            $zip->close();

            // ZIP dosyasını oku
            if (!file_exists($zipPath)) {
                throw new \Exception("ZIP file was not created");
            }

            $zipContent = file_get_contents($zipPath);
            if ($zipContent === false) {
                throw new \Exception("Failed to read ZIP file");
            }

            // R2'de dosya yolu: cart_id.zip
            $r2Path = "zips/{$this->cartId}/{$zipFileName}";
            
            // R2'ye yükle
            $uploadResult = Storage::disk('s3')->put($r2Path, $zipContent, 'public');
            
            if (!$uploadResult) {
                throw new \Exception("Failed to upload to R2");
            }

            // R2 URL'ini al
            $r2Url = Storage::disk('s3')->url($r2Path);

            Log::info("ZIP uploaded to R2 successfully", [
                'cart_id' => $this->cartId,
                'cart_identifier' => $cart->cart_id,
                'r2_path' => $r2Path,
                'r2_url' => $r2Url,
                'file_count' => count($this->mergedFiles),
                'zip_size' => strlen($zipContent)
            ]);

            // Geçici ZIP dosyasını sil
            if (file_exists($zipPath)) {
                unlink($zipPath);
            }
            
            // Geçici PDF dosyasını sil (eğer oluşturulduysa)
            $pdfPath = storage_path("app/public/zips/{$this->cartId}/order-detail-{$cart->cart_id}.pdf");
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }

            return $r2Url;

        } catch (\Exception $e) {
            Log::error("R2 upload failed in job", [
                'error' => $e->getMessage(),
                'cart_id' => $this->cartId,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Thumbnail'leri oluştur
     */
    private function createThumbnails()
    {
        try {
            $thumbnailUrls = [];
            $thumbnailDir = "thumbnails/{$this->cartId}";
            
            // Thumbnail dizinini oluştur
            if (!Storage::disk('public')->exists($thumbnailDir)) {
                Storage::disk('public')->makeDirectory($thumbnailDir);
            }
            
            // İlk 2 resim için thumbnail oluştur (0.jpg ve 1.jpg)
            for ($i = 0; $i < min(2, count($this->mergedFiles)); $i++) {
                $file = $this->mergedFiles[$i];
                $filePath = storage_path("app/public/" . $file['stored_path']);
                
                if (file_exists($filePath) && $this->isImageFile($file['original_name'])) {
                    $thumbnailPath = $this->createThumbnail($filePath, $thumbnailDir, $i);
                    
                    if ($thumbnailPath) {
                        $thumbnailUrls[] = $thumbnailPath;
                    }
                }
            }
            
            // Carts tablosuna thumbnail URL'lerini kaydet
            if (!empty($thumbnailUrls)) {
                $this->updateCartThumbnails($thumbnailUrls);
            }
            
        } catch (\Exception $e) {
            Log::error("Thumbnail creation failed in job", [
                'error' => $e->getMessage(),
                'cart_id' => $this->cartId
            ]);
        }
    }



    /**
     * Carts tablosunu güncelle
     */
    private function updateCart($s3ZipUrl)
    {
        try {
            $cart = Cart::find($this->cartId);
            
            if ($cart) {
                $cart->update([
                    'local_zip' => null, // Local ZIP yok, sadece S3
                    's3_zip' => $s3ZipUrl
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error("Cart update failed in job", [
                'error' => $e->getMessage(),
                'cart_id' => $this->cartId
            ]);
        }
    }

    /**
     * Carts tablosuna thumbnail'leri güncelle
     */
    private function updateCartThumbnails($thumbnailUrls)
    {
        try {
            $cart = Cart::find($this->cartId);
            
            if ($cart) {
                $imagesString = implode(',', $thumbnailUrls);
                $cart->update(['images' => $imagesString]);
            }
            
        } catch (\Exception $e) {
            Log::error("Cart thumbnails update failed in job", [
                'error' => $e->getMessage(),
                'cart_id' => $this->cartId
            ]);
        }
    }

    /**
     * Dosyanın resim olup olmadığını kontrol et
     */
    private function isImageFile($fileName)
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        return in_array($extension, $imageExtensions);
    }

    /**
     * Thumbnail oluştur
     */
    private function createThumbnail($sourcePath, $thumbnailDir, $index)
    {
        try {
            if (!extension_loaded('gd')) {
                return false;
            }
            
            $imageInfo = getimagesize($sourcePath);
            if (!$imageInfo) {
                return false;
            }
            
            $width = $imageInfo[0];
            $height = $imageInfo[1];
            $mimeType = $imageInfo['mime'];
            
            switch ($mimeType) {
                case 'image/jpeg':
                    $sourceImage = imagecreatefromjpeg($sourcePath);
                    break;
                case 'image/png':
                    $sourceImage = imagecreatefrompng($sourcePath);
                    break;
                case 'image/gif':
                    $sourceImage = imagecreatefromgif($sourcePath);
                    break;
                default:
                    return false;
            }
            
            if (!$sourceImage) {
                return false;
            }
            
            $thumbWidth = 150;
            $thumbHeight = 150;
            $thumbnail = imagecreatetruecolor($thumbWidth, $thumbHeight);
            
            if ($mimeType === 'image/png') {
                imagealphablending($thumbnail, false);
                imagesavealpha($thumbnail, true);
                $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
                imagefilledrectangle($thumbnail, 0, 0, $thumbWidth, $thumbHeight, $transparent);
            }
            
            imagecopyresampled($thumbnail, $sourceImage, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);
            
            $thumbnailFileName = "thumb_{$index}.jpg";
            $thumbnailPath = "{$thumbnailDir}/{$thumbnailFileName}";
            $fullThumbnailPath = storage_path("app/public/{$thumbnailPath}");
            
            imagejpeg($thumbnail, $fullThumbnailPath, 85);
            
            imagedestroy($sourceImage);
            imagedestroy($thumbnail);
            
            return $thumbnailPath;
            
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Sipariş detay PDF'ini oluştur
     */
    private function generateOrderDetailPdf($cart)
    {
        try {
            if (!$cart->order_id) {
                return null;
            }

            $order = \App\Models\Order::with([
                'user.customer',
                'cartItems.product',
                'cartItems.user.customer'
            ])->find($cart->order_id);

            if (!$order) {
                return null;
            }

            // Sadece bu cart item'ı için sipariş bilgisi oluştur
            // Order'ı clone'layıp sadece bu cart item'ı içermesini sağla
            $singleItemOrder = clone $order;
            $singleItemOrder->setRelation('cartItems', collect([$cart]));

            // PDF oluştur
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.orders.print', ['order' => $singleItemOrder]);
            
            $pdf->setPaper('a5');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => false,
                'isRemoteEnabled' => false,
                'defaultFont' => 'Arial',
                'chroot' => public_path(),
                'enable_remote' => false,
                'enable_local_file_access' => true,
                'dpi' => 72,
                'image_dpi' => 72
            ]);

            // Geçici PDF dosyası kaydet
            $pdfFileName = 'order-detail-' . $cart->cart_id . '.pdf';
            $pdfPath = storage_path("app/public/zips/{$this->cartId}/{$pdfFileName}");
            $pdf->save($pdfPath);

            Log::info("Order detail PDF created", [
                'cart_id' => $this->cartId,
                'pdf_path' => $pdfPath,
                'file_size' => filesize($pdfPath)
            ]);

            return $pdfPath;

        } catch (\Exception $e) {
            Log::error("Failed to generate order detail PDF", [
                'error' => $e->getMessage(),
                'cart_id' => $this->cartId
            ]);
            return null;
        }
    }

    /**
     * Geçici dosyaları temizle (merged ve zips, thumbnails kalacak)
     */
    private function cleanupTempFiles()
    {
        try {
            // Merged dosyaları temizle
            $mergedDir = "merged/{$this->cartId}";
            if (Storage::disk('public')->exists($mergedDir)) {
                Storage::disk('public')->deleteDirectory($mergedDir);
                Log::info("Merged directory cleaned up", ['cart_id' => $this->cartId]);
            }
            
            // Zips dizinini temizle
            $zipsDir = "zips/{$this->cartId}";
            if (Storage::disk('public')->exists($zipsDir)) {
                Storage::disk('public')->deleteDirectory($zipsDir);
                Log::info("Zips directory cleaned up", ['cart_id' => $this->cartId]);
            }
            
            // Chunks dizinini de temizle (eğer hala varsa)
            $chunksDir = "chunks/{$this->cartId}";
            if (Storage::disk('public')->exists($chunksDir)) {
                Storage::disk('public')->deleteDirectory($chunksDir);
                Log::info("Chunks directory cleaned up", ['cart_id' => $this->cartId]);
            }
            
            Log::info("Temp files cleanup completed", ['cart_id' => $this->cartId]);
            
        } catch (\Exception $e) {
            Log::error("Failed to cleanup temp files", [
                'error' => $e->getMessage(),
                'cart_id' => $this->cartId
            ]);
        }
    }
} 