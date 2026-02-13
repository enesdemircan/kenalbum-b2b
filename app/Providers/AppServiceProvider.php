<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
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

        // Pagination view'ını ayarla
        \Illuminate\Pagination\Paginator::defaultView('vendor.pagination.bootstrap-5');
        \Illuminate\Pagination\Paginator::defaultSimpleView('vendor.pagination.simple-bootstrap-5');
    }
}
