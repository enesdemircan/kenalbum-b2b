<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ChunkUploadController extends Controller
{
    /**
     * Chunk yükle  
     */  
    public function uploadChunk(Request $request)
    {
        // Timeout ve memory ayarları (CloudFlare 502 bypass - 10MB chunks FAST)
        set_time_limit(300); // 5 dakika
        ini_set('max_execution_time', '300');
        ini_set('memory_limit', '512M');
        
        // Connection timeout ayarları (502 bypass)
        ini_set('default_socket_timeout', '300');
        ini_set('max_input_time', '300');

        // CORS headers (502 bypass için)
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, X-CSRF-TOKEN, Accept');

        try {
            // Auth kontrolü
            if (!auth()->check()) {
                Log::warning('Upload chunk - Unauthorized attempt');
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized - Please login'
                ], 401);
            }

            // Validation - chunk max 12MB (10MB chunk + buffer)
            $validated = $request->validate([
                'cart_id' => 'required|integer',
                'file_index' => 'required|integer',
                'chunk_index' => 'required|integer',
                'total_chunks' => 'required|integer',
                'file_name' => 'required|string|max:255',
                'chunk' => 'required|file|max:12288' // Max 12MB per chunk (10MB + buffer)
            ]);
 
            $cartId = $validated['cart_id'];
            $fileIndex = $validated['file_index'];
            $chunkIndex = $validated['chunk_index'];
            $totalChunks = $validated['total_chunks'];
            $fileName = $validated['file_name'];
            $chunk = $request->file('chunk');

            // Chunk file kontrolü
            if (!$chunk->isValid()) {
                Log::error('Upload chunk - Invalid file', ['chunk_index' => $chunkIndex]);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid chunk file'
                ], 400);
            }

            // Chunk dosyasını güvenli oku
            $chunkPath = $chunk->getRealPath();
            if (!file_exists($chunkPath)) {
                Log::error('Upload chunk - File not found', ['path' => $chunkPath]);
                return response()->json([
                    'success' => false,
                    'message' => 'Chunk file not found'
                ], 400);
            }

            $chunkContent = @file_get_contents($chunkPath);
            if ($chunkContent === false) {
                Log::error('Upload chunk - Cannot read file', ['path' => $chunkPath]);
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot read chunk file'
                ], 500);
            }
            
            // Chunk boyutunu logla
            Log::info("Chunk received (binary)", [
                'cart_id' => $cartId,
                'file_index' => $fileIndex,
                'chunk_index' => $chunkIndex,
                'chunk_size' => strlen($chunkContent),
                'file_size' => $chunk->getSize()
            ]);
            
            // Chunk dizinini oluştur
            $chunkDir = "chunks/{$cartId}/{$fileIndex}";
            if (!Storage::disk('public')->exists($chunkDir)) {
                Storage::disk('public')->makeDirectory($chunkDir);
            }

            // Chunk'ı kaydet
            $chunkPath = "{$chunkDir}/chunk_{$chunkIndex}.tmp";
            Storage::disk('public')->put($chunkPath, $chunkContent);

            // Chunk metadata'sını kaydet
            $chunkInfo = [
                'file_index' => $fileIndex,
                'chunk_index' => $chunkIndex,
                'total_chunks' => $totalChunks,
                'file_name' => $fileName,
                'uploaded_at' => now()->toISOString()
            ];

            Storage::disk('public')->put("{$chunkDir}/chunk_info.json", json_encode($chunkInfo));

            Log::info("Chunk uploaded", [
                'cart_id' => $cartId,
                'file_index' => $fileIndex,
                'chunk_index' => $chunkIndex,
                'total_chunks' => $totalChunks,
                'file_name' => $fileName,
                'chunk_size' => strlen($chunkContent)
            ]);

            // Response headers (502 bypass)
            return response()->json([
                'success' => true,
                'message' => 'Chunk uploaded successfully',
                'chunk_index' => $chunkIndex,
                'chunk_size' => strlen($chunkContent)
            ])->header('Cache-Control', 'no-cache, no-store, must-revalidate')
              ->header('Pragma', 'no-cache')
              ->header('Expires', '0')
              ->header('Connection', 'close');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("Chunk upload validation error", [
                'errors' => $e->errors(),
                'request_data' => [
                    'cart_id' => $request->input('cart_id'),
                    'file_index' => $request->input('file_index'),
                    'chunk_index' => $request->input('chunk_index'),
                    'has_chunk' => $request->hasFile('chunk'),
                    'chunk_size' => $request->file('chunk') ? $request->file('chunk')->getSize() : 0
                ]
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422)->header('Connection', 'close');

        } catch (\Exception $e) {
            Log::error("Chunk upload error - 502 PREVENTED", [
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => [
                    'cart_id' => $request->input('cart_id'),
                    'file_index' => $request->input('file_index'),
                    'chunk_index' => $request->input('chunk_index'),
                    'has_chunk' => $request->hasFile('chunk')
                ],
                'server_info' => [
                    'memory_usage' => memory_get_usage(true),
                    'memory_limit' => ini_get('memory_limit'),
                    'max_execution_time' => ini_get('max_execution_time')
                ]
            ]);

            // Chunk upload hatası durumunda sepeti sil (opsiyonel)
            // NOT: İlk chunk'ta başarısız olursa cart zaten yok olabilir
            try {
                $cartId = $request->input('cart_id');
                if ($cartId) {
                    $this->deleteCartOnError($cartId);
                }
            } catch (\Exception $deleteError) {
                Log::warning('Could not delete cart on error', ['error' => $deleteError->getMessage()]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
                'error_type' => get_class($e)
            ], 500)->header('Connection', 'close');
        }
    }
    
    /**
     * Test endpoint - 502 debug
     */
    public function testChunk(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Test endpoint working',
            'auth' => auth()->check(),
            'user_id' => auth()->id(),
            'memory' => memory_get_usage(true),
            'time' => now()->toDateTimeString()
        ]);
    }

    /**
     * Dosya chunk'larını merge et
     */
    public function mergeFiles(Request $request)
    {
        // Auth kontrolü
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        
        // Timeout ve memory ayarlarını artır
        set_time_limit(600); // 10 dakika
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', '600');
        
        try {
            $request->validate([
                'cart_id' => 'required|integer'
            ]);

            $cartId = $request->input('cart_id');
            $chunksDir = "chunks/{$cartId}";
            
            if (!Storage::disk('public')->exists($chunksDir)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No chunks found for this cart'
                ], 404);
            }

            $mergedFiles = [];
            $directories = Storage::disk('public')->directories($chunksDir);

            foreach ($directories as $fileDir) {
                $fileIndex = basename($fileDir);
                $chunkInfoPath = "{$fileDir}/chunk_info.json";
                
                if (Storage::disk('public')->exists($chunkInfoPath)) {
                    $chunkInfo = json_decode(Storage::disk('public')->get($chunkInfoPath), true);
                    $fileName = $chunkInfo['file_name'];
                    $totalChunks = $chunkInfo['total_chunks'];

                    Log::info("Processing file directory", [
                        'file_dir' => $fileDir,
                        'file_index' => $fileIndex
                    ]);

                    // Dosyayı merge et
                    $mergedPath = $this->mergeFileChunks($fileDir, $totalChunks, $cartId, $fileName);
                    
                    if ($mergedPath) {
                        $mergedFiles[] = [
                            'original_name' => $fileName,
                            'stored_path' => $mergedPath
                        ];
                    }
                }
            }

            // Merge bilgilerini kaydet
            $mergeInfo = [
                'cart_id' => $cartId,
                'merged_files' => $mergedFiles,
                'merged_at' => now()->toISOString()
            ];

            $mergeInfoPath = "merged/{$cartId}/merge_info.json";
            Storage::disk('public')->put($mergeInfoPath, json_encode($mergeInfo));

            Log::info("Files merged successfully", [
                'cart_id' => $cartId,
                'merged_files_count' => count($mergedFiles)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Files merged successfully',
                'merged_files_count' => count($mergedFiles)
            ]);

        } catch (\Exception $e) {
            Log::error("File merge error", [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            // Hata durumunda sepeti sil
            $this->deleteCartOnError($cartId);

            return response()->json([
                'success' => false,
                'message' => 'File merge failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ZIP dosyası oluştur
     */
    public function createZip(Request $request)
    {
        // Auth kontrolü
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        
        try {
            $request->validate([
                'cart_id' => 'required|integer'
            ]);

            $cartId = $request->input('cart_id');
            $mergeInfoPath = "merged/{$cartId}/merge_info.json";
            
            if (!Storage::disk('public')->exists($mergeInfoPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No merge info found for this cart'
                ], 404);
            }

            $mergeInfo = json_decode(Storage::disk('public')->get($mergeInfoPath), true);
            $mergedFiles = $mergeInfo['merged_files'];

            // ZIP dizinini oluştur
            $zipDir = "zips/{$cartId}";
            if (!Storage::disk('public')->exists($zipDir)) {
                Storage::disk('public')->makeDirectory($zipDir);
            }
            
            // ZIP bilgilerini ÖNCE kaydet (job başlamadan)
            $zipInfo = [
                'cart_id' => $cartId,
                'status' => 'processing',
                'file_count' => count($mergedFiles),
                'created_at' => now()->toISOString()
            ];

            Storage::disk('public')->put("zips/{$cartId}/zip_info.json", json_encode($zipInfo));
            
            // ZIP bilgisinin kaydedildiğinden emin ol
            if (!Storage::disk('public')->exists("zips/{$cartId}/zip_info.json")) {
                throw new \Exception("Failed to create zip_info.json");
            }

            // Job'u HEMEN çalıştır (sync - queue worker gerekmez)
            \App\Jobs\CreateZipAndUploadToS3::dispatchSync($cartId, $mergedFiles);

            // Job tamamlandı (sync olduğu için)
            $cart = \App\Models\Cart::find($cartId);
            
            Log::info("ZIP processing completed", [
                'cart_id' => $cartId,
                'file_count' => count($mergedFiles),
                's3_zip' => $cart->s3_zip ?? 'not_set'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'ZIP created successfully',
                'file_count' => count($mergedFiles),
                'status' => 'completed',
                's3_zip' => $cart->s3_zip ?? null
            ]);

        } catch (\Exception $e) {
            Log::error("ZIP creation error", [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            // Hata durumunda sepeti sil
            $this->deleteCartOnError($cartId);

            return response()->json([
                'success' => false,
                'message' => 'ZIP creation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ZIP işlemi durumunu kontrol et
     */
    public function checkZipStatus($cartId)
    {
        try {
            // Cart'ı bul
            $cart = \App\Models\Cart::find($cartId);
            
            if (!$cart) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart not found',
                    'status' => 'not_found'
                ], 404);
            }
            
            // ZIP bilgilerini kontrol et
            $zipInfoPath = "zips/{$cartId}/zip_info.json";
            
            if (Storage::disk('public')->exists($zipInfoPath)) {
                $zipInfo = json_decode(Storage::disk('public')->get($zipInfoPath), true);
                
                // Failed durumu kontrolü
                if (isset($zipInfo['status']) && $zipInfo['status'] === 'failed') {
                    return response()->json([
                        'success' => false,
                        'status' => 'failed',
                        'message' => 'ZIP processing failed',
                        'error' => $zipInfo['error'] ?? 'Unknown error',
                        'file_count' => $zipInfo['file_count'] ?? 0
                    ]);
                }
                
                // S3 ZIP URL'i varsa işlem tamamlanmış
                if (!empty($cart->s3_zip)) {
                    return response()->json([
                        'success' => true,
                        'status' => 'completed',
                        's3_zip' => $cart->s3_zip,
                        'images' => $cart->images,
                        'file_count' => $zipInfo['file_count'] ?? 0
                    ]);
                }
                
                // Hala işlemde
                return response()->json([
                    'success' => true,
                    'status' => 'processing',
                    'message' => 'ZIP processing in progress',
                    'file_count' => $zipInfo['file_count'] ?? 0
                ]);
            }
            
            // ZIP bilgisi bulunamadı (henüz oluşturulmamış)
            return response()->json([
                'success' => true,
                'status' => 'pending',
                'message' => 'ZIP info not created yet'
            ]);
            
        } catch (\Exception $e) {
            Log::error("ZIP status check error", [
                'error' => $e->getMessage(),
                'cart_id' => $cartId
            ]);
            
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Error checking ZIP status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Dosya chunk'larını merge et
     */
    private function mergeFileChunks($fileDir, $totalChunks, $cartId, $fileName)
    {
        try {
            Log::info("Starting to merge file chunks", [
                'file_dir' => $fileDir,
                'total_chunks' => $totalChunks,
                'cart_id' => $cartId,
                'file_name' => $fileName
            ]);
            
            $mergedContent = '';
            
            // Chunk'ları sırayla oku ve birleştir
            for ($i = 0; $i < $totalChunks; $i++) {
                $chunkPath = "{$fileDir}/chunk_{$i}.tmp";
                
                if (!Storage::disk('public')->exists($chunkPath)) {
                    throw new \Exception("Chunk {$i} not found at path: {$chunkPath}");
                }

                $chunkContent = Storage::disk('public')->get($chunkPath);
                $mergedContent .= $chunkContent;
                
                Log::info("Chunk read successfully", [
                    'chunk_index' => $i,
                    'chunk_size' => strlen($chunkContent),
                    'total_merged_size' => strlen($mergedContent)
                ]);
            }
            
            // Merged dizinini oluştur
            $mergedDir = "merged/{$cartId}";
            if (!Storage::disk('public')->exists($mergedDir)) {
                Storage::disk('public')->makeDirectory($mergedDir);
            }
            
            // Dosya adını temizle
            $sanitizedFileName = $this->sanitizeFileName($fileName);
            $mergedFilePath = "{$mergedDir}/{$sanitizedFileName}";
            
            // Birleştirilmiş dosyayı kaydet
            Storage::disk('public')->put($mergedFilePath, $mergedContent);
            
            Log::info("Merged file saved", [
                'merged_file_path' => $mergedFilePath,
                'file_size' => strlen($mergedContent)
            ]);
            
            // Geçici chunk dizinini temizle
            Storage::disk('public')->deleteDirectory($fileDir);
            
            Log::info("File merged successfully", [
                'file_name' => $fileName,
                'merged_path' => $mergedFilePath
            ]);
            
            return $mergedFilePath;

        } catch (\Exception $e) {
            Log::error("File merge failed", [
                'error' => $e->getMessage(),
                'file_dir' => $fileDir,
                'cart_id' => $cartId
            ]);
            return false;
        }
    }

    /**
     * Hata durumunda sepeti ve ilgili dosyaları sil
     */
    private function deleteCartOnError($cartId)
    {
        try {
            // Cart'ı bul ve sil
            $cart = \App\Models\Cart::find($cartId);
            if ($cart) {
                $cart->delete();
                Log::info("Cart deleted due to error", ['cart_id' => $cartId]);
            }
            
            // Chunks dizinini temizle
            $chunksDir = "chunks/{$cartId}";
            if (Storage::disk('public')->exists($chunksDir)) {
                Storage::disk('public')->deleteDirectory($chunksDir);
                Log::info("Chunks directory cleaned up", ['cart_id' => $cartId]);
            }
            
            // Merged dizinini temizle
            $mergedDir = "merged/{$cartId}";
            if (Storage::disk('public')->exists($mergedDir)) {
                Storage::disk('public')->deleteDirectory($mergedDir);
                Log::info("Merged directory cleaned up", ['cart_id' => $cartId]);
            }
            
            // Zips dizinini temizle
            $zipsDir = "zips/{$cartId}";
            if (Storage::disk('public')->exists($zipsDir)) {
                Storage::disk('public')->deleteDirectory($zipsDir);
                Log::info("Zips directory cleaned up", ['cart_id' => $cartId]);
            }
            
            // Thumbnails dizinini temizle
            $thumbnailsDir = "thumbnails/{$cartId}";
            if (Storage::disk('public')->exists($thumbnailsDir)) {
                Storage::disk('public')->deleteDirectory($thumbnailsDir);
                Log::info("Thumbnails directory cleaned up", ['cart_id' => $cartId]);
            }
            
            Log::info("Cart cleanup completed after error", ['cart_id' => $cartId]);
            
        } catch (\Exception $e) {
            Log::error("Failed to cleanup cart after error", [
                'error' => $e->getMessage(),
                'cart_id' => $cartId
            ]);
        }
    }

    /**
     * Dosya adını temizle
     */
    private function sanitizeFileName($fileName)
    {
        // Özel karakterleri temizle
        $fileName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileName);
        
        // Çok uzun dosya adlarını kısalt
        if (strlen($fileName) > 100) {
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            $name = pathinfo($fileName, PATHINFO_FILENAME);
            $fileName = substr($name, 0, 90) . '.' . $extension;
        }
        
        return $fileName;
    }
} 