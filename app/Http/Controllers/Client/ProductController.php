<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function show($id)
    {
        $product = Product::with([
            'images',
            'category',
            'subcategory',
            'variants.color',
            'variants.size',
            'variants.inventory',
        ])->findOrFail($id);

        $relatedProducts = Product::with(['images'])
            ->where('category_id', $product->category_id)
            ->where('product_id', '!=', $product->product_id)
            ->take(4)
            ->get();

        return view('clients.page.product', compact('product', 'relatedProducts'));
    }
}
