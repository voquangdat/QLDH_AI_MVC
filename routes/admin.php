<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubcategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\OrderController;

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

    // Đơn hàng
    Route::get('orders',                                  [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{id}',                             [OrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{id}/confirm',                    [OrderController::class, 'confirm'])->name('orders.confirm');
    Route::post('orders/{id}/process',                    [OrderController::class, 'process'])->name('orders.process');
    Route::post('orders/{id}/deliver',                    [OrderController::class, 'deliver'])->name('orders.deliver');
    Route::post('orders/{id}/cancel',                     [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('orders/{id}/update-payment',             [OrderController::class, 'updatePayment'])->name('orders.update-payment');

    // Tồn kho
    Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('inventory/list', [InventoryController::class, 'list'])->name('inventory.list');
    Route::get('inventory/detail', [InventoryController::class, 'detail'])->name('inventory.detail');
    Route::post('inventory/update-stock', [InventoryController::class, 'updateStock'])->name('inventory.update-stock');
});

// Auth (không cần middleware)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

