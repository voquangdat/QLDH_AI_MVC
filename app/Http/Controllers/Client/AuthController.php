<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ===== ĐĂNG NHẬP =====
    public function showLogin()
    {
        // if (Auth::check() && !Auth::user()->isAdmin()) {
        //     return redirect()->route('home');
        // }
        return view('clients.page.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required'    => 'Vui lòng nhập email.',
            'email.email'       => 'Email không hợp lệ.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min'      => 'Mật khẩu tối thiểu 6 ký tự.',
        ]);

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Chặn tài khoản admin đăng nhập vào trang user
            // if ($user->isAdmin()) {
            //     Auth::logout();
            //     return back()->withErrors([
            //         'email' => 'Tài khoản admin vui lòng đăng nhập tại trang quản trị.',
            //     ])->onlyInput('email');
            // }

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Tài khoản đã bị vô hiệu hóa.']);
            }

            $request->session()->regenerate();
            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không đúng.',
        ])->onlyInput('email');
    }

    // ===== ĐĂNG KÝ =====
    public function showRegister()
    {
        // if (Auth::check() && !Auth::user()->isAdmin()) {
        //     return redirect()->route('home');
        // }
        return view('clients.page.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'fullname'              => 'required|string|max:100',
            'email'                 => 'required|email|unique:users,email',
            'phone'                 => 'required|string|max:15',
            'password'              => 'required|min:6|confirmed',
        ], [
            'fullname.required'             => 'Vui lòng nhập họ tên.',
            'email.required'                => 'Vui lòng nhập email.',
            'email.email'                   => 'Email không hợp lệ.',
            'email.unique'                  => 'Email đã được sử dụng.',
            'phone.required'                => 'Vui lòng nhập số điện thoại.',
            'password.required'             => 'Vui lòng nhập mật khẩu.',
            'password.min'                  => 'Mật khẩu tối thiểu 6 ký tự.',
            'password.confirmed'            => 'Mật khẩu xác nhận không khớp.',
        ]);

        $user = User::create([
            'fullname'  => $request->fullname,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'password'  => Hash::make($request->password),
            'role_id'   => 3, // customer
            'is_active' => true,
        ]);

        Auth::login($user);

        return redirect()->route('home');
    }

    // ===== ĐĂNG XUẤT =====
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}