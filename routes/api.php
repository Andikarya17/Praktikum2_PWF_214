<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController as ProductApiController;
use App\Http\Controllers\Api\CategoryController as CategoryApiController;
use App\Http\Controllers\Api\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Auth: Login untuk mendapatkan access_token
Route::post('/login', [AuthController::class, 'getToken']);

// Product API Routes
// GET (public - tanpa token)
Route::get('/product', [ProductApiController::class, 'index']);
Route::get('/product/{id}', [ProductApiController::class, 'show']);

// POST, PUT, DELETE (protected - butuh token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/product', [ProductApiController::class, 'store']);
    Route::put('/product/{id}', [ProductApiController::class, 'update']);
    Route::delete('/product/{id}', [ProductApiController::class, 'destroy']);
});

// Category API Routes
// GET (public - tanpa token)
Route::get('/category', [CategoryApiController::class, 'index']);
Route::get('/category/{id}', [CategoryApiController::class, 'show']);

// POST, PUT, DELETE (protected - butuh token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/category', [CategoryApiController::class, 'store']);
    Route::put('/category/{id}', [CategoryApiController::class, 'update']);
    Route::delete('/category/{id}', [CategoryApiController::class, 'destroy']);
});
