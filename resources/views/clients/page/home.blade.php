@extends('layouts.app')

@section('title', 'Trang chủ')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/clients/css/index-effects.css') }}">
@endpush

@section('content')
@include('clients.partials.slider')

<section class="product-display" id="product">
    <div class="container">
        <div class="section-header">
            <h1><i class="fas fa-fire"></i> Sản Phẩm Bán Chạy</h1>
            <p>Những sản phẩm được yêu thích nhất tại VoxFootball</p>
        </div>

        <div class="product-grid">
            @forelse($featuredProducts as $product)
            @php
                $avgRating = round($product->reviews->avg('rating') ?? 0, 1);

                // Group variants by size, pick best available per size
                $sizesWithVariants = collect();
                foreach ($product->variants->groupBy('product_size_id') as $variants) {
                    $best = $variants->first(fn($v) => $v->inventory && $v->inventory->soluong_co_the_ban > 0)
                        ?? $variants->first();
                    if ($best && $best->size) {
                        $sizesWithVariants->push([
                            'label'      => $best->size->product_size,
                            'variant_id' => $best->variant_id,
                            'in_stock'   => $best->inventory && $best->inventory->soluong_co_the_ban > 0,
                        ]);
                    }
                }

                $firstVariant = $product->variants->first(fn($v) => $v->inventory && $v->inventory->soluong_co_the_ban > 0)
                    ?? $product->variants->first();
            @endphp
            <div class="product-card" data-product-id="{{ $product->product_id }}">
                <div class="product-image-container">

                    {{-- Badge + action buttons --}}
                    <div class="product-badge">
                        @if ($product->product_hot)
                            <span class="discount-badge">HOT</span>
                        @endif
                        <div class="product-actions">
                            <button class="action-btn wishlist-btn" title="Yêu thích">
                                <i class="far fa-heart"></i>
                            </button>
                            <a class="action-btn quick-view-btn"
                               href="{{ route('product.show', $product->product_id) }}"
                               title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>

                    {{-- Hình ảnh --}}
                    <div class="product-images">
                        @if($product->images->isNotEmpty())
                            <img src="{{ asset('uploads/' . $product->images->first()->product_anh) }}"
                                 alt="{{ $product->product_name }}" class="main-image">
                            @if($product->images->count() > 1)
                                <img src="{{ asset('uploads/' . $product->images->get(1)->product_anh) }}"
                                     alt="{{ $product->product_name }}" class="hover-image">
                            @endif
                        @else
                            <img src="{{ asset('assets/clients/images/no-image.png') }}"
                                 alt="{{ $product->product_name }}" class="main-image">
                        @endif
                    </div>

                    {{-- Size options --}}
                    @if ($sizesWithVariants->isNotEmpty())
                    <div class="size-options">
                        <span class="size-label">Size:</span>
                        <div class="size-list">
                            @foreach ($sizesWithVariants as $sv)
                                <button class="size-btn {{ $sv['variant_id'] == $firstVariant?->variant_id ? 'active' : '' }} {{ !$sv['in_stock'] ? 'out-of-stock' : '' }}"
                                        data-variant-id="{{ $sv['variant_id'] }}">
                                    {{ $sv['label'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>{{-- /.product-image-container --}}

                <div class="product-info">

                    {{-- Rating --}}
                    @if ($avgRating > 0)
                    <div class="product-rating">
                        <div class="stars">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= floor($avgRating))
                                    <i class="fas fa-star"></i>
                                @elseif ($i - $avgRating < 1)
                                    <i class="fas fa-star-half-alt"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                        <span class="rating-count">({{ $avgRating }})</span>
                    </div>
                    @endif

                    <h3 class="product-name">
                        <a href="{{ route('product.show', $product->product_id) }}">
                            {{ $product->product_name }}
                        </a>
                    </h3>

                    <div class="product-price">
                        <span class="current-price">
                            {{ number_format($product->product_gia, 0, ',', '.') }}đ
                        </span>
                    </div>

                    <div class="product-buttons">
                        <button class="btn btn-cart"
                                data-product-id="{{ $product->product_id }}"
                                data-variant-id="{{ $firstVariant?->variant_id ?? '' }}"
                                data-product-url="{{ route('product.show', $product->product_id) }}">
                            <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                        </button>
                        <a href="{{ route('product.show', $product->product_id) }}" class="btn btn-buy">
                            <i class="fas fa-bolt"></i> Mua ngay
                        </a>
                    </div>

                </div>{{-- /.product-info --}}
            </div>{{-- /.product-card --}}
            @empty
            <p class="no-products">Không có sản phẩm nào.</p>
            @endforelse
        </div>

        <div class="section-footer">
            <a href="{{ route('category.index') }}" class="view-all-btn">
                <i class="fas fa-th-large"></i> Xem tất cả sản phẩm
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
const CART_STORE_URL = "{{ route('cart.store') }}";
const CART_MINI_URL  = "{{ route('cart.mini') }}";
</script>
<script src="{{ asset('assets/clients/js/home.js') }}"></script>
@endpush
