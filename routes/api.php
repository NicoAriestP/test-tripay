<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(CategoryController::class)->group(function () {
    Route::get('/categories', 'index');
    Route::post('/categories', 'store');
    Route::put('/categories/{model}', 'update');
    Route::delete('/categories/{model}', 'destroy');
});

Route::controller(ProductController::class)->group(function () {
    Route::get('/products', 'index');
    Route::post('/products', 'store');
    Route::put('/products/{model}', 'update');
    Route::delete('/products/{model}', 'destroy');
});

Route::controller(InvoiceController::class)->group(function () {
    Route::get('/invoices', 'index');
    Route::get('/invoices/{model}', 'show');
});
