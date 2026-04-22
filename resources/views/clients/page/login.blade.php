@extends('layouts.app')

@section('title', 'Đăng nhập')

@section('content')
<div style="max-width:450px; margin:60px auto; padding:0 20px;">
    <div style="background:#fff; padding:40px; border-radius:12px; box-shadow:0 4px 20px rgba(0,0,0,0.1);">

        <h2 style="text-align:center; margin-bottom:30px;">Đăng nhập</h2>

        @if ($errors->any())
            <div style="background:#f8d7da; color:#721c24; padding:12px; border-radius:6px; margin-bottom:20px;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div style="margin-bottom:20px;">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       placeholder="Nhập email"
                       style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; margin-top:6px; box-sizing:border-box;"
                       required>
            </div>

            <div style="margin-bottom:20px;">
                <label>Mật khẩu</label>
                <input type="password" name="password"
                       placeholder="Nhập mật khẩu"
                       style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; margin-top:6px; box-sizing:border-box;"
                       required>
            </div>

            <div style="margin-bottom:20px;">
                <label>
                    <input type="checkbox" name="remember"> Ghi nhớ đăng nhập
                </label>
            </div>

            <button type="submit"
                    style="width:100%; padding:13px; background:#e74c3c; color:#fff; border:none; border-radius:8px; font-size:16px; cursor:pointer;">
                Đăng nhập
            </button>
        </form>

        <p style="text-align:center; margin-top:20px;">
            Chưa có tài khoản?
            <a href="{{ route('register') }}" style="color:#e74c3c;">Đăng ký ngay</a>
        </p>

    </div>
</div>
@endsection
