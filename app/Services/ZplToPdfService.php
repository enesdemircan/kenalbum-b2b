<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use setasign\Fpdi\Fpdi;
use setasign\Fpdf\Fpdf;

class ZplToPdfService
{
    /**
     * ZPL verisini PDF'e çevir (FPDI ile)
     */
    public function convertZplToPdf(string $zplData, string $barcodeInfo = ''): string
    {
        try {
            // FPDI ile PDF oluştur
            $pdf = new Fpdi();
            
            // PDF ayarları
            $pdf->SetAutoPageBreak(true, 10);
            $pdf->SetMargins(20, 20, 20);
            
            // Sayfa ekle
            $pdf->AddPage();
            
            // Font ayarları
            $pdf->SetFont('Courier', '', 10);
            
            // Başlık
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(0, 10, 'ZPL Barcode Commands', 0, 1, 'C');
            $pdf->Ln(5);
            
            // ZPL verisi
            $pdf->SetFont('Courier', '', 9);
            $pdf->SetTextColor(0, 0, 0);
            
            // ZPL verisini satırlara böl
            $zplLines = explode("\n", $zplData);
            
            foreach ($zplLines as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    // Satır çok uzunsa böl
                    if (strlen($line) > 80) {
                        $chunks = str_split($line, 80);
                        foreach ($chunks as $chunk) {
                            $pdf->Cell(0, 5, $chunk, 0, 1);
                        }
                    } else {
                        $pdf->Cell(0, 5, $line, 0, 1);
                    }
                } else {
                    // Boş satır
                    $pdf->Ln(3);
                }
            }
            
            // PDF'i string olarak döndür
            return $pdf->Output('S');
            
        } catch (\Exception $e) {
            Log::error('ZPL to PDF conversion error (FPDI): ' . $e->getMessage(), [
                'zpl_data' => $zplData,
                'error' => $e->getMessage()
            ]);
            
            // FPDI başarısız olursa HTML to PDF dene
            return $this->convertZplToHtml($zplData);
        }
    }
    
    /**
     * ZPL verisini HTML'e çevir (DomPDF için)
     */
    public function convertZplToHtml(string $zplData): string
    {
        try {
            // ZPL verisini HTML olarak formatla
            $html = '<!DOCTYPE html>';
            $html .= '<html lang="tr">';
            $html .= '<head>';
            $html .= '<meta charset="UTF-8">';
            $html .= '<title>ZPL Barcode Commands</title>';
            $html .= '<style>';
            $html .= 'body { font-family: "Courier New", monospace; margin: 20px; font-size: 10px; line-height: 1.2; background: #fff; }';
            $html .= '.zpl-header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }';
            $html .= '.zpl-header h1 { color: #333; margin: 0; font-size: 18px; font-family: Arial, sans-serif; }';
            $html .= '.zpl-code { white-space: pre-wrap; word-wrap: break-word; background: #f8f9fa; padding: 15px; border: 1px solid #dee2e6; border-radius: 5px; font-size: 9px; }';
            $html .= '.zpl-info { background: #e9ecef; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 11px; }';
            $html .= '</style>';
            $html .= '</head>';
            $html .= '<body>';
            $html .= '<div class="zpl-header">';
            $html .= '<h1>ZPL Barcode Commands</h1>';
            $html .= '</div>';
            $html .= '<div class="zpl-info">';
            $html .= '<strong>ZPL Komutları:</strong> Bu veri barcode yazıcısına gönderilmek üzere hazırlanmıştır.';
            $html .= '</div>';
            $html .= '<div class="zpl-code">' . htmlspecialchars($zplData) . '</div>';
            $html .= '</body>';
            $html .= '</html>';
            
            return $html;
            
        } catch (\Exception $e) {
            Log::error('ZPL to HTML conversion error: ' . $e->getMessage());
            throw new \Exception('ZPL to HTML conversion failed: ' . $e->getMessage());
        }
    }
} 