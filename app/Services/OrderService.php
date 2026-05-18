<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class OrderService
{
    // ==================== TẠO ĐƠN HÀNG ====================

    /**
     * Sinh mã đơn hàng duy nhất: VFB + ngày + microsecond + random.
     * Dùng microsecond thay vì max(id) để tránh race condition khi nhiều request cùng lúc.
     */
    private function generateOrderNumber(): string
    {
        [$sec, $usec] = explode(' ', microtime());
        $micro = substr(str_replace('0.', '', $usec . $sec), 0, 10);
        return 'VFB' . now()->format('ymd') . strtoupper(substr($micro, -5)) . random_int(10, 99);
    }

    /**
     * Tạo đơn hàng từ giỏ hàng.
     * Kiểm tra tồn kho ngay khi đặt để tránh oversell.
     *
     * @param  array       $deliveryData  Thông tin giao hàng (name, phone, province, district, ward, address)
     * @param  Collection  $cartItems     Danh sách sản phẩm trong giỏ (Cart hoặc GuestCartItem)
     * @param  int|null    $userId        ID người dùng (null nếu là khách)
     * @return array       ['success', 'order_id', 'order_number', 'message']
     */
    public function createOrder(array $deliveryData, Collection $cartItems, ?int $userId = null): array
    {
        if ($cartItems->isEmpty()) {
            return ['success' => false, 'message' => 'Giỏ hàng trống'];
        }

        $orderId = null;

        try {
            DB::transaction(function () use ($deliveryData, $cartItems, $userId, &$orderId) {

                // ── Kiểm tra tồn kho với lock để tránh oversell ──────────
                $outOfStock = [];

                foreach ($cartItems as $item) {
                    $inventory = Inventory::lockForUpdate()
                        ->where('variant_id', $item->variant_id)
                        ->first();

                    if (!$inventory) {
                        $outOfStock[] = "{$item->variant->product->product_name}: Không có thông tin tồn kho";
                        continue;
                    }

                    $available = $inventory->availableQuantity();

                    if ($available < $item->quantity) {
                        $productName = $item->variant->product->product_name;
                        $colorName   = $item->variant->color->color_ten  ?? '';
                        $sizeName    = $item->variant->size->product_size ?? '';
                        $label       = trim("{$productName} ({$colorName} / {$sizeName})");
                        $outOfStock[] = "{$label}: Cần {$item->quantity}, còn {$available}";
                    }
                }

                if (!empty($outOfStock)) {
                    throw new \RuntimeException(
                        'Một số sản phẩm không đủ hàng: ' . implode('; ', $outOfStock)
                    );
                }

                // ── Tạo đơn hàng ─────────────────────────────────────────
                $subtotal    = $cartItems->sum(fn($item) => $item->subtotal());
                $shippingFee = $subtotal >= 2_000_000 ? 0 : 30_000;
                $total       = $subtotal + $shippingFee;

                $deliveryInfo = json_encode([
                    'name'     => $deliveryData['name'],
                    'phone'    => $deliveryData['phone'],
                    'province' => $deliveryData['province'],
                    'district' => $deliveryData['district'],
                    'ward'     => $deliveryData['ward'],
                    'address'  => $deliveryData['address'],
                ], JSON_UNESCAPED_UNICODE);

                $order = Order::create([
                    'user_id'         => $userId,
                    'order_number'    => $this->generateOrderNumber(),
                    'order_date'      => now(),
                    'order_status'    => 'pending',
                    'payment_status'  => 'unpaid',
                    'subtotal'        => $subtotal,
                    'shipping_fee'    => $shippingFee,
                    'discount_amount' => 0,
                    'tax'             => 0,
                    'total_amount'    => $total,
                    'notes'           => $deliveryInfo,
                ]);

                foreach ($cartItems as $item) {
                    $product = $item->variant->product;
                    $image   = $product->images->first();

                    OrderDetail::create([
                        'order_id'     => $order->id,
                        'product_id'   => $product->product_id,
                        'variant_id'   => $item->variant_id,
                        'quantity'     => $item->quantity,
                        'product_name' => $product->product_name,
                        'product_gia'  => $product->product_gia,
                        'product_anh'  => $image->product_anh ?? null,
                        'subtotal'     => $item->subtotal(),
                    ]);
                }

                $orderId = $order->id;
            });
        } catch (\RuntimeException $e) {
            // Lỗi hết hàng — trả về message thân thiện
            return ['success' => false, 'message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Có lỗi xảy ra, vui lòng thử lại.'];
        }

        $order = Order::find($orderId);

        // Lưu session để guest có thể xem lại đơn hàng
        if ($userId === null) {
            Session::put('last_order_id', $orderId);
        }

        return [
            'success'      => true,
            'message'      => 'Đặt hàng thành công',
            'order_id'     => $orderId,
            'order_number' => $order->order_number,
        ];
    }

    // ==================== QUẢN LÝ TRẠNG THÁI (ADMIN) ====================

    /**
     * Xác nhận đơn hàng: kiểm tra tồn kho, chuyển sang 'confirmed'.
     * Không thay đổi inventory — chỉ kiểm tra soluong_co_the_ban đủ không.
     */
    public function confirmOrder(int $orderId, ?int $adminId = null): array
    {
        try {
            return DB::transaction(function () use ($orderId, $adminId) {
                $order = Order::lockForUpdate()->findOrFail($orderId);

                if ($order->order_status !== 'pending') {
                    return ['success' => false, 'message' => "Đơn hàng không ở trạng thái pending. Trạng thái hiện tại: {$order->order_status}"];
                }

                $details = OrderDetail::where('order_id', $orderId)->get();

                if ($details->isEmpty()) {
                    return ['success' => false, 'message' => 'Không tìm thấy chi tiết đơn hàng'];
                }

                // Kiểm tra tồn kho khả dụng
                $insufficient = [];
                foreach ($details as $detail) {
                    $inventory = Inventory::lockForUpdate()
                        ->where('variant_id', $detail->variant_id)
                        ->first();

                    if (!$inventory) {
                        $insufficient[] = "Variant #{$detail->variant_id}: Không có thông tin tồn kho";
                        continue;
                    }

                    if ($inventory->soluong_co_the_ban < $detail->quantity) {
                        $insufficient[] = "Variant #{$detail->variant_id}: Cần {$detail->quantity}, còn {$inventory->soluong_co_the_ban}";
                    }
                }

                if (!empty($insufficient)) {
                    return ['success' => false, 'message' => 'Không đủ hàng trong kho: ' . implode('; ', $insufficient)];
                }

                // Xác nhận: xuất hàng khỏi kho, ghi nhận đang đặt, giảm có thể bán
                foreach ($details as $detail) {
                    Inventory::where('variant_id', $detail->variant_id)
                        ->update([
                            'soluong_ton'        => DB::raw("GREATEST(soluong_ton - {$detail->quantity}, 0)"),
                            'soluong_dat'        => DB::raw("soluong_dat + {$detail->quantity}"),
                            'soluong_co_the_ban' => DB::raw("GREATEST(soluong_co_the_ban - {$detail->quantity}, 0)"),
                        ]);
                }

                $order->update(['order_status' => 'confirmed']);
                $this->recordStatusHistory($orderId, 'pending', 'confirmed', 'Xác nhận đơn hàng', $adminId);

                return ['success' => true, 'message' => 'Xác nhận đơn hàng thành công'];
            });
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Chuyển đơn hàng sang trạng thái 'processing'.
     */
    public function processOrder(int $orderId, ?int $adminId = null): array
    {
        try {
            return DB::transaction(function () use ($orderId, $adminId) {
                $order = Order::lockForUpdate()->findOrFail($orderId);

                if ($order->order_status !== 'confirmed') {
                    return ['success' => false, 'message' => 'Đơn hàng phải ở trạng thái confirmed'];
                }

                $order->update(['order_status' => 'processing']);
                $this->recordStatusHistory($orderId, 'confirmed', 'processing', 'Bắt đầu xử lý đơn hàng', $adminId);

                return ['success' => true, 'message' => 'Chuyển sang trạng thái xử lý thành công'];
            });
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Hoàn tất giao hàng: tăng soluong_dat theo số lượng đã giao, chuyển sang 'delivered'.
     * soluong_ton không thay đổi — admin tự quản lý khi nhập hàng mới.
     */
    public function deliverOrder(int $orderId, ?int $adminId = null): array
    {
        try {
            return DB::transaction(function () use ($orderId, $adminId) {
                $order = Order::lockForUpdate()->findOrFail($orderId);

                if (!in_array($order->order_status, ['confirmed', 'processing'])) {
                    return ['success' => false, 'message' => 'Đơn hàng phải ở trạng thái confirmed hoặc processing'];
                }

                $oldStatus = $order->order_status;
                $details   = OrderDetail::where('order_id', $orderId)->get();

                // soluong_dat giữ nguyên khi delivered — phản ánh tổng đã xác nhận
                // soluong_ton và soluong_co_the_ban đã trừ từ bước confirm

                $order->update([
                    'order_status'   => 'delivered',
                    'payment_status' => 'paid',
                ]);
                $this->recordStatusHistory($orderId, $oldStatus, 'delivered', 'Hoàn tất giao hàng', $adminId);

                $this->completeCodPayment($orderId);

                return ['success' => true, 'message' => 'Hoàn tất đơn hàng thành công'];
            });
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Hủy đơn hàng (admin): giải phóng tồn kho nếu cần, chuyển sang 'cancelled'.
     */
    public function cancelOrder(int $orderId, string $note = '', ?int $adminId = null): array
    {
        try {
            return DB::transaction(function () use ($orderId, $note, $adminId) {
                $order = Order::lockForUpdate()->findOrFail($orderId);

                if ($order->order_status === 'delivered') {
                    return ['success' => false, 'message' => 'Không thể huỷ đơn hàng đã giao. Vui lòng tạo đơn đổi trả.'];
                }

                if (!in_array($order->order_status, ['pending', 'confirmed', 'processing'])) {
                    return ['success' => false, 'message' => "Không thể huỷ đơn hàng với trạng thái: {$order->order_status}"];
                }

                $oldStatus = $order->order_status;

                // Hoàn lại tồn kho nếu đơn đã được confirmed/processing
                if (in_array($oldStatus, ['confirmed', 'processing'])) {
                    $details = OrderDetail::where('order_id', $orderId)->get();
                    foreach ($details as $detail) {
                        Inventory::where('variant_id', $detail->variant_id)
                            ->update([
                                'soluong_ton'        => DB::raw("soluong_ton + {$detail->quantity}"),
                                'soluong_dat'        => DB::raw("GREATEST(soluong_dat - {$detail->quantity}, 0)"),
                                'soluong_co_the_ban' => DB::raw("soluong_co_the_ban + {$detail->quantity}"),
                            ]);
                    }
                }

                $order->update(['order_status' => 'cancelled']);

                $historyNote = 'Huỷ đơn hàng' . ($note ? ': ' . $note : '');
                $this->recordStatusHistory($orderId, $oldStatus, 'cancelled', $historyNote, $adminId);

                return ['success' => true, 'message' => 'Huỷ đơn hàng thành công'];
            });
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // ==================== PHẦN KHÁCH HÀNG ====================

    /**
     * Hủy đơn hàng bởi khách hàng (chỉ cho phép khi pending hoặc confirmed).
     */
    public function cancelOrderByCustomer(int $orderId, int $userId, string $reason = ''): bool
    {
        if (!$this->canCancelOrder($orderId, $userId)) {
            return false;
        }

        return DB::transaction(function () use ($orderId, $userId, $reason) {
            $order = Order::lockForUpdate()->findOrFail($orderId);
            $oldStatus = $order->order_status;

            // Hoàn lại tồn kho nếu đơn đã được confirmed/processing
            if (in_array($oldStatus, ['confirmed', 'processing'])) {
                $details = OrderDetail::where('order_id', $orderId)->get();
                foreach ($details as $detail) {
                    Inventory::where('variant_id', $detail->variant_id)
                        ->update([
                            'soluong_ton'        => DB::raw("soluong_ton + {$detail->quantity}"),
                            'soluong_dat'        => DB::raw("GREATEST(soluong_dat - {$detail->quantity}, 0)"),
                            'soluong_co_the_ban' => DB::raw("soluong_co_the_ban + {$detail->quantity}"),
                        ]);
                }
            }

            $order->update(['order_status' => 'cancelled']);

            $note = 'Khách hàng hủy đơn' . ($reason ? ': ' . $reason : '');
            $this->recordStatusHistory($orderId, $oldStatus, 'cancelled', $note, $userId);

            return true;
        });
    }

    /**
     * Kiểm tra đơn hàng có thể hủy bởi khách hàng không.
     */
    public function canCancelOrder(int $orderId, int $userId): bool
    {
        $order = Order::where('id', $orderId)
            ->where('user_id', $userId)
            ->first();

        return $order && in_array($order->order_status, ['pending', 'confirmed']);
    }

    /**
     * Lấy danh sách đơn hàng của khách hàng (phân trang).
     */
    public function getOrdersByUser(int $userId, int $perPage = 10)
    {
        return Order::with(['orderDetails'])
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Lấy chi tiết đơn hàng dành cho khách hàng.
     */
    public function getOrderDetailsForCustomer(int $orderId): array
    {
        $order = Order::with([
            'orderDetails.product.images',
            'orderDetails.variant.color',
            'orderDetails.variant.size',
            'statusHistories',
            'payments',
        ])->findOrFail($orderId);

        return [
            'order'   => $order,
            'details' => $order->orderDetails,
            'history' => $order->statusHistories()->orderByDesc('created_at')->get(),
            'payment' => $order->payments()->latest()->first(),
        ];
    }

    // ==================== THỐNG KÊ & KIỂM TRA ====================

    /**
     * Thống kê đơn hàng toàn hệ thống.
     */
    public function getOrderStatistics(): array
    {
        $stats = Order::selectRaw("
            COUNT(*) as total_orders,
            SUM(CASE WHEN order_status = 'pending'    THEN 1 ELSE 0 END) as pending_orders,
            SUM(CASE WHEN order_status = 'confirmed'  THEN 1 ELSE 0 END) as confirmed_orders,
            SUM(CASE WHEN order_status = 'processing' THEN 1 ELSE 0 END) as processing_orders,
            SUM(CASE WHEN order_status = 'delivered'  THEN 1 ELSE 0 END) as delivered_orders,
            SUM(CASE WHEN order_status = 'cancelled'  THEN 1 ELSE 0 END) as cancelled_orders,
            SUM(CASE WHEN payment_status = 'unpaid'   THEN 1 ELSE 0 END) as unpaid_orders,
            SUM(total_amount) as total_revenue
        ")->first();

        return $stats ? $stats->toArray() : [];
    }

    /**
     * Kiểm tra tồn kho của từng sản phẩm trong đơn hàng.
     */
    public function checkStockAvailability(int $orderId): Collection
    {
        $details = OrderDetail::with(['variant.inventory'])
            ->where('order_id', $orderId)
            ->get();

        return $details->map(function ($detail) {
            $inventory = $detail->variant?->inventory;
            $available = $inventory?->availableQuantity() ?? 0;

            return [
                'detail_id'     => $detail->detail_id,
                'variant_id'    => $detail->variant_id,
                'required_qty'  => $detail->quantity,
                'available_qty' => $available,
                'status'        => $available >= $detail->quantity ? 'OK' : 'INSUFFICIENT',
            ];
        });
    }

    /**
     * Tính tổng tiền thực tế đã thanh toán (completed - refunded).
     */
    public function getNetPaid(int $orderId): float
    {
        $result = Payment::where('order_id', $orderId)
            ->selectRaw("
                SUM(CASE WHEN payment_status = 'completed' THEN amount ELSE 0 END) as total_completed,
                SUM(CASE WHEN payment_status = 'refunded'  THEN amount ELSE 0 END) as total_refunded
            ")
            ->first();

        if (!$result) {
            return 0.0;
        }

        return (float) $result->total_completed - (float) $result->total_refunded;
    }

    /**
     * Tính toán lại và cập nhật payment_status dựa trên số tiền đã thanh toán.
     */
    public function recomputePaymentStatus(int $orderId): array
    {
        try {
            $order = Order::findOrFail($orderId);
            $netPaid = $this->getNetPaid($orderId);
            $currentStatus = $order->payment_status;

            if ($netPaid <= 0) {
                $newStatus = $netPaid < 0 ? 'refunded' : 'unpaid';
            } elseif ($netPaid >= $order->total_amount) {
                $newStatus = 'paid';
            } else {
                $newStatus = 'unpaid';
            }

            if ($newStatus !== $currentStatus) {
                $order->update(['payment_status' => $newStatus]);

                return [
                    'success'    => true,
                    'message'    => 'Cập nhật trạng thái thanh toán thành công',
                    'old_status' => $currentStatus,
                    'new_status' => $newStatus,
                    'net_paid'   => $netPaid,
                    'total'      => $order->total_amount,
                ];
            }

            return [
                'success' => true,
                'message' => 'Trạng thái thanh toán không thay đổi',
                'status'  => $currentStatus,
                'net_paid' => $netPaid,
                'total'   => $order->total_amount,
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // ==================== PRIVATE HELPERS ====================

    private function recordStatusHistory(int $orderId, string $oldStatus, string $newStatus, string $note = '', ?int $actorId = null): void
    {
        OrderStatusHistory::create([
            'order_id'   => $orderId,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by' => $actorId,
            'reason'     => $note,
            'created_at' => now(),
        ]);
    }

    private function completeCodPayment(int $orderId): void
    {
        Payment::where('order_id', $orderId)
            ->where('payment_method', 'Thu tiền tận nơi')
            ->where('payment_status', 'pending')
            ->update([
                'payment_status' => 'completed',
                'updated_at'     => now(),
            ]);
    }
}
