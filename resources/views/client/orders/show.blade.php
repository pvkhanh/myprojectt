@extends('layouts.client')

@section('title', 'Chi tiết đơn hàng #' . $order->order_number)

@push('styles')
    <style>
        .order-detail-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            color: #667eea;
            text-decoration: none;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .back-button:hover {
            color: #764ba2;
        }

        .order-header-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 24px;
        }

        .order-number-large {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .order-meta {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .timeline {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 24px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .timeline-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            padding: 40px 0;
        }

        .timeline-step {
            flex: 1;
            text-align: center;
            position: relative;
        }

        .timeline-step::before {
            content: '';
            position: absolute;
            top: 30px;
            left: 50%;
            right: -50%;
            height: 4px;
            background: #e0e0e0;
            z-index: 0;
        }

        .timeline-step:last-child::before {
            display: none;
        }

        .timeline-step.completed::before {
            background: #48bb78;
        }

        .timeline-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #e0e0e0;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-size: 24px;
            position: relative;
            z-index: 1;
            border: 4px solid white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .timeline-step.completed .timeline-icon {
            background: #48bb78;
        }

        .timeline-step.active .timeline-icon {
            background: #667eea;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        .detail-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .card-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #2d3748;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .product-row {
            display: flex;
            align-items: center;
            padding: 16px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .product-row:last-child {
            border-bottom: none;
        }

        .product-image {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 16px;
        }

        .product-info {
            flex: 1;
        }

        .product-name {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 4px;
        }

        .product-variant {
            font-size: 14px;
            color: #718096;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .summary-row:last-child {
            border-bottom: none;
            font-size: 20px;
            font-weight: bold;
            color: #667eea;
            padding-top: 16px;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-success {
            background: #48bb78;
            color: white;
        }

        .btn-danger {
            background: #f56565;
            color: white;
        }

        .btn-outline {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
    </style>
@endpush

@section('content')
    <div class="order-detail-container">
        <a href="{{ route('client.orders') }}" class="back-button">
            <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
        </a>

        <!-- Order Header -->
        <div class="order-header-card">
            <div class="order-number-large">Đơn hàng #{{ $order->order_number }}</div>
            <div class="order-meta">
                <div class="meta-item">
                    <i class="far fa-calendar"></i>
                    <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-tag"></i>
                    <span class="badge bg-light text-dark">{{ $order->status->label() }}</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-money-bill"></i>
                    <span>{{ number_format($order->total_amount) }}₫</span>
                </div>
            </div>
        </div>

        <!-- Timeline -->
        @if ($order->status->value !== 'cancelled')
            <div class="timeline">
                <div class="timeline-steps">
                    @php
                        $steps = [
                            ['status' => 'pending', 'icon' => 'clock', 'label' => 'Đơn hàng đã đặt'],
                            ['status' => 'paid', 'icon' => 'credit-card', 'label' => 'Đã thanh toán'],
                            ['status' => 'shipped', 'icon' => 'truck', 'label' => 'Đang giao hàng'],
                            ['status' => 'completed', 'icon' => 'check-circle', 'label' => 'Hoàn thành'],
                        ];
                        $currentIndex = array_search($order->status->value, array_column($steps, 'status'));
                    @endphp

                    @foreach ($steps as $index => $step)
                        <div
                            class="timeline-step {{ $index <= $currentIndex ? 'completed' : '' }} {{ $index === $currentIndex ? 'active' : '' }}">
                            <div class="timeline-icon">
                                <i class="fas fa-{{ $step['icon'] }}"></i>
                            </div>
                            <div class="fw-semibold">{{ $step['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                <!-- Products -->
                <div class="detail-card">
                    <div class="card-title">
                        <i class="fas fa-box"></i>
                        Sản phẩm đã đặt ({{ $order->orderItems->count() }})
                    </div>

                    @foreach ($order->orderItems as $item)
                        <div class="product-row">
                            @if ($item->product->image)
                                <img src="{{ asset('storage/' . $item->product->image) }}"
                                    alt="{{ $item->product->name }}" class="product-image">
                            @else
                                <div class="product-image"
                                    style="background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-image text-muted fs-3"></i>
                                </div>
                            @endif

                            <div class="product-info">
                                <div class="product-name">{{ $item->product->name }}</div>
                                @if ($item->variant)
                                    <div class="product-variant">{{ $item->variant->name }}</div>
                                @endif
                                <div class="text-muted small">{{ number_format($item->price) }}₫ × {{ $item->quantity }}
                                </div>
                            </div>

                            <div class="fw-bold text-primary">
                                {{ number_format($item->price * $item->quantity) }}₫
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Shipping Address -->
                <div class="detail-card">
                    <div class="card-title">
                        <i class="fas fa-location-dot"></i>
                        Địa chỉ giao hàng
                    </div>
                    <div>
                        <div class="fw-bold mb-2">{{ $order->shippingAddress->receiver_name }}</div>
                        <div class="text-muted mb-1">
                            <i class="fas fa-phone me-2"></i>{{ $order->shippingAddress->phone }}
                        </div>
                        <div class="text-muted">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            {{ $order->shippingAddress->address }},
                            {{ $order->shippingAddress->ward }},
                            {{ $order->shippingAddress->district }},
                            {{ $order->shippingAddress->province }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Summary -->
                <div class="detail-card">
                    <div class="card-title">
                        <i class="fas fa-receipt"></i>
                        Tóm tắt đơn hàng
                    </div>

                    <div class="summary-row">
                        <span>Tạm tính:</span>
                        <span>{{ number_format($order->subtotal) }}₫</span>
                    </div>

                    <div class="summary-row">
                        <span>Phí vận chuyển:</span>
                        <span>{{ number_format($order->shipping_fee) }}₫</span>
                    </div>

                    <div class="summary-row">
                        <span>Tổng cộng:</span>
                        <span>{{ number_format($order->total_amount) }}₫</span>
                    </div>
                </div>

                <!-- Payment Info -->
                @php $payment = $order->payments->first(); @endphp
                <div class="detail-card">
                    <div class="card-title">
                        <i class="fas fa-credit-card"></i>
                        Thanh toán
                    </div>

                    <div class="mb-2">
                        <strong>Phương thức:</strong><br>
                        {{ $payment?->payment_method->label() ?? 'N/A' }}
                    </div>

                    <div class="mb-2">
                        <strong>Trạng thái:</strong><br>
                        <span class="badge bg-{{ $payment?->status->color() ?? 'secondary' }}">
                            {{ $payment?->status->label() ?? 'N/A' }}
                        </span>
                    </div>

                    @if ($payment?->transaction_id)
                        <div>
                            <strong>Mã giao dịch:</strong><br>
                            <code>{{ $payment->transaction_id }}</code>
                        </div>
                    @endif
                </div>

                <!-- Actions -->
                <div class="detail-card">
                    <div class="card-title">
                        <i class="fas fa-tools"></i>
                        Thao tác
                    </div>

                    <div class="action-buttons">
                        @if (in_array($order->status->value, ['pending', 'paid']))
                            <button class="btn btn-danger w-100" onclick="cancelOrder({{ $order->id }})">
                                <i class="fas fa-times me-2"></i>Hủy đơn hàng
                            </button>
                        @endif

                        @if ($order->status->value === 'shipped')
                            <form action="{{ route('client.orders.confirm-received', $order->id) }}" method="POST"
                                class="w-100">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-check me-2"></i>Đã nhận hàng
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('client.orders.track', $order->id) }}" class="btn btn-outline w-100">
                            <i class="fas fa-route me-2"></i>Theo dõi đơn hàng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function cancelOrder(orderId) {
                Swal.fire({
                    title: 'Hủy đơn hàng?',
                    text: 'Bạn có chắc chắn muốn hủy đơn hàng này?',
                    icon: 'warning',
                    input: 'textarea',
                    inputPlaceholder: 'Nhập lý do hủy đơn...',
                    inputAttributes: {
                        required: true
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Xác nhận hủy',
                    cancelButtonText: 'Đóng',
                    confirmButtonColor: '#dc3545',
                    preConfirm: (reason) => {
                        if (!reason) {
                            Swal.showValidationMessage('Vui lòng nhập lý do');
                        }
                        return reason;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/client/orders/${orderId}/cancel`;

                        form.innerHTML = `
                @csrf
                <input type="hidden" name="reason" value="${result.value}">
            `;

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
        </script>
    @endpush
@endsection
