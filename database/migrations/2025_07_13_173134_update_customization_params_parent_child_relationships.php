<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Nubuk parametresini bul (ID'si muhtemelen 9-12 arası)
        $nubukParam = DB::table('customization_params')
            ->where('key', 'Nubuk')
            ->where('customization_category_id', 3) // Kumaş Seçimi kategorisi
            ->first();
            
        if ($nubukParam) {
            // NB-01 ve NB-02 parametrelerini Nubuk'un child'ları yap
            DB::table('customization_params')
                ->whereIn('key', ['NB-01', 'NB-02'])
                ->where('customization_category_id', 4) // Renk Seçimi kategorisi
                ->update(['ust_id' => $nubukParam->id]);
                
            echo "Updated NB-01 and NB-02 to be children of Nubuk (ID: {$nubukParam->id})\n";
        }
        
        // Keten parametresini bul
        $ketenParam = DB::table('customization_params')
            ->where('key', 'Keten')
            ->where('customization_category_id', 3) // Kumaş Seçimi kategorisi
            ->first();
            
        if ($ketenParam) {
            // K-01 ve K-02 parametrelerini Keten'in child'ları yap (eğer varsa)
            DB::table('customization_params')
                ->whereIn('key', ['K-01', 'K-02'])
                ->where('customization_category_id', 4) // Renk Seçimi kategorisi
                ->update(['ust_id' => $ketenParam->id]);
                
            echo "Updated K-01 and K-02 to be children of Keten (ID: {$ketenParam->id})\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // NB-01 ve NB-02'yi tekrar parent'sız yap
        DB::table('customization_params')
            ->whereIn('key', ['NB-01', 'NB-02'])
            ->update(['ust_id' => 0]);
            
        // K-01 ve K-02'yi tekrar parent'sız yap
        DB::table('customization_params')
            ->whereIn('key', ['K-01', 'K-02'])
            ->update(['ust_id' => 0]);
    }
};
