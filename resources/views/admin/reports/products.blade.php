@extends('admin.layouts.app')

@section('title', 'Báo cáo Sản phẩm & Tồn kho')
@section('page-title', 'Báo cáo Sản phẩm & Tồn kho')


@section('content')
<div class="reports-container">

    <a href="{{ route('admin.reports.index') }}" class="back-to-reports">
        <i class="fas fa-arrow-left"></i> Quay lại Báo cáo
    </a>

    <div class="reports-header">
        <h2><i class="fas fa-chart-pie"></i> Báo cáo Sản phẩm & Tồn kho Chi tiết</h2>
        <p>Phân tích sản phẩm bán chạy, tình trạng tồn kho và hiệu suất danh mục</p>
    </div>

    {{-- Bộ lọc --}}
    <div class="filter-section">
        <form method="GET" action="{{ route('admin.reports.products') }}" class="filter-form">
            <div class="filter-group">
                <label>Từ ngày:</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}">
            </div>
            <div class="filter-group">
                <label>Đến ngày:</label>
                <input type="date" name="date_to" value="{{ $dateTo }}">
            </div>
            <button type="submit" class="filter-btn pink">
                <i class="fas fa-filter"></i> Lọc
            </button>
        </form>
    </div>

    {{-- Thống kê tồn kho tổng quan --}}
    @if($stockStats)
    <div class="stats-grid">
        <div class="stat-card total">
            <i class="fas fa-boxes"></i>
            <h3>{{ number_format($stockStats->total_variants ?? 0) }}</h3>
            <p>Tổng biến thể</p>
        </div>
        <div class="stat-card in-stock">
            <i class="fas fa-check-circle"></i>
            <h3>{{ number_format($stockStats->in_stock ?? 0) }}</h3>
            <p>Còn hàng</p>
        </div>
        <div class="stat-card out-stock">
            <i class="fas fa-times-circle"></i>
            <h3>{{ number_format($stockStats->out_of_stock ?? 0) }}</h3>
            <p>Hết hàng</p>
        </div>
        <div class="stat-card low-stock">
            <i class="fas fa-exclamation-triangle"></i>
            <h3>{{ number_format($stockStats->low_stock ?? 0) }}</h3>
            <p>Sắp hết hàng</p>
        </div>
        <div class="stat-card quantity">
            <i class="fas fa-cubes"></i>
            <h3>{{ number_format($stockStats->total_quantity ?? 0) }}</h3>
            <p>Tổng tồn kho</p>
        </div>
        <div class="stat-card avg">
            <i class="fas fa-calculator"></i>
            <h3>{{ number_format($stockStats->avg_quantity ?? 0, 1) }}</h3>
            <p>TB/biến thể</p>
        </div>
    </div>
    @endif

    {{-- Top sản phẩm bán chạy --}}
    <div class="report-section">
        <h3 class="section-title pink">
            <i class="fas fa-trophy"></i>
            Top Sản phẩm Bán chạy
        </h3>
        @if($topProducts && count($topProducts) > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Xếp hạng</th>
                        <th>Tên sản phẩm</th>
                        <th>Đã bán</th>
                        <th>Doanh thu</th>
                        <th>Giá trung bình</th>
                        <th>Số đơn hàng</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topProducts as $i => $product)
                    <tr>
                        <td>
                            <span class="rank-badge {{ $i < 3 ? 'top3' : '' }}">#{{ $i + 1 }}</span>
                        </td>
                        <td><strong>{{ $product->product_name }}</strong></td>
                        <td><strong>{{ number_format($product->total_sold) }}</strong> sản phẩm</td>
                        <td>{{ number_format($product->total_revenue ?? 0, 0, ',', '.') }}đ</td>
                        <td>{{ number_format($product->avg_price ?? 0, 0, ',', '.') }}đ</td>
                        <td>{{ number_format($product->order_count) }} đơn</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                <i class="fas fa-trophy"></i>
                <p>Không có dữ liệu sản phẩm trong khoảng thời gian được chọn</p>
            </div>
        @endif
    </div>

    {{-- Sản phẩm sắp hết hàng --}}
    <div class="report-section">
        <h3 class="section-title pink">
            <i class="fas fa-exclamation-triangle"></i>
            Cảnh báo Sản phẩm Sắp hết hàng
        </h3>
        @if($lowStockProducts && count($lowStockProducts) > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Màu sắc</th>
                        <th>Kích thước</th>
                        <th>Tồn kho</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lowStockProducts as $product)
                    @php $qty = $product->stock_qty; @endphp
                    <tr>
                        <td><strong>{{ $product->product_name }}</strong></td>
                        <td>{{ $product->color_ten ?? 'N/A' }}</td>
                        <td>{{ $product->product_size ?? 'N/A' }}</td>
                        <td>
                            <strong style="color:#dc3545">{{ number_format($qty) }}</strong>
                            <div class="progress-bar">
                                <div class="progress-fill"
                                     style="width:{{ min(100, ($qty / $product->warning_level) * 100) }}%;
                                            background:{{ $qty <= 5 ? '#dc3545' : '#ffc107' }}">
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($qty <= 5)
                                <span class="stock-status stock-out">Rất ít</span>
                            @else
                                <span class="stock-status stock-low">Sắp hết</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                <i class="fas fa-check-circle"></i>
                <p style="color:#28a745">Tất cả sản phẩm đều có tồn kho ổn định</p>
            </div>
        @endif
    </div>

    {{-- Thống kê theo danh mục --}}
    <div class="report-section">
        <h3 class="section-title pink">
            <i class="fas fa-layer-group"></i>
            Thống kê theo Danh mục Sản phẩm
        </h3>
        @if($categoryStats && count($categoryStats) > 0)
            <div class="category-chart">
                @foreach($categoryStats as $category)
                <div class="category-item">
                    <h4>{{ $category->category_name }}</h4>
                    <div style="font-size:2rem;font-weight:bold;color:#f093fb;margin:10px 0">
                        {{ number_format($category->product_count) }}
                    </div>
                    <p style="color:#666">Sản phẩm</p>
                    <div class="category-stats">
                        <div>
                            <span>Đã bán:</span><br>
                            <strong>{{ number_format($category->total_sold ?? 0) }}</strong>
                        </div>
                        <div>
                            <span>Doanh thu:</span><br>
                            <strong>{{ number_format($category->revenue ?? 0, 0, ',', '.') }}đ</strong>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="no-data">
                <i class="fas fa-layer-group"></i>
                <p>Không có dữ liệu danh mục</p>
            </div>
        @endif
    </div>

    {{-- Phân tích tồn kho chi tiết --}}
    @if($stockStats)
    <div class="report-section">
        <h3 class="section-title pink">
            <i class="fas fa-chart-bar"></i>
            Phân tích Tồn kho Chi tiết
        </h3>
        @php
            $total        = $stockStats->total_variants ?? 0;
            $inStockRate  = $total > 0 ? round(($stockStats->in_stock  / $total) * 100, 1) : 0;
            $outStockRate = $total > 0 ? round(($stockStats->out_of_stock / $total) * 100, 1) : 0;
            $lowStockRate = $total > 0 ? round(($stockStats->low_stock / $total) * 100, 1) : 0;
        @endphp
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-percentage" style="color:#28a745"></i>
                <h3>{{ $inStockRate }}%</h3>
                <p>Tỷ lệ còn hàng</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-percentage" style="color:#dc3545"></i>
                <h3>{{ $outStockRate }}%</h3>
                <p>Tỷ lệ hết hàng</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-percentage" style="color:#ffc107"></i>
                <h3>{{ $lowStockRate }}%</h3>
                <p>Tỷ lệ sắp hết</p>
            </div>
        </div>
        <div style="margin-top:20px;text-align:center;color:#666">
            <p><i class="fas fa-info-circle"></i>
            Biến thể được coi là "sắp hết hàng" khi tồn kho ≤ mức cảnh báo được cài đặt</p>
        </div>
    </div>
    @endif

</div>
@endsection
