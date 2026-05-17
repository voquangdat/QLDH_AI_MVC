<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\ShippingAddress;
use App\Models\Variant;
use App\Services\OrderService;
use App\Support\GuestCartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryController extends Controller
{
    public function __construct(private OrderService $orderService) {}

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

    public function index()
    {
        $cartItems = $this->getCartItems();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        $total = $cartItems->sum(fn($item) => $item->subtotal());

        return view('clients.page.delivery', compact('cartItems', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name'     => 'required|string|max:255',
            'customer_phone'    => 'required|string|max:20',
            'customer_province' => 'required|string|max:100',
            'customer_district' => 'required|string|max:100',
            'customer_ward'     => 'required|string|max:100',
            'customer_address'  => 'required|string|max:255',
        ], [
            'customer_name.required'     => 'Vui lòng nhập họ tên.',
            'customer_phone.required'    => 'Vui lòng nhập số điện thoại.',
            'customer_province.required' => 'Vui lòng chọn Tỉnh/Thành phố.',
            'customer_district.required' => 'Vui lòng chọn Quận/Huyện.',
            'customer_ward.required'     => 'Vui lòng chọn Phường/Xã.',
            'customer_address.required'  => 'Vui lòng nhập địa chỉ cụ thể.',
        ]);

        $cartItems = $this->getCartItems();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        $deliveryData = [
            'name'     => $request->customer_name,
            'phone'    => $request->customer_phone,
            'province' => $request->customer_province,
            'district' => $request->customer_district,
            'ward'     => $request->customer_ward,
            'address'  => $request->customer_address,
        ];

        $result = $this->orderService->createOrder(
            $deliveryData,
            $cartItems,
            Auth::check() ? Auth::id() : null
        );

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        // Lưu địa chỉ cho user đã đăng nhập và dọn giỏ hàng
        if (Auth::check()) {
            ShippingAddress::create([
                'user_id'      => Auth::id(),
                'full_name'    => $request->customer_name,
                'phone_number' => $request->customer_phone,
                'province'     => $request->customer_province,
                'district'     => $request->customer_district,
                'ward'         => $request->customer_ward,
                'detail'       => $request->customer_address,
                'is_default'   => false,
            ]);

            Cart::where('users_id', Auth::id())->delete();
        } else {
            session()->forget('guest_cart');
        }

        return redirect()->route('payment.show', $result['order_id']);
    }
}
