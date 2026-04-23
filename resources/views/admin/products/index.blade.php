@extends('admin.layouts.app')

@section('title', 'Quản lý Sản phẩm')
@section('page-title', 'Quản lý Sản phẩm')

@section('content')
<div class="management-container">

    <div class="management-header">
        <div class="management-header-left">
            <h2>Hệ thống Quản lý Sản phẩm</h2>
            <p>Chọn chức năng bạn muốn quản lý</p>
        </div>
        <div class="action-buttons">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại Dashboard
            </a>
        </div>
    </div>

    <div class="section-nav">
        <div class="nav-grid">
            <a href="{{ route('admin.categories.index') }}" class="nav-card categories">
                <i class="fas fa-list"></i>
                <h3>Danh mục sản phẩm</h3>
                <p>Quản lý các danh mục chính của sản phẩm</p>
            </a>

            <a href="{{ route('admin.subcategories.index') }}" class="nav-card product-types">
                <i class="fas fa-tags"></i>
                <h3>Loại sản phẩm</h3>
                <p>Quản lý các loại sản phẩm theo từng danh mục</p>
            </a>

            <a href="{{ route('admin.products.list') }}" class="nav-card products">
                <i class="fas fa-box"></i>
                <h3>Sản phẩm</h3>
                <p>Thêm, sửa, xóa và quản lý toàn bộ sản phẩm</p>
            </a>

            <a href="#" class="nav-card images">
                <i class="fas fa-images"></i>
                <h3>Ảnh sản phẩm</h3>
                <p>Quản lý hình ảnh chi tiết của sản phẩm</p>
            </a>

            <a href="#" class="nav-card sizes">
                <i class="fas fa-ruler"></i>
                <h3>Size sản phẩm</h3>
                <p>Quản lý các size có sẵn cho từng sản phẩm</p>
            </a>

            <a href="#" class="nav-card colors">
                <i class="fas fa-palette"></i>
                <h3>Màu sắc sản phẩm</h3>
                <p>Quản lý bảng màu và hình ảnh màu sắc</p>
            </a>
        </div>
    </div>

</div>
@endsection
