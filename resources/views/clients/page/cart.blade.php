@extends('layouts.app')
@section('title', 'Giỏ hàng - VoxFootball')

@section('content')
<section class="cart">
    <div class="container">
        {{-- Thanh bước --}}
        <div class="cart-top-wrap">
            <div class="cart-top">
                <div class="cart-top-item cart-top-cart active">
                    <i class="fas fa-shopping-cart"></i>
                    <!-- <span>Giỏ hàng</span> -->
                </div>
                <div class="cart-top-item cart-top-address">
                    <i class="fas fa-map-marker-alt"></i>
                    <!-- <span>Địa chỉ</span> -->
                </div>
                <div class="cart-top-item cart-top-payment">
                    <i class="fas fa-money-check-alt"></i>
                    <!-- <span>Thanh toán</span> -->
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

        @if ($cartItems->isNotEmpty())
            <div class="cart-content row">

                {{-- Bảng sản phẩm --}}
                <div class="cart-content-left">
                    <table>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Tên sản phẩm</th>
                            <th>Màu</th>
                            <th>Size</th>
                            <th>SL</th>
                            <th>Giá</th>
                            <th>Xóa</th>
                        </tr>
                        @foreach ($cartItems as $item)
                            @php
                                $variant = $item->variant;
                                $product = $variant->product;
                                $image   = $product->images->first();
                            @endphp
                            <tr>
                                <td>
                                    <img src="{{ asset('uploads/' . ($image->product_anh ?? '')) }}"
                                         alt="{{ $product->product_name }}"
                                         onerror="this.src='{{ asset('assets/clients/images/no-image.png') }}'">
                                </td>
                                <td><p>{{ $product->product_name }}</p></td>
                                <td>
                                    @if ($variant->color->color_anh ?? null)
                                        <img src="{{ asset('uploads/' . $variant->color->color_anh) }}"
                                             alt="{{ $variant->color->color_ten }}"
                                             style="width:30px;height:30px;border-radius:50%;object-fit:cover;">
                                    @else
                                        <span>{{ $variant->color->color_ten ?? '—' }}</span>
                                    @endif
                                </td>
                                <td><p>{{ $variant->size->product_size ?? '—' }}</p></td>
                                <td><span>{{ $item->quantity }}</span></td>
                                <td><p>{{ number_format($product->product_gia) }}<sup>đ</sup></p></td>
                                <td>
                                    <form action="{{ route('cart.destroy', $item->cart_id) }}"
                                          method="POST" style="display:inline"
                                          onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="cart-remove-btn">
                                            <span>×</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>

                {{-- Tóm tắt đơn hàng --}}
                <div class="cart-content-right">
                    <table>
                        <tr>
                            <th colspan="2"><p>TỔNG TIỀN GIỎ HÀNG</p></th>
                        </tr>
                        <tr>
                            <td>TỔNG SẢN PHẨM</td>
                            <td>{{ number_format($totalQty) }}</td>
                        </tr>
                        <tr>
                            <td>TỔNG TIỀN HÀNG</td>
                            <td><p>{{ number_format($total) }}<sup>đ</sup></p></td>
                        </tr>
                        <tr>
                            <td>THÀNH TIỀN</td>
                            <td><p>{{ number_format($total) }}<sup>đ</sup></p></td>
                        </tr>
                        <tr>
                            <td>TẠM TÍNH</td>
                            <td><p style="font-weight:bold;color:black;">{{ number_format($total) }}<sup>đ</sup></p></td>
                        </tr>
                    </table>

                    <div class="cart-content-right-text">
                        <p>Bạn sẽ được miễn phí ship khi đơn hàng có tổng giá trị trên 2,000,000<sup>đ</sup></p>
                        <br>
                        @if ($total >= 2000000)
                            <p style="color:red;font-weight:bold;">
                                Đơn hàng của bạn đủ điều kiện được <span style="font-size:18px;">Free</span> ship
                            </p>
                        @else
                            @php $remaining = 2000000 - $total; @endphp
                            <p style="color:red;font-weight:bold;">
                                Mua thêm <span style="font-size:18px;">{{ number_format($remaining) }}<sup>đ</sup></span>
                                để được miễn phí SHIP
                            </p>
                        @endif
                    </div>

                    <div class="cart-content-right-button">
                        <button onclick="window.location.href='{{ route('home') }}'">TIẾP TỤC MUA SẮM</button>
                        <a href="#"><button>THANH TOÁN</button></a>
                    </div>

                    @guest
                    <div class="cart-content-right-dangnhap">
                        <p>TÀI KHOẢN Voxfootball</p><br>
                        <p>Hãy <a href="{{ route('login') }}">đăng nhập</a> tài khoản để tích điểm thành viên.</p>
                    </div>
                    @endguest
                </div>

            </div>
        @else
            <div class="cart-empty">
                <i class="fas fa-shopping-cart" style="font-size:4rem;color:#ccc;display:block;margin-bottom:15px;"></i>
                <p>Bạn vẫn chưa thêm sản phẩm nào vào giỏ hàng, Vui lòng chọn sản phẩm nhé!</p>
                <a href="{{ route('home') }}" class="continue-shopping">Tiếp tục mua sắm</a>
            </div>
        @endif
    </div>
</section>
@endsection
