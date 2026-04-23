<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::with('subcategories')->orderBy('category_id')->get();

        $currentCategory    = $request->category_id
            ? Category::find($request->category_id)
            : null;
        $currentSubcategory = $request->subcategory_id
            ? Subcategory::with('category')->find($request->subcategory_id)
            : null;

        // Nếu lọc theo subcategory thì lấy category cha của nó
        if ($currentSubcategory && !$currentCategory) {
            $currentCategory = $currentSubcategory->category;
        }

        $products = Product::with(['images'])
            ->when($request->category_id,    fn($q) => $q->where('category_id',    $request->category_id))
            ->when($request->subcategory_id, fn($q) => $q->where('subcategory_id', $request->subcategory_id))
            ->when($request->sort == 'price_high', fn($q) => $q->orderBy('product_gia', 'desc'))
            ->when($request->sort == 'price_low',  fn($q) => $q->orderBy('product_gia', 'asc'))
            ->get();

        return view('clients.page.category', compact(
            'products', 'categories', 'currentCategory', 'currentSubcategory'
        ));
    }
}