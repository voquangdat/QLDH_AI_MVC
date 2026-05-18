<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Client\DeliveryController;
use App\Http\Controllers\Client\PaymentController;

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
Route::get('/gio-hang', [CartController::class, 'index'])->name('cart.index');
Route::post('/gio-hang/them', [CartController::class, 'store'])->name('cart.store');
Route::get('/gio-hang/mini', [CartController::class, 'mini'])->name('cart.mini');
Route::delete('/gio-hang/xoa/{variantId}', [CartController::class, 'destroy'])->name('cart.destroy');

// Giao hàng / Đặt hàng
Route::get('/dat-hang', [DeliveryController::class, 'index'])->name('delivery.index');
Route::post('/dat-hang', [DeliveryController::class, 'store'])->name('delivery.store');

// Thanh toán
Route::get('/thanh-toan/{orderId}',              [PaymentController::class, 'show'])->name('payment.show');
Route::post('/thanh-toan/{orderId}',             [PaymentController::class, 'process'])->name('payment.process');
Route::get('/thanh-toan/{orderId}/thanh-cong',   [PaymentController::class, 'success'])->name('payment.success');

// MoMo callback (redirect từ MoMo về trình duyệt)
Route::get('/thanh-toan/momo/callback',  [PaymentController::class, 'momoCallback'])->name('payment.momo.callback');
// MoMo IPN (server-to-server POST, exempt CSRF ở bootstrap/app.php)
Route::post('/thanh-toan/momo/ipn',     [PaymentController::class, 'momoIpn'])->name('payment.momo.ipn');

// Đơn hàng
Route::get('/don-hang', function () { return redirect('/'); })->name('orders');
Route::get('/don-hang/{orderId}', [PaymentController::class, 'detail'])->name('order.detail');
Route::get('/don-hang-khach', [PaymentController::class, 'guestDetail'])->name('order.guest-detail');

// Tài khoản
Route::get('/profile', function () { return redirect('/'); })->name('profile');

// Khác
Route::get('/tin-tuc', function () { return redirect('/'); })->name('news');
Route::get('/lien-he', function () { return redirect('/'); })->name('contact');