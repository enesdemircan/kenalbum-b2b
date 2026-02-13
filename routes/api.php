<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\CustomizationPivotParamApiController;
use App\Http\Controllers\Api\CartApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
| 
*/ 

// ============================================================================
// AUTH ENDPOINTS (Token almak için)
// ============================================================================

// Login - Token Al (Role ID 11 gerekli)
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// Logout - Token İptal Et (Authentication gerekli)
Route::middleware(['auth:sanctum', 'api.role'])->post('/logout', [AuthController::class, 'logout'])->name('api.logout');

// Logout All - Tüm Token'ları İptal Et
Route::middleware(['auth:sanctum', 'api.role'])->post('/logout-all', [AuthController::class, 'logoutAll'])->name('api.logout-all');

// Me - Kullanıcı Bilgilerini Getir
Route::middleware(['auth:sanctum', 'api.role'])->get('/me', [AuthController::class, 'me'])->name('api.me');

// ============================================================================
// API Routes - Auth Middleware (Sanctum + Role ID 11)
// ============================================================================
Route::middleware(['auth:sanctum', 'api.role'])->group(function () {
    
    // Orders API - Sipariş CRUD İşlemleri
    Route::apiResource('orders', OrderApiController::class)->names([
        'index' => 'api.orders.index',
        'store' => 'api.orders.store',
        'show' => 'api.orders.show',
        'update' => 'api.orders.update',
        'destroy' => 'api.orders.destroy',
    ]);
    
    // Products API - Ürün CRUD İşlemleri
    Route::apiResource('products', ProductApiController::class)->names([
        'index' => 'api.products.index',
        'store' => 'api.products.store',
        'show' => 'api.products.show',
        //'update' => 'api.products.update',
        //'destroy' => 'api.products.destroy',
    ]);
    
    // Customization Pivot Params API - Özelleştirme Kategorileri CRUD İşlemleri
    Route::apiResource('customization-pivot-params', CustomizationPivotParamApiController::class)->names([
        'index' => 'api.customization-pivot-params.index',
        'store' => 'api.customization-pivot-params.store',
        'show' => 'api.customization-pivot-params.show',
        //'update' => 'api.customization-pivot-params.update',
        //'destroy' => 'api.customization-pivot-params.destroy',
    ]);
    
    // Carts API - Sipariş Kalemleri CRUD İşlemleri
    Route::apiResource('carts', CartApiController::class)->names([
        'index' => 'api.carts.index',
        'store' => 'api.carts.store',
        'show' => 'api.carts.show',
        'update' => 'api.carts.update',
        'destroy' => 'api.carts.destroy',
    ]);
    
    // Ek endpoint'ler
    Route::get('orders/{order}/items', [OrderApiController::class, 'getOrderItems'])->name('api.orders.items');
    Route::get('orders/{order}/carts', [CartApiController::class, 'getByOrder'])->name('api.orders.carts');
    Route::get('carts/barcode/{barcode}', [CartApiController::class, 'getByBarcode'])->name('api.carts.barcode');
    Route::patch('carts/{id}/status', [CartApiController::class, 'updateStatus'])->name('api.carts.update-status');
    Route::get('products/{product}/customization-params', [ProductApiController::class, 'getCustomizationParams'])->name('api.products.customization-params');
    Route::get('customization-pivot-params/{param}/children', [CustomizationPivotParamApiController::class, 'getChildren'])->name('api.customization-pivot-params.children');
});

// Test endpoint - tamamen açık
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API çalışıyor!',
        'timestamp' => now()->toDateTimeString(),
        'environment' => app()->environment()
    ]);
})->name('api.test');

// Public API Routes (Auth gerektirmeyen)
Route::prefix('public')->name('api.public.')->group(function () {
    Route::get('products', [ProductApiController::class, 'index'])->name('products.index');
    Route::get('products/{id}', [ProductApiController::class, 'show'])->name('products.show');
});

