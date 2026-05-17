<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - VoxFootball</title>
    <link rel="stylesheet" href="{{ asset('assets/clients/css/admin-dashboard.css') }}">
    <script src="https://kit.fontawesome.com/54f0cb7e4a.js" crossorigin="anonymous"></script>
</head>
<body>

    {{-- Header --}}
    <div class="admin-header">
        <div class="container">
            <h1><i class="fas fa-tachometer-alt"></i> Dashboard Quản Trị</h1>
            <div class="admin-user-info">
                <span>Xin chào, <strong>{{ auth('admin')->user()->fullname }}</strong></span>
                <form method="POST" action="{{ route('admin.logout') }}" style="margin:0;">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Đăng xuất
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="admin-container">

        {{-- Welcome --}}
        <div class="welcome-section">
            <h2>Chào mừng đến với hệ thống quản trị VoxFootball</h2>
            <p>Sử dụng các chức năng bên dưới để quản lý website của bạn</p>
        </div>

        {{-- Stats Cards --}}
        <div class="admin-grid">
            <div class="admin-card" style="cursor: pointer;" onclick="window.location='{{ route('admin.products.index') }}'">
                <a>
                    <i class="fas fa-box"></i>
                    <h3>Sản phẩm</h3>
                    <p>Quản lý danh mục sản phẩm</p>
                </a>
            </div>
            <div class="admin-card" style="cursor: pointer;" onclick="window.location='{{ route('admin.orders.index') }}'">
                <i class="fas fa-shopping-cart"></i>
                <h3>Đơn hàng</h3>
                <p>Theo dõi và xử lý đơn hàng</p>
            </div>
            <div class="admin-card">
                <i class="fas fa-chart-bar"></i>
                <h3>Báo cáo Thống kê</h3>
                <p>Doanh thu và phân tích kinh doanh</p>
            </div>
            <div class="admin-card" style="cursor: pointer;" onclick="window.location='{{ route('admin.inventory.index') }}'">
                <i class="fas fa-warehouse"></i>
                <h3>Tồn kho</h3>
                <p>Theo dõi và quản lý hàng tồn kho</p>
            </div>
        </div>

        {{-- Admin Menu --}}
        <div class="admin-menu">
            <h2>Menu Quản Trị</h2>
            <div class="menu-items">
                <a href="#" class="menu-item">
                    <i class="fas fa-users"></i>
                    <span>Quản lý Khách hàng</span>
                </a>
                <a href="{{ route('admin.products.list') }}" class="menu-item">
                    <i class="fas fa-box"></i>
                    <span>Quản lý Sản phẩm</span>
                </a>
                <a href="{{ route('admin.categories.index') }}" class="menu-item">
                    <i class="fas fa-tags"></i>
                    <span>Quản lý Danh mục</span>
                </a>
                <a href="{{ route('admin.inventory.index') }}" class="menu-item">
                    <i class="fas fa-warehouse"></i>
                    <span>Quản lý Tồn kho</span>
                </a>
                <a href="{{ route('admin.orders.index') }}" class="menu-item">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Quản lý Đơn hàng</span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-credit-card"></i>
                    <span>Quản lý Thanh toán</span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-newspaper"></i>
                    <span>Quản lý Tin tức</span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-comments"></i>
                    <span>Quản lý Bình luận</span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-cog"></i>
                    <span>Cài đặt Hệ thống</span>
                </a>
                <a href="{{ route('home') }}" target="_blank" class="menu-item">
                    <i class="fas fa-globe"></i>
                    <span>Xem Website</span>
                </a>
            </div>
        </div>

    </div>

</body>
</html>
