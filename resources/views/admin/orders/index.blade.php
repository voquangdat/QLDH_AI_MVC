@extends('admin.layouts.app')
@section('title', 'Quản lý Đơn hàng')
@section('page-title', 'Quản lý Đơn hàng')

@push('scripts')
<script>
(function () {
    const POLL_INTERVAL = 30000;
    const CHECK_URL     = '{{ route('admin.orders.check-new') }}';

    let lastTimestamp = Math.floor(Date.now() / 1000);
    let toastEl       = null;
    let newOrderCount = 0;

    // ── CSS ───────────────────────────────────────────────────────
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from { opacity:0; transform:translateX(40px); }
            to   { opacity:1; transform:translateX(0); }
        }
        @keyframes rowIn {
            from { opacity:0; background:#dbeafe; transform:translateY(-8px); }
            to   { opacity:1; background:#dbeafe; transform:translateY(0); }
        }
        .new-order-row {
            animation: rowIn .4s ease forwards;
        }
        .new-order-row td {
            background: #dbeafe !important;
            transition: background 3s ease;
        }
    `;
    document.head.appendChild(style);

    // ── Chèn hàng mới vào đầu bảng ───────────────────────────────
    function prependOrderRow(order) {
        const tbody = document.getElementById('orders-tbody');
        if (!tbody) return;

        // Xóa dòng "Không có đơn hàng nào" nếu còn
        const emptyRow = tbody.querySelector('td[colspan]');
        if (emptyRow) emptyRow.closest('tr').remove();

        const statusMap = {
            pending:    { bg:'#fff3cd', color:'#856404', label:'Chờ xác nhận' },
            confirmed:  { bg:'#d1ecf1', color:'#0c5460', label:'Đã xác nhận'  },
            processing: { bg:'#d0e8ff', color:'#1a56db', label:'Đang xử lý'   },
            delivered:  { bg:'#d4edda', color:'#155724', label:'Đã giao'       },
            cancelled:  { bg:'#f8d7da', color:'#721c24', label:'Đã hủy'        },
        };
        const sc = statusMap[order.order_status] ?? { bg:'#e9ecef', color:'#495057', label: order.order_status };

        const tr = document.createElement('tr');
        tr.classList.add('new-order-row');
        tr.setAttribute('data-order-id', order.id);
        tr.innerHTML = `
            <td style="padding:12px 14px;font-weight:600;color:#2563eb;">${order.order_number}</td>
            <td style="padding:12px 14px;">
                <div style="font-weight:500;">${order.customer}</div>
                <div style="color:#888;font-size:12px;">${order.phone ?? ''}</div>
            </td>
            <td style="padding:12px 14px;color:#555;">${order.created_at}</td>
            <td style="padding:12px 14px;text-align:right;font-weight:600;">${order.total_amount}</td>
            <td style="padding:12px 14px;text-align:center;">
                <span style="background:${sc.bg};color:${sc.color};padding:3px 10px;border-radius:20px;font-size:12px;font-weight:500;">
                    ${sc.label}
                </span>
            </td>
            <td style="padding:12px 14px;text-align:center;">
                <span style="background:#f8d7da;color:#721c24;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:500;">
                    Chưa TT
                </span>
            </td>
            <td style="padding:12px 14px;text-align:center;">
                <a href="${order.url}"
                   style="padding:5px 12px;background:#2563eb;color:#fff;border-radius:5px;font-size:12px;text-decoration:none;">
                    <i class="fas fa-eye"></i> Xem
                </a>
            </td>
        `;

        tbody.insertBefore(tr, tbody.firstChild);

        // Sau 4 giây, fade màu nền highlight về trắng
        setTimeout(() => {
            tr.querySelectorAll('td').forEach(td => td.style.background = '#fff');
        }, 4000);
    }

    // ── Toast notification ────────────────────────────────────────
    function showToast(order) {
        if (!toastEl) {
            toastEl = document.createElement('div');
            toastEl.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:10px;max-width:340px;';
            document.body.appendChild(toastEl);
        }

        const card = document.createElement('div');
        card.setAttribute('data-toast', '');
        card.style.cssText = 'background:#fff;border-left:4px solid #2563eb;border-radius:8px;padding:14px 16px;box-shadow:0 4px 16px rgba(0,0,0,.15);font-size:13px;animation:slideIn .3s ease;';
        card.innerHTML = `
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:6px;">
                <strong style="color:#2563eb;">🛒 Đơn hàng mới!</strong>
                <button onclick="this.closest('[data-toast]').remove()"
                        style="background:none;border:none;cursor:pointer;color:#999;font-size:16px;line-height:1;">×</button>
            </div>
            <div style="color:#333;margin-bottom:2px;"><strong>${order.order_number}</strong> — ${order.customer}</div>
            <div style="color:#888;margin-bottom:10px;">${order.total_amount} · ${order.created_at}</div>
            <a href="${order.url}" style="display:inline-block;padding:6px 14px;background:#2563eb;color:#fff;border-radius:5px;text-decoration:none;font-size:12px;">
                Xem đơn hàng
            </a>
        `;
        toastEl.appendChild(card);
        setTimeout(() => card.remove(), 15000);
    }

    // ── Badge số đơn mới ──────────────────────────────────────────
    function updateBadge(delta) {
        newOrderCount += delta;
        let badge = document.getElementById('new-order-badge');
        if (!badge) {
            badge = document.createElement('span');
            badge.id = 'new-order-badge';
            badge.style.cssText = 'background:#e53e3e;color:#fff;font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;margin-left:8px;vertical-align:middle;cursor:pointer;';
            badge.title = 'Nhấn để reset bộ đếm';
            badge.onclick = () => { newOrderCount = 0; badge.remove(); };
            const h1 = document.querySelector('.admin-header h1');
            if (h1) h1.appendChild(badge);
        }
        badge.textContent = `+${newOrderCount} đơn mới`;
    }

    // ── Cập nhật thống kê tổng đơn + chờ xác nhận ────────────────
    function updateStats(count) {
        const statCells = document.querySelectorAll('[data-stat]');
        statCells.forEach(el => {
            if (el.dataset.stat === 'total')   el.textContent = parseInt(el.textContent.replace(/\D/g,'')) + count;
            if (el.dataset.stat === 'pending')  el.textContent = parseInt(el.textContent.replace(/\D/g,'')) + count;
        });
    }

    // ── Polling ───────────────────────────────────────────────────
    async function poll() {
        try {
            const res  = await fetch(`${CHECK_URL}?since=${lastTimestamp}`);
            const data = await res.json();

            if (data.count > 0) {
                // Chèn từ dưới lên để thứ tự đúng (mới nhất ở trên cùng)
                [...data.orders].reverse().forEach(order => {
                    prependOrderRow(order);
                    showToast(order);
                });
                updateBadge(data.count);
                updateStats(data.count);
            }

            lastTimestamp = data.server_time;
        } catch (e) {
            console.warn('Polling lỗi:', e);
        }
    }

    setInterval(poll, POLL_INTERVAL);
})();
</script>
@endpush

@section('content')
<div class="product-management-container">

    <a href="{{ route('admin.dashboard') }}" class="back-to-dashboard">
        <i class="fas fa-arrow-left"></i> Quay lại Dashboard
    </a>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success" style="background:#d4edda;color:#155724;padding:12px 16px;border-radius:6px;margin-bottom:16px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-error" style="background:#f8d7da;color:#721c24;padding:12px 16px;border-radius:6px;margin-bottom:16px;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Thống kê --}}
    @if($statistics)
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;margin-bottom:24px;">
        <div style="background:#fff;border-radius:8px;padding:16px;text-align:center;box-shadow:0 1px 4px rgba(0,0,0,.08);">
            <div data-stat="total" style="font-size:24px;font-weight:700;color:#333;">{{ number_format($statistics['total_orders']) }}</div>
            <div style="color:#666;font-size:13px;">Tổng đơn</div>
        </div>
        <div style="background:#fff3cd;border-radius:8px;padding:16px;text-align:center;box-shadow:0 1px 4px rgba(0,0,0,.08);">
            <div data-stat="pending" style="font-size:24px;font-weight:700;color:#856404;">{{ number_format($statistics['pending_orders']) }}</div>
            <div style="color:#856404;font-size:13px;">Chờ xác nhận</div>
        </div>
        <div style="background:#d1ecf1;border-radius:8px;padding:16px;text-align:center;box-shadow:0 1px 4px rgba(0,0,0,.08);">
            <div style="font-size:24px;font-weight:700;color:#0c5460;">{{ number_format($statistics['confirmed_orders']) }}</div>
            <div style="color:#0c5460;font-size:13px;">Đã xác nhận</div>
        </div>
        <div style="background:#d4edda;border-radius:8px;padding:16px;text-align:center;box-shadow:0 1px 4px rgba(0,0,0,.08);">
            <div style="font-size:24px;font-weight:700;color:#155724;">{{ number_format($statistics['delivered_orders']) }}</div>
            <div style="color:#155724;font-size:13px;">Đã giao</div>
        </div>
        <div style="background:#f8d7da;border-radius:8px;padding:16px;text-align:center;box-shadow:0 1px 4px rgba(0,0,0,.08);">
            <div style="font-size:24px;font-weight:700;color:#721c24;">{{ number_format($statistics['cancelled_orders']) }}</div>
            <div style="color:#721c24;font-size:13px;">Đã hủy</div>
        </div>
        <div style="background:#fff;border-radius:8px;padding:16px;text-align:center;box-shadow:0 1px 4px rgba(0,0,0,.08);">
            <div style="font-size:18px;font-weight:700;color:#2563eb;">{{ number_format($statistics['total_revenue']) }}đ</div>
            <div style="color:#666;font-size:13px;">Doanh thu</div>
        </div>
    </div>
    @endif

    {{-- Bộ lọc --}}
    <form method="GET" action="{{ route('admin.orders.index') }}"
          style="background:#fff;padding:16px;border-radius:8px;margin-bottom:20px;display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end;box-shadow:0 1px 4px rgba(0,0,0,.08);">

        <div style="flex:1;min-width:160px;">
            <label style="font-size:12px;color:#555;display:block;margin-bottom:4px;">Tìm kiếm</label>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                   placeholder="Mã đơn, tên, SĐT..."
                   style="width:100%;padding:8px 10px;border:1px solid #ddd;border-radius:6px;font-size:13px;">
        </div>

        <div style="min-width:150px;">
            <label style="font-size:12px;color:#555;display:block;margin-bottom:4px;">Trạng thái đơn</label>
            <select name="order_status" style="padding:8px 10px;border:1px solid #ddd;border-radius:6px;font-size:13px;width:100%;">
                <option value="">-- Tất cả --</option>
                @foreach(['pending'=>'Chờ xác nhận','confirmed'=>'Đã xác nhận','processing'=>'Đang xử lý','delivered'=>'Đã giao','cancelled'=>'Đã hủy'] as $val => $label)
                    <option value="{{ $val }}" @selected(($filters['order_status'] ?? '') === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div style="min-width:140px;">
            <label style="font-size:12px;color:#555;display:block;margin-bottom:4px;">Thanh toán</label>
            <select name="payment_status" style="padding:8px 10px;border:1px solid #ddd;border-radius:6px;font-size:13px;width:100%;">
                <option value="">-- Tất cả --</option>
                @foreach(['unpaid'=>'Chưa thanh toán','paid'=>'Đã thanh toán','refunded'=>'Hoàn tiền'] as $val => $label)
                    <option value="{{ $val }}" @selected(($filters['payment_status'] ?? '') === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div style="min-width:130px;">
            <label style="font-size:12px;color:#555;display:block;margin-bottom:4px;">Từ ngày</label>
            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                   style="padding:8px 10px;border:1px solid #ddd;border-radius:6px;font-size:13px;">
        </div>

        <div style="min-width:130px;">
            <label style="font-size:12px;color:#555;display:block;margin-bottom:4px;">Đến ngày</label>
            <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                   style="padding:8px 10px;border:1px solid #ddd;border-radius:6px;font-size:13px;">
        </div>

        <div style="display:flex;gap:8px;">
            <button type="submit"
                    style="padding:8px 16px;background:#2563eb;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:13px;">
                <i class="fas fa-search"></i> Lọc
            </button>
            <a href="{{ route('admin.orders.index') }}"
               style="padding:8px 16px;background:#6c757d;color:#fff;border-radius:6px;font-size:13px;text-decoration:none;">
                <i class="fas fa-redo"></i> Reset
            </a>
        </div>
    </form>

    {{-- Bảng đơn hàng --}}
    <div style="background:#fff;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,.08);overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead style="background:#f8f9fa;">
                <tr>
                    <th style="padding:12px 14px;text-align:left;border-bottom:1px solid #dee2e6;">Mã đơn</th>
                    <th style="padding:12px 14px;text-align:left;border-bottom:1px solid #dee2e6;">Khách hàng</th>
                    <th style="padding:12px 14px;text-align:left;border-bottom:1px solid #dee2e6;">Ngày đặt</th>
                    <th style="padding:12px 14px;text-align:right;border-bottom:1px solid #dee2e6;">Tổng tiền</th>
                    <th style="padding:12px 14px;text-align:center;border-bottom:1px solid #dee2e6;">Trạng thái</th>
                    <th style="padding:12px 14px;text-align:center;border-bottom:1px solid #dee2e6;">Thanh toán</th>
                    <th style="padding:12px 14px;text-align:center;border-bottom:1px solid #dee2e6;">Thao tác</th>
                </tr>
            </thead>
            <tbody id="orders-tbody">
                @forelse($orders as $order)
                    @php
                        $info = $order->deliveryInfo();
                        $statusColors = [
                            'pending'    => ['bg'=>'#fff3cd','color'=>'#856404','label'=>'Chờ xác nhận'],
                            'confirmed'  => ['bg'=>'#d1ecf1','color'=>'#0c5460','label'=>'Đã xác nhận'],
                            'processing' => ['bg'=>'#d0e8ff','color'=>'#1a56db','label'=>'Đang xử lý'],
                            'delivered'  => ['bg'=>'#d4edda','color'=>'#155724','label'=>'Đã giao'],
                            'cancelled'  => ['bg'=>'#f8d7da','color'=>'#721c24','label'=>'Đã hủy'],
                        ];
                        $payColors = [
                            'unpaid'   => ['bg'=>'#f8d7da','color'=>'#721c24','label'=>'Chưa TT'],
                            'paid'     => ['bg'=>'#d4edda','color'=>'#155724','label'=>'Đã TT'],
                            'refunded' => ['bg'=>'#e2e3e5','color'=>'#383d41','label'=>'Hoàn tiền'],
                        ];
                        $sc = $statusColors[$order->order_status] ?? ['bg'=>'#e9ecef','color'=>'#495057','label'=>$order->order_status];
                        $pc = $payColors[$order->payment_status] ?? ['bg'=>'#e9ecef','color'=>'#495057','label'=>$order->payment_status];
                    @endphp
                    <tr style="border-bottom:1px solid #f0f0f0;">
                        <td style="padding:12px 14px;font-weight:600;color:#2563eb;">
                            {{ $order->order_number }}
                        </td>
                        <td style="padding:12px 14px;">
                            <div style="font-weight:500;">{{ $info['name'] ?? 'N/A' }}</div>
                            <div style="color:#888;font-size:12px;">{{ $info['phone'] ?? '' }}</div>
                        </td>
                        <td style="padding:12px 14px;color:#555;">
                            {{ $order->order_date?->format('d/m/Y H:i') }}
                        </td>
                        <td style="padding:12px 14px;text-align:right;font-weight:600;">
                            {{ number_format($order->total_amount) }}đ
                        </td>
                        <td style="padding:12px 14px;text-align:center;">
                            <span style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};padding:3px 10px;border-radius:20px;font-size:12px;font-weight:500;">
                                {{ $sc['label'] }}
                            </span>
                        </td>
                        <td style="padding:12px 14px;text-align:center;">
                            <span style="background:{{ $pc['bg'] }};color:{{ $pc['color'] }};padding:3px 10px;border-radius:20px;font-size:12px;font-weight:500;">
                                {{ $pc['label'] }}
                            </span>
                        </td>
                        <td style="padding:12px 14px;text-align:center;">
                            <a href="{{ route('admin.orders.show', $order->id) }}"
                               style="padding:5px 12px;background:#2563eb;color:#fff;border-radius:5px;font-size:12px;text-decoration:none;">
                                <i class="fas fa-eye"></i> Xem
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding:40px;text-align:center;color:#888;">
                            <i class="fas fa-inbox fa-2x" style="display:block;margin-bottom:8px;"></i>
                            Không có đơn hàng nào
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Phân trang --}}
    <div style="margin-top:16px;display:flex;justify-content:center;">
        {{ $orders->links() }}
    </div>

</div>
@endsection
