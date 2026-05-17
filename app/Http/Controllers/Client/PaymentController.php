<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function show(int $orderId)
    {
        $order = Order::with('orderDetails')->findOrFail($orderId);

        return view('clients.page.payment', compact('order'));
    }

    public function process(Request $request, int $orderId)
    {
        $request->validate([
            'delivery_method' => 'required|string',
            'payment_method'  => 'required|in:COD,momo',
        ], [
            'delivery_method.required' => 'Vui lòng chọn phương thức giao hàng.',
            'payment_method.required'  => 'Vui lòng chọn phương thức thanh toán.',
        ]);

        $order = Order::findOrFail($orderId);

        DB::transaction(function () use ($request, $order) {
            $isCOD = $request->payment_method === 'COD';

            // payments.payment_status: enum('pending','completed','failed','refunded')
            $paymentRecordStatus = $isCOD ? 'pending' : 'completed';

            // orders.payment_status: enum('unpaid','paid','refunded')
            $orderPaymentStatus  = $isCOD ? 'unpaid' : 'paid';

            Payment::create([
                'order_id'       => $order->id,
                'payment_method' => $request->payment_method,
                'transaction_id' => 'TXN-' . strtoupper(uniqid()),
                'payment_date'   => now(),
                'amount'         => $order->total_amount,
                'payment_status' => $paymentRecordStatus,
            ]);

            $order->update([
                'payment_status' => $orderPaymentStatus,
                'order_status'   => 'confirmed',
            ]);
        });

        return redirect()->route('payment.success', $orderId);
    }

    public function success(int $orderId)
    {
        $order = Order::with(['orderDetails', 'payments'])->findOrFail($orderId);

        return view('clients.page.payment_success', compact('order'));
    }

    public function detail(int $orderId)
    {
        $order = Order::with([
            'orderDetails.variant.color',
            'orderDetails.variant.size',
            'orderDetails.product.images',
            'payments',
            'statusHistories',
        ])->findOrFail($orderId);

        return view('clients.page.order_detail', compact('order'));
    }

    /**
     * Lấy đơn hàng gần nhất cho khách chưa đăng nhập (theo session).
     */
    public function guestDetail(Request $request)
    {
        $sessionOrderId = $request->session()->get('last_order_id');

        if (!$sessionOrderId) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng.');
        }

        $order = Order::with([
            'orderDetails.variant.color',
            'orderDetails.variant.size',
            'orderDetails.product.images',
            'payments',
            'statusHistories',
        ])->where('id', $sessionOrderId)
          ->whereNull('user_id')
          ->first();

        if (!$order) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng.');
        }

        return view('clients.page.order_detail', compact('order'));
    }
}
