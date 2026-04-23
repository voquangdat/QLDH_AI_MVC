@extends('layouts.app')

@section('title', $currentSubcategory?->subcategory_name ?? $currentCategory?->category_name ?? 'Danh mục')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/clients/css/index-effects.css') }}">
@endpush

@section('content')
@include('clients.partials.leftside')

            <div class="cartegory-right">
                <div class="cartegory-right-top row">
                    <div class="cartegory-right-top-item">
                        <p>{{ $currentSubcategory?->subcategory_name ?? $currentCategory?->category_name ?? 'Hiện tại chưa có loại sản phẩm nào' }}</p>
                    </div>
                    <div class="cartegory-right-top-item">
                        <button><span>Bộ lọc</span><i class="fas fa-sort-down"></i></button>
                    </div>
                    <div class="cartegory-right-top-item">
                        <form method="GET" action="{{ route('category.index') }}">
                            @if(request('subcategory_id'))
                                <input type="hidden" name="subcategory_id" value="{{ request('subcategory_id') }}">
                            @elseif(request('category_id'))
                                <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                            @endif
                            <select name="sort" onchange="this.form.submit()">
                                <option value="">Sắp xếp</option>
                                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Giá cao đến thấp</option>
                                <option value="price_low"  {{ request('sort') == 'price_low'  ? 'selected' : '' }}>Giá thấp đến cao</option>
                            </select>
                        </form>
                    </div>
                </div>

                <div class="cartegory-right-content row">
                    @forelse ($products as $product)
                    <div class="cartegory-right-content-item">
                        <a href="{{ route('product.show', $product->product_id) }}">
                            <img src="{{ asset('uploads/' . ($product->images->first()->product_anh ?? '')) }}" alt="{{ $product->product_name }}">
                        </a>
                        <a href="{{ route('product.show', $product->product_id) }}">
                            <h1>{{ $product->product_name }}</h1>
                        </a>
                        <p>{{ number_format($product->product_gia, 0, ',', '.') }}<sup>đ</sup></p>
                        <span>_new_</span>
                    </div>
                    @empty
                        <p>Không có sản phẩm nào trong danh mục này.</p>
                    @endforelse
                </div>

                <div class="cartegory-right-bottom row">
                    <div class="cartegory-right-bottom-items">
                        <p>Hiện thị {{ $products->count() }} sản phẩm</p>
                    </div>
                    <div class="cartegory-right-bottom-items">
                        <p><span>&#171;</span> 1 2 3 4 5 <span>&#187;</span> Trang cuối</p>
                    </div>
                </div>
            </div>  <!-- End cartegory-right -->
            </div>  <!-- End row -->
        </div>  <!-- End container -->
    </section>  <!-- End section.cartegory -->

@endsection
