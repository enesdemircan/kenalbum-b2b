<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'approval' => \App\Http\Middleware\ApprovalMiddleware::class,
            'check.route.permission' => \App\Http\Middleware\CheckRoutePermission::class,
            'ensure.customer' => \App\Http\Middleware\EnsureUserHasCustomer::class,
            'cors' => \App\Http\Middleware\CorsMiddleware::class,
            'api.role' => \App\Http\Middleware\ApiRoleMiddleware::class,
            'api.docs' => \App\Http\Middleware\ApiDocsAccessMiddleware::class,
        ]);
 
        // ShareViewData'yı sadece web route'larına uygula
        $middleware->appendToGroup('web', \App\Http\Middleware\ShareViewData::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // API route'larında authentication hatası olduğunda JSON response dön
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kimlik doğrulama gerekli. Lütfen geçerli bir Bearer token gönderin.',
                    'error' => 'Unauthenticated'
                ], 401);
            }
        });
    })
    ->withProviders([
        \Laravel\Fortify\FortifyServiceProvider::class,
        \App\Providers\CartServiceProvider::class,
    ])
    ->create();
