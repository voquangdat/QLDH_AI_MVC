@extends('admin.layouts.app')

@section('title', 'Thêm Danh mục')
@section('page-title', 'Thêm Danh mục')

@section('content')
<div class="form-container">

    <div class="form-header">
        <h2>Thêm Danh mục Sản phẩm</h2>
        <p>Tạo danh mục mới cho hệ thống sản phẩm</p>
    </div>

    <div class="form-wrapper">
        <div class="form-help">
            <i class="fas fa-info-circle"></i>
            <strong>Hướng dẫn:</strong> Danh mục là nhóm chính phân loại sản phẩm (ví dụ: ÁO CLB, ÁO ĐỘI TUYỂN QUỐC GIA).
        </div>

        <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf

            <div class="form-group">
                <label for="category_name">Tên Danh mục <span class="required">*</span></label>
                <input type="text"
                       id="category_name"
                       name="category_name"
                       class="form-control @error('category_name') is-invalid @enderror"
                       placeholder="Nhập tên danh mục (ví dụ: ÁO CLB, ÁO ĐỘI TUYỂN...)"
                       value="{{ old('category_name') }}"
                       autofocus>
                @error('category_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Lưu Danh mục
                </button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Hủy bỏ
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
