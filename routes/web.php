<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\Client\AuthController as ClientAuthController;

// Trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');

// Admin Auth (không cần middleware)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Admin Dashboard (cần middleware admin)
Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
});

// Client Auth
Route::get('/dang-nhap', [ClientAuthController::class, 'showLogin'])->name('login');
Route::post('/dang-nhap', [ClientAuthController::class, 'login'])->name('login.post');
Route::get('/dang-ky', [ClientAuthController::class, 'showRegister'])->name('register');
Route::post('/dang-ky', [ClientAuthController::class, 'register'])->name('register.post');
Route::post('/dang-xuat', [ClientAuthController::class, 'logout'])->name('logout');

// Danh mục
Route::get('/danh-muc', function () { return redirect('/'); })->name('category.index');
Route::get('/danh-muc/{id}', function () { return redirect('/'); })->name('category.show');

// Sản phẩm
Route::get('/san-pham/{id}', function () { return redirect('/'); })->name('product.show');
Route::get('/san-pham', function () { return redirect('/'); })->name('product.index');

// Giỏ hàng
Route::get('/gio-hang', function () { return redirect('/'); })->name('cart.index');

// Đơn hàng
Route::get('/don-hang', function () { return redirect('/'); })->name('orders');

// Tài khoản
Route::get('/profile', function () { return redirect('/'); })->name('profile');
Route::get('/tin-tuc', function () { return redirect('/'); })->name('news');
Route::get('/lien-he', function () { return redirect('/'); })->name('contact');