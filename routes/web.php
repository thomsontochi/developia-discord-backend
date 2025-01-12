<?php

use Inertia\Inertia;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Auth\VendorAuthController;
use App\Http\Controllers\Auth\EmailVerificationController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    // Placeholder stats without actual database queries
    $stats = [
        'vendors' => [
            'total' => 150,
            'active' => 120,
            'inactive' => 25,
            'pending' => 5,
            'newThisMonth' => 12,
        ],
        'categories' => [
            'total' => Category::count(),
        ],
        'customers' => [
            'total' => 1234,
            'active' => 1100,
            'inactive' => 134,
            'newThisMonth' => 45,
        ],
        'products' => [
            'total' => 2567,
            'active' => 2300,
            'outOfStock' => 123,
            'categories' => 45,
        ],
        'orders' => [
            'total' => 5678,
            'pending' => 45,
            'shipped' => 120,
            'completed' => 5400,
            'cancelled' => 113,
        ],
        'revenue' => [
            'total' => 234567,
            'today' => 1234,
            'thisWeek' => 12345,
            'thisMonth' => 45678,
            'platformFees' => 23456,
        ],
        'topVendors' => [
            ['id' => 1, 'name' => 'Top Store 1', 'sales' => 12345, 'orderCount' => 234],
            ['id' => 2, 'name' => 'Top Store 2', 'sales' => 10345, 'orderCount' => 198],
            ['id' => 3, 'name' => 'Top Store 3', 'sales' => 9345, 'orderCount' => 167],
        ],
        'topProducts' => [
            ['id' => 1, 'name' => 'Popular Product 1', 'sales' => 5678, 'stock' => 45],
            ['id' => 2, 'name' => 'Popular Product 2', 'sales' => 4567, 'stock' => 23],
            ['id' => 3, 'name' => 'Popular Product 3', 'sales' => 3456, 'stock' => 12],
        ],
        'recentOrders' => [
            ['id' => 1, 'customer' => 'John Doe', 'amount' => 234, 'status' => 'pending'],
            ['id' => 2, 'customer' => 'Jane Smith', 'amount' => 567, 'status' => 'processing'],
            ['id' => 3, 'customer' => 'Bob Wilson', 'amount' => 890, 'status' => 'shipped'],
        ],
        'salesTrends' => [
            'daily' => [
                ['date' => '2023-10-01', 'total' => 1234],
                ['date' => '2023-10-02', 'total' => 2345],
                ['date' => '2023-10-03', 'total' => 3456],
            ],
            'weekly' => [
                ['week' => '2023-W39', 'total' => 12345],
                ['week' => '2023-W40', 'total' => 23456],
            ],
            'monthly' => [
                ['month' => '2023-09', 'total' => 123456],
                ['month' => '2023-10', 'total' => 234567],
            ],
        ],
    ];
   
    return Inertia::render('Dashboard', [
        'stats' => $stats,
        
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

// Vendor email verification routes
Route::get('/vendor/email-verified', function() {
    return view('vendor.verified');
})->name('vendor.verification.success');

Route::middleware('auth')->group(function () {
    // Products Routes...
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Category Routes...
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // // Order Management Routes...
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/export', [OrderController::class, 'export'])->name('orders.export');
    Route::get('/disputes', [OrderController::class, 'disputes'])->name('orders.disputes');
    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update.status');
    Route::post('/{order}/resolve-dispute', [OrderController::class, 'resolveDispute'])->name('orders.resolve.dispute');

    // Profile Routes...
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route::middleware(['auth:vendor'])->group(function () {
//     Route::get('/email/verify', [EmailVerificationController::class, 'notice'])
//         ->name('verification.notice');

//     Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
//         ->middleware(['signed'])
//         ->name('verification.verify');

//     Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
//         ->middleware(['throttle:6,1'])
//         ->name('verification.send');
// });


// todo on vendor side , after regiseration , and you get mail , the mail should lead to the frontend page to verify the email then work on the rest onn this side admin bellow 
// What's left: for vendor

// Admin Approval Functionality

// Create AdminController

// Add approval/rejection endpoints

// Add email notifications for approval/rejection

// Vendor Dashboard Endpoints

// Profile management

// Store statistics

// Order management

// Revenue reports


require __DIR__.'/auth.php';
