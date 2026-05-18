@extends('admin.layouts.app')
@section('title', 'Chi tiết Tồn kho')
@section('page-title', 'Chi tiết Tồn kho')

@section('content')
<div class="management-container">

    {{-- Header --}}
    <div class="management-header management-header--center">
        <h2>Chi tiết Tồn kho{{ $product ? ' — ' . $product->product_name : '' }}</h2>
        <p>Xem và chỉnh sửa tồn kho theo từng biến thể sản phẩm</p>
        <div class="action-buttons">
            <a href="{{ route('admin.inventory.list') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>
    </div>

    {{-- Alerts --}}
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

    {{-- Thông tin sản phẩm --}}
    @if ($product)
        @php $mainImage = $product->images->first(); @endphp
        <div style="background:#fff; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,0.08); padding:24px; margin-bottom:24px; display:flex; align-items:center; gap:24px;">
            @if ($mainImage)
                <img src="{{ asset('uploads/' . $mainImage->product_anh) }}"
                     style="width:100px; height:100px; object-fit:cover; border-radius:10px; flex-shrink:0;"
                     alt="{{ $product->product_name }}"
                     onerror="this.src='{{ asset('assets/clients/images/no-image.png') }}'">
            @else
                <div style="width:100px; height:100px; background:#f8f9fa; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fas fa-image fa-2x" style="color:#ccc;"></i>
                </div>
            @endif
            <div>
                <h3 style="margin:0 0 6px 0; color:#333;">{{ $product->product_name }}</h3>
                <p style="margin:0 0 4px 0; color:#6c757d; font-size:14px;">
                    <i class="fas fa-tag"></i> {{ $product->category->category_name ?? '—' }}
                </p>
                <p style="margin:0; color:#6c757d; font-size:14px;">
                    <i class="fas fa-barcode"></i> {{ $product->product_code ?? '—' }}
                    &nbsp;&nbsp;
                    <i class="fas fa-money-bill-wave"></i>
                    {{ number_format($product->product_gia, 0, ',', '.') }}₫
                </p>
            </div>
        </div>
    @endif

    {{-- Thống kê --}}
    <div class="stats-section">
        <div class="stats-grid">
            <div class="stat-card total">
                <i class="fas fa-layer-group fa-2x" style="margin-bottom:10px;"></i>
                <h3>{{ number_format($stats['tong_bienthe']) }}</h3>
                <p>Tổng biến thể</p>
            </div>
            <div class="stat-card stock">
                <i class="fas fa-boxes fa-2x" style="margin-bottom:10px;"></i>
                <h3>{{ number_format($stats['tong_ton_kho']) }}</h3>
                <p>Tổng tồn kho</p>
            </div>
            <div class="stat-card warning" style="background:linear-gradient(135deg,#11998e,#38ef7d);">
                <i class="fas fa-shopping-cart fa-2x" style="margin-bottom:10px;"></i>
                <h3>{{ number_format($stats['co_the_ban']) }}</h3>
                <p>Có thể bán</p>
            </div>
            <div class="stat-card danger">
                <i class="fas fa-times-circle fa-2x" style="margin-bottom:10px;"></i>
                <h3>{{ number_format($stats['het_hang']) }}</h3>
                <p>Hết hàng</p>
            </div>
        </div>
    </div>

    {{-- Bảng biến thể --}}
    <div class="data-table">
        @if ($variants->isNotEmpty())
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>STT</th>
                            @if (!$product)
                                <th>Ảnh SP</th>
                                <th>Tên sản phẩm</th>
                            @endif
                            <th>SKU</th>
                            <th>Size</th>
                            <th>Màu sắc</th>
                            <th>SL Tồn</th>
                            <th>SL Đặt</th>
                            <th>SL Có thể bán</th>
                            <th>Mức cảnh báo</th>
                            <th>Trạng thái</th>
                            <th style="text-align:center;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($variants as $index => $variant)
                            @php
                                $inv      = $variant->inventory;
                                $prod     = $variant->product;
                                $image    = $prod->images->first();
                                $rowClass = $variant->isOutOfStock() ? 'stock-danger'
                                          : (($inv && $inv->isLowStock()) ? 'stock-warning' : '');
                            @endphp
                            <tr class="{{ $rowClass }}">
                                <td>{{ $index + 1 }}</td>

                                @if (!$product)
                                    <td>
                                        @if ($image)
                                            <img src="{{ asset('uploads/' . $image->product_anh) }}"
                                                 class="product-image"
                                                 alt="{{ $prod->product_name }}"
                                                 onerror="this.src='{{ asset('assets/clients/images/no-image.png') }}'">
                                        @else
                                            <div class="product-image" style="background:#f8f9fa;display:flex;align-items:center;justify-content:center;">
                                                <i class="fas fa-image" style="color:#ccc;"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $prod->product_name }}</strong><br>
                                        <small style="color:#6c757d;">{{ $prod->category->category_name ?? '—' }}</small>
                                    </td>
                                @endif

                                <td><span class="sku-code">{{ $variant->variant_code ?? '—' }}</span></td>
                                <td><span class="size-badge">{{ $variant->size->product_size ?? '—' }}</span></td>
                                <td>
                                    <div class="color-display">
                                        @if ($variant->color->color_anh ?? null)
                                            <img src="{{ asset('uploads/' . $variant->color->color_anh) }}"
                                                 class="color-swatch"
                                                 title="{{ $variant->color->color_ten }}">
                                        @else
                                            <div class="color-swatch"
                                                 style="background-color:#{{ substr(md5($variant->color->color_ten ?? ''), 0, 6) }};">
                                            </div>
                                        @endif
                                        <small>{{ $variant->color->color_ten ?? '—' }}</small>
                                    </div>
                                </td>

                                @if ($inv)
                                    <td class="quantity-display">
                                        <input type="number" class="editable-stock stock-input"
                                               data-field="soluong_ton"
                                               data-variant-id="{{ $variant->variant_id }}"
                                               value="{{ $inv->soluong_ton }}" min="0">
                                    </td>
                                    <td class="quantity-display">
                                        <input type="number" class="editable-stock stock-input"
                                               data-field="soluong_dat"
                                               data-variant-id="{{ $variant->variant_id }}"
                                               value="{{ $inv->soluong_dat }}" min="0">
                                    </td>
                                    <td class="quantity-display">
                                        <span class="available-qty" id="available-{{ $variant->variant_id }}">
                                            {{ $inv->availableQuantity() }}
                                        </span>
                                    </td>
                                    <td class="quantity-display">
                                        <input type="number" class="editable-stock stock-input"
                                               data-field="muc_canh_bao"
                                               data-variant-id="{{ $variant->variant_id }}"
                                               value="{{ $inv->muc_canh_bao }}" min="1">
                                    </td>
                                    <td>
                                        @if ($inv->isOutOfStock())
                                            <span class="status-badge status-danger">Hết hàng</span>
                                        @elseif ($inv->isLowStock())
                                            <span class="status-badge status-warning">Sắp hết</span>
                                        @else
                                            <span class="status-badge status-success">Còn hàng</span>
                                        @endif
                                    </td>
                                    <td style="text-align:center;">
                                        <button type="button"
                                                class="action-btn btn-save save-stock-btn"
                                                data-variant-id="{{ $variant->variant_id }}"
                                                title="Lưu thay đổi">
                                            <i class="fas fa-save"></i>
                                        </button>
                                    </td>
                                @else
                                    <td colspan="5" style="text-align:center; color:#adb5bd; font-style:italic;">
                                        Chưa có dữ liệu tồn kho
                                    </td>
                                    <td style="text-align:center;">
                                        <button type="button"
                                                class="action-btn btn-save init-stock-btn"
                                                data-variant-id="{{ $variant->variant_id }}"
                                                title="Khởi tạo tồn kho">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="no-data">
                <i class="fas fa-warehouse"></i>
                <h3>Không có biến thể nào</h3>
                <p>Sản phẩm này chưa có biến thể nào được tạo</p>
                <a href="{{ route('admin.products.index') }}" class="btn btn-primary" style="margin-top:15px;">
                    <i class="fas fa-plus"></i> Quản lý sản phẩm
                </a>
            </div>
        @endif
    </div>

</div>

<script>
    const UPDATE_STOCK_URL = "{{ route('admin.inventory.update-stock') }}";
    const INIT_STOCK_URL   = "{{ route('admin.inventory.init-stock') }}";
    const CSRF_TOKEN       = "{{ csrf_token() }}";

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.stock-input').forEach(input => {
            input.addEventListener('change', function () {
                const btn = document.querySelector(`.save-stock-btn[data-variant-id="${this.dataset.variantId}"]`);
                if (btn) btn.style.background = '#ffc107';
            });
        });

        document.querySelectorAll('.save-stock-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                saveStock(this.dataset.variantId, this);
            });
        });

        document.querySelectorAll('.init-stock-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                initStock(this.dataset.variantId, this);
            });
        });
    });

    function saveStock(variantId, button) {
        const inputs = document.querySelectorAll(`.stock-input[data-variant-id="${variantId}"]`);
        const formData = new FormData();
        formData.append('_token', CSRF_TOKEN);
        formData.append('variant_id', variantId);
        inputs.forEach(input => formData.append(input.dataset.field, input.value));

        setLoading(button, true);

        fetch(UPDATE_STOCK_URL, { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const span = document.getElementById(`available-${variantId}`);
                    if (span && data.data) span.textContent = data.data.soluong_co_the_ban;
                    button.style.background = '#28a745';
                    showAlert('success', data.message);
                } else {
                    showAlert('error', data.message);
                }
            })
            .catch(() => showAlert('error', 'Có lỗi xảy ra khi cập nhật!'))
            .finally(() => setLoading(button, false));
    }

    function initStock(variantId, button) {
        const formData = new FormData();
        formData.append('_token', CSRF_TOKEN);
        formData.append('variant_id', variantId);

        setLoading(button, true);

        fetch(INIT_STOCK_URL, { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showAlert('error', data.message);
                }
            })
            .catch(() => showAlert('error', 'Có lỗi xảy ra!'))
            .finally(() => setLoading(button, false));
    }

    function setLoading(button, loading) {
        if (loading) {
            button._original = button.innerHTML;
            button.disabled  = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        } else {
            button.disabled  = false;
            button.innerHTML = button._original;
        }
    }

    function showAlert(type, message) {
        const div = document.createElement('div');
        div.className = `alert alert-${type === 'success' ? 'success' : 'error'}`;
        div.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${message}`;
        const container = document.querySelector('.management-container');
        container.insertBefore(div, container.firstChild);
        setTimeout(() => div.remove(), 3000);
    }
</script>
@endsection
