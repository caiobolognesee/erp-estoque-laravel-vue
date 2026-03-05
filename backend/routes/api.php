<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;

Route::post('/products', [ProductController::class, 'store']);
Route::get('/products', [ProductController::class, 'index']);

Route::post('/purchases', [PurchaseController::class, 'store']);
Route::get('/purchases', [PurchaseController::class, 'index']);

Route::post('/sales', [SaleController::class, 'store']);
Route::get('/sales', [SaleController::class, 'index']);
