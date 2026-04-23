@extends('admin.layouts.app')

@section('title', 'Sửa Loại Sản phẩm')
@section('page-title', 'Sửa Loại Sản phẩm')

@section('content')
<div class="form-container">

    <div class="form-header">
        <h2>Sửa Loại Sản phẩm</h2>
        <p>Cập nhật thông tin loại sản phẩm</p>
    </div>

    <div class="category-info">
        <h4><i class="fas fa-info-circle"></i> Thông tin hiện tại</h4>
        <div class="info-item">
            <span class="info-label">ID:</span>
            <span>#{{ $subcategory->subcategory_id }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Danh mục:</span>
            <span>{{ $subcategory->category->category_name ?? '—' }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Tên hiện tại:</span>
            <span>{{ $subcategory->subcategory_name }}</span>
        </div>
    </div>

    <div class="form-wrapper">
        <form method="POST" action="{{ route('admin.subcategories.update', $subcategory) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="category_id">Danh mục <span class="required">*</span></label>
                <select id="category_id"
                        name="category_id"
                        class="form-control @error('category_id') is-invalid @enderror">
                    <option value="">-- Chọn danh mục --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->category_id }}"
                            {{ old('category_id', $subcategory->category_id) == $category->category_id ? 'selected' : '' }}>
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
                       value="{{ old('subcategory_name', $subcategory->subcategory_name) }}"
                       autofocus>
                @error('subcategory_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Cập nhật
                </button>
                <a href="{{ route('admin.subcategories.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Hủy bỏ
                </a>
            </div>
        </form>

        <form action="{{ route('admin.subcategories.destroy', $subcategory) }}" method="POST"
              style="margin-top: 15px"
              onsubmit="return confirm('Bạn có chắc muốn xóa loại sản phẩm này? Hành động không thể hoàn tác!')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Xóa loại sản phẩm
            </button>
        </form>
    </div>

</div>
@endsection
