<?php

namespace App\Helpers;

class TurkishHelper
{
    public static function turkishToUpper($string)
    {
        $turkish = array("ç", "ğ", "ı", "i", "ö", "ş", "ü");
        $turkishUpper = array("Ç", "Ğ", "I", "İ", "Ö", "Ş", "Ü");
        
        $string = str_replace($turkish, $turkishUpper, $string);
        return mb_strtoupper($string, 'UTF-8');
    }
} 