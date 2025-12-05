{{-- resources/views/client/checkout/index.blade.php --}}
@extends('client.layouts.master')

@section('title', 'Thanh toán')

@push('styles')
    <style>
        .checkout-page {
            padding: 60px 0;
            background: #f8fafc;
        }

        .checkout-section {
            background: white;
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-label {
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
        }

        .form-control,
        .form-select {
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            transition: all 0.3s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .shipping-method {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .shipping-method:hover {
            border-color: var(--primary-color);
            background: #f8fafc;
        }

        .shipping-method.active {
            border-color: var(--primary-color);
            background: #eff6ff;
        }

        .shipping-method input[type="radio"] {
            margin-right: 12px;
            width: 20px;
            height: 20px;
        }

        .payment-method {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .payment-method:hover {
            border-color: var(--primary-color);
            background: #f8fafc;
        }

        .payment-method.active {
            border-color: var(--primary-color);
            background: #eff6ff;
        }

        .payment-icon {
            width: 60px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .order-summary {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 100px;
        }

        .summary-item {
            border-bottom: 1px solid #e2e8f0;
            padding: 15px 0;
            display: flex;
            gap: 15px;
        }

        .summary-item:last-child {
            border-bottom: none;
        }

        .item-image {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            overflow: hidden;
        }

        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .item-variant {
            color: #64748b;
            font-size: 13px;
            margin-bottom: 5px;
        }

        .item-price {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
        }

        .summary-totals {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 15px;
        }

        .total-row.final {
            font-size: 22px;
            font-weight: 800;
            color: #ef4444;
            padding-top: 15px;
            border-top: 2px solid #e2e8f0;
            margin-top: 15px;
        }

        .btn-place-order {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 25px;
        }

        .btn-place-order:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(239, 68, 68, 0.4);
        }

        .security-note {
            text-align: center;
            color: #64748b;
            font-size: 13px;
            margin-top: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
    </style>
@endpush

@section('content')
    <div class="checkout-page">
        <div class="container">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('client.cart.index') }}">Giỏ hàng</a></li>
                    <li class="breadcrumb-item active">Thanh toán</li>
                </ol>
            </nav>

            <form action="{{ route('client.checkout.process') }}" method="POST" id="checkoutForm">
                @csrf
                <div class="row">
                    <!-- Checkout Form -->
                    <div class="col-lg-7">
                        <!-- Customer Information -->
                        <div class="checkout-section">
                            <h2 class="section-title">
                                <i class="fas fa-user"></i>
                                Thông tin khách hàng
                            </h2>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Họ và tên *</label>
                                    <input type="text" class="form-control" name="name"
                                        value="{{ auth()->user()->name ?? '' }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Số điện thoại *</label>
                                    <input type="tel" class="form-control" name="phone"
                                        value="{{ auth()->user()->phone ?? '' }}" required>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Email *</label>
                                    <input type="email" class="form-control" name="email"
                                        value="{{ auth()->user()->email ?? '' }}" required>
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Address -->
                        <div class="checkout-section">
                            <h2 class="section-title">
                                <i class="fas fa-map-marker-alt"></i>
                                Địa chỉ giao hàng
                            </h2>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Tỉnh/Thành phố *</label>
                                    <select class="form-select" name="province" required>
                                        <option value="">Chọn Tỉnh/TP</option>
                                        <option value="hanoi">Hà Nội</option>
                                        <option value="hcm">TP.HCM</option>
                                        <option value="danang">Đà Nẵng</option>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Quận/Huyện *</label>
                                    <select class="form-select" name="district" required>
                                        <option value="">Chọn Quận/Huyện</option>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Phường/Xã *</label>
                                    <select class="form-select" name="ward" required>
                                        <option value="">Chọn Phường/Xã</option>
                                    </select>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Địa chỉ cụ thể *</label>
                                    <input type="text" class="form-control" name="address"
                                        placeholder="Số nhà, tên đường..." required>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Ghi chú</label>
                                    <textarea class="form-control" name="note" rows="3"
                                        placeholder="Ghi chú về đơn hàng, ví dụ: thời gian hay chỉ dẫn địa điểm giao hàng chi tiết hơn"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Method -->
                        <div class="checkout-section">
                            <h2 class="section-title">
                                <i class="fas fa-truck"></i>
                                Phương thức vận chuyển
                            </h2>

                            <label class="shipping-method active">
                                <input type="radio" name="shipping_method" value="standard" checked>
                                <div>
                                    <strong>Giao hàng tiêu chuẩn</strong>
                                    <div class="text-muted mt-1">Giao trong 3-5 ngày - 30.000đ</div>
                                </div>
                            </label>

                            <label class="shipping-method">
                                <input type="radio" name="shipping_method" value="express">
                                <div>
                                    <strong>Giao hàng nhanh</strong>
                                    <div class="text-muted mt-1">Giao trong 1-2 ngày - 50.000đ</div>
                                </div>
                            </label>
                        </div>

                        <!-- Payment Method -->
                        <div class="checkout-section">
                            <h2 class="section-title">
                                <i class="fas fa-credit-card"></i>
                                Phương thức thanh toán
                            </h2>

                            <label class="payment-method active">
                                <input type="radio" name="payment_method" value="cod" checked>
                                <div class="payment-icon">
                                    <i class="fas fa-money-bill-wave" style="color: #10b981;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Thanh toán khi nhận hàng (COD)</strong>
                                    <div class="text-muted small mt-1">Thanh toán bằng tiền mặt khi nhận hàng</div>
                                </div>
                            </label>

                            <label class="payment-method">
                                <input type="radio" name="payment_method" value="bank">
                                <div class="payment-icon">
                                    <i class="fas fa-university" style="color: #3b82f6;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Chuyển khoản ngân hàng</strong>
                                    <div class="text-muted small mt-1">Chuyển khoản qua Internet Banking</div>
                                </div>
                            </label>

                            <label class="payment-method">
                                <input type="radio" name="payment_method" value="momo">
                                <div class="payment-icon">
                                    <i class="fab fa-apple-pay" style="color: #a61d4d;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>Ví MoMo</strong>
                                    <div class="text-muted small mt-1">Thanh toán qua ví điện tử MoMo</div>
                                </div>
                            </label>

                            <label class="payment-method">
                                <input type="radio" name="payment_method" value="vnpay">
                                <div class="payment-icon">
                                    <i class="fas fa-credit-card" style="color: #0064b0;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>VNPay</strong>
                                    <div class="text-muted small mt-1">Thanh toán qua cổng VNPay</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="col-lg-5">
                        <div class="order-summary">
                            <h2 class="section-title">
                                <i class="fas fa-receipt"></i>
                                Đơn hàng của bạn
                            </h2>

                            <div class="summary-items">
                                @foreach ($cartItems ?? [] as $item)
                                    <div class="summary-item">
                                        <div class="item-image">
                                            <img src="{{ $item->product->main_image ?? 'https://via.placeholder.com/80' }}"
                                                alt="{{ $item->product->name }}">
                                        </div>
                                        <div class="item-details">
                                            <div class="item-name">{{ $item->product->name }}</div>
                                            @if ($item->variant)
                                                <div class="item-variant">{{ $item->variant }}</div>
                                            @endif
                                            <div class="item-price">
                                                <span class="text-muted">SL: {{ $item->quantity }}</span>
                                                <strong>{{ number_format($item->price * $item->quantity) }}đ</strong>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="summary-totals">
                                <div class="total-row">
                                    <span>Tạm tính:</span>
                                    <strong>{{ number_format($subtotal ?? 0) }}đ</strong>
                                </div>

                                <div class="total-row">
                                    <span>Phí vận chuyển:</span>
                                    <strong id="shippingFee">30.000đ</strong>
                                </div>

                                @if (isset($discount) && $discount > 0)
                                    <div class="total-row" style="color: #10b981;">
                                        <span>Giảm giá:</span>
                                        <strong>-{{ number_format($discount) }}đ</strong>
                                    </div>
                                @endif

                                <div class="total-row final">
                                    <span>Tổng cộng:</span>
                                    <span
                                        id="finalTotal">{{ number_format(($subtotal ?? 0) + 30000 - ($discount ?? 0)) }}đ</span>
                                </div>
                            </div>

                            <button type="submit" class="btn-place-order">
                                <i class="fas fa-check-circle me-2"></i>
                                Đặt hàng
                            </button>

                            <div class="security-note">
                                <i class="fas fa-lock"></i>
                                Thông tin của bạn được bảo mật
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Handle shipping method change
            $('input[name="shipping_method"]').change(function() {
                $('.shipping-method').removeClass('active');
                $(this).closest('.shipping-method').addClass('active');

                const fee = $(this).val() === 'express' ? 50000 : 30000;
                updateShippingFee(fee);
            });

            // Handle payment method change
            $('input[name="payment_method"]').change(function() {
                $('.payment-method').removeClass('active');
                $(this).closest('.payment-method').addClass('active');
            });

            // Form validation
            $('#checkoutForm').submit(function(e) {
                e.preventDefault();

                if (!validateForm()) {
                    return false;
                }

                // Show loading
                const btn = $('.btn-place-order');
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...');

                // Submit form
                this.submit();
            });
        });

        function updateShippingFee(fee) {
            $('#shippingFee').text(fee.toLocaleString('vi-VN') + 'đ');

            // Recalculate total
            const subtotal = {{ $subtotal ?? 0 }};
            const discount = {{ $discount ?? 0 }};
            const total = subtotal + fee - discount;

            $('#finalTotal').text(total.toLocaleString('vi-VN') + 'đ');
        }

        function validateForm() {
            const required = $('[required]');
            let valid = true;

            required.each(function() {
                if (!$(this).val()) {
                    $(this).addClass('is-invalid');
                    valid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            if (!valid) {
                alert('Vui lòng điền đầy đủ thông tin bắt buộc');
            }

            return valid;
        }
    </script>
@endpush
