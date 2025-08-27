<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::apiResource('api/products', ProductController::class)->parameters([
    'products' => 'product'
]);

Route::apiResource('api/orders', OrderController::class)->only(['index', 'show', 'store']);

