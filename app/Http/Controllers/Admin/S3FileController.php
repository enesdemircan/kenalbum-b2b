<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class S3FileController extends Controller
{
    public function index(Request $request)
    {
        try {
            $disk = Storage::disk('s3');
            $currentPath = $request->get('path', '');
            $files = [];
            $folders = [];
            $totalSize = 0;
            $fileCount = 0;

            if (empty($currentPath)) {
                // Ana klasörleri listele
                $this->listMainFolders($disk, $folders, $files, $totalSize, $fileCount);
            } else {
                // Belirli klasörün içeriğini listele
                $this->listFolderContents($disk, $currentPath, $folders, $files, $totalSize, $fileCount);
            }

            // Dosyaları boyuta göre sırala (en büyük önce)
            usort($files, function($a, $b) {
                return $b['size'] - $a['size'];
            });

            return view('admin.s3_files.index', compact('files', 'folders', 'totalSize', 'fileCount', 'currentPath'));
            
        } catch (\Exception $e) {
            Log::error('S3 dosyaları listelenirken hata:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'S3 dosyaları listelenirken hata oluştu: ' . $e->getMessage());
        }
    }
    
    /**
     * Ana klasörleri listele
     */
    private function listMainFolders($disk, &$folders, &$files, &$totalSize, &$fileCount)
    {
        $allFiles = $disk->allFiles();
        
        // Klasör yapısını analiz et
        $folderStructure = [];
        foreach ($allFiles as $file) {
            $parts = explode('/', $file);
            if (count($parts) > 1) {
                $mainFolder = $parts[0];
                if (!isset($folderStructure[$mainFolder])) {
                    $folderStructure[$mainFolder] = [
                        'file_count' => 0,
                        'total_size' => 0
                    ];
                }
                
                $size = $disk->size($file);
                $folderStructure[$mainFolder]['file_count']++;
                $folderStructure[$mainFolder]['total_size'] += $size;
                $totalSize += $size;
                $fileCount++;
            }
        }
        
        // Klasörleri oluştur
        foreach ($folderStructure as $folderName => $stats) {
            $folders[] = [
                'name' => $folderName,
                'path' => $folderName,
                'file_count' => $stats['file_count'],
                'total_size' => $stats['total_size'],
                'size_formatted' => $this->formatBytes($stats['total_size'])
            ];
        }
    }
    
    /**
     * Klasör içeriğini listele
     */
    private function listFolderContents($disk, $path, &$folders, &$files, &$totalSize, &$fileCount)
    {
        $allFiles = $disk->allFiles($path);
        
        foreach ($allFiles as $file) {
            $relativePath = str_replace($path . '/', '', $file);
            
            if (strpos($relativePath, '/') !== false) {
                // Alt klasör
                $folderName = explode('/', $relativePath)[0];
                $folderPath = $path . '/' . $folderName;
                
                if (!in_array($folderPath, array_column($folders, 'path'))) {
                    $folders[] = [
                        'name' => $folderName,
                        'path' => $folderPath,
                        'file_count' => 0,
                        'total_size' => 0,
                        'size_formatted' => '0 B'
                    ];
                }
            } else {
                // Dosya
                $size = $disk->size($file);
                $lastModified = $disk->lastModified($file);
                
                $files[] = [
                    'path' => $file,
                    'name' => basename($file),
                    'size' => $size,
                    'size_formatted' => $this->formatBytes($size),
                    'last_modified' => date('Y-m-d H:i:s', $lastModified),
                    'url' => $disk->url($file)
                ];
                
                $totalSize += $size;
                $fileCount++;
            }
        }
        
        // Klasör boyutlarını hesapla
        foreach ($folders as &$folder) {
            $folderFiles = $disk->allFiles($folder['path']);
            $folderSize = 0;
            foreach ($folderFiles as $file) {
                $folderSize += $disk->size($file);
            }
            $folder['total_size'] = $folderSize;
            $folder['size_formatted'] = $this->formatBytes($folderSize);
        }
    }

    public function delete(Request $request)
    {
        $request->validate([
            'file_path' => 'required|string'
        ]);

        try {
            $disk = Storage::disk('s3');
            $filePath = $request->file_path;

            if ($disk->exists($filePath)) {
                $disk->delete($filePath);
                
                Log::info('S3 dosyası silindi:', [
                    'file_path' => $filePath,
                    'deleted_by' => auth()->id()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Dosya başarıyla silindi'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Dosya bulunamadı'
                ], 404);
            }
            
        } catch (\Exception $e) {
            Log::error('S3 dosyası silinirken hata:', [
                'file_path' => $request->file_path,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Dosya silinirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'file_paths' => 'required|array',
            'file_paths.*' => 'string'
        ]);

        try {
            $disk = Storage::disk('s3');
            $deletedCount = 0;
            $errors = [];

            foreach ($request->file_paths as $filePath) {
                if ($disk->exists($filePath)) {
                    $disk->delete($filePath);
                    $deletedCount++;
                    
                    Log::info('S3 dosyası silindi (toplu):', [
                        'file_path' => $filePath,
                        'deleted_by' => auth()->id()
                    ]);
                } else {
                    $errors[] = "Dosya bulunamadı: $filePath";
                }
            }

            return response()->json([
                'success' => true,
                'message' => "$deletedCount dosya başarıyla silindi",
                'deleted_count' => $deletedCount,
                'errors' => $errors
            ]);
            
        } catch (\Exception $e) {
            Log::error('S3 dosyaları toplu silinirken hata:', [
                'file_paths' => $request->file_paths,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Dosyalar silinirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function clearAll(Request $request)
    {
        try {
            $disk = Storage::disk('s3');
            $allFiles = $disk->allFiles();
            $deletedCount = 0;

            foreach ($allFiles as $file) {
                $disk->delete($file);
                $deletedCount++;
            }

            Log::info('Tüm S3 dosyaları silindi:', [
                'deleted_count' => $deletedCount,
                'deleted_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Tüm dosyalar başarıyla silindi ($deletedCount dosya)",
                'deleted_count' => $deletedCount
            ]);
            
        } catch (\Exception $e) {
            Log::error('Tüm S3 dosyaları silinirken hata:', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Dosyalar silinirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function formatBytes($size, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }
} 