<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Quản trị</title>
    <link rel="stylesheet" href="{{ asset('assets/clients/css/admin-login.css') }}">
    <script src="https://kit.fontawesome.com/54f0cb7e4a.js" crossorigin="anonymous"></script>
</head>
<body class="admin-login-body">

    <div class="admin-login-container">
        <div class="admin-login-box">

            {{-- Header --}}
            <div class="admin-login-header">
                <div class="admin-logo">
                    <img src="{{ asset('assets/clients/image/logoShop.png') }}" alt="VoxFootball" height="50">
                </div>
                <h1>Quản trị hệ thống</h1>
                <p>Đăng nhập vào trang quản trị VoxFootball</p>
                <div class="security-notice">
                    <i class="fas fa-shield-alt"></i> Khu vực bảo mật - Chỉ dành cho quản trị viên
                </div>
            </div>

            {{-- Hiển thị lỗi --}}
            @if ($errors->any())
                <div class="notification error show" style="position:static; transform:none; margin-bottom:15px; opacity:1;">
                    <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('admin.login.post') }}" class="admin-login-form" autocomplete="off">
                @csrf

                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="Nhập email admin"
                               autocomplete="off"
                               required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password"
                               id="password"
                               name="password"
                               placeholder="Nhập mật khẩu"
                               autocomplete="new-password"
                               required>
                        <span class="toggle-password" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="remember"> Ghi nhớ đăng nhập
                    </label>
                </div>

                <button type="submit" id="btnLogin" class="admin-login-btn">
                    <i class="fas fa-sign-in-alt"></i> Đăng nhập
                </button>
            </form>

            {{-- Footer --}}
            <div class="admin-login-footer">
                <p><a href="{{ route('home') }}">← Về trang chủ</a></p>
                <p class="admin-note">
                    <i class="fas fa-shield-alt"></i>
                    Khu vực dành riêng cho quản trị viên
                </p>
            </div>

        </div>
    </div>

    <script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon  = document.getElementById('toggleIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    document.getElementById('btnLogin').addEventListener('click', function() {
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner"></i> Đang đăng nhập...';
        this.closest('form').submit();
    });
    </script>

</body>
</html>
