<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubcategoryController;
use App\Http\Controllers\Admin\ProductController;

// Các trang cần middleware admin
Route::middleware('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Danh mục
    Route::resource('categories', CategoryController::class)->names('categories');

    // Loại sản phẩm
    Route::resource('subcategories', SubcategoryController::class)->names('subcategories');

    // Sản phẩm
    Route::get('products/list', [ProductController::class, 'list'])->name('products.list');
    Route::get('products/subcategories', [ProductController::class, 'getSubcategories'])->name('products.subcategories');
    Route::delete('products/images/destroy', [ProductController::class, 'destroyImage'])->name('products.images.destroy');
    Route::resource('products', ProductController::class)->names('products');
});

// Auth (không cần middleware)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');