<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::apiResource('products', ProductController::class)->parameters([
    'products' => 'product'
]);

Route::apiResource('orders', OrderController::class)->only(['index', 'show', 'store']);

