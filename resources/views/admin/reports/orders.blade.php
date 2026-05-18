@extends('admin.layouts.app')

@section('title', 'Báo cáo Đơn hàng')
@section('page-title', 'Báo cáo Đơn hàng')


@section('content')
<div class="reports-container">

    <a href="{{ route('admin.reports.index') }}" class="back-to-reports">
        <i class="fas fa-arrow-left"></i> Quay lại Báo cáo
    </a>

    <div class="reports-header">
        <h2><i class="fas fa-chart-line"></i> Báo cáo Đơn hàng Chi tiết</h2>
        <p>Phân tích tổng quan đơn hàng, trạng thái và hiệu suất kinh doanh</p>
    </div>

    {{-- Bộ lọc --}}
    <div class="filter-section">
        <form method="GET" action="{{ route('admin.reports.orders') }}" class="filter-form">
            <div class="filter-group">
                <label>Từ ngày:</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}">
            </div>
            <div class="filter-group">
                <label>Đến ngày:</label>
                <input type="date" name="date_to" value="{{ $dateTo }}">
            </div>
            <div class="filter-group">
                <label>Nhóm theo:</label>
                <select name="group_by">
                    <option value="day"   @selected($groupBy === 'day')>Ngày</option>
                    <option value="week"  @selected($groupBy === 'week')>Tuần</option>
                    <option value="month" @selected($groupBy === 'month')>Tháng</option>
                    <option value="year"  @selected($groupBy === 'year')>Năm</option>
                </select>
            </div>
            <button type="submit" class="filter-btn">
                <i class="fas fa-filter"></i> Lọc
            </button>
        </form>
    </div>

    {{-- Thống kê tổng quan --}}
    @if($overallStats)
    <div class="stats-grid">
        <div class="stat-card total">
            <i class="fas fa-shopping-cart"></i>
            <h3>{{ number_format($overallStats->total_orders ?? 0) }}</h3>
            <p>Tổng đơn hàng</p>
        </div>
        <div class="stat-card delivered">
            <i class="fas fa-check-circle"></i>
            <h3>{{ number_format($overallStats->delivered_orders ?? 0) }}</h3>
            <p>Đã giao hàng</p>
        </div>
        <div class="stat-card cancelled">
            <i class="fas fa-times-circle"></i>
            <h3>{{ number_format($overallStats->cancelled_orders ?? 0) }}</h3>
            <p>Đã hủy</p>
        </div>
        <div class="stat-card revenue">
            <i class="fas fa-dollar-sign"></i>
            <h3>{{ number_format($overallStats->total_revenue ?? 0, 0, ',', '.') }}đ</h3>
            <p>Tổng doanh thu</p>
        </div>
        <div class="stat-card avg">
            <i class="fas fa-calculator"></i>
            <h3>{{ number_format($overallStats->avg_order_value ?? 0, 0, ',', '.') }}đ</h3>
            <p>Giá trị TB/Đơn</p>
        </div>
    </div>
    @endif

    {{-- Thống kê theo trạng thái --}}
    <div class="report-section">
        <h3 class="section-title">
            <i class="fas fa-list-alt"></i>
            Thống kê theo Trạng thái Đơn hàng
        </h3>
        @if($statusStats && count($statusStats) > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Trạng thái</th>
                        <th>Số lượng</th>
                        <th>Tỷ lệ</th>
                        <th>Doanh thu</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $statusLabels = [
                            'pending'    => 'Chờ xác nhận',
                            'confirmed'  => 'Đã xác nhận',
                            'processing' => 'Đang xử lý',
                            'shipped'    => 'Đang giao',
                            'delivered'  => 'Đã giao',
                            'cancelled'  => 'Đã hủy',
                        ];
                    @endphp
                    @foreach($statusStats as $status)
                    <tr>
                        <td>
                            <span class="status-badge status-{{ $status->order_status }}">
                                {{ $statusLabels[$status->order_status] ?? $status->order_status }}
                            </span>
                        </td>
                        <td><strong>{{ number_format($status->cnt) }}</strong></td>
                        <td>
                            {{ $status->percentage }}%
                            <div class="percentage-bar">
                                <div class="percentage-fill" style="width: {{ $status->percentage }}%"></div>
                            </div>
                        </td>
                        <td>{{ number_format($status->revenue ?? 0, 0, ',', '.') }}đ</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                <i class="fas fa-chart-bar"></i>
                <p>Không có dữ liệu trong khoảng thời gian được chọn</p>
            </div>
        @endif
    </div>

    {{-- Thống kê theo khu vực --}}
    <div class="report-section">
        <h3 class="section-title">
            <i class="fas fa-map-marker-alt"></i>
            Thống kê theo Khu vực Địa lý
        </h3>
        @if($regionStats && count($regionStats) > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tỉnh/Thành phố</th>
                        <th>Số đơn hàng</th>
                        <th>Doanh thu</th>
                        <th>Giá trị TB/Đơn</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($regionStats as $region)
                    <tr>
                        <td>
                            <i class="fas fa-map-pin" style="color:#667eea;margin-right:5px"></i>
                            {{ $region->province_name ?? 'Không xác định' }}
                        </td>
                        <td><strong>{{ number_format($region->order_count) }}</strong></td>
                        <td>{{ number_format($region->revenue ?? 0, 0, ',', '.') }}đ</td>
                        <td>{{ number_format($region->avg_order_value ?? 0, 0, ',', '.') }}đ</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                <i class="fas fa-map"></i>
                <p>Không có dữ liệu khu vực trong khoảng thời gian được chọn</p>
            </div>
        @endif
    </div>

    {{-- Tỷ lệ thành công / thất bại --}}
    @if($successRateStats)
    <div class="report-section">
        <h3 class="section-title">
            <i class="fas fa-percentage"></i>
            Tỷ lệ Thành công - Thất bại
        </h3>
        @php
            $totalCount   = $successRateStats->total_count   ?? 1;
            $successCount = $successRateStats->success_count ?? 0;
            $failedCount  = $successRateStats->failed_count  ?? 0;
            $successRate  = $totalCount > 0 ? round(($successCount / $totalCount) * 100, 1) : 0;
            $failedRate   = $totalCount > 0 ? round(($failedCount  / $totalCount) * 100, 1) : 0;
        @endphp
        <div class="success-rate">
            <div class="rate-item">
                <div class="rate-circle rate-success">{{ $successRate }}%</div>
                <h4>Thành công</h4>
                <p>{{ number_format($successCount) }} đơn hàng</p>
            </div>
            <div class="rate-item">
                <div class="rate-circle rate-failed">{{ $failedRate }}%</div>
                <h4>Thất bại</h4>
                <p>{{ number_format($failedCount) }} đơn hàng</p>
            </div>
            <div class="rate-item">
                <div style="text-align:center">
                    <h2 style="color:#333;margin-bottom:10px">{{ number_format($totalCount) }}</h2>
                    <h4>Tổng đơn hàng</h4>
                    <p>Trong khoảng thời gian được chọn</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Xu hướng theo thời gian --}}
    @if($trendStats && count($trendStats) > 0)
    <div class="report-section">
        <h3 class="section-title">
            <i class="fas fa-chart-line"></i>
            Xu hướng Đơn hàng theo Thời gian
        </h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Thời gian</th>
                    <th>Số đơn hàng</th>
                    <th>Doanh thu</th>
                    <th>Tăng trưởng</th>
                </tr>
            </thead>
            <tbody>
                @php $previousRevenue = 0; @endphp
                @foreach($trendStats as $i => $trend)
                @php
                    $growth = ($i > 0 && $previousRevenue > 0)
                        ? round((($trend->revenue - $previousRevenue) / $previousRevenue) * 100, 1)
                        : 0;
                    $previousRevenue = $trend->revenue;
                @endphp
                <tr>
                    <td>
                        <i class="fas fa-calendar" style="color:#667eea;margin-right:5px"></i>
                        {{ $trend->period }}
                    </td>
                    <td><strong>{{ number_format($trend->order_count) }}</strong></td>
                    <td>{{ number_format($trend->revenue ?? 0, 0, ',', '.') }}đ</td>
                    <td>
                        @if($growth > 0)
                            <span style="color:#28a745"><i class="fas fa-arrow-up"></i> +{{ $growth }}%</span>
                        @elseif($growth < 0)
                            <span style="color:#dc3545"><i class="fas fa-arrow-down"></i> {{ $growth }}%</span>
                        @else
                            <span style="color:#6c757d">-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>
@endsection
