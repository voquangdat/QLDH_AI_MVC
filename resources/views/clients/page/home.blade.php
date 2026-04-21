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
            <div class="product-card" data-product-id="{{ $product->product_id }}">
                <div class="product-image-container">
                    <div class="product-images">
                        @if($product->images->isNotEmpty())
                        <img src="{{ asset($product->images->first()->product_anh) }}"
                             alt="{{ $product->product_name }}" class="main-image">
                        @if($product->images->count() > 1)
                        <img src="{{ asset($product->images->get(1)->product_anh) }}"
                             alt="{{ $product->product_name }}" class="hover-image">
                        @endif
                        @else
                        <img src="{{ asset('assets/clients/images/no-image.png') }}"
                             alt="{{ $product->product_name }}" class="main-image">
                        @endif
                    </div>
                </div>

                <div class="product-info">
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
                        <button class="btn btn-cart" data-product-id="{{ $product->product_id }}">
                            <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                        </button>
                        <a href="{{ route('product.show', $product->product_id) }}" class="btn btn-buy">
                            <i class="fas fa-bolt"></i> Mua ngay
                        </a>
                    </div>
                </div>
            </div>
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
<script src="{{ asset('assets/clients/js/home.js') }}"></script>
@endpush
