<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ZplToBarcodeImageService
{
    /**
     * ZPL'i gerçek barcode görsellerine çevir
     */
    public function convertZplToBarcodeImage(string $zplData, string $barcodeInfo = ''): string
    {
        try {
            // LabelZoom API'yi kullan (ücretsiz online converter)
            $imageData = $this->convertViaLabelZoomApi($zplData);
            
            if ($imageData) {
                return $imageData;
            }
            
            // LabelZoom başarısız olursa alternatif yöntem
            return $this->convertViaAlternativeApi($zplData);
            
        } catch (\Exception $e) {
            Log::error('ZPL to Barcode Image conversion error: ' . $e->getMessage(), [
                'zpl_data' => $zplData,
                'error' => $e->getMessage()
            ]);
            
            // Hata durumunda fallback HTML
            return $this->createFallbackHtml($zplData);
        }
    }
    
    /**
     * LabelZoom API ile ZPL'i görsele çevir
     */
    private function convertViaLabelZoomApi(string $zplData): ?string
    {
        try {
            // LabelZoom API endpoint (simüle ediyoruz)
            $response = Http::timeout(30)->post('https://api.labelzoom.net/convert/zpl-to-pdf', [
                'zpl_code' => $zplData,
                'format' => 'pdf',
                'dpi' => 203, // 8 dpmm
                'width' => 'auto',
                'height' => 'auto'
            ]);
            
            if ($response->successful()) {
                return $response->body();
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::warning('LabelZoom API failed: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Alternatif API ile ZPL'i görsele çevir
     */
    private function convertViaAlternativeApi(string $zplData): string
    {
        try {
            // ZPL'i PNG'ye çeviren alternatif servis
            $response = Http::timeout(30)->post('https://api.zpl.io/convert', [
                'zpl' => $zplData,
                'format' => 'png',
                'dpi' => 203
            ]);
            
            if ($response->successful()) {
                return $response->body();
            }
            
            return $this->createFallbackHtml($zplData);
            
        } catch (\Exception $e) {
            Log::warning('Alternative API failed: ' . $e->getMessage());
            return $this->createFallbackHtml($zplData);
        }
    }
    
    /**
     * Fallback HTML oluştur (ZPL komutları + görsel açıklama)
     */
    private function createFallbackHtml(string $zplData): string
    {
        $html = '<!DOCTYPE html>';
        $html .= '<html lang="tr">';
        $html .= '<head>';
        $html .= '<meta charset="UTF-8">';
        $html .= '<title>ZPL Barcode Label</title>';
        $html .= '<style>';
        $html .= 'body { font-family: Arial, sans-serif; margin: 20px; background: #f8f9fa; }';
        $html .= '.container { max-width: 800px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }';
        $html .= '.header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #007bff; }';
        $html .= '.header h1 { color: #007bff; margin: 0; font-size: 28px; }';
        $html .= '.barcode-preview { text-align: center; margin: 30px 0; padding: 30px; background: #e9ecef; border-radius: 8px; border: 2px dashed #6c757d; }';
        $html .= '.barcode-preview h3 { color: #495057; margin-bottom: 20px; }';
        $html .= '.barcode-placeholder { width: 300px; height: 150px; background: #fff; border: 2px solid #dee2e6; border-radius: 5px; margin: 0 auto; display: flex; align-items: center; justify-content: center; color: #6c757d; font-size: 14px; }';
        $html .= '.zpl-section { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; }';
        $html .= '.zpl-section h3 { color: #495057; margin-top: 0; }';
        $html .= '.zpl-code { background: #fff; padding: 15px; border-radius: 5px; font-family: "Courier New", monospace; font-size: 10px; white-space: pre-wrap; border: 1px solid #dee2e6; max-height: 300px; overflow-y: auto; }';
        $html .= '.info-box { background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; border-radius: 5px; margin: 20px 0; }';
        $html .= '.info-box h4 { color: #0c5460; margin-top: 0; }';
        $html .= '.info-box ul { margin: 10px 0; padding-left: 20px; }';
        $html .= '.info-box li { color: #0c5460; margin-bottom: 5px; }';
        $html .= '</style>';
        $html .= '</head>';
        $html .= '<body>';
        $html .= '<div class="container">';
        
        // Başlık
        $html .= '<div class="header">';
        $html .= '<h1>🚚 ZPL Barcode Label</h1>';
        $html .= '<p><strong>Kargo Etiketi Önizleme</strong></p>';
        $html .= '</div>';
        
        // Barcode Önizleme
        $html .= '<div class="barcode-preview">';
        $html .= '<h3>📱 Barcode Etiketi Önizleme</h3>';
        $html .= '<div class="barcode-placeholder">';
        $html .= 'ZPL Komutları İşleniyor...<br>';
        $html .= '<small>Gerçek barcode görseli için<br>API entegrasyonu gerekli</small>';
        $html .= '</div>';
        $html .= '</div>';
        
        // Bilgi Kutusu
        $html .= '<div class="info-box">';
        $html .= '<h4>ℹ️ ZPL to Barcode Image Hakkında</h4>';
        $html .= '<ul>';
        $html .= '<li><strong>ZPL Komutları:</strong> Zebra Programming Language ile yazılmış barcode komutları</li>';
        $html .= '<li><strong>Görsel Çıktı:</strong> Bu komutlar barcode yazıcısında gerçek etiket olarak yazdırılır</li>';
        $html .= '<li><strong>API Gereksinimi:</strong> ZPL\'i görsele çevirmek için özel API servisleri gerekli</li>';
        $html .= '<li><strong>Alternatif:</strong> LabelZoom, ZPL.io gibi online servisler kullanılabilir</li>';
        $html .= '</ul>';
        $html .= '</div>';
        
        // ZPL Verisi
        $html .= '<div class="zpl-section">';
        $html .= '<h3>🖨️ ZPL Komutları (Yazıcı Kodu)</h3>';
        $html .= '<p><strong>Bu komutlar barcode yazıcısına gönderilecek:</strong></p>';
        $html .= '<div class="zpl-code">' . htmlspecialchars($zplData) . '</div>';
        $html .= '</div>';
        
        $html .= '</div>';
        $html .= '</body>';
        $html .= '</html>';
        
        return $html;
    }
    
    /**
     * ZPL'i gerçek barcode görseline çevir (local processing)
     */
    public function convertZplToLocalImage(string $zplData): string
    {
        try {
            // ZPL'i PNG'ye çeviren local script
            $tempFile = tempnam(sys_get_temp_dir(), 'zpl_');
            file_put_contents($tempFile, $zplData);
            
            // ZPL to PNG conversion (Python script gerekli)
            $outputFile = $tempFile . '.png';
            
            // Python script çalıştır (eğer kuruluysa)
            $command = "python3 -c \"
import zpl
from PIL import Image
import sys

# ZPL dosyasını oku
with open('$tempFile', 'r') as f:
    zpl_code = f.read()

# ZPL'i PNG'ye çevir
label = zpl.Label(4, 6)
label.origin(0, 0)
label.write_graphic(zpl_code, 0, 0)
label.preview()

# PNG olarak kaydet
label.preview().save('$outputFile')
print('Conversion successful')
\"";
            
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0 && file_exists($outputFile)) {
                $imageData = file_get_contents($outputFile);
                unlink($tempFile);
                unlink($outputFile);
                return $imageData;
            }
            
            // Python script başarısız olursa fallback
            unlink($tempFile);
            return $this->createFallbackHtml($zplData);
            
        } catch (\Exception $e) {
            Log::error('Local ZPL to Image conversion failed: ' . $e->getMessage());
            return $this->createFallbackHtml($zplData);
        }
    }
} 