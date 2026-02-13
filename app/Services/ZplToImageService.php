<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ZplToImageService
{
    /**
     * ZPL'i gerçek barcode görseline çevir
     */
    public function convertZplToImage(string $zplData): ?string
    {
        try {
            // ZPL'i PNG'ye çeviren online servis
            $imageData = $this->convertViaZplConverter($zplData);
            
            if ($imageData) {
                return $imageData;
            }
            
            // Başarısız olursa null döndür
            return null;
            
        } catch (\Exception $e) {
            Log::error('ZPL to Image conversion error: ' . $e->getMessage(), [
                'zpl_data' => $zplData,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }
    
    /**
     * ZPL Converter API ile ZPL'i görsele çevir
     */
    private function convertViaZplConverter(string $zplData): ?string
    {
        try {
            // ZPL'i PNG'ye çeviren ücretsiz API
            $response = Http::timeout(30)->post('https://api.zpl.io/convert', [
                'zpl' => $zplData,
                'format' => 'png',
                'dpi' => 203
            ]);
            
            if ($response->successful()) {
                return $response->body();
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::warning('ZPL Converter API failed: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * ZPL'i base64 encoded image olarak döndür
     */
    public function convertZplToBase64(string $zplData): ?string
    {
        $imageData = $this->convertZplToImage($zplData);
        
        if ($imageData) {
            return base64_encode($imageData);
        }
        
        return null;
    }
} 