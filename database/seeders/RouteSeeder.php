<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class RouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Route seeder başlatılıyor...');
        
        try {
            // Admin route'larını import et
            $this->command->info('Admin route\'ları import ediliyor...');
            Artisan::call('route:import', [
                '--group' => 'admin',
                '--force' => true
            ]);
            
            // Customer route'larını import et
            $this->command->info('Customer route\'ları import ediliyor...');
            Artisan::call('route:import', [
                '--group' => 'customer',
                '--force' => true
            ]);
            
            // Frontend route'larını import et
            $this->command->info('Frontend route\'ları import ediliyor...');
            Artisan::call('route:import', [
                '--group' => 'frontend',
                '--force' => true
            ]);
            
            $this->command->info('Tüm route\'lar başarıyla import edildi.');
        } catch (\Exception $e) {
            $this->command->error('Route import hatası: ' . $e->getMessage());
        }
    }
}
