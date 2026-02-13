<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CartFile;
use App\Models\Cart;
use Illuminate\Support\Facades\Log;

class CartFileController extends Controller
{
    public function index()
    {
        $cartFiles = CartFile::with(['cart.user', 'cart.product', 'customizationPivotParam'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        // İstatistikler
        $stats = [
            'total' => CartFile::count(),
            'pending' => CartFile::where('status', 'pending')->count(),
            'uploading' => CartFile::where('status', 'uploading')->count(),
            'completed' => CartFile::where('status', 'completed')->count(),
            'failed' => CartFile::where('status', 'failed')->count(),
        ];

        return view('admin.cart_files.index', compact('cartFiles', 'stats'));
    }

    public function show($id)
    {
        $cartFile = CartFile::with(['cart.user', 'cart.product', 'customizationPivotParam'])
            ->findOrFail($id);

        return view('admin.cart_files.show', compact('cartFile'));
    }

    public function retry($id)
    {
        $cartFile = CartFile::findOrFail($id);
        
        if ($cartFile->status === 'failed') {
            $cartFile->update([
                'status' => 'pending',
                'error_message' => null
            ]);

            // Job'u tekrar dispatch et
            \App\Jobs\ProcessCartFileUpload::dispatch($cartFile);

            return response()->json([
                'success' => true,
                'message' => 'Dosya yeniden kuyruğa eklendi'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Sadece başarısız dosyalar yeniden denenebilir'
        ]);
    }

    public function delete($id)
    {
        $cartFile = CartFile::findOrFail($id);
        
        // S3'ten dosyayı sil
        if ($cartFile->s3_url) {
            try {
                $disk = \Storage::disk('s3');
                $s3Path = $cartFile->s3_url;
                $disk->delete($s3Path);
                
                Log::info('Cart file S3\'ten silindi:', [
                    'cart_file_id' => $cartFile->id,
                    's3_path' => $s3Path
                ]);
            } catch (\Exception $e) {
                Log::warning('S3 dosyası silinirken hata:', [
                    'cart_file_id' => $cartFile->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Local dosyayı sil
        if ($cartFile->local_file_url && file_exists($cartFile->local_file_url)) {
            unlink($cartFile->local_file_url);
        }

        $cartFile->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dosya başarıyla silindi'
        ]);
    }

    public function clearFailed()
    {
        $failedFiles = CartFile::where('status', 'failed')->get();
        $deletedCount = 0;

        foreach ($failedFiles as $cartFile) {
            // S3'ten dosyayı sil
            if ($cartFile->s3_url) {
                try {
                    $disk = \Storage::disk('s3');
                    $disk->delete($cartFile->s3_url);
                } catch (\Exception $e) {
                    Log::warning('S3 dosyası silinirken hata:', [
                        'cart_file_id' => $cartFile->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Local dosyayı sil
            if ($cartFile->local_file_url && file_exists($cartFile->local_file_url)) {
                unlink($cartFile->local_file_url);
            }

            $cartFile->delete();
            $deletedCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "$deletedCount başarısız dosya silindi"
        ]);
    }

    public function getStats()
    {
        $stats = [
            'total' => CartFile::count(),
            'pending' => CartFile::where('status', 'pending')->count(),
            'uploading' => CartFile::where('status', 'uploading')->count(),
            'completed' => CartFile::where('status', 'completed')->count(),
            'failed' => CartFile::where('status', 'failed')->count(),
        ];

        return response()->json($stats);
    }
} 