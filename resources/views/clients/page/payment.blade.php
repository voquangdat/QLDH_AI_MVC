@extends('layouts.app')
@section('title', 'Thanh toán - VoxFootball')

@section('content')

{{-- Flash Messages --}}
@if (session('flash_success'))
    <div class="alert alert-success">{{ session('flash_success') }}</div>
@endif
@if (session('flash_error') || session('error'))
    <div class="alert alert-error">{{ session('flash_error') ?? session('error') }}</div>
@endif
@if ($errors->any())
    <div class="alert alert-error">
        <ul style="margin:0;padding-left:16px;">
            @foreach ($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- PAYMENT -->
<section class="payment">
    <div class="container">
        <div class="payment-top-wrap">
            <div class="payment-top">
                <div class="delivery-top-delivery payment-top-item">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="delivery-top-adress payment-top-item">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="delivery-top-payment payment-top-item">
                    <i class="fas fa-money-check-alt"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        @if ($order)
            <div class="payment-content row">

                {{-- Cột trái: chọn phương thức --}}
                <div class="payment-content-left">
                    <form action="{{ route('payment.process', $order->id) }}" method="POST" id="payment-form">
                        @csrf

                        <div class="payment-content-left-method-delivery">
                            <p style="font-weight:bold;">Phương thức giao hàng</p><br>
                            <div class="payment-content-left-method-delivery-item">
                                <input name="delivery_method" value="Giao hàng chuyển phát nhanh" type="radio" id="express" checked>
                                <label for="express">Giao hàng chuyển phát nhanh</label>
                            </div>
                            <div class="payment-content-left-method-delivery-item">
                                <input name="delivery_method" value="Giao hàng tiêu chuẩn" type="radio" id="standard">
                                <label for="standard">Giao hàng tiêu chuẩn</label>
                            </div>
                        </div>
                        <br>

                        <div class="payment-content-left-method-payment">
                            <p style="font-weight:bold;">Phương thức thanh toán</p><br>
                            <div class="payment-content-left-method-payment-item">
                                <input value="COD" name="payment_method" id="cod" checked type="radio">
                                <label for="cod">Thu tiền tận nơi (COD)</label>
                            </div>
                            <div class="payment-content-left-method-payment-item">
                                <input value="momo" name="payment_method" id="momo" type="radio">
                                <label for="momo">Thanh toán Momo</label>
                            </div>
                            <div id="payment-method-info" style="display:none; margin-top:10px;"></div>
                        </div>

                        <div class="payment-content-right-payment">
                            <button type="submit" id="submit-btn">HOÀN THÀNH THANH TOÁN</button>
                        </div>
                    </form>
                </div>

                {{-- Cột phải: thông tin đơn hàng --}}
                <div class="payment-content-right">

                    {{-- Thông tin khách hàng --}}
                    @php
                        $info    = json_decode($order->notes, true) ?? [];
                        $address = collect([
                            $info['address']  ?? null,
                            $info['ward']     ?? null,
                            $info['district'] ?? null,
                            $info['province'] ?? null,
                        ])->filter()->implode(', ');
                    @endphp

                    <div class="order-info" style="background:#f8f9fa;padding:15px;margin-bottom:20px;border-radius:5px;">
                        <h4>Thông tin đơn hàng: {{ $order->order_number }}</h4>
                        <p><strong>Khách hàng:</strong> {{ $info['name'] ?? '—' }}</p>
                        <p><strong>Điện thoại:</strong> {{ $info['phone'] ?? '—' }}</p>
                        <p><strong>Địa chỉ:</strong> {{ $address ?: '—' }}</p>
                        <p><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    {{-- Mã giảm giá --}}
                    <div class="payment-content-right-button">
                        <input type="text" placeholder="Mã giảm giá/Quà tặng">
                        <button type="button"><i class="fas fa-check"></i></button>
                    </div>
                    <div class="payment-content-right-button">
                        <input type="text" placeholder="Mã cộng tác viên">
                        <button type="button"><i class="fas fa-check"></i></button>
                    </div>
                    <div class="payment-content-right-mnv">
                        <select>
                            <option value="">Chọn mã nhân viên thân thiết</option>
                            <option value="D345">D345</option>
                            <option value="C333">C333</option>
                            <option value="T567">T567</option>
                            <option value="D333">D333</option>
                        </select>
                    </div>
                    <br>

                    {{-- Bảng sản phẩm --}}
                    <table>
                        <tr>
                            <th>Tên sản phẩm</th>
                            <th>Đơn giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                        </tr>

                        @foreach ($order->orderDetails as $item)
                            <tr>
                                <td>
                                    {{ $item->product_name }}
                                </td>
                                <td>{{ number_format($item->product_gia) }}đ</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->subtotal) }}đ</td>
                            </tr>
                        @endforeach

                        <tr style="border-top:2px solid #ddd;">
                            <td colspan="3" style="font-weight:bold;">Tổng tiền hàng</td>
                            <td style="font-weight:bold;">{{ number_format($order->subtotal) }}đ</td>
                        </tr>

                        @if ($order->shipping_fee > 0)
                            <tr>
                                <td colspan="3">Phí vận chuyển</td>
                                <td>{{ number_format($order->shipping_fee) }}đ</td>
                            </tr>
                        @endif

                        <tr style="border-top:2px solid red;">
                            <td colspan="3" style="font-weight:bold;font-size:16px;">TỔNG THANH TOÁN</td>
                            <td style="font-weight:bold;color:red;font-size:16px;">
                                {{ number_format($order->total_amount) }}đ
                            </td>
                        </tr>
                    </table>

                </div>
            </div>

        @else
            {{-- Không có thông tin đơn hàng --}}
            <div class="cart-empty" style="text-align:center;padding:50px 0;">
                <p style="font-size:18px;margin-bottom:20px;">
                    Không tìm thấy thông tin đơn hàng. Vui lòng quay lại trang giao hàng.
                </p>
                <a href="{{ route('delivery.index') }}" style="display:inline-block;padding:10px 20px;background:#e53637;color:white;text-decoration:none;border-radius:5px;">
                    Quay lại trang giao hàng
                </a>
            </div>
        @endif
    </div>
</section>

@endsection

@push('scripts')
<script>
$(document).ready(function () {

    // Hiển thị thông tin phương thức thanh toán
    $('input[name="payment_method"]').on('change', function () {
        var method = $(this).val();
        var info   = '';
        if (method === 'COD') {
            info = '<i class="fas fa-info-circle"></i> Bạn sẽ thanh toán bằng tiền mặt khi nhận hàng.';
        } else if (method === 'momo') {
            info = '<i class="fas fa-info-circle"></i> Bạn sẽ được chuyển đến trang thanh toán Momo.';
        }
        $('#payment-method-info').html('<p style="margin:0;color:#0c5460;">' + info + '</p>').slideDown();
    });

    // Submit form
    $('#payment-form').on('submit', function (e) {
        var delivery = $('input[name="delivery_method"]:checked').val();
        var payment  = $('input[name="payment_method"]:checked').val();

        if (!delivery) {
            alert('Vui lòng chọn phương thức giao hàng');
            e.preventDefault();
            return false;
        }
        if (!payment) {
            alert('Vui lòng chọn phương thức thanh toán');
            e.preventDefault();
            return false;
        }

        var msg = 'Xác nhận thanh toán với:\nGiao hàng: ' + delivery + '\nThanh toán: ' + payment;
        if (!confirm(msg)) {
            e.preventDefault();
            return false;
        }

        $('#submit-btn').prop('disabled', true).text('Đang xử lý...');
    });

});
</script>

<style>
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 4px;
    animation: slideDown 0.3s ease-out;
}
.alert-success { background:#d4edda; border-color:#c3e6cb; color:#155724; }
.alert-error   { background:#f8d7da; border-color:#f5c6cb; color:#721c24; }
.alert-info    { background:#d1ecf1; border-color:#bee5eb; color:#0c5460; }

@keyframes slideDown {
    from { opacity:0; transform:translateY(-20px); }
    to   { opacity:1; transform:translateY(0); }
}

#payment-method-info {
    padding: 12px;
    background: #e7f3ff;
    border-left: 4px solid #2196F3;
    border-radius: 4px;
}
</style>
@endpush
