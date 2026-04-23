@extends('admin.layouts.app')

@section('title', 'Thêm Loại Sản phẩm')
@section('page-title', 'Thêm Loại Sản phẩm')

@section('content')
<div class="form-container">

    <div class="form-header">
        <h2>Thêm Loại Sản phẩm</h2>
        <p>Tạo loại sản phẩm mới thuộc một danh mục</p>
    </div>

    <div class="form-wrapper">
        <div class="form-help">
            <i class="fas fa-info-circle"></i>
            <strong>Hướng dẫn:</strong> Loại sản phẩm là phân loại con trong một danh mục (ví dụ: Ngoại Hạng Anh, La Liga trong danh mục Áo CLB).
        </div>

        <form method="POST" action="{{ route('admin.subcategories.store') }}">
            @csrf

            <div class="form-group">
                <label for="category_id">Danh mục <span class="required">*</span></label>
                <select id="category_id"
                        name="category_id"
                        class="form-control @error('category_id') is-invalid @enderror">
                    <option value="">-- Chọn danh mục --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->category_id }}"
                            {{ old('category_id') == $category->category_id ? 'selected' : '' }}>
                            {{ $category->category_name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="subcategory_name">Tên Loại Sản phẩm <span class="required">*</span></label>
                <input type="text"
                       id="subcategory_name"
                       name="subcategory_name"
                       class="form-control @error('subcategory_name') is-invalid @enderror"
                       placeholder="Nhập tên loại sản phẩm (ví dụ: Ngoại Hạng Anh, Việt Nam...)"
                       value="{{ old('subcategory_name') }}"
                       autofocus>
                @error('subcategory_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Lưu
                </button>
                <a href="{{ route('admin.subcategories.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Hủy bỏ
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
