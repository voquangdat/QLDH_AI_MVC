@extends('admin.layouts.app')

@section('title', 'Thêm Sản phẩm')
@section('page-title', 'Thêm Sản phẩm')

@section('content')
<div class="form-container" style="max-width:960px">
    <a href="{{ route('admin.products.list') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
    <div class="form-header">
        <h2>Thêm Sản phẩm mới</h2>
        <p>Điền đầy đủ thông tin để tạo sản phẩm</p>
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

    <div class="form-wrapper">
        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Thông tin cơ bản --}}
            <div class="section-title"><i class="fas fa-info-circle"></i> Thông tin cơ bản</div>
            <div class="form-row">
                <div class="form-group">
                    <label for="product_name">Tên sản phẩm <span class="required">*</span></label>
                    <input type="text" id="product_name" name="product_name"
                           class="form-control @error('product_name') is-invalid @enderror"
                           value="{{ old('product_name') }}"
                           placeholder="Áo CLB Barcelona 2024" autofocus>
                    @error('product_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="product_code">
                        Mã sản phẩm <span class="required">*</span>
                        <button type="button" id="regenCode"
                                style="margin-left:8px; padding:2px 10px; font-size:12px; border-radius:5px;
                                       background:#667eea; color:#fff; border:none; cursor:pointer">
                            <i class="fas fa-sync-alt"></i> Tự sinh
                        </button>
                    </label>
                    <input type="text" id="product_code" name="product_code"
                           class="form-control @error('product_code') is-invalid @enderror"
                           value="{{ old('product_code') }}"
                           placeholder="PRD-XXXXXX"
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
                                {{ old('category_id') == $cat->category_id ? 'selected' : '' }}>
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

            {{-- Màu sắc --}}
            <div class="section-title"><i class="fas fa-palette"></i> Màu sắc & Size</div>
            <div class="form-row">
                <div class="form-group">
                    <label for="color_id">Màu sắc <span class="required">*</span></label>
                    <select id="color_id" name="color_id"
                            class="form-control @error('color_id') is-invalid @enderror">
                        <option value="">-- Chọn màu --</option>
                        @foreach ($colors as $color)
                            <option value="{{ $color->color_id }}"
                                {{ old('color_id') == $color->color_id ? 'selected' : '' }}>
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
                <div class="size-grid">
                    @foreach ($sizes as $size)
                        <label class="size-item">
                            <input type="checkbox" name="size_ids[]" value="{{ $size->product_size_id }}"
                                   {{ in_array($size->product_size_id, old('size_ids', [])) ? 'checked' : '' }}>
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
                           value="{{ old('product_gia') }}" placeholder="350000">
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
                          placeholder="Mô tả chi tiết về sản phẩm...">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="care_note">Hướng dẫn bảo quản</label>
                <textarea id="care_note" name="care_note" rows="3"
                          class="form-control @error('care_note') is-invalid @enderror"
                          placeholder="Hướng dẫn giặt, ủi, bảo quản...">{{ old('care_note') }}</textarea>
                @error('care_note')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Hình ảnh --}}
            <div class="section-title"><i class="fas fa-images"></i> Hình ảnh</div>
            <div class="form-row">
                {{-- Ảnh chính --}}
                <div class="form-group">
                    <label for="main_image">
                        Ảnh chính <span class="required">*</span>
                        <small style="color:#888; font-weight:400"> — hiển thị mặc định</small>
                    </label>
                    <div class="img-upload-box" id="mainBox"
                         onclick="document.getElementById('main_image').click()">
                        <div style="text-align:center; padding:10px; color:#999">
                            <i class="fas fa-cloud-upload-alt" style="font-size:2rem; display:block; margin-bottom:6px"></i>
                            <span style="font-size:13px">Click hoặc kéo thả ảnh vào đây</span><br>
                            <small>JPG, PNG — tối đa 5MB</small>
                        </div>
                        <input type="file" id="main_image" name="main_image" accept="image/*"
                               class="@error('main_image') is-invalid @enderror"
                               style="display:none" onchange="previewSingle(this, 'mainPreview')">
                    </div>
                    <div id="mainPreview" style="margin-top:8px"></div>
                    @error('main_image')
                        <div class="invalid-feedback" style="display:block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Ảnh phụ (hover) --}}
                <div class="form-group">
                    <label for="sub_image">
                        Ảnh phụ
                        <small style="color:#888; font-weight:400"> — hiện khi hover chuột</small>
                    </label>
                    <div class="img-upload-box" id="subBox"
                         onclick="document.getElementById('sub_image').click()">
                        <div style="text-align:center; padding:10px; color:#999">
                            <i class="fas fa-hand-pointer" style="font-size:2rem; display:block; margin-bottom:6px"></i>
                            <span style="font-size:13px">Ảnh hiện khi di chuột vào sản phẩm</span><br>
                            <small>JPG, PNG — tối đa 5MB</small>
                        </div>
                        <input type="file" id="sub_image" name="sub_image" accept="image/*"
                               style="display:none" onchange="previewSingle(this, 'subPreview')">
                    </div>
                    <div id="subPreview" style="margin-top:8px"></div>
                </div>
            </div>

            {{-- Ảnh bổ sung --}}
            <div class="form-group">
                <label for="additional_images">Ảnh bổ sung <small style="color:#888; font-weight:400">(tối đa 10 ảnh)</small></label>
                <input type="file" id="additional_images" name="additional_images[]"
                       accept="image/*" multiple class="form-control">
            </div>

            {{-- Hot --}}
            <div class="section-title"><i class="fas fa-fire"></i> Trạng thái</div>
            <div class="form-group">
                <label style="display:inline-flex; align-items:center; gap:10px; cursor:pointer;
                              background:#fff8e1; border:2px solid #ffc107; border-radius:8px;
                              padding:10px 18px; font-weight:500">
                    <input type="checkbox" name="product_hot" value="1"
                           {{ old('product_hot') ? 'checked' : '' }}
                           style="width:18px; height:18px; cursor:pointer; accent-color:#ff4757">
                    <i class="fas fa-fire" style="color:#ff4757"></i> Đánh dấu là sản phẩm HOT
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Lưu Sản phẩm
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Hủy bỏ
                </a>
            </div>
        </form>
    </div>

</div>

<script>
// ===== Image preview (single) =====
function previewSingle(input, previewId) {
    const preview = document.getElementById(previewId);
    preview.innerHTML = '';

    const file = input.files[0];
    if (!file) return;

    if (!file.type.startsWith('image/')) {
        alert('Vui lòng chọn file ảnh!');
        input.value = '';
        return;
    }
    if (file.size > 5 * 1024 * 1024) {
        alert('File ảnh quá lớn! Tối đa 5MB.');
        input.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = e => {
        preview.innerHTML = `
            <div style="position:relative; display:inline-block">
                <img src="${e.target.result}" style="width:120px; height:120px; object-fit:cover;
                     border-radius:8px; border:2px solid #dee2e6; display:block">
                <button type="button" onclick="clearImage('${input.id}','${previewId}')"
                        style="position:absolute; top:-6px; right:-6px; width:22px; height:22px;
                               background:#dc3545; color:#fff; border:none; border-radius:50%;
                               cursor:pointer; font-size:12px; display:flex; align-items:center; justify-content:center">
                    &times;
                </button>
                <div style="font-size:11px; color:#888; text-align:center; margin-top:4px; max-width:120px;
                            overflow:hidden; text-overflow:ellipsis; white-space:nowrap">${file.name}</div>
            </div>`;
    };
    reader.readAsDataURL(file);
}

function clearImage(inputId, previewId) {
    document.getElementById(inputId).value = '';
    document.getElementById(previewId).innerHTML = '';
}

// Drag & drop cho upload box
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

// ===== Auto-generate product code =====
function generateCode() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let suffix = '';
    for (let i = 0; i < 6; i++) {
        suffix += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return 'PRD-' + suffix;
}

const codeInput = document.getElementById('product_code');
if (!codeInput.value) {
    codeInput.value = generateCode();
}

document.getElementById('regenCode').addEventListener('click', function () {
    codeInput.value = generateCode();
});

function removeAccents(str) {
    return str
        .replace(/đ/gi, 'd')
        .normalize('NFD')
        .replace(/[̀-ͯ]/g, '')
        .replace(/[^a-zA-Z0-9\s]/g, '');
}

// Gõ tên SP → cập nhật prefix của mã theo chữ cái đầu (không dấu)
document.getElementById('product_name').addEventListener('input', function () {
    const clean = removeAccents(this.value.trim()).toUpperCase();
    const words = clean.split(/\s+/).filter(Boolean);
    const prefix = words.slice(0, 3).map(w => w[0]).join('') || 'PRD';
    const current = codeInput.value;
    const suffix = current.includes('-') ? current.split('-').pop() : current;
    codeInput.value = prefix + '-' + suffix;
});

// ===== Subcategory AJAX =====
const categorySubcategoryMap = @json($categorySubcategoryMap);

document.getElementById('category_id').addEventListener('change', function () {
    const catId = this.value;
    const select = document.getElementById('subcategory_id');
    select.innerHTML = '<option value="">-- Chọn loại sản phẩm --</option>';

    if (catId && categorySubcategoryMap[catId]) {
        categorySubcategoryMap[catId].forEach(sub => {
            const opt = document.createElement('option');
            opt.value = sub.id;
            opt.textContent = sub.name;
            select.appendChild(opt);
        });
    }
});

// Khôi phục giá trị cũ khi có validation error
const oldCategory    = '{{ old('category_id') }}';
const oldSubcategory = '{{ old('subcategory_id') }}';
if (oldCategory) {
    document.getElementById('category_id').value = oldCategory;
    document.getElementById('category_id').dispatchEvent(new Event('change'));
    setTimeout(() => {
        document.getElementById('subcategory_id').value = oldSubcategory;
    }, 50);
}
</script>
@endsection
