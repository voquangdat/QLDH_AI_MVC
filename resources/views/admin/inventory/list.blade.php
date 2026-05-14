@extends('admin.layouts.app')
@section('title', 'Danh sách Tồn kho')
@section('page-title', 'Danh sách Tồn kho')

@section('content')
<div class="management-container">

    {{-- Header --}}
    <div class="management-header management-header--center">
        <h2>Quản lý Tồn kho</h2>
        <p>Theo dõi và quản lý số lượng tồn kho của tất cả sản phẩm trong hệ thống</p>
        <div class="action-buttons">
            <button onclick="window.location.reload()" class="btn btn-warning">
                <i class="fas fa-sync-alt"></i> Làm mới
            </button>
            <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
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

    {{-- Thống kê --}}
    <div class="stats-section">
        <h3 style="margin-bottom: 20px; text-align: center; color: #333;">Thống kê Tồn kho</h3>
        <div class="stats-grid">
            <div class="stat-card total">
                <i class="fas fa-cubes fa-2x" style="margin-bottom: 10px;"></i>
                <h3>{{ number_format($stats['tong_bienthe']) }}</h3>
                <p>Tổng biến thể</p>
            </div>
            <div class="stat-card stock">
                <i class="fas fa-boxes fa-2x" style="margin-bottom: 10px;"></i>
                <h3>{{ number_format($stats['tong_ton_kho']) }}</h3>
                <p>Tổng tồn kho</p>
            </div>
            <div class="stat-card warning">
                <i class="fas fa-exclamation-triangle fa-2x" style="margin-bottom: 10px;"></i>
                <h3>{{ number_format($stats['canh_bao']) }}</h3>
                <p>Sắp hết hàng</p>
            </div>
            <div class="stat-card danger">
                <i class="fas fa-times-circle fa-2x" style="margin-bottom: 10px;"></i>
                <h3>{{ number_format($stats['het_hang']) }}</h3>
                <p>Hết hàng</p>
            </div>
        </div>
    </div>

    {{-- Bộ lọc --}}
    <div class="filter-section">
        <form method="GET" action="{{ route('admin.inventory.list') }}" class="filter-form">
            <div class="form-group">
                <label>Tìm kiếm</label>
                <input type="text" class="form-control" name="search"
                       value="{{ request('search') }}"
                       placeholder="Tên sản phẩm hoặc SKU...">
            </div>
            <div class="form-group">
                <label>Danh mục</label>
                <select name="category" class="form-control">
                    <option value="">Tất cả danh mục</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->category_id }}"
                            {{ request('category') == $category->category_id ? 'selected' : '' }}>
                            {{ $category->category_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Trạng thái</label>
                <select name="status" class="form-control">
                    <option value="">Tất cả trạng thái</option>
                    <option value="in_stock"  {{ request('status') == 'in_stock'  ? 'selected' : '' }}>Còn hàng</option>
                    <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Sắp hết hàng</option>
                    <option value="out_stock" {{ request('status') == 'out_stock' ? 'selected' : '' }}>Hết hàng</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Tìm kiếm
                </button>
                <a href="{{ route('admin.inventory.list') }}" class="btn btn-secondary" style="margin-top: 10px;">
                    <i class="fas fa-undo"></i> Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Bảng dữ liệu --}}
    <div class="data-table">
        @if ($inventories->isNotEmpty())
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Ảnh SP</th>
                            <th>Tên sản phẩm</th>
                            <th>SKU</th>
                            <th>Size</th>
                            <th>Màu sắc</th>
                            <th>SL Tồn</th>
                            <th>SL Đặt</th>
                            <th>SL Có thể bán</th>
                            <th>Mức cảnh báo</th>
                            <th>Trạng thái</th>
                            <th style="text-align: center;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inventories as $index => $inventory)
                            @php
                                $variant = $inventory->variant;
                                $product = $variant->product;
                                $image   = $product->images->first();
                                $rowClass = $inventory->isOutOfStock() ? 'stock-danger' : ($inventory->isLowStock() ? 'stock-warning' : '');
                            @endphp
                            <tr class="{{ $rowClass }}">
                                <td>{{ $inventories->firstItem() + $index }}</td>
                                <td>
                                    @if ($image)
                                        <img src="{{ asset('uploads/' . $image->product_anh) }}"
                                             class="product-image"
                                             alt="{{ $product->product_name }}"
                                             onerror="this.src='{{ asset('assets/clients/images/no-image.png') }}'">
                                    @else
                                        <div class="product-image" style="background:#f8f9fa; display:flex; align-items:center; justify-content:center;">
                                            <i class="fas fa-image" style="color:#ccc;"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="product-title">
                                        <strong>{{ $product->product_name }}</strong>
                                    </div>
                                    <small style="color:#6c757d;">{{ $product->category->category_name ?? '—' }}</small>
                                </td>
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
                                                 style="background-color:#{{ substr(md5($variant->color->color_ten ?? ''), 0, 6) }}">
                                            </div>
                                        @endif
                                        <small>{{ $variant->color->color_ten ?? '—' }}</small>
                                    </div>
                                </td>
                                <td class="quantity-display">
                                    <input type="number" class="editable-stock stock-input"
                                           data-field="soluong_ton"
                                           data-variant-id="{{ $variant->variant_id }}"
                                           value="{{ $inventory->soluong_ton }}" min="0">
                                </td>
                                <td class="quantity-display">
                                    <input type="number" class="editable-stock stock-input"
                                           data-field="soluong_dat"
                                           data-variant-id="{{ $variant->variant_id }}"
                                           value="{{ $inventory->soluong_dat }}" min="0">
                                </td>
                                <td class="quantity-display">
                                    <span class="available-qty" id="available-{{ $variant->variant_id }}">
                                        {{ $inventory->soluong_co_the_ban }}
                                    </span>
                                </td>
                                <td class="quantity-display">
                                    <input type="number" class="editable-stock stock-input"
                                           data-field="muc_canh_bao"
                                           data-variant-id="{{ $variant->variant_id }}"
                                           value="{{ $inventory->muc_canh_bao }}" min="1">
                                </td>
                                <td>
                                    @if ($inventory->isOutOfStock())
                                        <span class="status-badge status-danger">Hết hàng</span>
                                    @elseif ($inventory->isLowStock())
                                        <span class="status-badge status-warning">Sắp hết</span>
                                    @else
                                        <span class="status-badge status-success">Còn hàng</span>
                                    @endif
                                </td>
                                <td style="text-align:center;">
                                    <a href="{{ route('admin.inventory.detail') }}?product_id={{ $product->product_id }}"
                                       class="action-btn btn-view" title="Chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <br>
                                    <button type="button"
                                            class="action-btn btn-save save-stock-btn"
                                            data-variant-id="{{ $variant->variant_id }}"
                                            title="Lưu thay đổi">
                                        <i class="fas fa-save"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Phân trang --}}
            @if ($inventories->lastPage() > 1)
                <div style="margin-top: 20px;">
                    <nav>
                        <ul class="pagination justify-content-center">
                            @if ($inventories->onFirstPage())
                                <li class="page-item disabled"><span class="page-link">Đầu</span></li>
                                <li class="page-item disabled"><span class="page-link">Trước</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $inventories->url(1) }}">Đầu</a></li>
                                <li class="page-item"><a class="page-link" href="{{ $inventories->previousPageUrl() }}">Trước</a></li>
                            @endif

                            @for ($i = max(1, $inventories->currentPage() - 2); $i <= min($inventories->lastPage(), $inventories->currentPage() + 2); $i++)
                                <li class="page-item {{ $i == $inventories->currentPage() ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $inventories->url($i) }}">{{ $i }}</a>
                                </li>
                            @endfor

                            @if ($inventories->hasMorePages())
                                <li class="page-item"><a class="page-link" href="{{ $inventories->nextPageUrl() }}">Sau</a></li>
                                <li class="page-item"><a class="page-link" href="{{ $inventories->url($inventories->lastPage()) }}">Cuối</a></li>
                            @else
                                <li class="page-item disabled"><span class="page-link">Sau</span></li>
                                <li class="page-item disabled"><span class="page-link">Cuối</span></li>
                            @endif
                        </ul>
                    </nav>
                    <div class="text-center">
                        <small style="color:#6c757d;">
                            Hiển thị {{ $inventories->firstItem() }} - {{ $inventories->lastItem() }}
                            trong tổng số {{ $inventories->total() }} mục
                        </small>
                    </div>
                </div>
            @endif

        @else
            <div class="no-data">
                <i class="fas fa-warehouse"></i>
                <h3>Không có dữ liệu tồn kho</h3>
                <p>Hãy thêm biến thể sản phẩm để bắt đầu quản lý tồn kho</p>
                <a href="{{ route('admin.products.index') }}" class="btn btn-primary" style="margin-top:15px;">
                    <i class="fas fa-plus"></i> Quản lý sản phẩm
                </a>
            </div>
        @endif
    </div>

    {{-- Sản phẩm sắp hết hàng --}}
    @if ($lowStocks->isNotEmpty())
        <div style="background:#fff; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,0.1); padding:20px; margin-top:20px;">
            <h3 style="color:#dc3545; margin-bottom:20px;">
                <i class="fas fa-exclamation-triangle"></i> Sản phẩm sắp hết hàng
            </h3>
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(300px, 1fr)); gap:15px;">
                @foreach ($lowStocks as $low)
                    @php $img = $low->variant->product->images->first(); @endphp
                    <div style="background:#fff3cd; border:1px solid #ffeaa7; border-radius:8px; padding:15px;">
                        <div style="display:flex; align-items:center; gap:15px;">
                            @if ($img)
                                <img src="{{ asset('uploads/' . $img->product_anh) }}"
                                     style="width:50px; height:50px; object-fit:cover; border-radius:8px;"
                                     alt="{{ $low->variant->product->product_name }}">
                            @else
                                <div style="width:50px; height:50px; background:#f8f9fa; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                                    <i class="fas fa-image" style="color:#ccc;"></i>
                                </div>
                            @endif
                            <div style="flex-grow:1;">
                                <h6 style="margin:0 0 5px 0; color:#333;">{{ $low->variant->product->product_name }}</h6>
                                <small style="color:#6c757d;">
                                    {{ $low->variant->color->color_ten ?? '—' }} - {{ $low->variant->size->product_size ?? '—' }}
                                </small>
                                <div style="margin-top:8px;">
                                    <span class="status-badge status-warning">Còn {{ $low->soluong_ton }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>

<script>
    const UPDATE_STOCK_URL = "{{ route('admin.inventory.update-stock') }}";
    const CSRF_TOKEN = "{{ csrf_token() }}";

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.stock-input').forEach(input => {
            input.addEventListener('change', function () {
                const btn = document.querySelector(`.save-stock-btn[data-variant-id="${this.dataset.variantId}"]`);
                if (btn) btn.style.background = '#ffc107';
            });
        });

        document.querySelectorAll('.save-stock-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                updateStock(this.dataset.variantId, this);
            });
        });
    });

    function updateStock(variantId, button) {
        const inputs = document.querySelectorAll(`.stock-input[data-variant-id="${variantId}"]`);
        const formData = new FormData();
        formData.append('_token', CSRF_TOKEN);
        formData.append('variant_id', variantId);
        inputs.forEach(input => formData.append(input.dataset.field, input.value));

        button.disabled = true;
        const original = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

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
            .finally(() => { button.disabled = false; button.innerHTML = original; });
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
