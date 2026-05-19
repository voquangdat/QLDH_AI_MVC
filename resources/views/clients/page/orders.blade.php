@extends('layouts.app')
@section('title', 'Đơn hàng của tôi')

@push('styles')
<style>
.orders-wrapper {
    max-width: 900px;
    margin: 40px auto;
    padding: 0 16px 60px;
}
.orders-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
}
.orders-header h2 {
    font-size: 22px;
    font-weight: 700;
    color: #333;
    margin: 0;
}
.orders-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,.08);
    overflow: hidden;
}
.order-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.order-table th {
    background: #f8f9fa;
    padding: 12px 16px;
    text-align: left;
    font-weight: 600;
    color: #555;
    border-bottom: 1px solid #dee2e6;
}
.order-table td {
    padding: 14px 16px;
    border-bottom: 1px solid #f0f0f0;
    vertical-align: middle;
}
.order-table tbody tr:last-child td { border-bottom: none; }
.order-status {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}
.btn-cancel-order {
    padding: 5px 12px;
    background: #fee2e2;
    color: #dc2626;
    border: none;
    border-radius: 6px;
    font-size: 12px;
    cursor: pointer;
    transition: background .2s;
}
.btn-cancel-order:hover { background: #fecaca; }
.empty-orders {
    padding: 60px 24px;
    text-align: center;
    color: #adb5bd;
}
.empty-orders i { font-size: 48px; margin-bottom: 16px; display: block; }
.empty-orders p { font-size: 15px; margin-bottom: 20px; }
.btn-shop {
    display: inline-block;
    padding: 10px 28px;
    background: #2563eb;
    color: #fff;
    border-radius: 8px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: background .2s;
}
.btn-shop:hover { background: #1d4ed8; }

/* Toast */
.orders-toast {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    padding: 12px 20px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    color: #fff;
    box-shadow: 0 4px 16px rgba(0,0,0,.15);
    display: none;
    align-items: center;
    gap: 8px;
    animation: slideInRight .3s ease;
}
@keyframes slideInRight {
    from { opacity:0; transform:translateX(30px); }
    to   { opacity:1; transform:translateX(0); }
}
.orders-toast.success { background: #22c55e; display:flex; }
.orders-toast.error   { background: #ef4444; display:flex; }

/* Profile Tabs */
.profile-tabs {
    display: flex;
    gap: 4px;
    margin-bottom: 20px;
    background: #fff;
    border-radius: 12px;
    padding: 6px;
    box-shadow: 0 2px 12px rgba(0,0,0,.08);
}
.profile-tab {
    flex: 1;
    padding: 10px 16px;
    border: none;
    background: transparent;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    color: #6c757d;
    cursor: pointer;
    transition: all .2s;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.profile-tab:hover { background: #f0f4ff; color: #2563eb; }
.profile-tab.active { background: #2563eb; color: #fff; }

.pagination-wrap {
    padding: 16px;
    display: flex;
    justify-content: center;
    gap: 6px;
    border-top: 1px solid #f0f0f0;
}
.page-btn {
    padding: 6px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 13px;
    text-decoration: none;
    color: #333;
    background: #fff;
    transition: all .2s;
}
.page-btn:hover { border-color: #2563eb; color: #2563eb; }
.page-btn.active { background: #2563eb; border-color: #2563eb; color: #fff; }
</style>
@endpush

@section('content')
<div class="orders-wrapper">

    <div class="orders-toast" id="orders-toast">
        <i class="fas fa-check-circle" id="toast-icon"></i>
        <span id="toast-msg"></span>
    </div>

    <div class="orders-header">
        <h2><i class="fas fa-box" style="color:#2563eb;margin-right:8px;"></i>Tài khoản của tôi</h2>
    </div>

    {{-- Tabs --}}
    <div class="profile-tabs">
        <!-- <a href="{{ route('profile') }}" class="profile-tab">
            <i class="fas fa-id-card"></i> Thông tin cá nhân
        </a>
        <a href="{{ route('profile') }}#password" class="profile-tab">
            <i class="fas fa-lock"></i> Đổi mật khẩu
        </a> -->
        <a href="{{ route('profile.orders') }}" class="profile-tab active">
            <i class="fas fa-box"></i> Đơn hàng của tôi
        </a>
    </div>

    <div class="orders-card">
        @php
            $statusMap = [
                'pending'    => ['bg'=>'#fff3cd','color'=>'#856404','label'=>'Chờ xác nhận'],
                'confirmed'  => ['bg'=>'#d1ecf1','color'=>'#0c5460','label'=>'Đã xác nhận'],
                'processing' => ['bg'=>'#d0e8ff','color'=>'#1a56db','label'=>'Đang xử lý'],
                'delivered'  => ['bg'=>'#d4edda','color'=>'#155724','label'=>'Đã giao'],
                'cancelled'  => ['bg'=>'#f8d7da','color'=>'#721c24','label'=>'Đã hủy'],
            ];
        @endphp

        @if ($orders->isNotEmpty())
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Ngày đặt</th>
                        <th style="text-align:right">Tổng tiền</th>
                        <th style="text-align:center">Trạng thái</th>
                        <th style="text-align:center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        @php $sc = $statusMap[$order->order_status] ?? ['bg'=>'#e9ecef','color'=>'#495057','label'=>$order->order_status]; @endphp
                        <tr>
                            <td style="font-weight:600;color:#2563eb;">{{ $order->order_number }}</td>
                            <td style="color:#555;">{{ $order->order_date?->format('d/m/Y H:i') }}</td>
                            <td style="text-align:right;font-weight:600;">{{ number_format($order->total_amount) }}đ</td>
                            <td style="text-align:center;">
                                <span class="order-status"
                                      style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};">
                                    {{ $sc['label'] }}
                                </span>
                            </td>
                            <td style="text-align:center;">
                                <a href="{{ route('order.detail', $order->id) }}"
                                   style="padding:5px 12px;background:#2563eb;color:#fff;border-radius:6px;font-size:12px;text-decoration:none;margin-right:4px;">
                                    <i class="fas fa-eye"></i> Xem
                                </a>
                                @if (in_array($order->order_status, ['pending', 'confirmed']))
                                    <button class="btn-cancel-order"
                                            data-order-id="{{ $order->id }}"
                                            data-order-number="{{ $order->order_number }}">
                                        <i class="fas fa-times"></i> Hủy
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($orders->lastPage() > 1)
                <div class="pagination-wrap">
                    @if (!$orders->onFirstPage())
                        <a href="{{ $orders->previousPageUrl() }}" class="page-btn">Trước</a>
                    @endif
                    @for ($i = 1; $i <= $orders->lastPage(); $i++)
                        <a href="{{ $orders->url($i) }}" class="page-btn {{ $i == $orders->currentPage() ? 'active' : '' }}">{{ $i }}</a>
                    @endfor
                    @if ($orders->hasMorePages())
                        <a href="{{ $orders->nextPageUrl() }}" class="page-btn">Sau</a>
                    @endif
                </div>
            @endif
        @else
            <div class="empty-orders">
                <i class="fas fa-box-open"></i>
                <p>Bạn chưa có đơn hàng nào.</p>
                <a href="{{ route('home') }}" class="btn-shop">Mua sắm ngay</a>
            </div>
        @endif
    </div>
</div>

<script>
const CANCEL_URL = "{{ route('profile.cancel-order') }}";
const CSRF       = "{{ csrf_token() }}";

function showToast(message, type = 'success') {
    const toast = document.getElementById('orders-toast');
    const icon  = document.getElementById('toast-icon');
    document.getElementById('toast-msg').textContent = message;
    toast.className = 'orders-toast ' + type;
    icon.className  = 'fas fa-' + (type === 'success' ? 'check-circle' : 'exclamation-circle');
    setTimeout(() => toast.className = 'orders-toast', 3000);
}

document.querySelectorAll('.btn-cancel-order').forEach(btn => {
    btn.addEventListener('click', async function () {
        const orderId  = this.dataset.orderId;
        const orderNum = this.dataset.orderNumber;
        if (!confirm(`Bạn có chắc muốn hủy đơn hàng ${orderNum}?`)) return;

        const fd = new FormData();
        fd.append('_token', CSRF);
        fd.append('order_id', orderId);

        try {
            const res  = await fetch(CANCEL_URL, { method: 'POST', body: fd });
            const data = await res.json();
            showToast(data.message, data.success ? 'success' : 'error');
            if (data.success) setTimeout(() => location.reload(), 1000);
        } catch {
            showToast('Có lỗi xảy ra!', 'error');
        }
    });
});
</script>
@endsection
