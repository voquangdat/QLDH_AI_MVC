@extends('admin.layouts.app')

@section('title', 'Báo cáo Thống kê')
@section('page-title', 'Báo cáo Thống kê')


@section('content')
<div class="reports-container">

    <a href="{{ route('admin.dashboard') }}" class="back-to-dashboard">
        <i class="fas fa-arrow-left"></i> Quay lại Dashboard
    </a>

    <div class="reports-header">
        <h2>Hệ thống Báo cáo Thống kê</h2>
        <p>Phân tích chi tiết dữ liệu kinh doanh và vận hành</p>
    </div>

    <div class="reports-nav">
        <a href="{{ route('admin.reports.orders') }}" class="report-card orders">
            <i class="fas fa-shopping-cart"></i>
            <h3>Báo cáo Đơn hàng</h3>
            <p>Thống kê tổng quan, trạng thái đơn hàng, phân tích theo khu vực địa lý và tỷ lệ thành công</p>
        </a>

        <a href="{{ route('admin.reports.products') }}" class="report-card products">
            <i class="fas fa-box-open"></i>
            <h3>Báo cáo Sản phẩm & Tồn kho</h3>
            <p>Sản phẩm bán chạy, tình trạng tồn kho, cảnh báo hết hàng và phân tích danh mục</p>
        </a>

        <a href="{{ route('admin.reports.revenue') }}" class="report-card revenue">
            <i class="fas fa-chart-bar"></i>
            <h3>Báo cáo Doanh thu</h3>
            <p>Doanh thu tổng thể theo thời gian, phương thức thanh toán và so sánh với kỳ trước</p>
        </a>
    </div>

    <div class="reports-header">
        <h3>Tính năng Báo cáo</h3>
    </div>

    <div class="features-grid">
        <div class="feature-item">
            <i class="fas fa-calendar-alt"></i>
            <h4>Lọc theo Thời gian</h4>
            <p>Chọn khoảng thời gian tùy chỉnh để phân tích dữ liệu</p>
        </div>
        <div class="feature-item">
            <i class="fas fa-chart-pie"></i>
            <h4>Biểu Đồ Trực quan</h4>
            <p>Hiển thị dữ liệu dưới dạng bảng và biểu đồ dễ hiểu</p>
        </div>
        <div class="feature-item">
            <i class="fas fa-map-marker-alt"></i>
            <h4>Phân tích Địa lý</h4>
            <p>Thống kê đơn hàng theo tỉnh thành và khu vực</p>
        </div>
        <div class="feature-item">
            <i class="fas fa-percentage"></i>
            <h4>Tỷ lệ Thành công</h4>
            <p>Phân tích tỷ lệ đơn hàng thành công và thất bại</p>
        </div>
        <div class="feature-item">
            <i class="fas fa-trophy"></i>
            <h4>Top Sản phẩm</h4>
            <p>Xếp hạng sản phẩm bán chạy nhất theo doanh số</p>
        </div>
        <div class="feature-item">
            <i class="fas fa-exclamation-triangle"></i>
            <h4>Cảnh báo Tồn kho</h4>
            <p>Theo dõi sản phẩm sắp hết hàng và cần nhập thêm</p>
        </div>
    </div>

</div>
@endsection
