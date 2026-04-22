<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }
        $user = Auth::user();
        if (!$user->isAdmin()) {
            Auth::logout();
            return redirect()->route('admin.login')
                ->withErrors(['email' => 'Bạn không có quyền truy cập.']);
        }

        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('admin.login')
                ->withErrors(['email' => 'Tài khoản đã bị vô hiệu hóa.']);
        }

        return $next($request);
    }
}