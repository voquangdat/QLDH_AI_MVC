@extends('admin.layouts.app')

@section('title', 'Sửa Danh mục')
@section('page-title', 'Sửa Danh mục')

@section('content')
<div class="form-container">

    <div class="form-header">
        <h2>Sửa Danh mục Sản phẩm</h2>
        <p>Cập nhật thông tin danh mục sản phẩm</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="category-info">
        <h4><i class="fas fa-info-circle"></i> Thông tin hiện tại</h4>
        <div class="info-item">
            <span class="info-label">ID:</span>
            <span>#{{ $category->category_id }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Tên hiện tại:</span>
            <span>{{ $category->category_name }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Ngày tạo:</span>
            <span><i class="fas fa-calendar"></i> {{ $category->created_at->format('d/m/Y H:i') }}</span>
        </div>
    </div>

    <div class="form-wrapper">
        <div class="form-help">
            <i class="fas fa-info-circle"></i>
            <strong>Lưu ý:</strong> Thay đổi tên danh mục sẽ ảnh hưởng đến tất cả sản phẩm thuộc danh mục này.
        </div>

        <form method="POST" action="{{ route('admin.categories.update', $category) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="category_name">Tên Danh mục <span class="required">*</span></label>
                <input type="text"
                       id="category_name"
                       name="category_name"
                       class="form-control @error('category_name') is-invalid @enderror"
                       value="{{ old('category_name', $category->category_name) }}"
                       autofocus>
                @error('category_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Cập nhật
                </button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Hủy bỏ
                </a>
            </div>
        </form>

        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
              style="margin-top: 15px"
              onsubmit="return confirm('Bạn có chắc muốn xóa danh mục này? Hành động không thể hoàn tác!')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Xóa danh mục
            </button>
        </form>
    </div>

</div>
@endsection
