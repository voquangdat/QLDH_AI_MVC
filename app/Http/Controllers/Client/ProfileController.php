<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    // ===== TRANG THÔNG TIN CÁ NHÂN =====

    public function show()
    {
        $user = Auth::user();
        return view('clients.page.profile', compact('user'));
    }

    // ===== CẬP NHẬT THÔNG TIN =====

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'fullname' => 'required|string|max:100',
            'phone'    => 'nullable|string|max:15',
        ], [
            'fullname.required' => 'Vui lòng nhập họ tên.',
            'fullname.max'      => 'Họ tên tối đa 100 ký tự.',
            'phone.max'         => 'Số điện thoại tối đa 15 ký tự.',
        ]);

        $user->update([
            'fullname' => $request->fullname,
            'phone'    => $request->phone,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thông tin thành công!',
        ]);
    }

    // ===== ĐỔI MẬT KHẨU =====

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => ['required', 'confirmed', Password::min(6)],
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'new_password.required'     => 'Vui lòng nhập mật khẩu mới.',
            'new_password.confirmed'    => 'Xác nhận mật khẩu không khớp.',
            'new_password.min'          => 'Mật khẩu mới tối thiểu 6 ký tự.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Mật khẩu hiện tại không đúng.',
            ], 422);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json([
            'success' => true,
            'message' => 'Đổi mật khẩu thành công!',
        ]);
    }

    // ===== DANH SÁCH ĐƠN HÀNG =====

    public function orders()
    {
        $user   = Auth::user();
        $orders = $this->orderService->getOrdersByUser($user->user_id, 10);

        return view('clients.page.orders', compact('user', 'orders'));
    }

    // ===== HỦY ĐƠN HÀNG =====

    public function cancelOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'reason'   => 'nullable|string|max:500',
        ]);

        $result = $this->orderService->cancelOrderByCustomer(
            $request->order_id,
            Auth::id(),
            $request->reason ?? ''
        );

        return response()->json(
            $result
                ? ['success' => true,  'message' => 'Hủy đơn hàng thành công!']
                : ['success' => false, 'message' => 'Không thể hủy đơn hàng này.']
        );
    }
}
