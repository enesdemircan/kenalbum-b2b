<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cart;

class GenerateCartBarcodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'carts:generate-barcodes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate barcodes for existing cart items';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating barcodes for existing cart items...');
        
        $carts = Cart::whereNull('barcode')->get();
        $count = 0;
        
        $bar = $this->output->createProgressBar($carts->count());
        $bar->start();
        
        foreach ($carts as $cart) {
            try {
                $cart->barcode = $cart->generateBarcode();
                $cart->save();
                $count++;
            } catch (\Exception $e) {
                $this->error("Error generating barcode for cart ID {$cart->id}: " . $e->getMessage());
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        
        $this->info("Successfully generated barcodes for {$count} cart items.");
        
        return 0;
    }
} 