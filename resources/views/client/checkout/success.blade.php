@extends('client.layouts.master')

@section('title', 'Đặt hàng thành công')

@push('styles')
    <style>
        .success-container {
            max-width: 900px;
            margin: 60px auto;
            padding: 0 20px;
        }

        .success-animation {
            text-align: center;
            margin-bottom: 40px;
        }

        .success-icon {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: scaleIn 0.6s ease-out, pulse 2s ease-in-out 0.6s infinite;
            box-shadow: 0 20px 60px rgba(16, 185, 129, 0.4);
        }

        .success-icon i {
            font-size: 70px;
            color: white;
            animation: checkmark 0.8s ease-in-out 0.2s both;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0) rotate(-180deg);
                opacity: 0;
            }

            to {
                transform: scale(1) rotate(0deg);
                opacity: 1;
            }
        }

        @keyframes checkmark {
            0% {
                transform: scale(0);
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .success-title {
            font-size: 36px;
            font-weight: 900;
            background: linear-gradient(135deg, #10b981, #059669);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 16px;
            animation: fadeInUp 0.6s ease-out 0.3s both;
        }

        .success-message {
            font-size: 18px;
            color: #64748b;
            margin-bottom: 50px;
            animation: fadeInUp 0.6s ease-out 0.4s both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .order-card {
            background: white;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 10px 50px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            animation: fadeInUp 0.6s ease-out 0.5s both;
        }

        .order-number {
            text-align: center;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            margin-bottom: 35px;
        }

        .order-number-label {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .order-number-value {
            color: white;
            font-size: 28px;
            font-weight: 900;
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
        }

        .order-details {
            display: grid;
            gap: 20px;
        }

        .detail-row {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 20px;
            padding: 20px;
            border-radius: 12px;
            background: #f8fafc;
            transition: all 0.3s;
        }

        .detail-row:hover {
            background: #f1f5f9;
            transform: translateX(5px);
        }

        .detail-label {
            color: #64748b;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .detail-label i {
            font-size: 18px;
            color: #667eea;
        }

        .detail-value {
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
        }

        .order-items {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #e2e8f0;
        }

        .order-items-title {
            font-size: 20px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .order-item {
            display: flex;
            gap: 20px;
            padding: 15px;
            background: white;
            border-radius: 12px;
            margin-bottom: 12px;
            border: 2px solid #f1f5f9;
        }

        .order-item-image {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            overflow: hidden;
            flex-shrink: 0;
        }

        .order-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .order-item-details {
            flex: 1;
        }

        .order-item-name {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 5px;
        }

        .order-item-meta {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .order-item-price {
            font-weight: 800;
            color: #ef4444;
            font-size: 18px;
        }

        .total-section {
            margin-top: 20px;
            padding: 25px;
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border-radius: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-label {
            font-size: 24px;
            font-weight: 800;
            color: #92400e;
        }

        .total-value {
            font-size: 32px;
            font-weight: 900;
            color: #ef4444;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            animation: fadeInUp 0.6s ease-out 0.6s both;
        }

        .btn-action {
            padding: 18px 35px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 16px;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.5);
        }

        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-secondary:hover {
            background: #667eea;
            color: white;
            transform: translateY(-3px);
        }

        .next-steps {
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
            border-radius: 20px;
            padding: 35px;
            margin-top: 40px;
            animation: fadeInUp 0.6s ease-out 0.7s both;
        }

        .next-steps-title {
            font-size: 24px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 30px;
            text-align: center;
        }

        .step-timeline {
            position: relative;
        }

        .step-item {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            position: relative;
        }

        .step-item:last-child {
            margin-bottom: 0;
        }

        .step-item:not(:last-child)::before {
            content: '';
            position: absolute;
            left: 23px;
            top: 50px;
            width: 3px;
            height: calc(100% - 20px);
            background: linear-gradient(to bottom, #667eea, #e2e8f0);
        }

        .step-number {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 20px;
            flex-shrink: 0;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            z-index: 1;
        }

        .step-content {
            flex: 1;
            padding-top: 5px;
        }

        .step-title {
            font-weight: 800;
            color: #1e293b;
            font-size: 18px;
            margin-bottom: 8px;
        }

        .step-description {
            color: #64748b;
            font-size: 15px;
            line-height: 1.6;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 13px;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-info {
            background: #dbeafe;
            color: #1e40af;
        }

        @media (max-width: 768px) {
            .success-title {
                font-size: 28px;
            }

            .order-number-value {
                font-size: 20px;
            }

            .detail-row {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .action-buttons {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <div class="success-container">
        <div class="success-animation">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            <h1 class="success-title">Đặt Hàng Thành Công!</h1>
            <p class="success-message">
                Cảm ơn bạn đã đặt hàng. Chúng tôi sẽ liên hệ với bạn trong thời gian sớm nhất.
            </p>
        </div>

        <div class="order-card">
            <div class="order-number">
                <div class="order-number-label">Mã đơn hàng</div>
                <div class="order-number-value">{{ $order->order_number }}</div>
            </div>

            <div class="order-details">
                <div class="detail-row">
                    <div class="detail-label">
                        <i class="fas fa-calendar-alt"></i>
                        Ngày đặt hàng:
                    </div>
                    <div class="detail-value">
                        {{ $order->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">
                        <i class="fas fa-box"></i>
                        Tổng sản phẩm:
                    </div>
                    <div class="detail-value">
                        {{ $order->orderItems->count() }} sản phẩm
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">
                        <i class="fas fa-credit-card"></i>
                        Phương thức thanh toán:
                    </div>
                    <div class="detail-value">
                        @php
                            $payment = $order->payments->first();
                        @endphp
                        {{ $payment?->payment_method->label() ?? 'N/A' }}
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">
                        <i class="fas fa-check-circle"></i>
                        Trạng thái thanh toán:
                    </div>
                    <div class="detail-value">
                        <span class="badge badge-{{ $payment?->status->value === 'completed' ? 'success' : 'warning' }}">
                            {{ $payment?->status->label() ?? 'Đang xử lý' }}
                        </span>
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">
                        <i class="fas fa-map-marker-alt"></i>
                        Địa chỉ giao hàng:
                    </div>
                    <div class="detail-value">
                        {{ $order->shippingAddress->address }},
                        {{ $order->shippingAddress->ward }},
                        {{ $order->shippingAddress->district }},
                        {{ $order->shippingAddress->province }}
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="order-items">
                <div class="order-items-title">
                    <i class="fas fa-shopping-bag"></i>
                    Chi tiết đơn hàng
                </div>
                @foreach ($order->orderItems as $item)
                    <div class="order-item">
                        <div class="order-item-image">
                            <img src="{{ $item->product->image ?? 'https://via.placeholder.com/80' }}"
                                alt="{{ $item->product->name }}">
                        </div>
                        <div class="order-item-details">
                            <div class="order-item-name">{{ $item->product->name }}</div>
                            <div class="order-item-meta">
                                @if ($item->variant)
                                    Phân loại: {{ $item->variant->name }} |
                                @endif
                                Số lượng: x{{ $item->quantity }}
                            </div>
                            <div class="order-item-price">
                                {{ number_format($item->price * $item->quantity) }}₫
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Total -->
            <div class="total-section">
                <div class="total-label">Tổng thanh toán:</div>
                <div class="total-value">{{ number_format($order->total_amount) }}₫</div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('client.orders.show', $order->id) }}" class="btn-action btn-primary">
                <i class="fas fa-receipt"></i>
                Chi tiết đơn hàng
            </a>
            <a href="{{ route('client.products.index') }}" class="btn-action btn-secondary">
                <i class="fas fa-shopping-bag"></i>
                Tiếp tục mua sắm
            </a>
        </div>

        <!-- Next Steps -->
        <div class="next-steps">
            <h3 class="next-steps-title">Các bước tiếp theo</h3>
            <div class="step-timeline">
                <div class="step-item">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <div class="step-title">Xác nhận đơn hàng</div>
                        <div class="step-description">
                            Chúng tôi sẽ gửi email xác nhận đơn hàng đến địa chỉ email của bạn trong vòng 5-10 phút
                        </div>
                    </div>
                </div>

                <div class="step-item">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <div class="step-title">Đóng gói & vận chuyển</div>
                        <div class="step-description">
                            Đơn hàng sẽ được đóng gói cẩn thận và giao đến bạn trong 2-3 ngày làm việc (khu vực nội thành)
                        </div>
                    </div>
                </div>

                <div class="step-item">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <div class="step-title">Nhận hàng & thanh toán</div>
                        <div class="step-description">
                            Vui lòng kiểm tra kỹ sản phẩm trước khi thanh toán (nếu chọn COD). Đảm bảo sản phẩm nguyên vẹn
                            và đúng như đơn hàng
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Support Info -->
        <div class="text-center mt-5" style="color: #64748b;">
            <p style="font-size: 15px;">
                <i class="fas fa-headset me-2"></i>
                Nếu bạn có bất kỳ thắc mắc nào, vui lòng liên hệ
                <strong style="color: #667eea;">Hotline: 1900-xxxx</strong> hoặc
                <strong style="color: #667eea;">Email: support@yourstore.com</strong>
            </p>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Confetti effect on load
        window.addEventListener('load', function() {
            // Optional: Add confetti animation library if you want
            console.log('Order placed successfully!');
        });
    </script>
@endpush
