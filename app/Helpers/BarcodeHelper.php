<?php

namespace App\Helpers;

use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGenerator;

class BarcodeHelper
{
    /**
     * Generate CODE-128 barcode image from barcode string
     * @param string $barcode
     * @return string Base64 encoded image
     */
    public static function generateBarcodeImage($barcode)
    {
        try {
            // Create CODE-128 barcode generator
            // CODE-128 supports any alphanumeric content and is more flexible than EAN-13
            $generator = new BarcodeGeneratorPNG();
            
            // Generate barcode image using CODE-128 (supports any length)
            $barcodeImage = $generator->getBarcode($barcode, $generator::TYPE_CODE_128, 2, 50);
            
            // Convert to base64
            return 'data:image/png;base64,' . base64_encode($barcodeImage);
            
        } catch (\Exception $e) {
            // Fallback to simple text if barcode generation fails
            \Log::error('Barcode generation failed: ' . $e->getMessage());
            return self::generateFallbackBarcode($barcode);
        }
    }
    
    /**
     * Generate fallback barcode using GD library
     * @param string $barcode
     * @return string Base64 encoded image
     */
    private static function generateFallbackBarcode($barcode)
    {
        // Simple barcode generation using GD library as fallback
        $width = 250;
        $height = 60;
        
        // Create image
        $image = imagecreate($width, $height);
        
        // Colors
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        
        // Fill background
        imagefill($image, 0, 0, $white);
        
        // Add text
        $fontSize = 3;
        $textWidth = strlen($barcode) * imagefontwidth($fontSize);
        $textX = ($width - $textWidth) / 2;
        imagestring($image, $fontSize, $textX, 20, $barcode, $black);
        
        // Convert to base64
        ob_start();
        imagepng($image);
        $imageData = ob_get_contents();
        ob_end_clean();
        
        imagedestroy($image);
        
        return 'data:image/png;base64,' . base64_encode($imageData);
    }
} 