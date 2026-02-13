<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;

class CartServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $cartCount = 0;
            
            if (Auth::check()) {
                $cartCount = Cart::where('user_id', Auth::id())
                    ->where('status', 0) // Sadece status = 0 olan sepet ürünlerini say
                    ->sum('quantity');
            }
            
            $view->with('cartCount', $cartCount);
        });
    }
}
