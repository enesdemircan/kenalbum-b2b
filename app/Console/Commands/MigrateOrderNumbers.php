<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Order;
use App\Models\Cart;

class MigrateOrderNumbers extends Command
{
    protected $signature = 'orders:migrate-numbers';
    protected $description = 'Migrate all order numbers to ken-XXXXXXXXX format and update cart identifiers';

    public function handle()
    {
        // 1. Eski formattaki siparişleri sıralı şekilde güncelle
        $orders = Order::where('order_number', 'not like', 'ken-%')
            ->orderBy('id')
            ->get();

        if ($orders->isEmpty()) {
            $this->info('All orders already use ken- format.');
        } else {
            $this->info("Migrating {$orders->count()} order numbers...");

            // Mevcut en yüksek ken- numarasını bul
            $lastKen = Order::where('order_number', 'like', 'ken-%')
                ->orderByRaw("CAST(SUBSTRING(order_number, 5) AS UNSIGNED) DESC")
                ->first();

            $nextNumber = $lastKen ? (int) substr($lastKen->order_number, 4) + 1 : 1;

            $bar = $this->output->createProgressBar($orders->count());
            $bar->start();

            foreach ($orders as $order) {
                $oldNumber = $order->order_number;
                $newNumber = 'ken-' . str_pad($nextNumber, 9, '0', STR_PAD_LEFT);

                $order->order_number = $newNumber;
                $order->save();

                if ($nextNumber <= 5) {
                    $this->line("\n  {$oldNumber} → {$newNumber}");
                }

                $nextNumber++;
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info("Migrated {$orders->count()} order numbers.");
        }

        // 2. Tüm cart_id'leri güncelle
        $this->newLine();
        $this->info('Updating cart identifiers...');

        $carts = Cart::with('order')->whereNotNull('order_id')->get();
        $bar = $this->output->createProgressBar($carts->count());
        $bar->start();
        $count = 0;

        foreach ($carts as $cart) {
            try {
                $oldCartId = $cart->cart_id;
                $orderNumber = $cart->order ? $cart->order->order_number : null;
                $newCartId = $cart->generateCartIdentifier($orderNumber);

                if ($oldCartId !== $newCartId) {
                    // S3'teki ZIP dosyasını rename et
                    if (!empty($cart->s3_zip)) {
                        try {
                            $oldPath = "zips/{$cart->id}/{$oldCartId}.zip";
                            $newPath = "zips/{$cart->id}/{$newCartId}.zip";

                            if (Storage::disk('s3')->exists($oldPath)) {
                                Storage::disk('s3')->copy($oldPath, $newPath);
                                Storage::disk('s3')->delete($oldPath);
                                $cart->s3_zip = Storage::disk('s3')->url($newPath);
                            }
                        } catch (\Exception $e) {
                            $this->warn("\n  S3 rename failed cart #{$cart->id}: " . $e->getMessage());
                        }
                    }

                    $cart->cart_id = $newCartId;
                    $cart->save();
                    $count++;

                    if ($count <= 5) {
                        $this->line("\n  {$oldCartId} → {$newCartId}");
                    }
                }
            } catch (\Exception $e) {
                $this->error("\nError cart #{$cart->id}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Updated {$count} cart identifiers.");

        return 0;
    }
}
