<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        return view('admin.inventory.index');
    }

    public function list(Request $request)
    {
        $search     = $request->get('search');
        $categoryId = $request->get('category');
        $status     = $request->get('status');

        $query = Inventory::with([
            'variant.product.category',
            'variant.product.images',
            'variant.color',
            'variant.size',
        ]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('variant.product', fn($q2) => $q2->where('product_name', 'like', "%$search%"))
                  ->orWhereHas('variant', fn($q2) => $q2->where('variant_code', 'like', "%$search%"));
            });
        }

        if ($categoryId) {
            $query->whereHas('variant.product', fn($q) => $q->where('category_id', $categoryId));
        }

        match ($status) {
            'in_stock'  => $query->inStock(),
            'low_stock' => $query->lowStock(),
            'out_stock' => $query->outOfStock(),
            default     => null,
        };

        $inventories = $query->paginate(15)->withQueryString();

        $stats = [
            'tong_bienthe' => Inventory::count(),
            'tong_ton_kho' => Inventory::sum('soluong_ton'),
            'canh_bao'     => Inventory::lowStock()->count(),
            'het_hang'     => Inventory::outOfStock()->count(),
        ];

        $lowStockItems = Inventory::with(['variant.product.images', 'variant.color', 'variant.size'])
            ->lowStock()
            ->take(6)
            ->get();

        $categories = Category::orderBy('category_name')->get();

        return view('admin.inventory.list', compact(
            'inventories', 'stats', 'lowStockItems', 'categories'
        ));
    }

    public function detail(Request $request)
    {
        $productId = $request->get('product_id');

        $product = $productId
            ? Product::with(['category', 'images'])->findOrFail($productId)
            : null;

        $variants = Variant::with(['product.category', 'product.images', 'color', 'size', 'inventory'])
            ->when($productId, fn($q) => $q->where('product_id', $productId))
            ->get();

        $stats = [
            'tong_bienthe' => $variants->count(),
            'tong_ton_kho' => $variants->sum(fn($v) => $v->inventory?->soluong_ton ?? 0),
            'co_the_ban'   => $variants->sum(fn($v) => $v->inventory?->availableQuantity() ?? 0),
            'het_hang'     => $variants->filter(fn($v) => $v->isOutOfStock())->count(),
        ];

        return view('admin.inventory.detail', compact('variants', 'product', 'stats'));
    }

    public function initStock(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|integer|exists:variant,variant_id',
        ]);

        $exists = Inventory::where('variant_id', $request->variant_id)->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Tồn kho đã tồn tại']);
        }

        Inventory::create([
            'variant_id'         => $request->variant_id,
            'soluong_ton'        => 0,
            'soluong_dat'        => 0,
            'soluong_co_the_ban' => 0,
            'muc_canh_bao'       => 5,
        ]);

        return response()->json(['success' => true, 'message' => 'Khởi tạo tồn kho thành công!']);
    }

    public function updateStock(Request $request)
    {
        $request->validate([
            'variant_id'  => 'required|integer|exists:variant,variant_id',
            'soluong_ton' => 'nullable|integer|min:0',
            'soluong_dat' => 'nullable|integer|min:0',
            'muc_canh_bao'=> 'nullable|integer|min:1',
        ]);

        $inventory = Inventory::where('variant_id', $request->variant_id)->first();

        if (!$inventory) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy tồn kho']);
        }

        $inventory->soluong_ton  = (int) ($request->soluong_ton  ?? $inventory->soluong_ton);
        $inventory->soluong_dat  = (int) ($request->soluong_dat  ?? $inventory->soluong_dat);
        $inventory->muc_canh_bao = (int) ($request->muc_canh_bao ?? $inventory->muc_canh_bao);
        $inventory->recalculate();
        $inventory->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thành công!',
            'data'    => ['soluong_co_the_ban' => $inventory->availableQuantity()],
        ]);
    }
}
