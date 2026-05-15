<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Inventory;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::with(['variant.product.images', 'variant.color', 'variant.size'])
            ->where('users_id', Auth::id())
            ->get();

        $total    = $cartItems->sum(fn($item) => $item->subtotal());
        $totalQty = $cartItems->sum('quantity');

        return view('clients.page.cart', compact('cartItems', 'total', 'totalQty'));
    }

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập để mua hàng!', 'redirect' => route('login')], 401);
        }

        $request->validate([
            'variant_id' => 'required|exists:variant,variant_id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $variant   = Variant::with('inventory')->findOrFail($request->variant_id);
        $inventory = $variant->inventory;
        $quantity  = (int) $request->quantity;

        if (!$inventory || $inventory->soluong_co_the_ban < $quantity) {
            return response()->json(['success' => false, 'message' => 'Số lượng tồn kho không đủ!']);
        }

        $cartItem = Cart::where('users_id', Auth::id())
            ->where('variant_id', $request->variant_id)
            ->first();

        if ($cartItem) {
            $newQty = $cartItem->quantity + $quantity;
            if ($inventory->soluong_co_the_ban < $newQty) {
                return response()->json(['success' => false, 'message' => 'Vượt quá số lượng tồn kho!']);
            }
            $cartItem->update(['quantity' => $newQty]);
        } else {
            Cart::create([
                'users_id'   => Auth::id(),
                'variant_id' => $request->variant_id,
                'quantity'   => $quantity,
            ]);
        }

        $cartCount = Cart::where('users_id', Auth::id())->sum('quantity');

        return response()->json([
            'success'    => true,
            'message'    => 'Đã thêm vào giỏ hàng!',
            'cart_count' => $cartCount,
        ]);
    }

    public function mini()
    {
        if (!Auth::check()) {
            return response()->json(['html' => '', 'count' => 0, 'total' => 0]);
        }

        $miniCartItems = Cart::with(['variant.product.images', 'variant.color', 'variant.size'])
            ->where('users_id', Auth::id())
            ->get();
        $miniCartTotal = $miniCartItems->sum(fn($i) => $i->subtotal());
        $miniCartCount = $miniCartItems->sum('quantity');

        $html = view('components.mini-cart', compact('miniCartItems', 'miniCartTotal', 'miniCartCount'))->render();

        return response()->json(['html' => $html, 'count' => $miniCartCount, 'total' => $miniCartTotal]);
    }

    public function destroy($cartId)
    {
        $item = Cart::where('cart_id', $cartId)->where('users_id', Auth::id())->firstOrFail();
        $item->delete();

        return redirect()->route('cart.index')->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }
}
