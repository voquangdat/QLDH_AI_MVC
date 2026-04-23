@extends('admin.layouts.app')

@section('title', 'Quản lý Danh mục')
@section('page-title', 'Quản lý Danh mục')

@section('content')
<div class="management-container">

    <div class="management-header">
        <div class="management-header-left">
            <h2>Danh mục Sản phẩm</h2>
            <p>Thêm, sửa, xóa các danh mục chính của sản phẩm</p>
        </div>
        <div class="action-buttons">
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm Danh mục
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
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
        @if ($categories->isNotEmpty())
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:60px">STT</th>
                        <th>Tên Danh mục</th>
                        <th style="width:140px">Số sản phẩm</th>
                        <th style="width:160px">Ngày tạo</th>
                        <th style="width:140px; text-align:center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $index => $category)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $category->category_name }}</strong></td>
                            <td>
                                <span class="badge badge-info">
                                    {{ $category->products_count }} sản phẩm
                                </span>
                            </td>
                            <td>{{ $category->created_at->format('d/m/Y H:i') }}</td>
                            <td style="text-align:center">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="action-btn btn-edit">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category) }}"
                                      method="POST" style="display:inline"
                                      onsubmit="return confirm('Bạn có chắc muốn xóa danh mục này?')">
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
        @else
            <div class="no-data">
                <i class="fas fa-inbox"></i>
                <h3>Chưa có danh mục nào</h3>
                <p>Hãy thêm danh mục đầu tiên cho sản phẩm của bạn</p>
                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary" style="margin-top:15px">
                    <i class="fas fa-plus"></i> Thêm ngay
                </a>
            </div>
        @endif
    </div>

</div>
@endsection
