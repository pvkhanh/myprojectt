@extends('layouts.client')

@section('title', 'Thanh toán')

@push('styles')
    <style>
        .checkout-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .checkout-step {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 24px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .step-header {
            display: flex;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid #f0f0f0;
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 16px;
        }

        .step-title {
            font-size: 20px;
            font-weight: 600;
            color: #2d3748;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
            color: #4a5568;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .payment-method {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
        }

        .payment-method:hover {
            border-color: #667eea;
            background: #f7fafc;
        }

        .payment-method input[type="radio"] {
            width: 20px;
            height: 20px;
            margin-right: 16px;
        }

        .payment-method.selected {
            border-color: #667eea;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        }

        .payment-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 16px;
            font-size: 24px;
        }

        .order-summary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            padding: 30px;
            position: sticky;
            top: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .summary-row.total {
            font-size: 24px;
            font-weight: bold;
            border-bottom: none;
            margin-top: 10px;
        }

        .btn-checkout {
            width: 100%;
            padding: 16px;
            background: white;
            color: #667eea;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 20px;
        }

        .btn-checkout:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .cart-item {
            display: flex;
            align-items: center;
            padding: 16px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            margin-bottom: 12px;
        }

        .item-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 16px;
        }

        .item-info {
            flex: 1;
        }

        .item-name {
            font-weight: 500;
            margin-bottom: 4px;
        }

        .item-price {
            opacity: 0.9;
            font-size: 14px;
        }
    </style>
@endpush

@section('content')
    <div class="checkout-container">
        <form action="{{ route('client.checkout.process') }}" method="POST" id="checkoutForm">
            @csrf

            <div class="row">
                <!-- Left Column: Form -->
                <div class="col-lg-7">
                    <!-- Step 1: Shipping Address -->
                    <div class="checkout-step">
                        <div class="step-header">
                            <div class="step-number">1</div>
                            <div class="step-title">Thông tin giao hàng</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Họ tên người nhận <span class="text-danger">*</span></label>
                                    <input type="text" name="receiver_name" class="form-control"
                                        value="{{ old('receiver_name', $defaultAddress->receiver_name ?? '') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="tel" name="phone" class="form-control"
                                        value="{{ old('phone', $defaultAddress->phone ?? '') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Địa chỉ <span class="text-danger">*</span></label>
                            <input type="text" name="address" class="form-control"
                                value="{{ old('address', $defaultAddress->address ?? '') }}"
                                placeholder="Số nhà, tên đường..." required>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tỉnh/Thành phố <span class="text-danger">*</span></label>
                                    <select name="province" class="form-control" required>
                                        <option value="">Chọn tỉnh/thành</option>
                                        <option value="Hà Nội" selected>Hà Nội</option>
                                        <option value="Hồ Chí Minh">Hồ Chí Minh</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Quận/Huyện <span class="text-danger">*</span></label>
                                    <select name="district" class="form-control" required>
                                        <option value="">Chọn quận/huyện</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Phường/Xã <span class="text-danger">*</span></label>
                                    <select name="ward" class="form-control" required>
                                        <option value="">Chọn phường/xã</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Ghi chú (tùy chọn)</label>
                            <textarea name="note" class="form-control" rows="3" placeholder="Ghi chú về đơn hàng...">{{ old('note') }}</textarea>
                        </div>
                    </div>

                    <!-- Step 2: Payment Method -->
                    <div class="checkout-step">
                        <div class="step-header">
                            <div class="step-number">2</div>
                            <div class="step-title">Phương thức thanh toán</div>
                        </div>

                        @foreach ($paymentMethods as $method)
                            <label class="payment-method" for="payment_{{ $method->value }}">
                                <input type="radio" name="payment_method" id="payment_{{ $method->value }}"
                                    value="{{ $method->value }}" {{ $loop->first ? 'checked' : '' }} required>

                                <div class="payment-icon">
                                    <i class="fas fa-{{ $method->icon() }}"></i>
                                </div>

                                <div class="flex-1">
                                    <div class="fw-bold">{{ $method->label() }}</div>
                                    @if ($method === App\Enums\PaymentMethod::COD)
                                        <small class="text-muted">Thanh toán khi nhận hàng</small>
                                    @elseif($method === App\Enums\PaymentMethod::Card)
                                        <small class="text-muted">Thanh toán trực tuyến an toàn qua Stripe</small>
                                    @elseif($method === App\Enums\PaymentMethod::Bank)
                                        <small class="text-muted">Chuyển khoản sau khi đặt hàng</small>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Right Column: Order Summary -->
                <div class="col-lg-5">
                    <div class="order-summary">
                        <h4 class="mb-4">Đơn hàng của bạn</h4>

                        <div class="cart-items mb-4">
                            @foreach ($cartItems as $item)
                                <div class="cart-item">
                                    @if ($item->product->image)
                                        <img src="{{ asset('storage/' . $item->product->image) }}"
                                            alt="{{ $item->product->name }}" class="item-image">
                                    @endif

                                    <div class="item-info">
                                        <div class="item-name">{{ $item->product->name }}</div>
                                        <div class="item-price">
                                            {{ number_format($item->product->price) }}₫ × {{ $item->quantity }}
                                        </div>
                                    </div>

                                    <div class="fw-bold">
                                        {{ number_format($item->product->price * $item->quantity) }}₫
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="summary-row">
                            <span>Tạm tính:</span>
                            <span>{{ number_format($subtotal) }}₫</span>
                        </div>

                        <div class="summary-row">
                            <span>Phí vận chuyển:</span>
                            <span id="shippingFeeDisplay">{{ number_format($shippingFee) }}₫</span>
                        </div>

                        <div class="summary-row total">
                            <span>Tổng cộng:</span>
                            <span id="totalDisplay">{{ number_format($total) }}₫</span>
                        </div>

                        <button type="submit" class="btn-checkout">
                            <i class="fas fa-lock me-2"></i>
                            Đặt hàng
                        </button>

                        <div class="text-center mt-3">
                            <small>🔒 Thông tin của bạn được bảo mật</small>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Payment method selection
                document.querySelectorAll('.payment-method').forEach(method => {
                    method.addEventListener('click', function() {
                        document.querySelectorAll('.payment-method').forEach(m => m.classList.remove(
                            'selected'));
                        this.classList.add('selected');
                        this.querySelector('input[type="radio"]').checked = true;
                    });
                });

                // Calculate shipping on address change
                const provinceSelect = document.querySelector('select[name="province"]');
                const districtSelect = document.querySelector('select[name="district"]');
                const wardSelect = document.querySelector('select[name="ward"]');

                [provinceSelect, districtSelect, wardSelect].forEach(select => {
                    select?.addEventListener('change', calculateShipping);
                });

                function calculateShipping() {
                    // TODO: Implement real shipping calculation
                    console.log('Calculate shipping...');
                }
            });
        </script>
    @endpush
@endsection
