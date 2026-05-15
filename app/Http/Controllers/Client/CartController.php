<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Inventory;
use App\Models\Variant;
use App\Support\GuestCartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // ===== Helpers =====

    private function getCartItems()
    {
        if (Auth::check()) {
            return Cart::with(['variant.product.images', 'variant.color', 'variant.size'])
                ->where('users_id', Auth::id())
                ->get();
        }

        $guestCart = session('guest_cart', []);
        $items     = collect();

        foreach ($guestCart as $variantId => $quantity) {
            $variant = Variant::with(['product.images', 'color', 'size'])->find($variantId);
            if (!$variant) continue;
            $items->push(new GuestCartItem((int) $variantId, $variant, $quantity));
        }

        return $items;
    }

    // ===== Actions =====

    public function index()
    {
        $cartItems = $this->getCartItems();
        $total     = $cartItems->sum(fn($item) => $item->subtotal());
        $totalQty  = $cartItems->sum('quantity');

        return view('clients.page.cart', compact('cartItems', 'total', 'totalQty'));
    }

    public function store(Request $request)
    {
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

        if (Auth::check()) {
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
        } else {
            $guestCart  = session('guest_cart', []);
            $variantId  = (int) $request->variant_id;
            $currentQty = $guestCart[$variantId] ?? 0;
            $newQty     = $currentQty + $quantity;

            if ($inventory->soluong_co_the_ban < $newQty) {
                return response()->json(['success' => false, 'message' => 'Vượt quá số lượng tồn kho!']);
            }

            $guestCart[$variantId] = $newQty;
            session(['guest_cart' => $guestCart]);

            $cartCount = array_sum($guestCart);
        }

        return response()->json([
            'success'    => true,
            'message'    => 'Đã thêm vào giỏ hàng!',
            'cart_count' => $cartCount,
        ]);
    }

    public function mini()
    {
        $miniCartItems = $this->getCartItems();
        $miniCartTotal = $miniCartItems->sum(fn($i) => $i->subtotal());
        $miniCartCount = $miniCartItems->sum('quantity');

        $html = view('components.mini-cart', compact('miniCartItems', 'miniCartTotal', 'miniCartCount'))->render();

        return response()->json(['html' => $html, 'count' => $miniCartCount, 'total' => $miniCartTotal]);
    }

    public function destroy($variantId)
    {
        if (Auth::check()) {
            $item = Cart::where('variant_id', $variantId)
                ->where('users_id', Auth::id())
                ->firstOrFail();
            $item->delete();
        } else {
            $guestCart = session('guest_cart', []);
            unset($guestCart[(int) $variantId]);
            session(['guest_cart' => $guestCart]);
        }

        return redirect()->route('cart.index')->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }
}
