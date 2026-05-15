<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Cart;
use App\Models\Category;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        View::composer('*', function ($view) {
            $view->with('categories', Category::with('subcategories')->get());

            if (Auth::check()) {
                $miniCartItems = Cart::with(['variant.product.images', 'variant.color', 'variant.size'])
                    ->where('users_id', Auth::id())
                    ->get();
                $miniCartTotal = $miniCartItems->sum(fn($i) => $i->subtotal());
                $miniCartCount = $miniCartItems->sum('quantity');
            } else {
                $miniCartItems = collect();
                $miniCartTotal = 0;
                $miniCartCount = 0;
            }

            $view->with(compact('miniCartItems', 'miniCartTotal', 'miniCartCount'));
        });
    }
}
