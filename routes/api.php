<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VendorController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Auth\VendorAuthController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::prefix('v1')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Public Routes
    |--------------------------------------------------------------------------
    */

    // Customer Auth
    Route::post('login', [AuthenticatedSessionController::class, 'apiLogin']);
    Route::post('register', [RegisteredUserController::class, 'apiRegister']);

    // Vendor Auth
    Route::post('vendor/register', [VendorAuthController::class, 'register'])->name('api.vendor.register');
    Route::post('vendor/login', [VendorAuthController::class, 'login'])->name('api.vendor.login');

    // Email Verification
    Route::get('check-verification/{email}', [VendorAuthController::class, 'checkVerificationStatus'])
        ->name('vendor.verification.check');
    Route::get('/vendor/verify-email/{id}/{hash}', [VendorAuthController::class, 'verify'])
        ->middleware(['signed'])
        ->name('vendor.verification.verify');

    // Public Product Routes
    Route::get('products', [ProductController::class, 'index'])->name('products.api.index');
    Route::get('products/{slug}', [ProductController::class, 'show'])->name('products.api.show');
    Route::get('categories/{slug}/products', [ProductController::class, 'byCategory'])->name('products.api.byCategory');

    /*
    |--------------------------------------------------------------------------
    | Protected Routes
    |--------------------------------------------------------------------------
    */

    // General Auth Routes
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('auth/me', [AuthenticatedSessionController::class, 'getAuthenticatedUser'])->name('api.auth.me');
        Route::post('logout', [AuthenticatedSessionController::class, 'apiLogout'])->name('api.logout');
    });

    // Protected Vendor Routes
    Route::middleware('auth:sanctum')->prefix('vendor')->group(function () {
        // Onboarding
        Route::post('setup-store', [VendorAuthController::class, 'setupStore']);
        Route::post('setup-payment', [VendorAuthController::class, 'setupPayment']);
        Route::post('upload-documents', [VendorAuthController::class, 'uploadDocuments']);
        Route::post('logout', [VendorAuthController::class, 'logout'])->name('api.vendor.logout');

        // Profile & Store Management
        Route::get('profile', [VendorController::class, 'profile'])->name('api.vendor.profile');
        Route::put('profile', [VendorController::class, 'updateProfile'])->name('api.vendor.profile.update');
        Route::get('store', [VendorController::class, 'getStore'])->name('api.vendor.store');
        Route::put('store', [VendorController::class, 'updateStore'])->name('api.vendor.store.update');

        // Order Management
        Route::get('orders', [VendorController::class, 'orders'])->name('api.vendor.orders');
        Route::put('orders/{order}/status', [VendorController::class, 'updateOrderStatus'])->name('api.vendor.orders.status');

        // Analytics
        Route::get('analytics', [VendorController::class, 'analytics'])->name('api.vendor.analytics');
        Route::get('revenue', [VendorController::class, 'revenue'])->name('api.vendor.revenue');

        // Email Verification
        Route::prefix('email')->middleware('auth:vendor')->group(function () {
            Route::post('verification-notification', [VendorAuthController::class, 'resendVerification'])
                ->middleware(['throttle:6,1'])
                ->name('vendor.verification.send');
        });
    });

    // Admin Routes
    Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
        Route::get('vendors', [VendorController::class, 'index'])->name('api.admin.vendors');
        Route::get('vendors/{vendor}', [VendorController::class, 'show'])->name('api.admin.vendors.show');
        Route::put('vendors/{vendor}/status', [VendorController::class, 'updateStatus'])->name('api.admin.vendors.status');
    });

    // Customer Routes
    Route::middleware(['auth:sanctum', 'customer'])->prefix('customer')->group(function () {
        // Customer specific routes here
    });
});
