@extends('admin.layouts.app')

@section('title', 'Danh sách Sản phẩm')
@section('page-title', 'Danh sách Sản phẩm')

@section('content')
<div class="management-container">

    <div class="management-header">
        <div class="management-header-left">
            <h2>Danh sách Sản phẩm</h2>
            <p>Thêm, sửa, xóa và quản lý toàn bộ sản phẩm</p>
        </div>
        <div class="action-buttons">
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm Sản phẩm
            </a>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <div class="data-table">
        @if ($products->isNotEmpty())
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:50px">STT</th>
                        <th style="width:70px">Ảnh</th>
                        <th>Tên Sản phẩm</th>
                        <th style="width:150px">Danh mục</th>
                        <th style="width:160px">Loại SP</th>
                        <th style="width:120px">Giá</th>
                        <th style="width:80px; text-align:center">Hot</th>
                        <th style="width:130px; text-align:center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $index => $product)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                @if ($product->images->isNotEmpty())
                                    <img src="{{ asset('uploads/' . $product->images->first()->product_anh) }}"
                                         alt="{{ $product->product_name }}"
                                         class="product-img">
                                @else
                                    <div class="product-img-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $product->product_name }}</strong>
                                {{-- @if ($product->description)
                                    <br><small style="color:#888">{{ Str::limit($product->description, 60) }}</small>
                                @endif --}}
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    {{ $product->category?->category_name ?? '—' }}
                                </span>
                            </td>
                            <td>{{ $product->subcategory?->subcategory_name ?? '—' }}</td>
                            <td>{{ number_format($product->product_gia, 0, ',', '.') }}đ</td>
                            <td style="text-align:center">
                                @if ($product->product_hot)
                                    <span class="badge badge-hot"><i class="fas fa-fire"></i> Hot</span>
                                @else
                                    <span class="badge badge-secondary">—</span>
                                @endif
                            </td>
                            <td style="text-align:center">
                                <a href="{{ route('admin.products.edit', $product) }}" class="action-btn btn-edit">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>
                                <form action="{{ route('admin.products.destroy', $product) }}"
                                      method="POST" style="display:inline"
                                      onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn btn-delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                <i class="fas fa-box-open"></i>
                <h3>Chưa có sản phẩm nào</h3>
                <p>Hãy thêm sản phẩm đầu tiên cho cửa hàng của bạn</p>
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary" style="margin-top:15px">
                    <i class="fas fa-plus"></i> Thêm ngay
                </a>
            </div>
        @endif
    </div>

</div>
@endsection
