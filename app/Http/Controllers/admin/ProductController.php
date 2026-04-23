<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Color;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSize;
use App\Models\Subcategory;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        return view('admin.products.index');
    }

    public function list()
    {
        $products = Product::with(['category', 'subcategory', 'images'])
            ->latest('product_id')
            ->get();

        return view('admin.products.list', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('category_id')->get();
        $colors     = Color::orderBy('color_ten')->get();
        $sizes      = ProductSize::orderBy('product_size')->get();

        $categorySubcategoryMap = $categories->mapWithKeys(fn($cat) => [
            $cat->category_id => Subcategory::where('category_id', $cat->category_id)
                ->get(['subcategory_id as id', 'subcategory_name as name']),
        ]);

        return view('admin.products.create', compact(
            'categories', 'colors', 'sizes', 'categorySubcategoryMap'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name'   => 'required|string|max:255',
            'category_id'    => 'required|exists:categories,category_id',
            'subcategory_id' => 'required|exists:subcategories,subcategory_id',
            'product_gia'    => 'required|numeric|min:0',
            'main_image'     => 'required|image|max:5120',
        ], [
            'product_name.required'   => 'Vui lòng nhập tên sản phẩm.',
            'category_id.required'    => 'Vui lòng chọn danh mục.',
            'subcategory_id.required' => 'Vui lòng chọn loại sản phẩm.',
            'product_gia.required'    => 'Vui lòng nhập giá sản phẩm.',
            'main_image.required'     => 'Vui lòng chọn ảnh chính.',
        ]);

        DB::transaction(function () use ($request) {
            $product = Product::create([
                'product_name'   => $request->product_name,
                'description'    => $request->description,
                'care_note'      => $request->care_note,
                'category_id'    => $request->category_id,
                'subcategory_id' => $request->subcategory_id,
                'product_gia'    => $request->product_gia,
                'product_hot'    => $request->boolean('product_hot'),
            ]);

            // Ảnh chính
            ProductImage::create([
                'product_id'  => $product->product_id,
                'product_anh' => $this->uploadImage($request->file('main_image')),
            ]);

            // Ảnh phụ
            if ($request->hasFile('additional_images')) {
                foreach ($request->file('additional_images') as $img) {
                    ProductImage::create([
                        'product_id'  => $product->product_id,
                        'product_anh' => $this->uploadImage($img),
                    ]);
                }
            }

            // Tạo biến thể (color + sizes)
            if ($request->color_id && $request->size_ids) {
                $this->syncVariants($product, (int) $request->color_id, $request->size_ids);
            }
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Thêm sản phẩm thành công!');
    }

    public function edit(Product $product)
    {
        $product->load(['images', 'variants.color', 'variants.size']);
        $categories = Category::orderBy('category_id')->get();
        $colors     = Color::orderBy('color_ten')->get();
        $sizes      = ProductSize::orderBy('product_size')->get();

        $categorySubcategoryMap = $categories->mapWithKeys(fn($cat) => [
            $cat->category_id => Subcategory::where('category_id', $cat->category_id)
                ->get(['subcategory_id as id', 'subcategory_name as name']),
        ]);

        return view('admin.products.edit', compact(
            'product', 'categories', 'colors', 'sizes', 'categorySubcategoryMap'
        ));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'product_name'   => 'required|string|max:255',
            'category_id'    => 'required|exists:categories,category_id',
            'subcategory_id' => 'required|exists:subcategories,subcategory_id',
            'product_gia'    => 'required|numeric|min:0',
            'main_image'     => 'nullable|image|max:5120',
        ], [
            'product_name.required'   => 'Vui lòng nhập tên sản phẩm.',
            'category_id.required'    => 'Vui lòng chọn danh mục.',
            'subcategory_id.required' => 'Vui lòng chọn loại sản phẩm.',
            'product_gia.required'    => 'Vui lòng nhập giá sản phẩm.',
        ]);

        DB::transaction(function () use ($request, $product) {
            $product->update([
                'product_name'   => $request->product_name,
                'description'    => $request->description,
                'care_note'      => $request->care_note,
                'category_id'    => $request->category_id,
                'subcategory_id' => $request->subcategory_id,
                'product_gia'    => $request->product_gia,
                'product_hot'    => $request->boolean('product_hot'),
            ]);

            if ($request->hasFile('main_image')) {
                ProductImage::create([
                    'product_id'  => $product->product_id,
                    'product_anh' => $this->uploadImage($request->file('main_image')),
                ]);
            }

            if ($request->hasFile('additional_images')) {
                foreach ($request->file('additional_images') as $img) {
                    ProductImage::create([
                        'product_id'  => $product->product_id,
                        'product_anh' => $this->uploadImage($img),
                    ]);
                }
            }

            if ($request->color_id && $request->size_ids) {
                $this->syncVariants($product, (int) $request->color_id, $request->size_ids);
            }
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Cập nhật sản phẩm thành công!');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Xóa sản phẩm thành công!');
    }

    // AJAX: lấy subcategory theo category
    public function getSubcategories(Request $request)
    {
        $subcategories = Subcategory::where('category_id', $request->category_id)
            ->get(['subcategory_id as id', 'subcategory_name as name']);

        return response()->json(['success' => true, 'data' => $subcategories]);
    }

    // AJAX: xóa ảnh
    public function destroyImage(Request $request)
    {
        $image = ProductImage::findOrFail($request->image_id);
        $image->delete();

        return response()->json(['success' => true, 'message' => 'Xóa ảnh thành công!']);
    }

    // ==================== PRIVATE HELPERS ====================

    private function uploadImage($file): string
    {
        $filename = uniqid() . '.' . strtolower($file->getClientOriginalExtension());
        $file->move(public_path('uploads'), $filename);
        return $filename;
    }

    private function syncVariants(Product $product, int $colorId, array $sizeIds): void
    {
        $existingSizeIds = $product->variants()
            ->where('color_id', $colorId)
            ->pluck('product_size_id')
            ->toArray();

        // Thêm variants mới
        foreach (array_diff($sizeIds, $existingSizeIds) as $sizeId) {
            $size = ProductSize::find($sizeId);
            if (!$size) continue;

            $variant = Variant::create([
                'product_id'      => $product->product_id,
                'color_id'        => $colorId,
                'product_size_id' => $sizeId,
                'quantity'        => 0,
                'variant_code'    => $product->product_id . '-' . $colorId . '-' . $size->product_size,
            ]);

            Inventory::create([
                'variant_id'         => $variant->variant_id,
                'soluong_ton'        => 0,
                'soluong_dat'        => 0,
                'soluong_co_the_ban' => 0,
                'muc_canh_bao'       => 10,
            ]);
        }

        // Xóa variants bị bỏ
        foreach (array_diff($existingSizeIds, $sizeIds) as $sizeId) {
            $variant = $product->variants()
                ->where('color_id', $colorId)
                ->where('product_size_id', $sizeId)
                ->first();

            $variant?->inventory?->delete();
            $variant?->delete();
        }
    }
}
