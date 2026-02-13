<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

// Horizon Windows'ta çalışmaz (pcntl extension gerektirir)
// Bu yüzden şartlı olarak yüklenecek
if (class_exists(\Laravel\Horizon\HorizonApplicationServiceProvider::class)) {
    class HorizonServiceProvider extends \Laravel\Horizon\HorizonApplicationServiceProvider
    {
        /**
         * Bootstrap any application services.
         */
        public function boot(): void
        {
            parent::boot();

            // Horizon::routeSmsNotificationsTo('15556667777');
            // Horizon::routeMailNotificationsTo('example@example.com');
            // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');
        }

        /**
         * Register the Horizon gate.
         *
         * This gate determines who can access Horizon in non-local environments.
         */
        protected function gate(): void
        {
            Gate::define('viewHorizon', function ($user = null) {
                return in_array(optional($user)->email, [
                    //
                ]);
            });
        }
    }
} else {
    // Horizon yüklü değilse boş bir ServiceProvider oluştur
    class HorizonServiceProvider extends ServiceProvider
    {
        public function boot(): void
        {
            // Horizon yüklü değil
        }

        public function register(): void
        {
            // Horizon yüklü değil
        }
    }
}
