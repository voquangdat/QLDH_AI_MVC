@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa Sản phẩm')
@section('page-title', 'Chỉnh sửa Sản phẩm')

@section('content')
<div class="form-container" style="max-width:960px">

    <div class="form-header">
        <h2>Chỉnh sửa: {{ $product->product_name }}</h2>
        <p>Mã sản phẩm: <strong style="font-family:monospace">{{ $product->product_code }}</strong></p>
    </div>

    @if ($errors->any())
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <ul style="margin:0; padding-left:18px">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="form-wrapper">
        <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Thông tin cơ bản --}}
            <div class="section-title"><i class="fas fa-info-circle"></i> Thông tin cơ bản</div>
            <div class="form-row">
                <div class="form-group">
                    <label for="product_name">Tên sản phẩm <span class="required">*</span></label>
                    <input type="text" id="product_name" name="product_name"
                           class="form-control @error('product_name') is-invalid @enderror"
                           value="{{ old('product_name', $product->product_name) }}" autofocus>
                    @error('product_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="product_code">Mã sản phẩm <span class="required">*</span></label>
                    <input type="text" id="product_code" name="product_code"
                           class="form-control @error('product_code') is-invalid @enderror"
                           value="{{ old('product_code', $product->product_code) }}"
                           style="text-transform:uppercase; font-family:monospace; letter-spacing:1px">
                    @error('product_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Phân loại --}}
            <div class="section-title"><i class="fas fa-tags"></i> Phân loại</div>
            <div class="form-row">
                <div class="form-group">
                    <label for="category_id">Danh mục <span class="required">*</span></label>
                    <select id="category_id" name="category_id"
                            class="form-control @error('category_id') is-invalid @enderror">
                        <option value="">-- Chọn danh mục --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->category_id }}"
                                {{ old('category_id', $product->category_id) == $cat->category_id ? 'selected' : '' }}>
                                {{ $cat->category_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="subcategory_id">Loại sản phẩm <span class="required">*</span></label>
                    <select id="subcategory_id" name="subcategory_id"
                            class="form-control @error('subcategory_id') is-invalid @enderror">
                        <option value="">-- Chọn danh mục trước --</option>
                    </select>
                    @error('subcategory_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Màu sắc & Size --}}
            <div class="section-title"><i class="fas fa-palette"></i> Màu sắc & Size</div>
            <div class="form-row">
                <div class="form-group">
                    <label for="color_id">Màu sắc <span class="required">*</span></label>
                    @php $currentColorId = $product->variants->first()?->color_id; @endphp
                    <select id="color_id" name="color_id"
                            class="form-control @error('color_id') is-invalid @enderror">
                        <option value="">-- Chọn màu --</option>
                        @foreach ($colors as $color)
                            <option value="{{ $color->color_id }}"
                                {{ old('color_id', $currentColorId) == $color->color_id ? 'selected' : '' }}>
                                {{ $color->color_ten }}
                            </option>
                        @endforeach
                    </select>
                    @error('color_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label>Size sản phẩm <span class="required">*</span></label>
                @error('size_ids')
                    <div class="invalid-feedback" style="display:block; margin-bottom:8px">{{ $message }}</div>
                @enderror
                @php
                    $currentSizeIds = old('size_ids', $product->variants->pluck('product_size_id')->toArray());
                @endphp
                <div class="size-grid">
                    @foreach ($sizes as $size)
                        <label class="size-item">
                            <input type="checkbox" name="size_ids[]" value="{{ $size->product_size_id }}"
                                   {{ in_array($size->product_size_id, $currentSizeIds) ? 'checked' : '' }}>
                            {{ $size->product_size }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Giá --}}
            <div class="section-title"><i class="fas fa-tag"></i> Giá bán</div>
            <div class="form-row">
                <div class="form-group">
                    <label for="product_gia">Giá bán (VNĐ) <span class="required">*</span></label>
                    <input type="number" id="product_gia" name="product_gia" min="0"
                           class="form-control @error('product_gia') is-invalid @enderror"
                           value="{{ old('product_gia', $product->product_gia) }}">
                    @error('product_gia')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Mô tả --}}
            <div class="section-title"><i class="fas fa-align-left"></i> Mô tả</div>
            <div class="form-group">
                <label for="description">Mô tả chi tiết</label>
                <textarea id="description" name="description" rows="4"
                          class="form-control @error('description') is-invalid @enderror"
                          placeholder="Mô tả chi tiết về sản phẩm...">{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="care_note">Hướng dẫn bảo quản</label>
                <textarea id="care_note" name="care_note" rows="3"
                          class="form-control @error('care_note') is-invalid @enderror"
                          placeholder="Hướng dẫn giặt, ủi, bảo quản...">{{ old('care_note', $product->care_note) }}</textarea>
                @error('care_note')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Hình ảnh hiện tại --}}
            <div class="section-title"><i class="fas fa-images"></i> Hình ảnh</div>

            @if ($product->images->isNotEmpty())
                <div class="form-group">
                    <label>Ảnh hiện tại <small style="color:#888; font-weight:400">(click X để xóa)</small></label>
                    <div style="display:flex; flex-wrap:wrap; gap:12px; margin-top:8px" id="existingImages">
                        @foreach ($product->images as $i => $img)
                            <div style="position:relative; text-align:center" id="imgWrapper_{{ $img->product_anh_id }}">
                                <img src="{{ asset('uploads/' . $img->product_anh) }}"
                                     style="width:100px; height:100px; object-fit:cover; border-radius:8px;
                                            border:2px solid {{ $i === 0 ? '#667eea' : ($i === 1 ? '#28a745' : '#dee2e6') }}">
                                <div style="font-size:11px; color:#888; margin-top:3px">
                                    @if ($i === 0) <span style="color:#667eea">Ảnh chính</span>
                                    @elseif ($i === 1) <span style="color:#28a745">Ảnh hover</span>
                                    @else Ảnh {{ $i + 1 }}
                                    @endif
                                </div>
                                <button type="button" onclick="deleteImage({{ $img->product_anh_id }})"
                                        style="position:absolute; top:-6px; right:-6px; width:22px; height:22px;
                                               background:#dc3545; color:#fff; border:none; border-radius:50%;
                                               cursor:pointer; font-size:13px; display:flex;
                                               align-items:center; justify-content:center">
                                    &times;
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Upload ảnh mới --}}
            <div class="form-row">
                <div class="form-group">
                    <label for="main_image">
                        Thay ảnh chính
                        <small style="color:#888; font-weight:400"> (để trống nếu không đổi)</small>
                    </label>
                    <div class="img-upload-box" id="mainBox"
                         onclick="document.getElementById('main_image').click()">
                        <div style="text-align:center; padding:10px; color:#999">
                            <i class="fas fa-cloud-upload-alt" style="font-size:2rem; display:block; margin-bottom:6px"></i>
                            <small>Click hoặc kéo thả ảnh</small>
                        </div>
                        <input type="file" id="main_image" name="main_image" accept="image/*"
                               style="display:none" onchange="previewSingle(this, 'mainPreview')">
                    </div>
                    <div id="mainPreview" style="margin-top:8px"></div>
                    @error('main_image')
                        <div class="invalid-feedback" style="display:block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="sub_image">
                        Thay ảnh hover
                        <small style="color:#888; font-weight:400"> (để trống nếu không đổi)</small>
                    </label>
                    <div class="img-upload-box" id="subBox"
                         onclick="document.getElementById('sub_image').click()">
                        <div style="text-align:center; padding:10px; color:#999">
                            <i class="fas fa-hand-pointer" style="font-size:2rem; display:block; margin-bottom:6px"></i>
                            <small>Hiện khi di chuột vào sản phẩm</small>
                        </div>
                        <input type="file" id="sub_image" name="sub_image" accept="image/*"
                               style="display:none" onchange="previewSingle(this, 'subPreview')">
                    </div>
                    <div id="subPreview" style="margin-top:8px"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="additional_images">
                    Thêm ảnh bổ sung
                    <small style="color:#888; font-weight:400">(tối đa 10 ảnh)</small>
                </label>
                <input type="file" id="additional_images" name="additional_images[]"
                       accept="image/*" multiple class="form-control">
            </div>

            {{-- Trạng thái --}}
            <div class="section-title"><i class="fas fa-fire"></i> Trạng thái</div>
            <div class="form-group">
                <label style="display:inline-flex; align-items:center; gap:10px; cursor:pointer;
                              background:#fff8e1; border:2px solid #ffc107; border-radius:8px;
                              padding:10px 18px; font-weight:500">
                    <input type="checkbox" name="product_hot" value="1"
                           {{ old('product_hot', $product->product_hot) ? 'checked' : '' }}
                           style="width:18px; height:18px; cursor:pointer; accent-color:#ff4757">
                    <i class="fas fa-fire" style="color:#ff4757"></i> Đánh dấu là sản phẩm HOT
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Cập nhật
                </button>
                <a href="{{ route('admin.products.list') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Hủy bỏ
                </a>
                <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                      style="display:inline; margin-left:auto"
                      onsubmit="return confirm('Xóa sản phẩm này? Hành động không thể hoàn tác!')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Xóa sản phẩm
                    </button>
                </form>
            </div>
        </form>
    </div>

</div>

<script>
// ===== Xóa ảnh hiện tại (AJAX) =====
function deleteImage(imageId) {
    if (!confirm('Xóa ảnh này?')) return;

    fetch('{{ route('admin.products.images.destroy') }}', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ image_id: imageId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const el = document.getElementById('imgWrapper_' + imageId);
            el?.remove();
        } else {
            alert('Xóa ảnh thất bại!');
        }
    })
    .catch(() => alert('Lỗi kết nối!'));
}

// ===== Preview ảnh mới upload =====
function previewSingle(input, previewId) {
    const preview = document.getElementById(previewId);
    preview.innerHTML = '';
    const file = input.files[0];
    if (!file) return;
    if (!file.type.startsWith('image/')) { alert('Vui lòng chọn file ảnh!'); input.value = ''; return; }
    if (file.size > 5 * 1024 * 1024) { alert('File ảnh quá lớn! Tối đa 5MB.'); input.value = ''; return; }

    const reader = new FileReader();
    reader.onload = e => {
        preview.innerHTML = `
            <div style="position:relative; display:inline-block">
                <img src="${e.target.result}" style="width:120px; height:120px; object-fit:cover;
                     border-radius:8px; border:2px solid #dee2e6; display:block">
                <button type="button" onclick="this.closest('div').remove(); document.getElementById('${input.id}').value=''"
                        style="position:absolute; top:-6px; right:-6px; width:22px; height:22px;
                               background:#dc3545; color:#fff; border:none; border-radius:50%;
                               cursor:pointer; font-size:12px">&times;</button>
                <div style="font-size:11px;color:#888;text-align:center;margin-top:4px;
                            max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                    ${file.name}
                </div>
            </div>`;
    };
    reader.readAsDataURL(file);
}

// Drag & drop
['mainBox', 'subBox'].forEach(boxId => {
    const box = document.getElementById(boxId);
    if (!box) return;
    box.addEventListener('dragover', e => { e.preventDefault(); box.classList.add('drag-over'); });
    box.addEventListener('dragleave', () => box.classList.remove('drag-over'));
    box.addEventListener('drop', e => {
        e.preventDefault();
        box.classList.remove('drag-over');
        const input = box.querySelector('input[type="file"]');
        const previewId = boxId === 'mainBox' ? 'mainPreview' : 'subPreview';
        if (e.dataTransfer.files.length) {
            const dt = new DataTransfer();
            dt.items.add(e.dataTransfer.files[0]);
            input.files = dt.files;
            previewSingle(input, previewId);
        }
    });
});

// ===== Subcategory AJAX =====
const categorySubcategoryMap = @json($categorySubcategoryMap);

function loadSubcategories(catId, selectedId) {
    const select = document.getElementById('subcategory_id');
    select.innerHTML = '<option value="">-- Chọn loại sản phẩm --</option>';
    if (catId && categorySubcategoryMap[catId]) {
        categorySubcategoryMap[catId].forEach(sub => {
            const opt = document.createElement('option');
            opt.value = sub.id;
            opt.textContent = sub.name;
            if (sub.id == selectedId) opt.selected = true;
            select.appendChild(opt);
        });
    }
}

document.getElementById('category_id').addEventListener('change', function () {
    loadSubcategories(this.value, null);
});

// Khởi tạo subcategory từ giá trị hiện tại
loadSubcategories(
    '{{ old('category_id', $product->category_id) }}',
    '{{ old('subcategory_id', $product->subcategory_id) }}'
);
</script>
@endsection
