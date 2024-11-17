<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Foundation\Application;

Route::prefix('v1')->group(function () {
    Route::get('products', [ProductController::class, 'index'])->name('products.api.index');
    Route::get('products/{slug}', [ProductController::class, 'show'])->name('products.api.show');
    Route::get('categories/{slug}/products', [ProductController::class, 'byCategory'])->name('products.api.byCategory');
});