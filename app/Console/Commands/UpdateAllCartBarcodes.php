<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cart;

class UpdateAllCartBarcodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'carts:update-all-barcodes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all existing cart barcodes with the new format';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating all existing cart barcodes...');
        
        $carts = Cart::all();
        $count = 0;
        
        $bar = $this->output->createProgressBar($carts->count());
        $bar->start();
        
        foreach ($carts as $cart) {
            try {
                $oldBarcode = $cart->barcode;
                $newBarcode = $cart->generateBarcode();
                
                if ($oldBarcode !== $newBarcode) {
                    $cart->barcode = $newBarcode;
                    $cart->save();
                    $count++;
                    
                    $this->line("\nUpdated Cart ID {$cart->id}:");
                    $this->line("  Old: {$oldBarcode}");
                    $this->line("  New: {$newBarcode}");
                }
            } catch (\Exception $e) {
                $this->error("Error updating barcode for cart ID {$cart->id}: " . $e->getMessage());
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        
        $this->info("Successfully updated {$count} cart barcodes.");
        
        return 0;
    }
} 