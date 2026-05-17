@extends('layouts.app')
@section('title', 'Chi tiết đơn hàng #{{ $order->order_number }} - VoxFootball')

@section('content')

@php
    $info    = json_decode($order->notes, true) ?? [];
    $payment = $order->payments->first();
    $address = collect([
        $info['address']  ?? null,
        $info['ward']     ?? null,
        $info['district'] ?? null,
        $info['province'] ?? null,
    ])->filter()->implode(', ');

    $statusLabel = [
        'pending'    => ['text' => 'Chờ xác nhận',  'color' => '#f39c12'],
        'confirmed'  => ['text' => 'Đã xác nhận',   'color' => '#3498db'],
        'processing' => ['text' => 'Đang xử lý',    'color' => '#9b59b6'],
        'shipped'    => ['text' => 'Đang giao',      'color' => '#1abc9c'],
        'delivered'  => ['text' => 'Đã giao hàng',  'color' => '#27ae60'],
        'cancelled'  => ['text' => 'Đã huỷ',        'color' => '#e74c3c'],
    ];
    $paymentLabel = [
        'unpaid'   => ['text' => 'Chưa thanh toán', 'color' => '#e74c3c'],
        'paid'     => ['text' => 'Đã thanh toán',   'color' => '#27ae60'],
        'refunded' => ['text' => 'Đã hoàn tiền',    'color' => '#95a5a6'],
    ];
    $currentStatus  = $statusLabel[$order->order_status]  ?? ['text' => $order->order_status,  'color' => '#333'];
    $currentPayment = $paymentLabel[$order->payment_status] ?? ['text' => $order->payment_status, 'color' => '#333'];
@endphp

<section class="delivery" style="padding: 40px 0;">
    <div class="container">

        {{-- Tiêu đề --}}
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
            <h2 style="margin:0;">Chi tiết đơn hàng</h2>
            <a href="{{ route('home') }}"
               style="padding:8px 16px;background:#e53637;color:white;border-radius:5px;text-decoration:none;font-size:14px;">
                ← Tiếp tục mua sắm
            </a>
        </div>

        <div class="delivery-content row" style="align-items:flex-start;">

            {{-- Cột trái: thông tin đơn + khách hàng --}}
            <div class="delivery-content-left">

                {{-- Thông tin đơn hàng --}}
                <div style="background:#fff;border:1px solid #e0e0e0;border-radius:8px;padding:20px;margin-bottom:20px;">
                    <h4 style="margin-bottom:16px;border-bottom:2px solid #e53637;padding-bottom:8px;">
                        <i class="fas fa-file-alt" style="color:#e53637;"></i> Thông tin đơn hàng
                    </h4>
                    <p><strong>Mã đơn hàng:</strong>
                        <span style="color:#e53637;font-weight:bold;">{{ $order->order_number }}</span>
                    </p>
                    <p><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Trạng thái đơn:</strong>
                        <span style="color:{{ $currentStatus['color'] }};font-weight:bold;">
                            {{ $currentStatus['text'] }}
                        </span>
                    </p>
                    <p><strong>Trạng thái thanh toán:</strong>
                        <span style="color:{{ $currentPayment['color'] }};font-weight:bold;">
                            {{ $currentPayment['text'] }}
                        </span>
                    </p>
                    @if ($payment)
                        <p><strong>Phương thức thanh toán:</strong>
                            {{ $payment->payment_method === 'COD' ? 'Thu tiền tận nơi (COD)' : $payment->payment_method }}
                        </p>
                    @endif
                </div>

                {{-- Thông tin người nhận --}}
                <div style="background:#fff;border:1px solid #e0e0e0;border-radius:8px;padding:20px;">
                    <h4 style="margin-bottom:16px;border-bottom:2px solid #e53637;padding-bottom:8px;">
                        <i class="fas fa-map-marker-alt" style="color:#e53637;"></i> Thông tin giao hàng
                    </h4>
                    <p><strong>Người nhận:</strong> {{ $info['name'] ?? '—' }}</p>
                    <p><strong>Điện thoại:</strong> {{ $info['phone'] ?? '—' }}</p>
                    <p><strong>Địa chỉ:</strong> {{ $address ?: '—' }}</p>
                </div>

            </div>

            {{-- Cột phải: sản phẩm --}}
            <div class="delivery-content-right">
                <div style="background:#fff;border:1px solid #e0e0e0;border-radius:8px;padding:20px;">
                    <h4 style="margin-bottom:16px;border-bottom:2px solid #e53637;padding-bottom:8px;">
                        <i class="fas fa-shopping-bag" style="color:#e53637;"></i> Sản phẩm đã đặt
                    </h4>

                    <table>
                        <tr>
                            <th>Tên sản phẩm</th>
                            <th>Đơn giá</th>
                            <th>SL</th>
                            <th>Thành tiền</th>
                        </tr>

                        @foreach ($order->orderDetails as $item)
                            <tr>
                                <td>
                                    @if ($item->product_anh)
                                        <img src="{{ asset('uploads/' . $item->product_anh) }}"
                                             style="width:40px;height:40px;object-fit:cover;border-radius:4px;margin-right:8px;vertical-align:middle;">
                                    @endif
                                    {{ $item->product_name }}
                                </td>
                                <td>{{ number_format($item->product_gia) }}đ</td>
                                <td style="text-align:center;">{{ $item->quantity }}</td>
                                <td>{{ number_format($item->subtotal) }}đ</td>
                            </tr>
                        @endforeach

                        <tr style="border-top:2px solid #ddd;">
                            <td colspan="3" style="font-weight:bold;padding-top:10px;">Tổng tiền hàng</td>
                            <td style="font-weight:bold;padding-top:10px;">{{ number_format($order->subtotal) }}đ</td>
                        </tr>

                        @if ($order->shipping_fee > 0)
                            <tr>
                                <td colspan="3">Phí vận chuyển</td>
                                <td>{{ number_format($order->shipping_fee) }}đ</td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="3">Phí vận chuyển</td>
                                <td style="color:green;font-weight:bold;">Miễn phí</td>
                            </tr>
                        @endif

                        <tr style="border-top:2px solid red;">
                            <td colspan="3" style="font-weight:bold;font-size:16px;padding-top:10px;">TỔNG THANH TOÁN</td>
                            <td style="font-weight:bold;color:red;font-size:16px;padding-top:10px;">
                                {{ number_format($order->total_amount) }}đ
                            </td>
                        </tr>
                    </table>
                </div>

                {{-- Ghi chú --}}
                <div style="margin-top:20px;padding:15px;background:#fff8e1;border-left:4px solid #f39c12;border-radius:4px;">
                    <p style="margin:0;font-size:13px;color:#666;">
                        <i class="fas fa-info-circle" style="color:#f39c12;"></i>
                        Mọi thắc mắc vui lòng liên hệ hotline
                        <strong style="color:red;">0973 999 949</strong>
                        hoặc chat với kênh hỗ trợ trên website.
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection
