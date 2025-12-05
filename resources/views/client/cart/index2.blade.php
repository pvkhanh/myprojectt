@extends('client.layouts.master')

@section('title', 'Giỏ hàng')

@push('styles')
    <style>
        .cart-container {
            padding: 40px 0;
            min-height: 60vh;
        }

        .cart-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
        }

        .cart-title {
            font-size: 32px;
            font-weight: 800;
            color: white;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .cart-count {
            background: rgba(255, 255, 255, 0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 18px;
        }

        .cart-content {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
        }

        .cart-items {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .cart-item {
            display: grid;
            grid-template-columns: 140px 1fr auto;
            gap: 25px;
            padding: 25px;
            border-bottom: 2px solid #f1f5f9;
            transition: all 0.3s;
        }

        .cart-item:hover {
            background: #f8fafc;
            border-radius: 12px;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .item-image {
            width: 140px;
            height: 140px;
            border-radius: 16px;
            overflow: hidden;
            background: #f8fafc;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .item-image:hover img {
            transform: scale(1.1);
        }

        .item-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #ef4444;
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .item-details {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .item-name {
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 10px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            transition: color 0.3s;
        }

        .item-name:hover {
            color: #667eea;
        }

        .item-variant {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            font-size: 14px;
            margin-bottom: 12px;
            background: #f1f5f9;
            padding: 6px 12px;
            border-radius: 8px;
            width: fit-content;
        }

        .item-price-section {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .item-price {
            font-size: 24px;
            font-weight: 800;
            color: #ef4444;
        }

        .item-price-old {
            font-size: 18px;
            color: #94a3b8;
            text-decoration: line-through;
        }

        .item-discount-badge {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .item-actions {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            justify-content: space-between;
            gap: 15px;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            background: white;
        }

        .qty-btn {
            width: 40px;
            height: 40px;
            border: none;
            background: #f8fafc;
            color: #475569;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
        }

        .qty-btn:hover:not(:disabled) {
            background: #667eea;
            color: white;
        }

        .qty-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .qty-input {
            width: 60px;
            text-align: center;
            border: none;
            font-weight: 700;
            font-size: 16px;
            color: #1e293b;
        }

        .remove-item {
            color: #ef4444;
            background: #fee;
            border: none;
            cursor: pointer;
            padding: 10px 16px;
            font-size: 16px;
            transition: all 0.3s;
            border-radius: 10px;
            font-weight: 600;
        }

        .remove-item:hover {
            background: #ef4444;
            color: white;
            transform: scale(1.05);
        }

        .cart-summary {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 100px;
            height: fit-content;
        }

        .summary-title {
            font-size: 24px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 3px solid #e2e8f0;
        }

        .coupon-section {
            padding: 20px;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-radius: 12px;
            margin-bottom: 25px;
            border: 2px dashed #fbbf24;
        }

        .coupon-label {
            font-weight: 700;
            color: #92400e;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .coupon-input {
            display: flex;
            gap: 10px;
        }

        .coupon-input input {
            flex: 1;
            padding: 12px 16px;
            border: 2px solid #fbbf24;
            border-radius: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .coupon-input input:focus {
            outline: none;
            border-color: #f59e0b;
        }

        .btn-apply-coupon {
            padding: 12px 25px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            white-space: nowrap;
            transition: all 0.3s;
        }

        .btn-apply-coupon:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(245, 158, 11, 0.4);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 18px;
            font-size: 16px;
            align-items: center;
        }

        .summary-row .label {
            color: #64748b;
            font-weight: 500;
        }

        .summary-row .value {
            font-weight: 700;
            color: #1e293b;
        }

        .summary-divider {
            height: 2px;
            background: linear-gradient(to right, transparent, #e2e8f0, transparent);
            margin: 25px 0;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            font-size: 26px;
            font-weight: 900;
            color: #1e293b;
            margin-bottom: 25px;
            padding: 20px;
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
            border-radius: 12px;
        }

        .summary-total .value {
            color: #ef4444;
        }

        .btn-checkout {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 800;
            transition: all 0.3s;
            margin-bottom: 15px;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
        }

        .btn-checkout:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(239, 68, 68, 0.5);
        }

        .btn-continue {
            width: 100%;
            padding: 16px;
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
            border-radius: 12px;
            font-weight: 700;
            transition: all 0.3s;
        }

        .btn-continue:hover {
            background: #667eea;
            color: white;
        }

        .trust-badges {
            margin-top: 25px;
            padding-top: 25px;
            border-top: 2px solid #e2e8f0;
        }

        .trust-badge-item {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
            padding: 12px;
            background: #f8fafc;
            border-radius: 10px;
        }

        .trust-badge-item i {
            font-size: 24px;
        }

        .empty-cart {
            text-align: center;
            padding: 100px 20px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .empty-cart-icon {
            font-size: 120px;
            color: #cbd5e1;
            margin-bottom: 30px;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .empty-cart h3 {
            font-size: 32px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 15px;
        }

        .empty-cart p {
            color: #64748b;
            font-size: 18px;
            margin-bottom: 35px;
        }

        .btn-start-shopping {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 18px 40px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: 700;
            font-size: 18px;
            transition: all 0.3s;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-start-shopping:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.5);
        }

        @media (max-width: 1024px) {
            .cart-content {
                grid-template-columns: 1fr;
            }

            .cart-summary {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .cart-item {
                grid-template-columns: 100px 1fr;
                gap: 15px;
            }

            .item-image {
                width: 100px;
                height: 100px;
            }

            .item-actions {
                grid-column: 1 / -1;
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }

            .cart-title {
                font-size: 24px;
            }
        }
    </style>
@endpush
@section('content')
    <div class="container cart-container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('client.home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active">Giỏ hàng</li>
            </ol>
        </nav>

        @if (isset($cartItems) && count($cartItems) > 0)
            <div class="cart-header">
                <h1 class="cart-title">
                    <i class="fas fa-shopping-cart"></i>
                    Giỏ Hàng Của Bạn
                    <span class="cart-count">{{ count($cartItems) }} sản phẩm</span>
                </h1>
            </div>

            <div class="cart-content">
                <!-- Cart Items -->
                <div class="cart-items">
                    @foreach ($cartItems as $item)
                        @php
                            // Lấy giá ĐÚNG từ variant hoặc product
                            if ($item->variant_id && $item->variant) {
                                $currentPrice = $item->variant->sale_price ?? $item->variant->price;
                                $originalPrice = $item->variant->price;
                                $itemName = $item->product->name . ' - ' . $item->variant->name;

                                // Tồn kho từ variant
                                $availableStock = $item->variant->stockItems->sum('quantity');
                            } else {
                                $currentPrice = $item->product->sale_price ?? $item->product->price;
                                $originalPrice = $item->product->price;
                                $itemName = $item->product->name;

                                // Tồn kho từ tất cả variants của product
                                $availableStock = $item->product->variants->sum(function ($variant) {
                                    return $variant->stockItems->sum('quantity');
                                });
                            }

                            $hasDiscount = $currentPrice < $originalPrice;
                            $discountPercent = $hasDiscount
                                ? round((($originalPrice - $currentPrice) / $originalPrice) * 100)
                                : 0;
                            $itemSubtotal = $currentPrice * $item->quantity;
                        @endphp

                        <div class="cart-item" data-item-id="{{ $item->id }}">
                            <div class="item-image">
                                <img src="{{ $item->product->main_image }}" alt="{{ $item->product->name }}"
                                    onerror="this.src='https://via.placeholder.com/140'">
                                @if ($hasDiscount)
                                    <span class="item-badge">-{{ $discountPercent }}%</span>
                                @endif
                            </div>

                            <div class="item-details">
                                <a href="{{ route('client.products.show', $item->product->slug) }}"
                                    class="item-name text-decoration-none">
                                    {{ $itemName }}
                                </a>

                                @if ($item->variant)
                                    <div class="item-variant">
                                        <i class="fas fa-tag"></i>
                                        Phân loại: {{ $item->variant->name }}
                                    </div>
                                @endif

                                {{-- Hiển thị tồn kho --}}
                                <div
                                    class="item-stock {{ $availableStock > 10 ? 'in-stock' : ($availableStock > 0 ? 'low-stock' : 'out-of-stock') }}">
                                    <i class="fas fa-box"></i>
                                    @if ($availableStock > 10)
                                        Còn hàng
                                    @elseif($availableStock > 0)
                                        Chỉ còn {{ $availableStock }} sản phẩm
                                    @else
                                        Hết hàng
                                    @endif
                                </div>

                                <div class="item-price-section">
                                    <span class="item-price">{{ number_format($currentPrice) }}₫</span>
                                    @if ($hasDiscount)
                                        <span class="item-price-old">{{ number_format($originalPrice) }}₫</span>
                                        <span class="item-discount-badge">
                                            Tiết kiệm {{ number_format($originalPrice - $currentPrice) }}₫
                                        </span>
                                    @endif
                                </div>

                                {{-- Hiển thị tổng tiền của item --}}
                                <div class="item-subtotal" style="margin-top: 10px; font-weight: 700; color: #1e293b;">
                                    Thành tiền: <span
                                        style="color: #ef4444; font-size: 18px;">{{ number_format($itemSubtotal) }}₫</span>
                                </div>
                            </div>

                            <div class="item-actions">
                                <button class="remove-item" data-item-id="{{ $item->id }}" title="Xóa sản phẩm">
                                    <i class="fas fa-trash-alt"></i> Xóa
                                </button>

                                <div class="quantity-control">
                                    <button class="qty-btn qty-decrease" data-item-id="{{ $item->id }}"
                                        {{ $item->quantity <= 1 || $availableStock < 1 ? 'disabled' : '' }}>
                                        -
                                    </button>
                                    <input type="number" class="qty-input" value="{{ $item->quantity }}" min="1"
                                        max="{{ $availableStock }}" data-item-id="{{ $item->id }}"
                                        data-max-stock="{{ $availableStock }}" readonly>
                                    <button class="qty-btn qty-increase" data-item-id="{{ $item->id }}"
                                        {{ $item->quantity >= $availableStock || $availableStock < 1 ? 'disabled' : '' }}>
                                        +
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Cart Summary -->
                <div class="cart-summary">
                    <h3 class="summary-title">
                        <i class="fas fa-receipt me-2"></i>Tóm Tắt Đơn Hàng
                    </h3>

                    <div class="summary-row">
                        <span class="label">
                            <i class="fas fa-box me-2"></i>Tạm tính:
                        </span>
                        <span class="value" id="subtotal-display">{{ number_format($subtotal) }}₫</span>
                    </div>

                    <div class="summary-row">
                        <span class="label">
                            <i class="fas fa-gift me-2"></i>Giảm giá:
                        </span>
                        <span class="value text-success" id="discount-display">-{{ number_format($discount) }}₫</span>
                    </div>

                    <div class="summary-row">
                        <span class="label">
                            <i class="fas fa-shipping-fast me-2"></i>Phí vận chuyển:
                        </span>
                        <span class="value">{{ number_format($shipping ?? 30000) }}đ</span>
                        {{-- <span class="value" id="shipping-display">{{ number_format($shipping) }}₫</span> --}}
                    </div>

                    <div class="summary-divider"></div>

                    <div class="summary-total">
                        <span>Tổng cộng:</span>
                        <span class="value" id="total-display">{{ number_format($total) }}₫</span>
                    </div>

                    <a href="{{ route('client.checkout.index') }}" class="btn btn-checkout">
                        <i class="fas fa-credit-card me-2"></i>Tiến Hành Thanh Toán
                    </a>

                    <a href="{{ route('client.products.index') }}" class="btn btn-continue">
                        <i class="fas fa-arrow-left me-2"></i>Tiếp Tục Mua Sắm
                    </a>
                </div>
            </div>
        @else
            <!-- Empty Cart -->
            <div class="empty-cart">
                <div class="empty-cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h3>Giỏ Hàng Trống</h3>
                <p>Bạn chưa có sản phẩm nào trong giỏ hàng. Hãy khám phá và thêm sản phẩm yêu thích!</p>
                <a href="{{ route('client.products.index') }}" class="btn-start-shopping">
                    <i class="fas fa-shopping-bag"></i>
                    Bắt Đầu Mua Sắm
                </a>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Increase quantity
            $('.qty-increase').click(function() {
                const btn = $(this);
                const itemId = btn.data('item-id');
                const input = $(`.qty-input[data-item-id="${itemId}"]`);
                const maxStock = parseInt(input.data('max-stock'));
                const current = parseInt(input.val());

                if (maxStock < 1) {
                    showToast('Sản phẩm đã hết hàng', 'warning');
                    return;
                }

                if (current >= maxStock) {
                    showToast('Đã đạt số lượng tối đa trong kho', 'warning');
                    return;
                }

                input.val(current + 1);
                updateCartItem(itemId, current + 1);
            });

            // Decrease quantity
            $('.qty-decrease').click(function() {
                const btn = $(this);
                const itemId = btn.data('item-id');
                const input = $(`.qty-input[data-item-id="${itemId}"]`);
                const current = parseInt(input.val());

                if (current <= 1) {
                    showToast('Số lượng tối thiểu là 1', 'warning');
                    return;
                }

                input.val(current - 1);
                updateCartItem(itemId, current - 1);
            });

            // Update cart item
            function updateCartItem(itemId, quantity) {
                $.ajax({
                    url: `/client/cart/update/${itemId}`,
                    method: 'PATCH', // ✅ SỬA: Dùng PATCH thay vì PUT
                    data: {
                        quantity: quantity,
                        _method: 'PATCH' // Laravel method spoofing
                    },
                    beforeSend: function() {
                        $(`.qty-btn[data-item-id="${itemId}"]`).prop('disabled', true);
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast('Đã cập nhật giỏ hàng', 'success');

                            // Reload để cập nhật tổng tiền
                            setTimeout(() => location.reload(), 500);
                        } else {
                            showToast(response.message || 'Có lỗi xảy ra', 'danger');
                        }
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message || 'Có lỗi xảy ra!';
                        showToast(message, 'danger');
                        setTimeout(() => location.reload(), 500);
                    },
                    complete: function() {
                        $(`.qty-btn[data-item-id="${itemId}"]`).prop('disabled', false);
                    }
                });
            }

            // Remove item
            $('.remove-item').click(function() {
                if (!confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) return;

                const itemId = $(this).data('item-id');
                $.ajax({
                    url: `/client/cart/remove/${itemId}`,
                    method: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            showToast('Đã xóa sản phẩm', 'success');
                            setTimeout(() => location.reload(), 500);
                        } else {
                            showToast(response.message || 'Có lỗi xảy ra', 'danger');
                        }
                    },
                    error: function() {
                        showToast('Có lỗi xảy ra khi xóa sản phẩm', 'danger');
                    }
                });
            });

            // Toast notification
            function showToast(message, type = 'info') {
                const bgColors = {
                    success: 'linear-gradient(135deg, #10b981, #059669)',
                    danger: 'linear-gradient(135deg, #ef4444, #dc2626)',
                    warning: 'linear-gradient(135deg, #f59e0b, #d97706)',
                    info: 'linear-gradient(135deg, #3b82f6, #2563eb)'
                };

                const icons = {
                    success: 'check-circle',
                    danger: 'exclamation-circle',
                    warning: 'exclamation-triangle',
                    info: 'info-circle'
                };

                const toast = $(`
            <div class="toast-notification" style="
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${bgColors[type]};
                color: white;
                padding: 16px 24px;
                border-radius: 12px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.3);
                z-index: 9999;
                font-weight: 600;
                display: flex;
                align-items: center;
                gap: 12px;
                animation: slideInRight 0.3s ease-out;
                max-width: 400px;
            ">
                <i class="fas fa-${icons[type]}"></i>
                ${message}
            </div>
        `);

                $('body').append(toast);
                setTimeout(() => toast.fadeOut(300, () => toast.remove()), 3000);
            }

            // Animation
            if (!$('style#toast-animation').length) {
                $('head').append(`
            <style id="toast-animation">
                @keyframes slideInRight {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
            </style>
        `);
            }
        });
    </script>
@endpush
