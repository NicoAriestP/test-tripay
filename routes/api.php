<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::controller(CategoryController::class)->group(function () {
//     Route::get('/categories', 'index');
//     Route::post('/categories', 'store');
//     Route::put('/categories/{model}', 'update');
//     Route::delete('/categories/{model}', 'destroy');
// });

Route::controller(ProductController::class)->group(function () {
    Route::get('/products', 'index');
    Route::post('/products', 'store');
    Route::put('/products/{model}', 'update');
    Route::delete('/products/{model}', 'destroy');
});

Route::controller(TransactionController::class)->group(function () {
    Route::get('/transactions/payment-channels', 'paymentChannels');
    Route::post('/transactions/create', 'create');
});

Route::controller(InvoiceController::class)->group(function () {
    Route::get('/invoices', 'index');
});
