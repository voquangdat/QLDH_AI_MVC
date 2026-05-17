@extends('layouts.app')
@section('title', 'Đặt hàng thành công - VoxFootball')

@section('content')

@php
    $info = json_decode($order->notes, true) ?? [];
    $payment = $order->payments->first();
@endphp

<!-- Success Page - Order Confirmation -->
<section class="success">
    <div class="success-top">
        <p>ĐẶT HÀNG THÀNH CÔNG</p>
    </div>

    <div class="success-text">
        @if ($order)
            <p>Chào <span style="font-size:20px;color:#378000;">{{ $info['name'] ?? 'Khách hàng' }}</span>,
               đơn hàng của bạn với mã <span style="font-size:20px;color:#378000;">{{ $order->order_number }}</span> đã được đặt thành công.
            </p>

            <div class="order-details" style="margin:20px 0;padding:15px;background:#f8f9fa;border-radius:8px;">
                <h4 style="margin-bottom:10px;color:#333;">Chi tiết đơn hàng:</h4>
                <p><strong>Mã đơn hàng:</strong> {{ $order->order_number }}</p>
                <p><strong>Tổng tiền:</strong>
                    <span style="color:#e74c3c;font-weight:bold;">
                        {{ number_format($order->total_amount, 0, ',', '.') }} VNĐ
                    </span>
                </p>
                <p><strong>Phương thức thanh toán:</strong>
                    {{ $payment?->payment_method === 'COD' ? 'Thu tiền tận nơi (COD)' : ($payment?->payment_method ?? 'Không xác định') }}
                </p>
                <p><strong>Thời gian đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>

            <div class="important-notes" style="margin:20px 0;">
                <p>Đơn hàng của bạn đã được xác nhận tự động.</p>
                <p style="font-weight:bold;">
                    Hiện tại do đang trong Chương trình Sale lớn, đơn hàng của quý khách sẽ gửi chậm hơn so với thời gian dự kiến từ 5-10 ngày.
                    Rất mong quý khách thông cảm vì sự bất tiện này!
                </p>
                <p style="color:red;">
                    (Lưu ý: VOXFOOTBALL sẽ không gọi xác nhận đơn hàng, đơn hàng được xử lý tự động và sẽ giao cho bạn trong thời gian sớm nhất)
                </p>
                <p>Cảm ơn <span style="font-size:20px;color:#378000;">{{ $info['name'] ?? 'bạn' }}</span> đã tin dùng sản phẩm của VOXFOOTBALL.</p>
            </div>
        @else
            <p style="color:#e74c3c;">Không tìm thấy thông tin đơn hàng. Vui lòng liên hệ với chúng tôi để được hỗ trợ.</p>
        @endif
    </div>

    <div class="success-button">
        <a href="{{ route('order.detail', $order->id) }}"><button>XEM CHI TIẾT ĐƠN HÀNG</button></a>
        <a href="{{ route('home') }}"><button>TIẾP TỤC MUA SẮM</button></a>
    </div>

    <p>Mọi thắc mắc quý khách vui lòng liên hệ hotline
       <span style="font-size:20px;color:red;">0973 999 949</span>
       hoặc chat với kênh hỗ trợ trên website để được hỗ trợ nhanh nhất.
    </p>
</section>

@endsection
