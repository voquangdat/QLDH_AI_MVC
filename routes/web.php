<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\AuthController;

// Trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth
Route::get('/dang-nhap', [AuthController::class, 'showLogin'])->name('login');
Route::post('/dang-nhap', [AuthController::class, 'login'])->name('login.post');
Route::get('/dang-ky', [AuthController::class, 'showRegister'])->name('register');
Route::post('/dang-ky', [AuthController::class, 'register'])->name('register.post');
Route::post('/dang-xuat', [AuthController::class, 'logout'])->name('logout');

// Danh mục
// Thay dòng cũ bằng dòng này ✅
Route::get('/danh-muc', [\App\Http\Controllers\Client\CategoryController::class, 'index'])
    ->name('category.index');
Route::get('/danh-muc/{id}', function () { return redirect('/'); })->name('category.show');

// Sản phẩm
// Route::get('/san-pham', function () { return redirect('/'); })->name('product.index');
// Route::get('/san-pham/{id}', function () { return redirect('/'); })->name('product.show');
Route::get('/san-pham/{id}', [\App\Http\Controllers\Client\ProductController::class, 'show'])
    ->name('product.show');

// Giỏ hàng
Route::get('/gio-hang', function () { return redirect('/'); })->name('cart.index');

// Đơn hàng
Route::get('/don-hang', function () { return redirect('/'); })->name('orders');

// Tài khoản
Route::get('/profile', function () { return redirect('/'); })->name('profile');

// Khác
Route::get('/tin-tuc', function () { return redirect('/'); })->name('news');
Route::get('/lien-he', function () { return redirect('/'); })->name('contact');