<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cart;

class UpdateCartIdentifiers extends Command
{
    protected $signature = 'carts:update-identifiers';
    protected $description = 'Update existing carts with new cart_id and barcode fields';

    public function handle()
    {
        $this->info('Updating existing carts with new identifiers...');
        
        $carts = Cart::with('order')->get();
        $count = 0;
        $bar = $this->output->createProgressBar($carts->count());
        $bar->start();

        foreach ($carts as $cart) {
            try {
                $oldCartId = $cart->cart_id;
                $oldBarcode = $cart->barcode;

                // Generate new identifiers (sipariş numarası varsa onu kullan)
                $orderNumber = $cart->order ? $cart->order->order_number : null;
                $newCartId = $cart->generateCartIdentifier($orderNumber);
                $newBarcode = $cart->generateUniqueBarcode();
                
                // Update cart
                $cart->cart_id = $newCartId;
                $cart->barcode = $newBarcode;
                $cart->save();
                
                $count++;
                
                // Show changes for first few carts
                if ($count <= 5) {
                    $this->line("\nUpdated Cart ID {$cart->id}:");
                    $this->line("  Cart ID: {$oldCartId} → {$newCartId}");
                    $this->line("  Barcode: {$oldBarcode} → {$newBarcode}");
                }
                
            } catch (\Exception $e) {
                $this->error("Error updating cart ID {$cart->id}: " . $e->getMessage());
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info("Successfully updated {$count} carts with new identifiers.");
        
        return 0;
    }
} 