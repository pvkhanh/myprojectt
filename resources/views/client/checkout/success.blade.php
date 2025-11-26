@extends('layouts.client')

@section('title', 'Đặt hàng thành công')

@push('styles')
    <style>
        .success-container {
            max-width: 700px;
            margin: 60px auto;
            padding: 0 20px;
            text-align: center;
        }

        .success-icon {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: scaleIn 0.5s ease-out;
        }

        .success-icon i {
            font-size: 60px;
            color: white;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .success-title {
            font-size: 32px;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 16px;
        }

        .success-message {
            font-size: 18px;
            color: #718096;
            margin-bottom: 40px;
        }

        .order-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .order-number {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 30px;
        }

        .order-details {
            text-align: left;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 16px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .detail-row:last-child {
            border-bottom: none;
            font-size: 20px;
            font-weight: bold;
        }

        .action-buttons {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 14px 32px;
            border-radius: 10px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-secondary:hover {
            background: #667eea;
            color: white;
        }

        .next-steps {
            background: #f7fafc;
            border-radius: 12px;
            padding: 24px;
            margin-top: 30px;
        }

        .step-item {
            display: flex;
            align-items: center;
            margin-bottom: 16px;
            text-align: left;
        }

        .step-number {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #667eea;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 16px;
            flex-shrink: 0;
        }
    </style>
@endpush

@section('content')
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>

        <h1 class="success-title">Đặt hàng thành công!</h1>
        <p class="success-message">
            Cảm ơn bạn đã đặt hàng. Chúng tôi sẽ liên hệ với bạn trong thời gian sớm nhất.
        </p>

        <div class="order-card">
            <div class="order-number">
                Mã đơn hàng: {{ $order->order_number }}
            </div>

            <div class="order-details">
                <div class="detail-row">
                    <span class="text-muted">Tổng sản phẩm:</span>
                    <span class="fw-semibold">{{ $order->orderItems->count() }} sản phẩm</span>
                </div>

                <div class="detail-row">
                    <span class="text-muted">Phương thức thanh toán:</span>
                    <span class="fw-semibold">
                        @php
                            $payment = $order->payments->first();
                        @endphp
                        @if ($payment)
                            {{ $payment->payment_method->label() }}
                        @endif
                    </span>
                </div>

                <div class="detail-row">
                    <span class="text-muted">Trạng thái thanh toán:</span>
                    <span class="badge bg-{{ $payment?->status->color() ?? 'secondary' }} px-3 py-2">
                        {{ $payment?->status->label() ?? 'N/A' }}
                    </span>
                </div>

                <div class="detail-row">
                    <span class="text-muted">Địa chỉ giao hàng:</span>
                    <span class="fw-semibold text-end">
                        {{ $order->shippingAddress->address }},
                        {{ $order->shippingAddress->ward }},
                        {{ $order->shippingAddress->district }}
                    </span>
                </div>

                <div class="detail-row">
                    <span class="fw-bold">Tổng thanh toán:</span>
                    <span class="fw-bold text-primary">{{ number_format($order->total_amount) }}₫</span>
                </div>
            </div>
        </div>

        <div class="action-buttons">
            <a href="{{ route('client.orders.show', $order->id) }}" class="btn-action btn-primary">
                <i class="fas fa-receipt me-2"></i>
                Xem chi tiết đơn hàng
            </a>
            <a href="{{ route('client.products.index') }}" class="btn-action btn-secondary">
                <i class="fas fa-shopping-bag me-2"></i>
                Tiếp tục mua sắm
            </a>
        </div>

        <div class="next-steps">
            <h5 class="mb-4">Bước tiếp theo</h5>

            <div class="step-item">
                <div class="step-number">1</div>
                <div>
                    <strong>Xác nhận đơn hàng</strong><br>
                    <small class="text-muted">Chúng tôi sẽ gửi email xác nhận đơn hàng cho bạn</small>
                </div>
            </div>

            <div class="step-item">
                <div class="step-number">2</div>
                <div>
                    <strong>Đóng gói & vận chuyển</strong><br>
                    <small class="text-muted">Đơn hàng sẽ được đóng gói và giao đến bạn trong 2-3 ngày</small>
                </div>
            </div>

            <div class="step-item">
                <div class="step-number">3</div>
                <div>
                    <strong>Nhận hàng</strong><br>
                    <small class="text-muted">Kiểm tra sản phẩm trước khi thanh toán (nếu COD)</small>
                </div>
            </div>
        </div>
    </div>
@endsection
