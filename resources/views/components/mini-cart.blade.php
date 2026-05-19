<div class="mini-cart-wrapper" id="mini-cart-wrapper">
    <a href="{{ route('cart.index') }}" class="mini-cart-trigger" id="mini-cart-trigger">
        <i class="fas fa-shopping-cart"></i>
        <span class="cart-count" id="cart-count">{{ $miniCartCount }}</span>
    </a>

    <div class="mini-cart-dropdown" id="mini-cart-dropdown">
        <div class="mini-cart-header">
            <span>Giỏ hàng (<span id="mc-count">{{ $miniCartCount }}</span> sản phẩm)</span>
        </div>

        <div class="mini-cart-items" id="mc-items">
            @forelse ($miniCartItems as $item)
                @php
                    $img = $item->variant->product->images->first();
                @endphp
                <div class="mc-item">
                    <img src="{{ asset('uploads/' . ($img->product_anh ?? '')) }}"
                         alt="{{ $item->variant->product->product_name }}"
                         onerror="this.src='{{ asset('assets/clients/images/no-image.png') }}'">
                    <div class="mc-item-info">
                        <p class="mc-item-name">{{ $item->variant->product->product_name }}</p>
                        <small>
                            {{ $item->variant->color->color_ten ?? '' }}
                            — {{ $item->variant->size->product_size ?? '' }}
                        </small>
                        <p class="mc-item-price">
                            {{ $item->quantity }} × {{ number_format($item->variant->product->product_gia) }}đ
                        </p>
                    </div>
                </div>
            @empty
                <div class="mc-empty" id="mc-empty">
                    <i class="fas fa-shopping-cart"></i>
                    <p>Giỏ hàng trống</p>
                </div>
            @endforelse
        </div>

        @if ($miniCartItems->isNotEmpty())
            <div class="mini-cart-footer" id="mc-footer">
                <div class="mc-total">
                    <span>Tổng tiền:</span>
                    <strong id="mc-total">{{ number_format($miniCartTotal) }}đ</strong>
                </div>
                <a href="{{ route('cart.index') }}" class="mc-btn-cart">Xem giỏ hàng</a>
                <a href="{{ route('delivery.index') }}" class="mc-btn-checkout">Thanh toán</a>
            </div>
        @else
            <div class="mini-cart-footer" id="mc-footer" style="display:none"></div>
        @endif
    </div>
</div>
