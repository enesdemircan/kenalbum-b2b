<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MainCategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CustomizationCategoryController;
use App\Http\Controllers\CustomerPanelController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;

// Frontend routes
Route::get('/', [FrontendController::class, 'index'])->name('home');
Route::get('/products', [FrontendController::class, 'index'])->name('products.index');
Route::get('/category/{slug}', [FrontendController::class, 'category'])->name('category.show');
Route::get('/products/{slug}', [FrontendController::class, 'show'])->name('products.show');
Route::get('/page/{slug}', [FrontendController::class, 'page'])->name('page.show');
Route::get('/search', [FrontendController::class, 'search'])->name('search')->middleware('throttle:20,1');

// Customization parameters routes
Route::get('/products/{product}/customization-params', [FrontendController::class, 'getCustomizationParams'])->name('products.customization-params');
Route::get('/products/{product}/customization-params/{parentParam}/children', [FrontendController::class, 'getCustomizationChildren'])->name('products.customization-children');

// Approval pending route
Route::get('/approval-pending', function () {
    return view('frontend.approval-pending');
})->name('approval.pending')->middleware('auth');

// Firma ataması gerekli route
Route::get('/customer-assignment-required', function () {
    return view('frontend.customer-assignment-required');
})->name('customer.assignment.required')->middleware('auth');

// Auth routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
 Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');


// Cart routes
Route::middleware(['auth', 'approval'])->group(function () {

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::get('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::match(['GET', 'POST'], '/cart/complete', [CartController::class, 'complete'])->name('cart.complete');
    Route::get('/cart/order', [CartController::class, 'order'])->name('cart.order');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/add-extra', [CartController::class, 'addExtra'])->name('cart.add-extra');
    Route::post('/cart/extra/set', [CartController::class, 'setExtraQuantity'])->name('cart.extra.set');
    Route::put('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/quantity/{id}', [CartController::class, 'updateQuantity'])->name('cart.quantity');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/cart/count', [CartController::class, 'getCartCount'])->name('cart.count');
    Route::post('/products/{id}/order', [OrderController::class, 'storeOrder'])->name('products.order');


    Route::get('/profile', [CustomerPanelController::class, 'index'])->name('profile.index');
    Route::get('/profile/orders', [CustomerPanelController::class, 'orders'])->name('profile.orders');
    Route::put('/profile/orders/{id}/delivery', [CustomerPanelController::class, 'updateOrderDelivery'])->name('profile.orders.delivery.update');
    Route::get('/profile/addresses', [CustomerPanelController::class, 'addresses'])->name('profile.addresses');
    Route::post('/profile/addresses', [CustomerPanelController::class, 'storeAddress'])->name('profile.addresses.store');
    Route::put('/profile/addresses/{id}', [CustomerPanelController::class, 'updateAddress'])->name('profile.addresses.update');
    Route::delete('/profile/addresses/{id}', [CustomerPanelController::class, 'deleteAddress'])->name('profile.addresses.delete');
    Route::get('/profile/detail', [CustomerPanelController::class, 'detail'])->name('profile.detail');
    Route::put('/profile/detail', [CustomerPanelController::class, 'updateDetail'])->name('profile.detail.update');
});

// R2 direct multipart upload (auth + firma yetkisi + throttle)
Route::middleware(['auth', 'approval', 'ensure.customer'])->group(function () {
    // Cart-item seviyesi (legacy, eski sipariş akışı için — yeni akış order-level kullanıyor)
    Route::post('/upload/r2/initiate', [App\Http\Controllers\R2DirectUploadController::class, 'initiate'])
        ->middleware('throttle:30,1')
        ->name('upload.r2.initiate');
    Route::post('/upload/r2/complete', [App\Http\Controllers\R2DirectUploadController::class, 'complete'])
        ->middleware('throttle:60,1')
        ->name('upload.r2.complete');
    Route::post('/upload/r2/abort', [App\Http\Controllers\R2DirectUploadController::class, 'abort'])
        ->name('upload.r2.abort');

    // Order-level (checkout'ta tek ZIP — yeni akış)
    Route::post('/upload/r2/order/initiate', [App\Http\Controllers\R2DirectUploadController::class, 'initiateOrder'])
        ->middleware('throttle:30,1')
        ->name('upload.r2.order.initiate');
    Route::post('/upload/r2/order/complete', [App\Http\Controllers\R2DirectUploadController::class, 'completeOrder'])
        ->middleware('throttle:60,1')
        ->name('upload.r2.order.complete');
    Route::post('/upload/r2/order/abort', [App\Http\Controllers\R2DirectUploadController::class, 'abortOrder'])
        ->name('upload.r2.order.abort');
});




// Profile routes (firma ataması gerekli - sipariş sayfaları için)
Route::middleware(['auth', 'approval', 'check.route.permission', 'ensure.customer'])->group(function () {
   
    
    Route::get('/profile/personels', [CustomerPanelController::class, 'personels'])->name('profile.personels');
    Route::post('/profile/personels', [CustomerPanelController::class, 'storePersonel'])->name('profile.personels.store');
    Route::delete('/profile/personels/{id}', [CustomerPanelController::class, 'deletePersonel'])->name('profile.personels.delete');
    Route::get('/profile/personels/{id}/orders', [CustomerPanelController::class, 'personelOrders'])->name('profile.personels.orders');


    Route::get('/products/order/{id}', [OrderController::class, 'ordercreateById'])->name('products.ordercreate.id')->where('id', '[0-9]+');

    Route::get('/products/order/{slug}', [OrderController::class, 'ordercreate'])->name('products.ordercreate');

    Route::post('/order/create', [OrderController::class, 'create'])->name('order.create');
    

   
});


    // Customization file download route (admin only)
    Route::get('/download/customization/{path}', function ($path) {
        try {
            $filePath = base64_decode($path);
            $disk = \Storage::disk('s3');
            
            if ($disk->exists($filePath)) {
                $fileContent = $disk->get($filePath);
                $fileName = basename($filePath);
                
                return response($fileContent)
                    ->header('Content-Type', 'application/zip')
                    ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
            }
            
            abort(404, 'Dosya bulunamadı');
        } catch (\Exception $e) {
            abort(500, 'Dosya indirme hatası');
        }
    })->name('download.customization.file')->middleware(['auth', 'role:administrator,editor']);
    
    // Admin customization file download route with temporary URL
    Route::get('/admin/download/customization/{path}', function ($path) {
        try {
            $filePath = base64_decode($path);
            $disk = \Storage::disk('s3');
            
            if ($disk->exists($filePath)) {
                // Geçici URL oluştur (15 dakika geçerli)
                $temporaryUrl = $disk->temporaryUrl($filePath, now()->addMinutes(15));
                
                return redirect($temporaryUrl);
            }
            
            abort(404, 'Dosya bulunamadı');
        } catch (\Exception $e) {
            abort(500, 'Dosya indirme hatası');
        }
    })->name('admin.download.customization')->middleware(['auth', 'role:administrator,editor']);

    // Frontend download route for customization files
    Route::get('/download/customization/{path}', function ($path) {
        try {
            $decodedPath = base64_decode($path);
            
            // S3'ten dosya indirme
            if (Storage::disk('s3')->exists($decodedPath)) {
                $url = Storage::disk('s3')->temporaryUrl($decodedPath, now()->addMinutes(5));
                return redirect($url);
            }
            
            // Local dosya indirme
            if (Storage::disk('public')->exists($decodedPath)) {
                return Storage::disk('public')->download($decodedPath);
            }
            
            abort(404, 'Dosya bulunamadı');
        } catch (\Exception $e) {
            abort(500, 'Dosya indirme hatası');
        }
    })->name('download.customization')->middleware(['auth']);









// Password reset routes are handled by Fortify

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['check.route.permission'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Main Categories
    Route::resource('main-categories', MainCategoryController::class);
    
    // Products
    Route::resource('products', ProductController::class);
    Route::post('products/{product}/customization', [ProductController::class, 'storeCustomization'])->name('products.customization.store');
    Route::delete('products/{product}/images/{imageIndex}', [ProductController::class, 'deleteImage'])->name('products.delete-image');
    
    // Product Customization Parameters
    Route::get('products/{product}/customization-params', [\App\Http\Controllers\Admin\ProductCustomizationParamController::class, 'index'])->name('product-customization-params.index');
    Route::get('products/{product}/customization-params/create', [\App\Http\Controllers\Admin\ProductCustomizationParamController::class, 'create'])->name('product-customization-params.create');
    Route::post('products/{product}/customization-params', [\App\Http\Controllers\Admin\ProductCustomizationParamController::class, 'store'])->name('product-customization-params.store');
    
    // Hiyerarşik parametre yönetimi - özel route'lar önce tanımlanmalı
    Route::get('products/{product}/customization-params/hierarchical', [\App\Http\Controllers\Admin\ProductCustomizationParamController::class, 'hierarchical'])->name('product-customization-params.hierarchical');
    Route::get('products/{product}/customization-params/{parentParam}/children', [\App\Http\Controllers\Admin\ProductCustomizationParamController::class, 'getChildParameters'])->name('product-customization-params.children');
    Route::get('products/{product}/customization-params/{pivotParam}/details', [\App\Http\Controllers\Admin\ProductCustomizationParamController::class, 'getParamDetails'])->name('product-customization-params.details');
    Route::post('products/{product}/customization-params/{pivotParam}/update-hierarchy', [\App\Http\Controllers\Admin\ProductCustomizationParamController::class, 'updateHierarchy'])->name('product-customization-params.update-hierarchy');
    
    // Genel parametre yönetimi route'ları
    Route::get('products/{product}/customization-params/{pivotParam}/edit', [\App\Http\Controllers\Admin\ProductCustomizationParamController::class, 'edit'])->name('product-customization-params.edit');
    Route::put('products/{product}/customization-params/{pivotParam}', [\App\Http\Controllers\Admin\ProductCustomizationParamController::class, 'update'])->name('product-customization-params.update');
    Route::delete('products/{product}/customization-params/{pivotParam}', [\App\Http\Controllers\Admin\ProductCustomizationParamController::class, 'destroy'])->name('product-customization-params.destroy');
    
    Route::get('customization-params/{category}/hierarchical', [\App\Http\Controllers\Admin\ProductCustomizationParamController::class, 'getParametersByLevel'])->name('customization-params.hierarchical');
    
    // Product Details
    Route::get('products/{product}/details', [\App\Http\Controllers\Admin\ProductDetailController::class, 'index'])->name('product-details.index');
    Route::get('products/{product}/details/create', [\App\Http\Controllers\Admin\ProductDetailController::class, 'create'])->name('product-details.create');
    Route::post('products/{product}/details', [\App\Http\Controllers\Admin\ProductDetailController::class, 'store'])->name('product-details.store');
    Route::get('products/{product}/details/{detail}/edit', [\App\Http\Controllers\Admin\ProductDetailController::class, 'edit'])->name('product-details.edit');
    Route::put('products/{product}/details/{detail}', [\App\Http\Controllers\Admin\ProductDetailController::class, 'update'])->name('product-details.update');
    Route::delete('products/{product}/details/{detail}', [\App\Http\Controllers\Admin\ProductDetailController::class, 'destroy'])->name('product-details.destroy');
    
    // Image Upload for CKEditor
    Route::post('upload-image', [\App\Http\Controllers\Admin\ImageUploadController::class, 'upload'])->name('upload-image');
    
    // Customization Categories
    Route::resource('customization-categories', CustomizationCategoryController::class);
    
    // Customization Params
    Route::get('customization-params/{category}', [\App\Http\Controllers\Admin\CustomizationParamController::class, 'index'])->name('customization-params.index');
    Route::get('customization-params/{category}/create', [\App\Http\Controllers\Admin\CustomizationParamController::class, 'create'])->name('customization-params.create');
    Route::post('customization-params/{category}', [\App\Http\Controllers\Admin\CustomizationParamController::class, 'store'])->name('customization-params.store');
    Route::get('customization-params/{category}/edit/{param}', [\App\Http\Controllers\Admin\CustomizationParamController::class, 'edit'])->name('customization-params.edit');
    Route::put('customization-params/{category}/{param}', [\App\Http\Controllers\Admin\CustomizationParamController::class, 'update'])->name('customization-params.update');
    Route::delete('customization-params/{category}/{param}', [\App\Http\Controllers\Admin\CustomizationParamController::class, 'destroy'])->name('customization-params.destroy');
    
    // AJAX routes for customization params
    Route::get('customization-params/{category}/params', [\App\Http\Controllers\Admin\CustomizationParamController::class, 'getParamsByCategory'])->name('customization-params.get-params');
    Route::get('customization-params/{param}/parents', [\App\Http\Controllers\Admin\CustomizationParamController::class, 'getParentParams'])->name('customization-params.get-parents');
    
    // Users
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('users/{user}/roles', [UserController::class, 'updateRole'])->name('users.update-role');
    Route::post('users/{user}/customer', [UserController::class, 'updateCustomer'])->name('users.update-customer');
    Route::post('users/{user}/status', [UserController::class, 'updateStatus'])->name('users.update-status');
    
    // Role management
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
    
    // Route management
    Route::resource('routes', \App\Http\Controllers\Admin\RouteController::class);
    Route::get('routes/import', [\App\Http\Controllers\Admin\RouteController::class, 'importRoutes'])->name('routes.import');
    Route::get('routes/group/{group}', [\App\Http\Controllers\Admin\RouteController::class, 'byGroup'])->name('routes.by-group');
    Route::patch('routes/{route}/toggle-status', [\App\Http\Controllers\Admin\RouteController::class, 'toggleStatus'])->name('routes.toggle-status');
    
    // Route role management
    Route::post('routes/{route}/assign-role', [\App\Http\Controllers\Admin\RouteController::class, 'assignRole'])->name('routes.assign-role');
    Route::delete('routes/{route}/remove-role/{role}', [\App\Http\Controllers\Admin\RouteController::class, 'removeRole'])->name('routes.remove-role');
    Route::put('routes/{route}/update-role-permissions/{role}', [\App\Http\Controllers\Admin\RouteController::class, 'updateRolePermissions'])->name('routes.update-role-permissions');
    
    // Discount groups management
    Route::resource('discount-groups', \App\Http\Controllers\Admin\DiscountGroupController::class);
    
    // Order statuses management
    Route::resource('order-statuses', \App\Http\Controllers\Admin\OrderStatusController::class);
    
    // Site settings management
    Route::get('site-settings', [\App\Http\Controllers\Admin\SiteSettingController::class, 'index'])->name('site-settings.index');
    Route::put('site-settings', [\App\Http\Controllers\Admin\SiteSettingController::class, 'update'])->name('site-settings.update');
     
    // Slider management
    Route::resource('sliders', \App\Http\Controllers\Admin\SliderController::class);
    
    // Bank accounts management
    Route::resource('bank-accounts', \App\Http\Controllers\Admin\BankAccountController::class);
    
    // Customers management
    Route::resource('customers', \App\Http\Controllers\Admin\CustomerController::class);
    
    // Collections management
    Route::resource('collections', \App\Http\Controllers\Admin\CollectionController::class);
    Route::get('customers/{customer}/collections', [\App\Http\Controllers\Admin\CollectionController::class, 'customerCollections'])->name('customers.collections');
    Route::get('customers/{customer}/collections/create', [\App\Http\Controllers\Admin\CollectionController::class, 'createForCustomer'])->name('customers.collections.create');
    Route::post('customers/{customer}/collections', [\App\Http\Controllers\Admin\CollectionController::class, 'storeForCustomer'])->name('customers.collections.store');
    
    // S3 Files management
    Route::get('s3-files', [\App\Http\Controllers\Admin\S3FileController::class, 'index'])->name('s3-files.index');
    Route::post('s3-files/delete', [\App\Http\Controllers\Admin\S3FileController::class, 'delete'])->name('s3-files.delete');
    Route::post('s3-files/delete-multiple', [\App\Http\Controllers\Admin\S3FileController::class, 'deleteMultiple'])->name('s3-files.delete-multiple');
    Route::post('s3-files/clear-all', [\App\Http\Controllers\Admin\S3FileController::class, 'clearAll'])->name('s3-files.clear-all');
    
    // Cart Files management
    Route::get('cart-files', [\App\Http\Controllers\Admin\CartFileController::class, 'index'])->name('cart-files.index');
    Route::get('cart-files/{id}', [\App\Http\Controllers\Admin\CartFileController::class, 'show'])->name('cart-files.show');
    Route::post('cart-files/{id}/retry', [\App\Http\Controllers\Admin\CartFileController::class, 'retry'])->name('cart-files.retry');
    Route::delete('cart-files/{id}/delete', [\App\Http\Controllers\Admin\CartFileController::class, 'delete'])->name('cart-files.delete');
    Route::post('cart-files/clear-failed', [\App\Http\Controllers\Admin\CartFileController::class, 'clearFailed'])->name('cart-files.clear-failed');
    
    // Customer selection for customization params
    Route::get('customers/list', [\App\Http\Controllers\Admin\CustomerController::class, 'getCustomersList'])->name('customers.list');
    Route::post('customization-params-customers/add', [\App\Http\Controllers\Admin\CustomizationParamsCustomerController::class, 'add'])->name('customization-params-customers.add');
    Route::get('customization-params-customers/existing', [\App\Http\Controllers\Admin\CustomizationParamsCustomerController::class, 'getExistingCustomers'])->name('customization-params-customers.existing');
    
    // Pages
    Route::resource('pages', \App\Http\Controllers\Admin\PageController::class);
    
    // Orders
    Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class);
    Route::get('orders/{order}/print', [\App\Http\Controllers\Admin\OrderController::class, 'print'])->name('orders.print');
    Route::put('orders/{order}/main-status', [\App\Http\Controllers\Admin\OrderController::class, 'updateOrderMainStatus'])->name('orders.main-status');
    Route::put('orders/{order}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::put('orders/{order}/cart-status', [\App\Http\Controllers\Admin\OrderController::class, 'updateCartStatus'])->name('orders.update-cart-status');
    Route::delete('orders/status-history/{historyId}', [\App\Http\Controllers\Admin\OrderController::class, 'deleteStatusHistory'])->name('orders.delete-status-history');
    Route::get('orders/{order}/cart/{cart}/zpl-pdf', [\App\Http\Controllers\Admin\OrderController::class, 'generateZplPdf'])->name('orders.zpl-pdf');
    Route::get('orders/{order}/cart/{cart}/download-files', [\App\Http\Controllers\Admin\OrderController::class, 'downloadCartFiles'])->name('orders.download-cart-files');
    Route::get('orders/{order}/cart/{cart}/cargo-pdf', [\App\Http\Controllers\Admin\OrderController::class, 'generateCargoPdf'])->name('orders.cargo-pdf');


    // Barcode Search
    Route::get('barcode-search', [\App\Http\Controllers\Admin\BarcodeController::class, 'search'])->name('barcode.search');
    Route::post('barcode-search/remove-from-list', [\App\Http\Controllers\Admin\BarcodeController::class, 'removeFromCartList'])->name('barcode.remove-from-list');
    Route::post('barcode-search/clear-list', [\App\Http\Controllers\Admin\BarcodeController::class, 'clearCartList'])->name('barcode.clear-list');
    Route::post('barcode-search/update-statuses', [\App\Http\Controllers\Admin\BarcodeController::class, 'updateCartStatuses'])->name('barcode.update-statuses');

});

// Impersonation (firma adına giriş) - kendi rol kontrolü var, middleware dışında
Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('impersonate/{customer}', [\App\Http\Controllers\Admin\ImpersonationController::class, 'start'])->name('impersonate.start');
});

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/impersonate/stop', [\App\Http\Controllers\Admin\ImpersonationController::class, 'stop'])->name('impersonate.stop');
});

// Customer Panel routes
Route::middleware(['role:müşteri'])->group(function () {
    Route::get('/panel', [CustomerPanelController::class, 'index'])->name('customer.panel');
});
