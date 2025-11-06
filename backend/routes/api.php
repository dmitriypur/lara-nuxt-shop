<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductVariantController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public API routes
Route::prefix('v1')->group(function () {
    // Products
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{product}', [ProductController::class, 'show']);
    
    // Categories
    Route::get('/categories', [CategoryController::class, 'index']); // Дерево категорий
    Route::get('/categories/roots', [CategoryController::class, 'roots']); // Корневые категории
    Route::get('/categories/search', [CategoryController::class, 'search']); // Поиск категорий
    Route::get('/categories/{slug}', [CategoryController::class, 'show']); // Категория по slug
    Route::get('/categories/{slug}/children', [CategoryController::class, 'children']); // Дочерние категории
    Route::get('/categories/{slug}/breadcrumbs', [CategoryController::class, 'breadcrumbs']); // Хлебные крошки
    Route::get('/categories/{slug}/products', [CategoryController::class, 'products']); // Товары категории
    
    // Search
    Route::get('products/search', [ProductController::class, 'search']);
    Route::get('products/{product:slug}', [ProductController::class, 'show']);
    Route::get('products/{product}/related', [ProductController::class, 'related']);
    
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{category:slug}', [CategoryController::class, 'show']);
});