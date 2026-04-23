@extends('admin.layouts.app')

@section('title', 'Quản lý Loại Sản phẩm')
@section('page-title', 'Quản lý Loại Sản phẩm')

@section('content')
<div class="management-container">

    <div class="management-header">
        <div class="management-header-left">
            <h2>Loại Sản phẩm</h2>
            <p>Thêm, sửa, xóa các loại sản phẩm theo từng danh mục</p>
        </div>
        <div class="action-buttons">
            <a href="{{ route('admin.subcategories.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm Loại SP
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
        @if ($subcategories->isNotEmpty())
            @foreach ($subcategories->groupBy('category_id') as $categoryId => $items)
                <div style="margin-bottom: 30px;">
                    <div style="background:#f0f4ff; border-left:4px solid #4f46e5; padding:10px 16px; margin-bottom:8px; border-radius:4px; font-weight:600; color:#4f46e5;">
                        <i class="fas fa-folder"></i>
                        {{ $items->first()->category->category_name ?? '—' }}
                        <span style="font-weight:400; color:#888; font-size:13px; margin-left:8px">({{ $items->count() }} loại)</span>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width:60px">STT</th>
                                <th>Tên Loại Sản phẩm</th>
                                <th style="width:150px">Số sản phẩm</th>
                                <th style="width:140px; text-align:center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $index => $subcategory)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $subcategory->subcategory_name }}</strong></td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ $subcategory->products_count }} sản phẩm
                                        </span>
                                    </td>
                                    <td style="text-align:center">
                                        <a href="{{ route('admin.subcategories.edit', $subcategory) }}" class="action-btn btn-edit">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                        <form action="{{ route('admin.subcategories.destroy', $subcategory) }}"
                                              method="POST" style="display:inline"
                                              onsubmit="return confirm('Bạn có chắc muốn xóa loại sản phẩm này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn btn-delete">
                                                <i class="fas fa-trash"></i> Xóa
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        @else
            <div class="no-data">
                <i class="fas fa-inbox"></i>
                <h3>Chưa có loại sản phẩm nào</h3>
                <p>Hãy thêm loại sản phẩm đầu tiên</p>
                <a href="{{ route('admin.subcategories.create') }}" class="btn btn-primary" style="margin-top:15px">
                    <i class="fas fa-plus"></i> Thêm ngay
                </a>
            </div>
        @endif
    </div>

</div>
@endsection
