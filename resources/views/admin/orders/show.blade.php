@extends('admin.layouts.app')
@section('title', 'Chi tiết Đơn hàng #' . $order->order_number)
@section('page-title', 'Chi tiết Đơn hàng')

@section('content')
<div class="product-management-container">

    <a href="{{ route('admin.orders.index') }}" class="back-to-dashboard">
        <i class="fas fa-arrow-left"></i> Quay lại danh sách
    </a>

    {{-- Flash messages --}}
    @if(session('success'))
        <div style="background:#d4edda;color:#155724;padding:12px 16px;border-radius:6px;margin-bottom:16px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background:#f8d7da;color:#721c24;padding:12px 16px;border-radius:6px;margin-bottom:16px;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    @php
        $info = $order->deliveryInfo();
        $statusColors = [
            'pending'    => ['bg'=>'#fff3cd','color'=>'#856404','label'=>'Chờ xác nhận'],
            'confirmed'  => ['bg'=>'#d1ecf1','color'=>'#0c5460','label'=>'Đã xác nhận'],
            'processing' => ['bg'=>'#d0e8ff','color'=>'#1a56db','label'=>'Đang xử lý'],
            'delivered'  => ['bg'=>'#d4edda','color'=>'#155724','label'=>'Đã giao'],
            'cancelled'  => ['bg'=>'#f8d7da','color'=>'#721c24','label'=>'Đã hủy'],
        ];
        $sc = $statusColors[$order->order_status] ?? ['bg'=>'#e9ecef','color'=>'#495057','label'=>$order->order_status];
    @endphp

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;">

        {{-- Cột trái --}}
        <div>
            {{-- Thông tin đơn hàng --}}
            <div style="background:#fff;border-radius:8px;padding:20px;margin-bottom:20px;box-shadow:0 1px 4px rgba(0,0,0,.08);">
                <h3 style="margin:0 0 16px;font-size:15px;color:#333;border-bottom:1px solid #eee;padding-bottom:10px;">
                    <i class="fas fa-info-circle"></i> Thông tin đơn hàng
                </h3>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;font-size:13px;">
                    <div><span style="color:#888;">Mã đơn:</span> <strong style="color:#2563eb;">{{ $order->order_number }}</strong></div>
                    <div><span style="color:#888;">Ngày đặt:</span> {{ $order->order_date?->format('d/m/Y H:i') }}</div>
                    <div>
                        <span style="color:#888;">Trạng thái:</span>
                        <span style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};padding:2px 10px;border-radius:20px;font-size:12px;font-weight:500;">
                            {{ $sc['label'] }}
                        </span>
                    </div>
                    <div>
                        <span style="color:#888;">Thanh toán:</span>
                        <span style="font-weight:600;">
                            @if($order->payment_status === 'paid') <span style="color:#155724;">Đã thanh toán</span>
                            @elseif($order->payment_status === 'refunded') <span style="color:#383d41;">Hoàn tiền</span>
                            @else <span style="color:#721c24;">Chưa thanh toán</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            {{-- Thông tin giao hàng --}}
            <div style="background:#fff;border-radius:8px;padding:20px;margin-bottom:20px;box-shadow:0 1px 4px rgba(0,0,0,.08);">
                <h3 style="margin:0 0 16px;font-size:15px;color:#333;border-bottom:1px solid #eee;padding-bottom:10px;">
                    <i class="fas fa-map-marker-alt"></i> Thông tin giao hàng
                </h3>
                <div style="font-size:13px;line-height:1.8;">
                    <div><span style="color:#888;width:80px;display:inline-block;">Họ tên:</span> <strong>{{ $info['name'] ?? 'N/A' }}</strong></div>
                    <div><span style="color:#888;width:80px;display:inline-block;">SĐT:</span> {{ $info['phone'] ?? 'N/A' }}</div>
                    <div><span style="color:#888;width:80px;display:inline-block;">Địa chỉ:</span> {{ $info['address'] ?? '' }}, {{ $info['ward'] ?? '' }}, {{ $info['district'] ?? '' }}, {{ $info['province'] ?? '' }}</div>
                </div>
            </div>

            {{-- Danh sách sản phẩm --}}
            <div style="background:#fff;border-radius:8px;padding:20px;margin-bottom:20px;box-shadow:0 1px 4px rgba(0,0,0,.08);">
                <h3 style="margin:0 0 16px;font-size:15px;color:#333;border-bottom:1px solid #eee;padding-bottom:10px;">
                    <i class="fas fa-box"></i> Sản phẩm ({{ $order->orderDetails->count() }} sản phẩm)
                </h3>

                @foreach($order->orderDetails as $detail)
                    @php
                        $stockItem = $stockCheck->firstWhere('detail_id', $detail->detail_id);
                        $image = $detail->product?->images?->first();
                    @endphp
                    <div style="display:flex;gap:12px;padding:12px 0;border-bottom:1px solid #f5f5f5;align-items:center;">
                        <img src="{{ $image ? asset('storage/' . $image->product_anh) : asset('assets/clients/images/no-image.jpg') }}"
                             alt="" style="width:56px;height:56px;object-fit:cover;border-radius:6px;background:#f5f5f5;">
                        <div style="flex:1;font-size:13px;">
                            <div style="font-weight:600;margin-bottom:4px;">{{ $detail->product_name }}</div>
                            <div style="color:#888;">
                                @if($detail->variant?->color) Màu: {{ $detail->variant->color->color_ten }} @endif
                                @if($detail->variant?->size) &nbsp;|&nbsp; Size: {{ $detail->variant->size->product_size }} @endif
                            </div>
                        </div>
                        <div style="text-align:right;font-size:13px;min-width:100px;">
                            <div>{{ number_format($detail->product_gia) }}đ × {{ $detail->quantity }}</div>
                            <div style="font-weight:700;color:#2563eb;">{{ number_format($detail->subtotal) }}đ</div>
                        </div>
                        <div style="min-width:100px;text-align:center;">
                            @if($stockItem)
                                @if($stockItem['status'] === 'OK')
                                    <span style="background:#d4edda;color:#155724;padding:3px 8px;border-radius:4px;font-size:11px;">
                                        Đủ hàng ({{ $stockItem['available_qty'] }})
                                    </span>
                                @else
                                    <span style="background:#f8d7da;color:#721c24;padding:3px 8px;border-radius:4px;font-size:11px;">
                                        Thiếu hàng ({{ $stockItem['available_qty'] }}/{{ $stockItem['required_qty'] }})
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach

                {{-- Tổng tiền --}}
                <div style="margin-top:12px;font-size:13px;text-align:right;line-height:2;">
                    <div>Tạm tính: <strong>{{ number_format($order->subtotal) }}đ</strong></div>
                    <div>Phí vận chuyển: <strong>{{ number_format($order->shipping_fee) }}đ</strong></div>
                    @if($order->discount_amount > 0)
                        <div>Giảm giá: <strong style="color:#e53e3e;">-{{ number_format($order->discount_amount) }}đ</strong></div>
                    @endif
                    <div style="font-size:16px;color:#2563eb;">
                        Tổng cộng: <strong>{{ number_format($order->total_amount) }}đ</strong>
                    </div>
                </div>
            </div>

            {{-- Lịch sử trạng thái --}}
            @if($order->statusHistories->isNotEmpty())
            <div style="background:#fff;border-radius:8px;padding:20px;box-shadow:0 1px 4px rgba(0,0,0,.08);">
                <h3 style="margin:0 0 16px;font-size:15px;color:#333;border-bottom:1px solid #eee;padding-bottom:10px;">
                    <i class="fas fa-history"></i> Lịch sử trạng thái
                </h3>
                @foreach($order->statusHistories->sortByDesc('created_at') as $history)
                <div style="display:flex;gap:12px;padding:8px 0;border-bottom:1px solid #f5f5f5;font-size:13px;">
                    <div style="color:#888;min-width:130px;">{{ $history->created_at?->format('d/m/Y H:i') }}</div>
                    <div>
                        <span style="color:#888;">{{ $history->old_status }}</span>
                        <i class="fas fa-arrow-right" style="margin:0 6px;color:#aaa;"></i>
                        <strong>{{ $history->new_status }}</strong>
                        @if($history->reason)
                            <span style="color:#666;"> — {{ $history->reason }}</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Cột phải: actions --}}
        <div>
            {{-- Cập nhật trạng thái đơn --}}
            <div style="background:#fff;border-radius:8px;padding:20px;margin-bottom:20px;box-shadow:0 1px 4px rgba(0,0,0,.08);">
                <h3 style="margin:0 0 16px;font-size:15px;color:#333;border-bottom:1px solid #eee;padding-bottom:10px;">
                    <i class="fas fa-edit"></i> Cập nhật trạng thái
                </h3>

                @if($order->isPending())
                    <form method="POST" action="{{ route('admin.orders.confirm', $order->id) }}">
                        @csrf
                        <button type="submit" onclick="return confirm('Xác nhận đơn hàng này?')"
                                style="width:100%;padding:10px;background:#0c5460;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:13px;margin-bottom:8px;">
                            <i class="fas fa-check"></i> Xác nhận đơn hàng
                        </button>
                    </form>
                @endif

                @if($order->isConfirmed())
                    <form method="POST" action="{{ route('admin.orders.process', $order->id) }}">
                        @csrf
                        <button type="submit" onclick="return confirm('Chuyển sang đang xử lý?')"
                                style="width:100%;padding:10px;background:#1a56db;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:13px;margin-bottom:8px;">
                            <i class="fas fa-cog"></i> Bắt đầu xử lý
                        </button>
                    </form>
                @endif

                @if($order->isConfirmed() || $order->isProcessing())
                    <form method="POST" action="{{ route('admin.orders.deliver', $order->id) }}">
                        @csrf
                        <button type="submit" onclick="return confirm('Xác nhận đã giao hàng?')"
                                style="width:100%;padding:10px;background:#155724;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:13px;margin-bottom:8px;">
                            <i class="fas fa-truck"></i> Hoàn tất giao hàng
                        </button>
                    </form>
                @endif

                @if(!$order->isDelivered() && !$order->isCancelled())
                    <form method="POST" action="{{ route('admin.orders.cancel', $order->id) }}"
                          onsubmit="return confirm('Huỷ đơn hàng này?')">
                        @csrf
                        <input type="text" name="cancel_note" placeholder="Lý do huỷ (tuỳ chọn)"
                               style="width:100%;padding:8px 10px;border:1px solid #ddd;border-radius:6px;font-size:13px;margin-bottom:8px;box-sizing:border-box;">
                        <button type="submit"
                                style="width:100%;padding:10px;background:#721c24;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:13px;">
                            <i class="fas fa-times"></i> Huỷ đơn hàng
                        </button>
                    </form>
                @endif

                @if($order->isDelivered())
                    <div style="text-align:center;padding:12px;background:#d4edda;border-radius:6px;color:#155724;font-size:13px;">
                        <i class="fas fa-check-circle"></i> Đơn hàng đã hoàn tất
                    </div>
                @endif

                @if($order->isCancelled())
                    <div style="text-align:center;padding:12px;background:#f8d7da;border-radius:6px;color:#721c24;font-size:13px;">
                        <i class="fas fa-times-circle"></i> Đơn hàng đã bị huỷ
                    </div>
                @endif
            </div>

            {{-- Cập nhật thanh toán --}}
            <div style="background:#fff;border-radius:8px;padding:20px;margin-bottom:20px;box-shadow:0 1px 4px rgba(0,0,0,.08);">
                <h3 style="margin:0 0 16px;font-size:15px;color:#333;border-bottom:1px solid #eee;padding-bottom:10px;">
                    <i class="fas fa-credit-card"></i> Trạng thái thanh toán
                </h3>
                <form method="POST" action="{{ route('admin.orders.update-payment', $order->id) }}">
                    @csrf
                    <select name="payment_status"
                            style="width:100%;padding:8px 10px;border:1px solid #ddd;border-radius:6px;font-size:13px;margin-bottom:8px;">
                        <option value="unpaid"   @selected($order->payment_status === 'unpaid')>Chưa thanh toán</option>
                        <option value="paid"     @selected($order->payment_status === 'paid')>Đã thanh toán</option>
                        <option value="refunded" @selected($order->payment_status === 'refunded')>Hoàn tiền</option>
                    </select>
                    <button type="submit"
                            style="width:100%;padding:9px;background:#495057;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:13px;">
                        <i class="fas fa-save"></i> Cập nhật
                    </button>
                </form>
            </div>

            {{-- Thông tin thanh toán --}}
            @if($order->payments->isNotEmpty())
            <div style="background:#fff;border-radius:8px;padding:20px;box-shadow:0 1px 4px rgba(0,0,0,.08);">
                <h3 style="margin:0 0 16px;font-size:15px;color:#333;border-bottom:1px solid #eee;padding-bottom:10px;">
                    <i class="fas fa-receipt"></i> Lịch sử thanh toán
                </h3>
                @foreach($order->payments as $payment)
                <div style="font-size:13px;padding:8px 0;border-bottom:1px solid #f5f5f5;line-height:1.8;">
                    <div><span style="color:#888;">Phương thức:</span> {{ $payment->payment_method }}</div>
                    <div><span style="color:#888;">Số tiền:</span> <strong>{{ number_format($payment->amount) }}đ</strong></div>
                    <div><span style="color:#888;">Trạng thái:</span> {{ $payment->payment_status }}</div>
                    <div><span style="color:#888;">Ngày:</span> {{ $payment->payment_date?->format('d/m/Y H:i') }}</div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
