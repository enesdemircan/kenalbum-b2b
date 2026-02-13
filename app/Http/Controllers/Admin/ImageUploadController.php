<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
    /**
     * Upload image for CKEditor
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        try {
            if ($request->hasFile('upload')) {
                $file = $request->file('upload');
                $fileName = time() . '_' . $file->getClientOriginalName();
                
                // Public disk'e kaydet
                $path = $file->storeAs('uploads/images', $fileName, 'public');
                
                // URL oluştur
                $url = Storage::disk('public')->url($path);
                
                // CKEditor format
                return response()->json([
                    'uploaded' => true,
                    'url' => $url
                ]);
            }
            
            return response()->json([
                'uploaded' => false,
                'error' => [
                    'message' => 'Dosya yüklenemedi'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'uploaded' => false,
                'error' => [
                    'message' => $e->getMessage()
                ]
            ]);
        }
    }
}

