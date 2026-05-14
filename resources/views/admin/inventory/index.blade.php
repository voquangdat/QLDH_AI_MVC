@extends('admin.layouts.app')
@section('title', 'Quản lý Tồn kho')
@section('page-title', 'Quản lý Tồn kho')

@section('content')
<div class="product-management-container">

    <a href="{{ route('admin.dashboard') }}" class="back-to-dashboard">
        <i class="fas fa-arrow-left"></i> Quay lại Dashboard
    </a>

    <div class="section-nav">
        <h2>Hệ thống Quản lý Tồn kho VoxFootball</h2>
        <p style="text-align: center; color: #666; margin-bottom: 30px;">
            Chọn chức năng bạn muốn quản lý
        </p>

        <div class="nav-grid">
            <a href="{{ route('admin.inventory.list') }}" class="nav-card categories">
                <i class="fas fa-list"></i>
                <h3>Danh sách tồn kho</h3>
                <p>Quản lý các danh sách của tồn kho</p>
            </a>

            <a href="{{ route('admin.inventory.detail') }}" class="nav-card product-types">
                <i class="fas fa-tags"></i>
                <h3>Chi tiết sản phẩm tồn kho</h3>
                <p>Quản lý sản phẩm theo từng biến thể</p>
            </a>
        </div> 
    </div>

</div>
@endsection
