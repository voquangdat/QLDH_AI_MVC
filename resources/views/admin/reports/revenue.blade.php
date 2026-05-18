@extends('admin.layouts.app')

@section('title', 'Báo cáo Doanh thu')
@section('page-title', 'Báo cáo Doanh thu')


@section('content')
@php $revenueGrowth = 0; @endphp
<div class="reports-container">

    <a href="{{ route('admin.reports.index') }}" class="back-to-reports">
        <i class="fas fa-arrow-left"></i> Quay lại Báo cáo
    </a>

    <div class="reports-header">
        <h2><i class="fas fa-dollar-sign"></i> Báo cáo Doanh thu Chi tiết</h2>
        <p>Phân tích doanh thu tổng thể, xu hướng và hiệu suất kinh doanh</p>
    </div>

    {{-- Bộ lọc --}}
    <div class="filter-section">
        <form method="GET" action="{{ route('admin.reports.revenue') }}" class="filter-form">
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
            <button type="submit" class="filter-btn blue">
                <i class="fas fa-filter"></i> Lọc
            </button>
        </form>
    </div>

    {{-- Thống kê doanh thu tổng quan --}}
    @if($revenueStats)
    <div class="stats-grid">
        <div class="stat-card revenue">
            <i class="fas fa-dollar-sign"></i>
            <h3>{{ number_format($revenueStats->total_revenue ?? 0, 0, ',', '.') }}đ</h3>
            <p>Tổng doanh thu</p>
        </div>
        <div class="stat-card orders">
            <i class="fas fa-shopping-cart"></i>
            <h3>{{ number_format($revenueStats->total_orders ?? 0) }}</h3>
            <p>Tổng đơn hàng</p>
        </div>
        <div class="stat-card avg">
            <i class="fas fa-calculator"></i>
            <h3>{{ number_format($revenueStats->avg_order_value ?? 0, 0, ',', '.') }}đ</h3>
            <p>Giá trị TB/Đơn</p>
        </div>
        <div class="stat-card max">
            <i class="fas fa-arrow-up"></i>
            <h3>{{ number_format($revenueStats->max_order_value ?? 0, 0, ',', '.') }}đ</h3>
            <p>Đơn hàng cao nhất</p>
        </div>
        <div class="stat-card min">
            <i class="fas fa-arrow-down"></i>
            <h3>{{ number_format($revenueStats->min_order_value ?? 0, 0, ',', '.') }}đ</h3>
            <p>Đơn hàng thấp nhất</p>
        </div>
    </div>
    @endif

    {{-- So sánh với kỳ trước --}}
    @if($comparison && count($comparison) >= 2)
    <div class="report-section">
        <h3 class="section-title blue">
            <i class="fas fa-balance-scale"></i>
            So sánh với Kỳ trước
        </h3>
        @php
            $current  = collect($comparison)->firstWhere('period', 'current');
            $previous = collect($comparison)->firstWhere('period', 'previous');

            $revenueGrowth = ($previous && $previous->revenue > 0)
                ? round((($current->revenue - $previous->revenue) / $previous->revenue) * 100, 1)
                : 0;
            $orderGrowth = ($previous && $previous->orders > 0)
                ? round((($current->orders - $previous->orders) / $previous->orders) * 100, 1)
                : 0;
        @endphp
        <div class="comparison-card">
            <div class="comparison-item current">
                <h4>Kỳ hiện tại</h4>
                <div class="comparison-value">
                    {{ number_format($current->revenue ?? 0, 0, ',', '.') }}đ
                </div>
                <p>{{ number_format($current->orders ?? 0) }} đơn hàng</p>
                <div class="growth-indicator">
                    @if($revenueGrowth > 0)
                        <span class="growth-up"><i class="fas fa-arrow-up"></i> +{{ $revenueGrowth }}%</span>
                    @elseif($revenueGrowth < 0)
                        <span class="growth-down"><i class="fas fa-arrow-down"></i> {{ $revenueGrowth }}%</span>
                    @else
                        <span class="growth-neutral"><i class="fas fa-minus"></i> 0%</span>
                    @endif
                </div>
            </div>
            <div class="comparison-item previous">
                <h4>Kỳ trước</h4>
                <div class="comparison-value">
                    {{ number_format($previous->revenue ?? 0, 0, ',', '.') }}đ
                </div>
                <p>{{ number_format($previous->orders ?? 0) }} đơn hàng</p>
                <div style="margin-top:15px;color:rgba(255,255,255,0.8)">
                    <i class="fas fa-info-circle"></i> Tham chiếu
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Doanh thu theo phương thức thanh toán --}}
    <div class="report-section">
        <h3 class="section-title blue">
            <i class="fas fa-credit-card"></i>
            Doanh thu theo Phương thức Thanh toán
        </h3>
        @if($paymentMethodStats && count($paymentMethodStats) > 0)
            @php
                $methodLabels = [
                    'cod'          => 'COD',
                    'bank_transfer'=> 'Chuyển khoản',
                    'momo'         => 'MoMo',
                    'vnpay'        => 'VNPay',
                    'zalopay'      => 'ZaloPay',
                ];
            @endphp
            <div class="payment-method-grid">
                @foreach($paymentMethodStats as $method)
                <div class="payment-method-item">
                    <h4>{{ $methodLabels[$method->payment_method] ?? $method->payment_method }}</h4>
                    <div style="font-size:1.8rem;font-weight:bold;color:#4facfe;margin:15px 0">
                        {{ number_format($method->revenue ?? 0, 0, ',', '.') }}đ
                    </div>
                    <div class="payment-stats">
                        <div>
                            <span>Đơn hàng:</span><br>
                            <strong>{{ number_format($method->order_count) }}</strong>
                        </div>
                        <div>
                            <span>TB/Đơn:</span><br>
                            <strong>{{ number_format($method->avg_order_value ?? 0, 0, ',', '.') }}đ</strong>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="no-data">
                <i class="fas fa-credit-card"></i>
                <p>Không có dữ liệu phương thức thanh toán trong khoảng thời gian được chọn</p>
            </div>
        @endif
    </div>

    {{-- Xu hướng doanh thu theo thời gian --}}
    <div class="report-section">
        <h3 class="section-title blue">
            <i class="fas fa-chart-line"></i>
            Xu hướng Doanh thu theo Thời gian
        </h3>
        @if($revenueTrend && count($revenueTrend) > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Thời gian</th>
                        <th>Doanh thu</th>
                        <th>Số đơn hàng</th>
                        <th>Giá trị TB/Đơn</th>
                        <th>Xu hướng</th>
                    </tr>
                </thead>
                <tbody>
                    @php $prevRevenue = 0; @endphp
                    @foreach($revenueTrend as $i => $trend)
                    @php
                        $growth = ($i > 0 && $prevRevenue > 0)
                            ? round((($trend->revenue - $prevRevenue) / $prevRevenue) * 100, 1)
                            : 0;
                        $prevRevenue = $trend->revenue;
                    @endphp
                    <tr>
                        <td>
                            <i class="fas fa-calendar" style="color:#4facfe;margin-right:5px"></i>
                            {{ $trend->period }}
                        </td>
                        <td><strong>{{ number_format($trend->revenue ?? 0, 0, ',', '.') }}đ</strong></td>
                        <td>{{ number_format($trend->order_count) }}</td>
                        <td>{{ number_format($trend->avg_order_value ?? 0, 0, ',', '.') }}đ</td>
                        <td>
                            @if($growth > 0)
                                <span class="trend-indicator trend-up">
                                    <i class="fas fa-arrow-up"></i> +{{ $growth }}%
                                </span>
                            @elseif($growth < 0)
                                <span class="trend-indicator trend-down">
                                    <i class="fas fa-arrow-down"></i> {{ $growth }}%
                                </span>
                            @else
                                <span class="trend-indicator trend-neutral">
                                    <i class="fas fa-minus"></i> -
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                <i class="fas fa-chart-line"></i>
                <p>Không có dữ liệu xu hướng trong khoảng thời gian được chọn</p>
            </div>
        @endif
    </div>

    {{-- Tóm tắt báo cáo --}}
    <div class="report-section">
        <h3 class="section-title blue">
            <i class="fas fa-clipboard-list"></i>
            Tóm tắt Báo cáo
        </h3>
        <div style="background:#f8f9fa;padding:20px;border-radius:10px;color:#333">
            <h4 style="margin-bottom:15px;color:#4facfe">
                <i class="fas fa-info-circle"></i> Kết quả Phân tích
            </h4>
            <ul style="list-style:none;padding:0">
                <li style="margin-bottom:10px">
                    <i class="fas fa-check" style="color:#28a745;margin-right:10px"></i>
                    Khoảng thời gian: <strong>{{ $dateFrom }}</strong> đến <strong>{{ $dateTo }}</strong>
                </li>
                @if($revenueStats)
                <li style="margin-bottom:10px">
                    <i class="fas fa-check" style="color:#28a745;margin-right:10px"></i>
                    Tổng doanh thu đạt được:
                    <strong>{{ number_format($revenueStats->total_revenue ?? 0, 0, ',', '.') }}đ</strong>
                </li>
                <li style="margin-bottom:10px">
                    <i class="fas fa-check" style="color:#28a745;margin-right:10px"></i>
                    Tổng số đơn hàng:
                    <strong>{{ number_format($revenueStats->total_orders ?? 0) }}</strong> đơn
                </li>
                <li style="margin-bottom:10px">
                    <i class="fas fa-check" style="color:#28a745;margin-right:10px"></i>
                    Giá trị trung bình mỗi đơn hàng:
                    <strong>{{ number_format($revenueStats->avg_order_value ?? 0, 0, ',', '.') }}đ</strong>
                </li>
                @endif
                @if($comparison && count($comparison) >= 2)
                <li style="margin-bottom:10px">
                    @php
                        $growthColor = $revenueGrowth > 0 ? '#28a745' : ($revenueGrowth < 0 ? '#dc3545' : '#6c757d');
                        $growthIcon  = $revenueGrowth > 0 ? 'arrow-up' : ($revenueGrowth < 0 ? 'arrow-down' : 'minus');
                    @endphp
                    <i class="fas fa-{{ $growthIcon }}" style="color:{{ $growthColor }};margin-right:10px"></i>
                    So với kỳ trước:
                    <strong style="color:{{ $growthColor }}">
                        {{ $revenueGrowth > 0 ? '+' : '' }}{{ $revenueGrowth }}%
                    </strong>
                </li>
                @endif
            </ul>
        </div>
    </div>

</div>
@endsection
