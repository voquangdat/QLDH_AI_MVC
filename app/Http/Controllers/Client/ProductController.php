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

        $relatedProducts = $this->getRelatedProducts($product);
        $variantsByColor = $this->getProductVariantsWithStock($product);

        return view('clients.page.product', compact('product', 'relatedProducts', 'variantsByColor'));
    }

    private function getRelatedProducts(Product $product, int $limit = 8): \Illuminate\Database\Eloquent\Collection
    {
        // Ưu tiên cùng loại sản phẩm, fallback cùng danh mục
        $related = Product::with(['images'])
            ->where('product_id', '!=', $product->product_id)
            ->where('subcategory_id', $product->subcategory_id)
            ->take($limit)
            ->get();

        if ($related->count() < $limit) {
            $excluded = $related->pluck('product_id')->push($product->product_id);
            $fallback = Product::with(['images'])
                ->where('category_id', $product->category_id)
                ->whereNotIn('product_id', $excluded)
                ->take($limit - $related->count())
                ->get();
            $related = $related->merge($fallback);
        }

        return $related;
    }

    private function getProductVariantsWithStock(Product $product): \Illuminate\Support\Collection
    {
        return $product->variants
            ->groupBy('color_id')
            ->map(function ($variants) {
                $first = $variants->first();
                return [
                    'color_id'  => $first->color_id,
                    'color_ten' => $first->color->color_ten ?? '—',
                    'sizes'     => $variants->map(fn($v) => [
                        'variant_id' => $v->variant_id,
                        'size'       => $v->size->product_size ?? '—',
                        'in_stock'   => ($v->inventory?->soluong_co_the_ban ?? 0) > 0,
                        'stock_qty'  => $v->inventory?->soluong_co_the_ban ?? 0,
                    ])->values(),
                ];
            })->values();
    }
}
