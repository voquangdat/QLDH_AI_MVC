<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::with(['images', 'variants.inventory'])
            ->where('product_hot', true)
            ->latest()
            ->take(8)
            ->get();

        return view('clients.page.home', compact('featuredProducts'));
    }
}