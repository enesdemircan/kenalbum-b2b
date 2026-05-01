<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use App\Helpers\AuthorizationHelper;
use App\Listeners\AssignCustomerRole;
use Illuminate\Auth\Events\Registered;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // R2UploadService singleton — S3Client init'i her cart item için tekrarlanmasın
        // (checkout'ta loop içinde cart_id rename yaparken instance reuse edilsin)
        $this->app->singleton(\App\Services\R2UploadService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Mail gönderiminde site ayarlarından "From" adını kullan (UTF-8 encoding için)
        $settings = \App\Models\SiteSetting::first();
        if ($settings && ($settings->company_title || $settings->title)) {
            config(['mail.from.name' => $settings->company_title ?? $settings->title]);
        }
        // Pagination View
        \Illuminate\Pagination\Paginator::defaultView('pagination.material');
        \Illuminate\Pagination\Paginator::defaultSimpleView('pagination.material');
        
        // Model Observers
        \App\Models\Order::observe(\App\Observers\OrderObserver::class);
        
        // Event listeners
        Event::listen(Registered::class, AssignCustomerRole::class);

        // Role directive
        Blade::directive('role', function ($expression) {
            return "<?php if (App\Helpers\AuthorizationHelper::hasRole($expression)): ?>";
        });

        Blade::directive('endrole', function () {
            return "<?php endif; ?>";
        });

        // Permission directive
        Blade::directive('permission', function ($expression) {
            return "<?php if (App\Helpers\AuthorizationHelper::hasPermission($expression)): ?>";
        });

        Blade::directive('endpermission', function () {
            return "<?php endif; ?>";
        });

        // Any role directive
        Blade::directive('anyrole', function ($expression) {
            return "<?php if (App\Helpers\AuthorizationHelper::hasAnyRole($expression)): ?>";
        });

        Blade::directive('endanyrole', function () {
            return "<?php endif; ?>";
        });

        // Any permission directive
        Blade::directive('anypermission', function ($expression) {
            return "<?php if (App\Helpers\AuthorizationHelper::hasAnyPermission($expression)): ?>";
        });

        Blade::directive('endanypermission', function () {
            return "<?php endif; ?>";
        });

        // Route access directive
        Blade::directive('canaccess', function ($expression) {
            return "<?php if (App\Helpers\AuthorizationHelper::canAccessRoute($expression)): ?>";
        });

        Blade::directive('endcanaccess', function () {
            return "<?php endif; ?>";
        });

        // Action permission directive
        Blade::directive('canperform', function ($expression) {
            return "<?php if (App\Helpers\AuthorizationHelper::canPerformAction($expression)): ?>";
        });

        Blade::directive('endcanperform', function () {
            return "<?php endif; ?>";
        });

        // Administrator directive
        Blade::directive('administrator', function () {
            return "<?php if (App\Helpers\AuthorizationHelper::isAdministrator()): ?>";
        });

        Blade::directive('endadministrator', function () {
            return "<?php endif; ?>";
        });

        // Fiyat görüntüleme — admin (role 1) veya editor (role 3) ise true.
        // Eski pattern "Auth::check() and roles->contains('id',3) or roles->contains('id',1)"
        // PHP precedence yüzünden buggy idi (and/or assignment seviyesinde) — bu directive
        // tek doğru noktada kontrol eder.
        Blade::if('canSeePrices', function () {
            if (!auth()->check()) return false;
            $roles = auth()->user()->roles;
            return $roles->contains('id', 1) || $roles->contains('id', 3);
        });
    }
}
