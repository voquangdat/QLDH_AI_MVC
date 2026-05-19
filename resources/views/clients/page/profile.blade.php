@extends('layouts.app')
@section('title', 'Tài khoản của tôi')

@push('styles')
<style>
.profile-wrapper {
    max-width: 860px;
    margin: 40px auto;
    padding: 0 16px 60px;
}
.profile-tabs {
    display: flex;
    gap: 4px;
    border-bottom: 2px solid #e9ecef;
    margin-bottom: 28px;
}
.profile-tab {
    padding: 10px 22px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    color: #6c757d;
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
    transition: color .2s, border-color .2s;
    background: none;
    border-top: none;
    border-left: none;
    border-right: none;
}
.profile-tab.active {
    color: #2563eb;
    border-bottom-color: #2563eb;
}
.profile-panel { display: none; }
.profile-panel.active { display: block; }

.profile-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,.08);
    padding: 32px;
}
.profile-avatar-section {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 28px;
    padding-bottom: 24px;
    border-bottom: 1px solid #f0f0f0;
}
.profile-avatar {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    object-fit: cover;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    color: #adb5bd;
    flex-shrink: 0;
    overflow: hidden;
}
.profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
.profile-name { font-size: 18px; font-weight: 600; color: #333; }
.profile-email { font-size: 13px; color: #6c757d; margin-top: 4px; }

.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
@media(max-width:600px){ .form-row { grid-template-columns: 1fr; } }

.form-group { margin-bottom: 20px; }
.form-group label {
    display: block;
    font-size: 13px;
    font-weight: 500;
    color: #555;
    margin-bottom: 6px;
}
.form-group input {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color .2s;
    box-sizing: border-box;
}
.form-group input:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,.1);
}
.form-group input:read-only {
    background: #f8f9fa;
    color: #6c757d;
    cursor: not-allowed;
}
.btn-save {
    padding: 10px 28px;
    background: #2563eb;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: background .2s;
}
.btn-save:hover { background: #1d4ed8; }
.btn-save:disabled { background: #93c5fd; cursor: not-allowed; }

/* Toast */
.profile-toast {
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
.profile-toast.success { background: #22c55e; display:flex; }
.profile-toast.error   { background: #ef4444; display:flex; }

/* Order table */
.order-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.order-table th {
    background: #f8f9fa;
    padding: 10px 14px;
    text-align: left;
    font-weight: 600;
    color: #555;
    border-bottom: 1px solid #dee2e6;
}
.order-table td {
    padding: 12px 14px;
    border-bottom: 1px solid #f0f0f0;
    vertical-align: middle;
}
.order-status {
    padding: 3px 10px;
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
</style>
@endpush

@section('content')
<div class="profile-wrapper">

    {{-- Toast --}}
    <div class="profile-toast" id="profile-toast">
        <i class="fas fa-check-circle" id="toast-icon"></i>
        <span id="toast-msg"></span>
    </div>

    <h2 style="font-size:22px;font-weight:700;color:#333;margin-bottom:20px;">
        <i class="fas fa-user-circle" style="color:#2563eb;margin-right:8px;"></i>
        Tài khoản của tôi
    </h2>

    {{-- Tabs --}}
    <div class="profile-tabs">
        <button class="profile-tab active" data-tab="info">
            <i class="fas fa-id-card"></i> Thông tin cá nhân
        </button>
        <button class="profile-tab" data-tab="password">
            <i class="fas fa-lock"></i> Đổi mật khẩu
        </button>
        <!-- <button class="profile-tab" data-tab="orders">
            <i class="fas fa-box"></i> Đơn hàng của tôi
        </button> -->
    </div>

    {{-- Tab: Thông tin cá nhân --}}
    <div class="profile-panel active" id="tab-info">
        <div class="profile-card">
            <div class="profile-avatar-section">
                <div class="profile-avatar">
                    @if ($user->avatar)
                        <img src="{{ asset('uploads/' . $user->avatar) }}" alt="avatar">
                    @else
                        <i class="fas fa-user"></i>
                    @endif
                </div>
                <div>
                    <div class="profile-name">{{ $user->fullname }}</div>
                    <div class="profile-email">{{ $user->email }}</div>
                </div>
            </div>

            <form id="form-update-info">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label>Họ và tên <span style="color:red">*</span></label>
                        <input type="text" name="fullname" value="{{ $user->fullname }}"
                               placeholder="Nhập họ tên..." required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" value="{{ $user->email }}" readonly>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="text" name="phone" value="{{ $user->phone ?? '' }}"
                               placeholder="Nhập số điện thoại...">
                    </div>
                </div>
                <button type="submit" class="btn-save" id="btn-save-info">
                    <i class="fas fa-save"></i> Lưu thay đổi
                </button>
            </form>
        </div>
    </div>

    {{-- Tab: Đổi mật khẩu --}}
    <div class="profile-panel" id="tab-password">
        <div class="profile-card">
            <h3 style="font-size:16px;font-weight:600;margin-bottom:24px;color:#333;">Đổi mật khẩu</h3>
            <form id="form-change-password" style="max-width:400px;">
                @csrf
                <div class="form-group">
                    <label>Mật khẩu hiện tại <span style="color:red">*</span></label>
                    <input type="password" name="current_password" placeholder="Nhập mật khẩu hiện tại...">
                </div>
                <div class="form-group">
                    <label>Mật khẩu mới <span style="color:red">*</span></label>
                    <input type="password" name="new_password" placeholder="Tối thiểu 6 ký tự...">
                </div>
                <div class="form-group">
                    <label>Xác nhận mật khẩu mới <span style="color:red">*</span></label>
                    <input type="password" name="new_password_confirmation" placeholder="Nhập lại mật khẩu mới...">
                </div>
                <button type="submit" class="btn-save" id="btn-save-password">
                    <i class="fas fa-key"></i> Đổi mật khẩu
                </button>
            </form>
        </div>
    </div>

    <!-- {{-- Tab: Đơn hàng --}}
    <div class="profile-panel" id="tab-orders">
        <div class="profile-card" style="padding:0;overflow:hidden;">
            @php
                $statusMap = [
                    'pending'    => ['bg'=>'#fff3cd','color'=>'#856404','label'=>'Chờ xác nhận'],
                    'confirmed'  => ['bg'=>'#d1ecf1','color'=>'#0c5460','label'=>'Đã xác nhận'],
                    'processing' => ['bg'=>'#d0e8ff','color'=>'#1a56db','label'=>'Đang xử lý'],
                    'delivered'  => ['bg'=>'#d4edda','color'=>'#155724','label'=>'Đã giao'],
                    'cancelled'  => ['bg'=>'#f8d7da','color'=>'#721c24','label'=>'Đã hủy'],
                ];
            @endphp

            @if (isset($orders) && $orders->isNotEmpty())
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
                    <div style="padding:16px;display:flex;justify-content:center;gap:6px;">
                        @if (!$orders->onFirstPage())
                            <a href="{{ $orders->previousPageUrl() }}#orders"
                               style="padding:6px 12px;border:1px solid #ddd;border-radius:6px;font-size:13px;text-decoration:none;color:#333;">
                                Trước
                            </a>
                        @endif
                        @for ($i = 1; $i <= $orders->lastPage(); $i++)
                            <a href="{{ $orders->url($i) }}#orders"
                               style="padding:6px 12px;border:1px solid {{ $i == $orders->currentPage() ? '#2563eb' : '#ddd' }};border-radius:6px;font-size:13px;text-decoration:none;color:{{ $i == $orders->currentPage() ? '#fff' : '#333' }};background:{{ $i == $orders->currentPage() ? '#2563eb' : '#fff' }};">
                                {{ $i }}
                            </a>
                        @endfor
                        @if ($orders->hasMorePages())
                            <a href="{{ $orders->nextPageUrl() }}#orders"
                               style="padding:6px 12px;border:1px solid #ddd;border-radius:6px;font-size:13px;text-decoration:none;color:#333;">
                                Sau
                            </a>
                        @endif
                    </div>
                @endif
            @else
                <div style="padding:48px;text-align:center;color:#adb5bd;">
                    <i class="fas fa-box-open fa-3x" style="margin-bottom:12px;display:block;"></i>
                    <p style="font-size:15px;">Bạn chưa có đơn hàng nào.</p>
                    <a href="{{ route('home') }}"
                       style="display:inline-block;margin-top:16px;padding:10px 24px;background:#2563eb;color:#fff;border-radius:8px;text-decoration:none;font-size:14px;">
                        Mua sắm ngay
                    </a>
                </div>
            @endif
        </div>
    </div> -->

</div>

<script>
const UPDATE_URL   = "{{ route('profile.update') }}";
const PASSWORD_URL = "{{ route('profile.password') }}";
const CANCEL_URL   = "{{ route('profile.cancel-order') }}";
const CSRF         = "{{ csrf_token() }}";

// ── Tabs ─────────────────────────────────────────────────────────────────────
document.querySelectorAll('.profile-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.profile-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.profile-panel').forEach(p => p.classList.remove('active'));
        tab.classList.add('active');
        document.getElementById('tab-' + tab.dataset.tab).classList.add('active');

        // Load đơn hàng khi chuyển sang tab orders
        if (tab.dataset.tab === 'orders') {
            loadOrders();
        }
    });
});

// Tự động mở tab orders nếu URL có #orders
if (location.hash === '#orders') {
    document.querySelector('[data-tab="orders"]').click();
}

// ── Toast ─────────────────────────────────────────────────────────────────────
function showToast(message, type = 'success') {
    const toast = document.getElementById('profile-toast');
    const icon  = document.getElementById('toast-icon');
    document.getElementById('toast-msg').textContent = message;
    toast.className = 'profile-toast ' + type;
    icon.className  = 'fas fa-' + (type === 'success' ? 'check-circle' : 'exclamation-circle');
    setTimeout(() => toast.className = 'profile-toast', 3000);
}

// ── Cập nhật thông tin ────────────────────────────────────────────────────────
document.getElementById('form-update-info').addEventListener('submit', async function (e) {
    e.preventDefault();
    const btn = document.getElementById('btn-save-info');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';

    const fd = new FormData(this);
    fd.append('_token', CSRF);

    try {
        const res  = await fetch(UPDATE_URL, { method: 'POST', body: fd });
        const data = await res.json();
        showToast(data.message, data.success ? 'success' : 'error');
    } catch {
        showToast('Có lỗi xảy ra!', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Lưu thay đổi';
    }
});

// ── Đổi mật khẩu ──────────────────────────────────────────────────────────────
document.getElementById('form-change-password').addEventListener('submit', async function (e) {
    e.preventDefault();
    const btn = document.getElementById('btn-save-password');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

    const fd = new FormData(this);
    fd.append('_token', CSRF);

    try {
        const res  = await fetch(PASSWORD_URL, { method: 'POST', body: fd });
        const data = await res.json();
        showToast(data.message, data.success ? 'success' : 'error');
        if (data.success) this.reset();
    } catch {
        showToast('Có lỗi xảy ra!', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-key"></i> Đổi mật khẩu';
    }
});

// ── Load đơn hàng (AJAX) ──────────────────────────────────────────────────────
function loadOrders() {
    const panel = document.getElementById('tab-orders');
    if (panel.dataset.loaded) return;
    panel.dataset.loaded = '1';
    fetch("{{ route('profile.orders') }}", {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.text())
    .then(html => {
        const parser = new DOMParser();
        const doc    = parser.parseFromString(html, 'text/html');
        const inner  = doc.getElementById('tab-orders');
        if (inner) panel.innerHTML = inner.innerHTML;
        bindCancelButtons();
    });
}

// ── Hủy đơn hàng ─────────────────────────────────────────────────────────────
function bindCancelButtons() {
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
                if (data.success) {
                    document.getElementById('tab-orders').dataset.loaded = '';
                    loadOrders();
                }
            } catch {
                showToast('Có lỗi xảy ra!', 'error');
            }
        });
    });
}

bindCancelButtons();
</script>
@endsection
