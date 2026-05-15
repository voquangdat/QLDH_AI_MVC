<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - VoxFootball</title>
    <link rel="stylesheet" href="{{ asset('assets/clients/css/admin-dashboard.css') }}">
    <script src="https://kit.fontawesome.com/54f0cb7e4a.js" crossorigin="anonymous"></script>
</head>
<body>

    {{-- Header --}}
    <div class="admin-header">
        <div class="container">
            <h1><i class="fas fa-tachometer-alt"></i> @yield('page-title', 'Admin Panel')</h1>
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

    {{-- Content --}}
    @yield('content')

</body>
</html>
