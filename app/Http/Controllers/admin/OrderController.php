<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    /**
     * Danh sách đơn hàng với filter + phân trang + thống kê.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['order_status', 'payment_status', 'search', 'date_from', 'date_to']);

        $orders = Order::with(['payments'])
            ->when($filters['order_status'] ?? null, fn($q, $v) => $q->where('order_status', $v))
            ->when($filters['payment_status'] ?? null, fn($q, $v) => $q->where('payment_status', $v))
            ->when($filters['search'] ?? null, fn($q, $v) =>
                $q->where(fn($sub) =>
                    $sub->where('id', 'like', "%{$v}%")
                        ->orWhere('order_number', 'like', "%{$v}%")
                        ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(notes, '$.name')) LIKE ?", ["%{$v}%"])
                        ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(notes, '$.phone')) LIKE ?", ["%{$v}%"])
                )
            )
            ->when($filters['date_from'] ?? null, fn($q, $v) => $q->whereDate('order_date', '>=', $v))
            ->when($filters['date_to'] ?? null,   fn($q, $v) => $q->whereDate('order_date', '<=', $v))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $statistics = $this->orderService->getOrderStatistics();

        return view('admin.orders.index', compact('orders', 'filters', 'statistics'));
    }

    /**
     * Chi tiết đơn hàng kèm kiểm tra tồn kho.
     */
    public function show(int $id)
    {
        $order = Order::with([
            'orderDetails.variant.color',
            'orderDetails.variant.size',
            'orderDetails.product.images',
            'payments',
            'statusHistories',
        ])->findOrFail($id);

        $stockCheck = $this->orderService->checkStockAvailability($id);

        return view('admin.orders.show', compact('order', 'stockCheck'));
    }

    /**
     * Xác nhận đơn hàng (pending → confirmed), kiểm tra & giữ kho.
     */
    public function confirm(Request $request, int $id)
    {
        $result = $this->orderService->confirmOrder($id, Auth::id());

        return redirect()->route('admin.orders.show', $id)
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Chuyển sang xử lý (confirmed → processing).
     */
    public function process(Request $request, int $id)
    {
        $result = $this->orderService->processOrder($id, Auth::id());

        return redirect()->route('admin.orders.show', $id)
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Hoàn tất giao hàng (confirmed/processing → delivered), xuất kho.
     */
    public function deliver(Request $request, int $id)
    {
        $result = $this->orderService->deliverOrder($id, Auth::id());

        return redirect()->route('admin.orders.show', $id)
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Hủy đơn hàng với lý do.
     */
    public function cancel(Request $request, int $id)
    {
        $request->validate([
            'cancel_note' => 'nullable|string|max:500',
        ]);

        $result = $this->orderService->cancelOrder($id, $request->cancel_note ?? '', Auth::id());

        return redirect()->route('admin.orders.show', $id)
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Polling API — trả về đơn hàng mới kể từ timestamp.
     * JS admin gọi mỗi 30 giây: GET /admin/orders/check-new?since=1234567890
     */
    public function checkNew(Request $request)
    {
        $since = $request->query('since');

        $query = Order::select('id', 'order_number', 'order_status', 'total_amount', 'notes', 'created_at')
            ->orderByDesc('created_at');

        if ($since) {
            $query->where('created_at', '>', \Carbon\Carbon::createFromTimestamp($since));
        } else {
            $query->where('created_at', '>=', now()->subSeconds(30));
        }

        $newOrders = $query->get()->map(function ($order) {
            $info = $order->deliveryInfo();
            return [
                'id'           => $order->id,
                'order_number' => $order->order_number,
                'order_status' => $order->order_status,
                'total_amount' => number_format($order->total_amount) . 'đ',
                'customer'     => $info['name']  ?? 'Khách',
                'phone'        => $info['phone'] ?? '',
                'created_at'   => $order->created_at->format('d/m/Y H:i'),
                'url'          => route('admin.orders.show', $order->id),
            ];
        });

        return response()->json([
            'count'      => $newOrders->count(),
            'orders'     => $newOrders,
            'server_time' => now()->timestamp,
        ]);
    }

    /**
     * Cập nhật trạng thái thanh toán thủ công.
     */
    public function updatePayment(Request $request, int $id)
    {
        $request->validate([
            'payment_status' => 'required|in:unpaid,paid,refunded',
        ]);

        $order = Order::findOrFail($id);
        $order->update(['payment_status' => $request->payment_status]);

        return redirect()->route('admin.orders.show', $id)
            ->with('success', 'Cập nhật trạng thái thanh toán thành công');
    }
}
