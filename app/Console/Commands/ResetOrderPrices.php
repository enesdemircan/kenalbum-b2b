<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Cart;

class ResetOrderPrices extends Command
{
    protected $signature = 'orders:reset-prices';
    protected $description = 'Reset all order and cart prices to zero';

    public function handle()
    {
        $orderCount = Order::count();
        $cartCount = Cart::count();

        $this->info("Resetting prices for {$orderCount} orders and {$cartCount} carts...");

        Order::query()->update([
            'total_price' => 0,
            'discount_amount' => 0,
        ]);
        $this->info("Orders: total_price and discount_amount set to 0.");

        Cart::query()->update([
            'price' => 0,
            'original_price' => 0,
        ]);
        $this->info("Carts: price and original_price set to 0.");

        $this->info('Done.');
        return 0;
    }
}
