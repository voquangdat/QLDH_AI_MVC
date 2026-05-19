@extends('layouts.app')
@section('title', 'Giao hàng - VoxFootball')

@section('content')
<section class="delivery">
    <div class="container">
        {{-- Thanh bước --}}
        <div class="delivery-top-wrap">
            <div class="delivery-top">
                <div class="delivery-top-item delivery-top-delivery">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="delivery-top-item delivery-top-adress">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="delivery-top-item delivery-top-payment">
                    <i class="fas fa-money-check-alt"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="container">

        {{-- Alert --}}
        @if (session('success'))
            <div class="cart-alert cart-alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="cart-alert cart-alert-error">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="cart-alert cart-alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <ul style="margin:0;padding-left:16px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="delivery-content row">

            {{-- Form logout (ngoài form delivery) --}}
            @auth
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
            @endauth

            {{-- Form địa chỉ giao hàng --}}
            <div class="delivery-content-left">
                <form action="{{ route('delivery.store') }}" method="POST">
                    @csrf

                    <p>Vui lòng chọn địa chỉ giao hàng</p>

                    @auth
                    <div class="delivery-content-left-dangnhap row">
                        <i class="fas fa-user-circle"></i>
                        <p>Xin chào, <strong>{{ Auth::user()->fullname }}</strong> &nbsp;|&nbsp;
                           <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();" style="color:#e74c3c;font-size:11px;">Đăng xuất</a>
                        </p>
                    </div>
                    @else
                    <div class="delivery-content-left-dangnhap row" style="background:#fff8e1;padding:8px 12px;border-left:3px solid #f39c12;margin-top:12px;">
                        <i class="fas fa-info-circle" style="color:#f39c12;margin-right:8px;"></i>
                        <p style="font-size:12px;">Bạn đang mua với tư cách khách.
                           <a href="{{ route('login') }}" style="color:#0DB7EA;">Đăng nhập</a> để tích điểm thành viên.
                        </p>
                    </div>
                    @endauth
                    <br>

                    <div class="delivery-content-left-input-top row">
                        <div class="delivery-content-left-input-top-item">
                            <label>Họ tên <span style="color:red;">*</span></label>
                            <input name="customer_name" type="text" required
                                   value="{{ old('customer_name', Auth::user()->fullname ?? '') }}">
                        </div>
                        <div class="delivery-content-left-input-top-item">
                            <label>Điện thoại <span style="color:red;">*</span></label>
                            <input name="customer_phone" type="text" required
                                   value="{{ old('customer_phone', Auth::user()->phone ?? '') }}">
                        </div>
                        <div class="delivery-content-left-input-top-item">
                            <label>Tỉnh/Tp <span style="color:red;">*</span></label>
                            <select name="customer_province" id="tinh_tp" required>
                                <option value="">Chọn Tỉnh/Tp</option>
                            </select>
                        </div>
                        <div class="delivery-content-left-input-top-item">
                            <label>Quận/Huyện <span style="color:red;">*</span></label>
                            <select name="customer_district" id="quan_huyen" required>
                                <option value="">Chọn Quận/Huyện</option>
                            </select>
                        </div>
                    </div>

                    <div class="delivery-content-left-input-bottom">
                        <label>Phường/Xã <span style="color:red;">*</span></label>
                        <select name="customer_ward" id="phuong_xa" required>
                            <option value="">Chọn Phường/Xã</option>
                        </select>
                    </div>

                    <div class="delivery-content-left-input-bottom">
                        <label>Địa chỉ cụ thể <span style="color:red;">*</span></label>
                        <input name="customer_address" type="text" required
                               placeholder="Số nhà, tên đường..."
                               value="{{ old('customer_address') }}">
                    </div>

                    <div class="delivery-content-left-input-bottom">
                        <label>Ghi chú đơn hàng</label>
                        <input name="notes" type="text"
                               placeholder="Ghi chú thêm (nếu có)..."
                               value="{{ old('notes') }}">
                    </div>

                    <div class="delivery-content-left-button row">
                        <a href="{{ route('cart.index') }}">
                            <span>&#8810;</span>
                            <p>Quay lại giỏ hàng</p>
                        </a>
                        <button type="submit">
                            <p style="font-weight:bold;">THANH TOÁN VÀ GIAO HÀNG</p>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Tóm tắt đơn hàng --}}
            <div class="delivery-content-right">
                <table>
                    <tr>
                        <th>Tên sản phẩm</th>
                        <th>SL</th>
                        <th>Thành tiền</th>
                    </tr>
                    @php $totalAmount = 0; @endphp
                    @foreach ($cartItems as $item)
                        @php
                            $variant  = $item->variant;
                            $product  = $variant->product;
                            $subtotal = $item->subtotal();
                            $totalAmount += $subtotal;
                        @endphp
                        <tr>
                            <td>
                                {{ $product->product_name }}
                                @if($variant->size->product_size ?? null)
                                    <span style="color:#888;font-size:11px;">({{ $variant->size->product_size }})</span>
                                @endif
                            </td>
                            <td style="text-align:center;">{{ $item->quantity }}</td>
                            <td>{{ number_format($subtotal) }}đ</td>
                        </tr>
                    @endforeach

                    @php $shippingFee = $totalAmount >= 2000000 ? 0 : 30000; @endphp

                    <tr>
                        <td colspan="2">Phí vận chuyển</td>
                        <td>
                            @if($shippingFee === 0)
                                <span style="color:green;font-weight:bold;">Miễn phí</span>
                            @else
                                {{ number_format($shippingFee) }}đ
                            @endif
                        </td>
                    </tr>
                    <tr style="border-top:2px solid #dddddd;">
                        <td colspan="2" style="font-weight:bold;padding-top:10px;">Tổng cộng</td>
                        <td style="font-weight:bold;color:red;padding-top:10px;">
                            {{ number_format($totalAmount + $shippingFee) }}đ
                        </td>
                    </tr>
                </table>

                @if($totalAmount < 2000000)
                    <p style="margin-top:12px;font-size:11px;color:#888;text-align:center;">
                        Mua thêm <strong style="color:red;">{{ number_format(2000000 - $totalAmount) }}đ</strong>
                        để được miễn phí vận chuyển.
                    </p>
                @endif
            </div>

        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
$(document).ready(function () {

    var API = 'https://esgoo.net/api-tinhthanh';

    // Load 63 tỉnh/tp
    $.getJSON(API + '/1/0.htm', function (res) {
        if (res.error !== 0) return;
        var opts = '<option value="">Chọn Tỉnh/Tp</option>';
        $.each(res.data, function (i, tinh) {
            opts += '<option value="' + tinh.full_name + '" data-id="' + tinh.id + '">'
                  + tinh.full_name + '</option>';
        });
        $('#tinh_tp').html(opts);
    });

    // Chọn tỉnh → load huyện
    $('#tinh_tp').on('change', function () {
        var id = $(this).find(':selected').data('id');
        $('#quan_huyen').html('<option value="">Chọn Quận/Huyện</option>');
        $('#phuong_xa').html('<option value="">Chọn Phường/Xã</option>');
        if (!id) return;

        $.getJSON(API + '/2/' + id + '.htm', function (res) {
            if (res.error !== 0) return;
            var opts = '<option value="">Chọn Quận/Huyện</option>';
            $.each(res.data, function (i, huyen) {
                opts += '<option value="' + huyen.full_name + '" data-id="' + huyen.id + '">'
                      + huyen.full_name + '</option>';
            });
            $('#quan_huyen').html(opts);
        });
    });

    // Chọn huyện → load xã
    $('#quan_huyen').on('change', function () {
        var id = $(this).find(':selected').data('id');
        $('#phuong_xa').html('<option value="">Chọn Phường/Xã</option>');
        if (!id) return;

        $.getJSON(API + '/3/' + id + '.htm', function (res) {
            if (res.error !== 0) return;
            var opts = '<option value="">Chọn Phường/Xã</option>';
            $.each(res.data, function (i, xa) {
                opts += '<option value="' + xa.full_name + '">' + xa.full_name + '</option>';
            });
            $('#phuong_xa').html(opts);
        });
    });

});
</script>
@endpush
