<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\HomeController;

// Trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');

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

// Auth
Route::get('/dang-nhap', function () { return redirect('/'); })->name('login');
Route::get('/dang-ky', function () { return redirect('/'); })->name('register');
Route::post('/dang-xuat', function () { return redirect('/'); })->name('logout');
