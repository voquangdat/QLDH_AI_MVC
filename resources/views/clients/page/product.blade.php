@extends('layouts.app')

@section('title', $product->product_name)

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/clients/css/index-effects.css') }}">
@endpush

@section('content')
{{-- @include('clients.partials.leftside') --}}

<section class="product">
    <div class="container">
        <div class="product-top row">
            <p><a href="{{ route('home') }}">Trang chủ</a></p>
            <span>&#8594;</span>
            <p>{{ $product->category?->category_name ?? '—' }}</p>
            <span>&#8594;</span>
            <p>{{ $product->subcategory?->subcategory_name ?? '—' }}</p>
            <span>&#8594;</span>
            <p>{{ $product->product_name }}</p>
        </div>

        <div class="product-content row">
            <div class="product-content-left row">
                <div class="product-content-left-big-img">
                    <img class="sanpham_anh"
                         src="{{ asset('uploads/' . ($product->images->first()?->product_anh ?? '')) }}"
                         alt="{{ $product->product_name }}">
                </div>
                <div class="product-content-left-small-img">
                    @foreach ($product->images as $image)
                    <img src="{{ asset('uploads/' . $image->product_anh) }}" alt="{{ $product->product_name }}">
                    @endforeach
                </div>
            </div>

            <div class="product-content-right">
                <div class="product-content-right-product-name">
                    <input class="sanpham_id" type="hidden" value="{{ $product->product_id }}">
                    <h1 class="sanpham_tieude">{{ $product->product_name }}</h1>
                    <p>{{ $product->product_code }}</p>
                </div>

                <div class="product-content-right-product-price">
                    <p><span>{{ number_format($product->product_gia, 0, ',', '.') }}</span><sup>đ</sup></p>
                    <input class="sanpham_gia" type="hidden" value="{{ $product->product_gia }}">
                </div>

                @php $firstVariant = $product->variants->first(); @endphp
                <div class="product-content-right-product-color">
                    <p>
                        <span style="font-weight: bold;">Màu sắc</span>:
                        {{ $firstVariant?->color?->color_ten ?? '—' }}
                        <span style="color: red;">*</span>
                    </p>
                    <div class="product-content-right-product-color-IMG">
                        @if ($firstVariant?->color?->color_anh)
                            <img class="color_anh"
                                 src="{{ asset('uploads/' . $firstVariant->color->color_anh) }}"
                                 alt="{{ $firstVariant->color->color_ten }}">
                        @endif
                    </div>
                </div>

                <div class="product-content-right-product-size">
                    <p style="font-weight: bold">Size:</p>
                    <div class="size">
                        @if ($product->variants->count())
                            @foreach ($product->variants as $variant)
                            <div class="size-item">
                                <input class="size-item-input" name="size-item" type="radio"
                                    value="{{ $variant->size?->product_size }}"
                                    data-bienthe-id="{{ $variant->variant_id }}"
                                    data-stock="{{ $variant->inventory?->soluong_co_the_ban ?? 0 }}">
                                <span>{{ $variant->size?->product_size }}</span>
                            </div>
                            @endforeach
                        @else
                        <p>Chưa thiết lập biến thể cho sản phẩm này.</p>
                        @endif
                    </div>

                    <div class="quantity">
                        <p style="font-weight: bold">Số lượng:</p>
                        <input class="quantitys" type="number" min="1" value="1">
                    </div>
                    <p class="size-alert" style="color: red;"></p>
                    <p class="stock-hint" style="margin-top:6px;color:#555;"></p>
                </div>

                <div class="product-content-right-product-button">
                    <button class="add-cart-btn">
                        <i class="fas fa-shopping-cart"></i>
                        <p>MUA NGAY</p>
                    </button>
                    <button>
                        <p>THÊM VÀO GIỎ HÀNG</p>
                    </button>
                </div>

                <div class="product-content-right-product-icon">
                    <div class="product-content-right-product-icon-item">
                        <i class="fas fa-phone-alt"></i>
                        <p>Hotline</p>
                    </div>
                    <div class="product-content-right-product-icon-item">
                        <i class="far fa-comments"></i>
                        <p>Chat</p>
                    </div>
                    <div class="product-content-right-product-icon-item">
                        <i class="far fa-envelope"></i>
                        <p>Mail</p>
                    </div>
                </div>

                <div class="product-content-right-product-QR">
                    <img src="{{ asset('assets/clients/image/qrcode2.png') }}" alt="">
                </div>

                <div class="product-content-right-bottom">
                    <div class="product-content-right-bottom-top">&#8744;</div>
                    <div class="product-content-right-bottom-content-big">
                        <div class="product-content-right-bottom-title">
                            <div class="product-content-right-bottom-title-item chitiet">
                                <p>Chi tiết</p>
                            </div>
                            <div class="product-content-right-bottom-title-item baoquan">
                                <p>Bảo quản</p>
                            </div>
                            <div class="product-content-right-bottom-title-item">
                                <p>Tham khảo size</p>
                            </div>
                        </div>
                        <div class="product-content-right-bottom-content">
                            <div class="product-content-right-bottom-content-chitiet">
                                {!! $product->description !!}
                            </div>
                            <div class="product-content-right-bottom-content-baoquan">
                                {!! $product->care_note !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="product-related">
    <div class="container">
        <div class="product-related-title">
            <p>SẢN PHẨM LIÊN QUAN</p>
        </div>
        <div class="row justify-between">
            @forelse ($relatedProducts as $related)
            <div class="product-related-item">
                <a href="{{ route('product.show', $related->product_id) }}">
                    <img src="{{ asset('uploads/' . ($related->images->first()?->product_anh ?? '')) }}"
                         alt="{{ $related->product_name }}">
                </a>
                <a href="{{ route('product.show', $related->product_id) }}">
                    <h1>{{ $related->product_name }}</h1>
                </a>
                <p>{{ number_format($related->product_gia, 0, ',', '.') }}<sup>đ</sup></p>
                <span>_new_</span>
            </div>
            @empty
            <p>Không có sản phẩm liên quan.</p>
            @endforelse
        </div>
    </div>
</section>

<script>
$(function() {
    var selectedVariantId = null;

    $(document).on('change', '.size-item-input', function() {
        selectedVariantId = $(this).data('bienthe-id');
        var stock = $(this).data('stock');
        $('.size-alert').text('');
        $('.stock-hint').text('Còn lại: ' + (typeof stock === 'number' ? stock : 0));
    });

    function showCartToast(message, isSuccess) {
        var existing = $('#cart-toast');
        if (existing.length) existing.remove();

        var bgColor = isSuccess ? '#2ecc71' : '#e74c3c';
        var icon    = isSuccess ? 'fa-check-circle' : 'fa-exclamation-circle';

        $('body').append(
            '<div id="cart-toast" style="' +
                'position:fixed;top:80px;right:20px;z-index:99999;' +
                'background:' + bgColor + ';color:#fff;' +
                'padding:12px 18px;border-radius:6px;' +
                'font-size:13px;font-family:sans-serif;' +
                'display:flex;align-items:center;gap:8px;' +
                'box-shadow:0 4px 12px rgba(0,0,0,0.2);' +
                'animation:slideInRight .3s ease;' +
            '">' +
                '<i class="fas ' + icon + '"></i>' +
                '<span>' + message + '</span>' +
            '</div>'
        );

        setTimeout(function() {
            $('#cart-toast').fadeOut(300, function() { $(this).remove(); });
        }, 2500);
    }

    function addToCart(variantId, quantity, callback) {
        $.ajax({
            url: '{{ route('cart.store') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                variant_id: variantId,
                quantity: quantity,
            },
            success: function(res) {
                if (res.success) {
                    // Hiện toast ngay lập tức, không cần chờ mini-cart refresh
                    showCartToast('Đã thêm vào giỏ hàng!', true);
                    // Refresh mini-cart sau đó
                    refreshMiniCart(function() {
                        openMiniCartBriefly();
                    });
                    callback(true, res);
                } else {
                    showCartToast(res.message || 'Có lỗi xảy ra!', false);
                    callback(false, res);
                }
            },
            error: function(xhr) {
                var res = xhr.responseJSON || {};
                showCartToast(res.message || 'Có lỗi xảy ra!', false);
                callback(false, res);
            }
        });
    }

    function refreshMiniCart(done) {
        $.ajax({
            url: '{{ route('cart.mini') }}',
            method: 'GET',
            success: function(res) {
                if (res.html) {
                    $('#mini-cart-wrapper').replaceWith(res.html);
                }
                if (typeof done === 'function') done();
            },
            error: function() {
                // Mini-cart refresh thất bại nhưng không ảnh hưởng toast
                if (typeof done === 'function') done();
            }
        });
    }

    function openMiniCartBriefly() {
        var $dropdown = $('#mini-cart-dropdown');
        if (!$dropdown.length) return;
        $dropdown.addClass('open');
        setTimeout(function() { $dropdown.removeClass('open'); }, 2500);
    }

    // Nút MUA NGAY
    $('.add-cart-btn').on('click', function() {
        if (!selectedVariantId) {
            $('.size-alert').text('Vui lòng chọn size*');
            return;
        }
        var quantity = Math.max(1, parseInt($('.quantitys').val() || '1', 10));
        var $btn = $(this);
        $btn.prop('disabled', true).find('p').text('Đang xử lý...');

        addToCart(selectedVariantId, quantity, function(success, res) {
            if (success) {
                window.location.href = '{{ route('cart.index') }}';
            } else {
                $('.size-alert').text(res.message || 'Có lỗi xảy ra!');
                $btn.prop('disabled', false).find('p').text('MUA NGAY');
            }
        });
    });

    // Nút THÊM VÀO GIỎ HÀNG
    $('.add-cart-btn').closest('.product-content-right-product-button')
        .find('button:last').on('click', function() {
        if (!selectedVariantId) {
            $('.size-alert').text('Vui lòng chọn size*');
            return;
        }
        var quantity = Math.max(1, parseInt($('.quantitys').val() || '1', 10));
        var $btn = $(this);
        $btn.prop('disabled', true).find('p').text('Đang thêm...');

        addToCart(selectedVariantId, quantity, function(success, res) {
            if (success) {
                $btn.find('p').text('Đã thêm ✓');
                setTimeout(function() {
                    $btn.prop('disabled', false).find('p').text('THÊM VÀO GIỎ HÀNG');
                }, 2000);
            } else {
                $('.size-alert').text(res.message || 'Có lỗi xảy ra!');
                $btn.prop('disabled', false).find('p').text('THÊM VÀO GIỎ HÀNG');
            }
        });
    });
});
</script>

@endsection
