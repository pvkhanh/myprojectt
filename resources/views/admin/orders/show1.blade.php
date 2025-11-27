@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng #' . $order->order_number)

@push('styles')
    <style>
        .timeline-container {
            position: relative;
            padding: 40px 0;
        }

        .timeline-step {
            position: relative;
            text-align: center;
            flex: 1;
        }

        .timeline-step::before {
            content: '';
            position: absolute;
            top: 40px;
            left: 50%;
            right: -50%;
            height: 4px;
            background: #e0e0e0;
            z-index: 0;
        }

        .timeline-step:last-child::before {
            display: none;
        }

        .timeline-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 30px;
            color: white;
            position: relative;
            z-index: 1;
            background: #e0e0e0;
            border: 5px solid #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .timeline-step.active .timeline-icon {
            animation: pulse 2s infinite;
            box-shadow: 0 6px 25px rgba(102, 126, 234, 0.4);
        }

        .timeline-step.completed::before {
            background: linear-gradient(to right, #28a745, #20c997);
        }

        .timeline-step.completed .timeline-icon {
            background: linear-gradient(135deg, #28a745, #20c997);
        }

        .timeline-step.active .timeline-icon {
            background: linear-gradient(135deg, #667eea, #764ba2);
            transform: scale(1.1);
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1.1);
            }

            50% {
                transform: scale(1.15);
            }
        }

        .payment-method-badge {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .payment-status-badge {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .payment-alert {
            border-left: 4px solid #ffc107;
            animation: glow 2s ease-in-out infinite;
        }

        @keyframes glow {

            0%,
            100% {
                box-shadow: 0 0 5px rgba(255, 193, 7, 0.3);
            }

            50% {
                box-shadow: 0 0 20px rgba(255, 193, 7, 0.6);
            }
        }

        .action-card {
            transition: transform 0.2s;
        }

        .action-card:hover {
            transform: translateY(-3px);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold text-dark mb-2">
                            <i class="fa-solid fa-file-invoice text-primary me-2"></i>
                            Chi tiết đơn hàng #{{ $order->order_number }}
                        </h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Đơn hàng</a></li>
                                <li class="breadcrumb-item active">#{{ $order->order_number }}</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-warning btn-lg">
                            <i class="fa-solid fa-pen me-2"></i> Chỉnh sửa
                        </a>
                        <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank"
                            class="btn btn-success btn-lg">
                            <i class="fa-solid fa-print me-2"></i> In hóa đơn
                        </a>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-lg">
                            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Timeline -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">
                    <i class="fa-solid fa-timeline text-primary me-2"></i>Trạng thái đơn hàng
                </h5>

                @if ($order->status->value === 'cancelled')
                    <div class="alert alert-danger">
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-ban fs-3 me-3"></i>
                            <div>
                                <strong>Đơn hàng đã bị hủy</strong>
                                @if ($order->cancelled_at)
                                    <p class="mb-0 small">Thời gian: {{ $order->cancelled_at->format('d/m/Y H:i') }}</p>
                                @endif
                                @if ($order->admin_note)
                                    <p class="mb-0 small">Lý do: {{ $order->admin_note }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="timeline-container">
                        <div class="d-flex justify-content-between">
                            @php
                                $steps = [
                                    [
                                        'key' => 'pending',
                                        'icon' => 'clock',
                                        'label' => 'Chờ xử lý',
                                        'time' => $order->created_at,
                                    ],
                                    [
                                        'key' => 'paid',
                                        'icon' => 'credit-card',
                                        'label' => 'Đã thanh toán',
                                        'time' => $order->paid_at,
                                    ],
                                    [
                                        'key' => 'shipped',
                                        'icon' => 'truck',
                                        'label' => 'Đang giao',
                                        'time' => $order->shipped_at,
                                    ],
                                    [
                                        'key' => 'completed',
                                        'icon' => 'check-circle',
                                        'label' => 'Hoàn thành',
                                        'time' => $order->completed_at,
                                    ],
                                ];
                                $currentStep = array_search($order->status->value, array_column($steps, 'key'));
                            @endphp

                            @foreach ($steps as $index => $step)
                                @php
                                    $isCompleted = $index < $currentStep;
                                    $isActive = $index === $currentStep;
                                @endphp
                                <div
                                    class="timeline-step {{ $isCompleted ? 'completed' : '' }} {{ $isActive ? 'active' : '' }}">
                                    <div class="timeline-icon">
                                        <i class="fa-solid fa-{{ $step['icon'] }}"></i>
                                    </div>
                                    <div class="fw-semibold mb-1">{{ $step['label'] }}</div>
                                    @if ($step['time'])
                                        <div class="small text-muted">{{ $step['time']->format('d/m/Y H:i') }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Column -->
            <div class="col-lg-8">
                <!-- Order Items -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-box text-primary me-2"></i>Sản phẩm đã đặt
                            <span class="badge bg-primary ms-2">{{ $order->orderItems->count() }}</span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-4 py-3">Sản phẩm</th>
                                        <th class="px-4 py-3 text-center">Đơn giá</th>
                                        <th class="px-4 py-3 text-center">Số lượng</th>
                                        <th class="px-4 py-3 text-end">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->orderItems as $item)
                                        <tr>
                                            <td class="px-4 py-3">
                                                <div class="d-flex align-items-center">
                                                    @if ($item->product->image)
                                                        <img src="{{ asset('storage/' . $item->product->image) }}"
                                                            alt="{{ $item->product->name }}" class="rounded me-3"
                                                            style="width: 60px; height: 60px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                                            style="width: 60px; height: 60px;">
                                                            <i class="fa-solid fa-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="fw-semibold text-dark">{{ $item->product->name }}</div>
                                                        @if ($item->variant)
                                                            <div class="small text-muted">
                                                                <i
                                                                    class="fa-solid fa-tag me-1"></i>{{ $item->variant->name }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="fw-semibold">{{ number_format($item->price) }}₫</span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="badge bg-primary fs-6 px-3 py-2">{{ $item->quantity }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-end">
                                                <span class="fw-bold text-primary fs-6">
                                                    {{ number_format($item->price * $item->quantity) }}₫
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <td colspan="3" class="px-4 py-3 text-end fw-semibold">Tạm tính:</td>
                                        <td class="px-4 py-3 text-end fw-bold">{{ number_format($order->subtotal) }}₫</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="px-4 py-3 text-end fw-semibold">Phí vận chuyển:</td>
                                        <td class="px-4 py-3 text-end fw-bold">{{ number_format($order->shipping_fee) }}₫
                                        </td>
                                    </tr>
                                    <tr class="table-primary">
                                        <td colspan="3" class="px-4 py-3 text-end fw-bold fs-5">TỔNG CỘNG:</td>
                                        <td class="px-4 py-3 text-end fw-bold text-primary fs-5">
                                            {{ number_format($order->total_amount) }}₫
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Shipping Address -->
                @if ($order->shippingAddress)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-location-dot text-primary me-2"></i>Địa chỉ giao hàng
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="text-muted small mb-1">Người nhận</label>
                                    <div class="fw-semibold">{{ $order->shippingAddress->receiver_name }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small mb-1">Số điện thoại</label>
                                    <div class="fw-semibold">
                                        <i class="fa-solid fa-phone text-primary me-2"></i>
                                        <a href="tel:{{ $order->shippingAddress->phone }}" class="text-decoration-none">
                                            {{ $order->shippingAddress->phone }}
                                        </a>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="text-muted small mb-1">Địa chỉ</label>
                                    <div class="fw-semibold">
                                        <i class="fa-solid fa-map-marker-alt text-primary me-2"></i>
                                        {{ $order->shippingAddress->address }},
                                        {{ $order->shippingAddress->ward }},
                                        {{ $order->shippingAddress->district }},
                                        {{ $order->shippingAddress->province }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Notes -->
                @if ($order->customer_note || $order->admin_note)
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-note-sticky text-primary me-2"></i>Ghi chú
                            </h5>
                        </div>
                        <div class="card-body">
                            @if ($order->customer_note)
                                <div class="mb-3">
                                    <label class="text-muted small mb-1">Ghi chú của khách hàng:</label>
                                    <div class="p-3 bg-light rounded">{{ $order->customer_note }}</div>
                                </div>
                            @endif
                            @if ($order->admin_note)
                                <div>
                                    <label class="text-muted small mb-1">Ghi chú nội bộ (Admin):</label>
                                    <div class="p-3 bg-warning bg-opacity-10 rounded">{{ $order->admin_note }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column -->
            <div class="col-lg-4">
                <!-- Customer Info -->
                <div class="card border-0 shadow-sm mb-4 action-card">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-user text-primary me-2"></i>Thông tin khách hàng
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($order->user)
                            <div class="text-center mb-3">
                                <div class="avatar-lg mx-auto mb-3">
                                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
                                        style="width: 80px; height: 80px;">
                                        <i class="fa-solid fa-user text-primary fs-1"></i>
                                    </div>
                                </div>
                                <h5 class="fw-bold mb-1">{{ $order->user->first_name }} {{ $order->user->last_name }}
                                </h5>
                                <p class="text-muted mb-0">
                                    <i class="fa-solid fa-envelope me-2"></i>{{ $order->user->email }}
                                </p>
                            </div>

                            <hr>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="p-2 bg-light rounded text-center">
                                        <div class="small text-muted mb-1">Tổng đơn</div>
                                        <div class="fw-bold text-primary">{{ $order->user->orders->count() }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 bg-light rounded text-center">
                                        <div class="small text-muted mb-1">Đã hoàn thành</div>
                                        <div class="fw-bold text-success">
                                            {{ $order->user->orders->where('status', App\Enums\OrderStatus::Completed)->count() }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.orders.customer-details', $order->id) }}"
                                    class="btn btn-outline-primary">
                                    <i class="fa-solid fa-user-circle me-2"></i>Xem chi tiết khách hàng
                                </a>
                                <a href="mailto:{{ $order->user->email }}" class="btn btn-outline-success">
                                    <i class="fa-solid fa-envelope me-2"></i>Gửi email
                                </a>
                                @if ($order->shippingAddress)
                                    <a href="tel:{{ $order->shippingAddress->phone }}" class="btn btn-outline-info">
                                        <i class="fa-solid fa-phone me-2"></i>Gọi điện
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Payment Info - IMPROVED -->
                <div class="card border-0 shadow-sm mb-4 action-card">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-credit-card text-primary me-2"></i>Thông tin thanh toán
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($payment)
                            @php
                                $paymentMethod = $payment->payment_method->value;
                                $paymentStatus = $payment->status->value;
                                $orderStatus = $order->status->value;
                            @endphp

                            {{-- Cảnh báo cần xác nhận --}}
                            @if ($paymentMethod === 'cod' && $orderStatus === 'shipped' && $paymentStatus === 'pending')
                                <div class="alert alert-warning payment-alert mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fa-solid fa-exclamation-triangle fs-4 me-3"></i>
                                        <div>
                                            <strong>COD - Cần xác nhận khi giao hàng!</strong>
                                            <p class="mb-0 small">Xác nhận sau khi khách đã nhận hàng và thanh toán tiền
                                                mặt</p>
                                        </div>
                                    </div>
                                </div>
                            @elseif ($paymentMethod === 'bank' && $paymentStatus === 'pending')
                                <div class="alert alert-warning payment-alert mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fa-solid fa-clock fs-4 me-3"></i>
                                        <div>
                                            <strong>Chờ xác nhận chuyển khoản!</strong>
                                            <p class="mb-0 small">Kiểm tra và xác nhận khi đã nhận được tiền</p>
                                        </div>
                                    </div>
                                </div>
                            @elseif (in_array($paymentMethod, ['card', 'stripe']) && $paymentStatus === 'processing')
                                <div class="alert alert-info mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fa-solid fa-spinner fa-spin fs-4 me-3"></i>
                                        <div>
                                            <strong>Stripe đang xử lý...</strong>
                                            <p class="mb-0 small">Giao dịch đang được xác thực</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Phương thức thanh toán --}}
                            <div class="mb-3">
                                <label class="text-muted small mb-2">Phương thức thanh toán</label>
                                <div>
                                    @switch($paymentMethod)
                                        @case('cod')
                                            <span class="payment-method-badge bg-success bg-opacity-10 text-success">
                                                <i class="fa-solid fa-money-bill-wave me-2"></i>
                                                <span>COD - Thanh toán khi nhận hàng</span>
                                            </span>
                                        @break

                                        @case('bank')
                                            <span class="payment-method-badge bg-info bg-opacity-10 text-info">
                                                <i class="fa-solid fa-university me-2"></i>
                                                <span>Chuyển khoản ngân hàng</span>
                                            </span>
                                        @break

                                        @case('card')
                                        @case('stripe')
                                            <span class="payment-method-badge bg-primary bg-opacity-10 text-primary">
                                                <i class="fa-brands fa-stripe me-2"></i>
                                                <span>Stripe - Thanh toán online</span>
                                            </span>
                                        @break

                                        @case('wallet')
                                            <span class="payment-method-badge bg-warning bg-opacity-10 text-warning">
                                                <i class="fa-solid fa-wallet me-2"></i>
                                                <span>Ví điện tử</span>
                                            </span>
                                        @break
                                    @endswitch
                                </div>
                            </div>

                            {{-- Trạng thái thanh toán --}}
                            <div class="mb-3">
                                <label class="text-muted small mb-2">Trạng thái thanh toán</label>
                                <div>
                                    @switch($paymentStatus)
                                        @case('pending')
                                            <span class="payment-status-badge bg-warning text-dark">
                                                <i class="fa-solid fa-hourglass-half me-2"></i>
                                                @if ($paymentMethod === 'cod')
                                                    Chưa thanh toán (COD)
                                                @elseif($paymentMethod === 'bank')
                                                    Chờ xác nhận chuyển khoản
                                                @else
                                                    Chờ thanh toán
                                                @endif
                                            </span>
                                        @break

                                        @case('processing')
                                            <span class="payment-status-badge bg-info text-white">
                                                <i class="fa-solid fa-spinner fa-spin me-2"></i>
                                                Đang xử lý
                                            </span>
                                        @break

                                        @case('success')
                                        @case('paid')
                                            <span class="payment-status-badge bg-success text-white">
                                                <i class="fa-solid fa-check-circle me-2"></i>
                                                Đã thanh toán
                                            </span>
                                        @break

                                        @case('failed')
                                            <span class="payment-status-badge bg-danger text-white">
                                                <i class="fa-solid fa-times-circle me-2"></i>
                                                Thanh toán thất bại
                                            </span>
                                        @break
                                    @endswitch
                                </div>
                            </div>

                            {{-- Thông tin xác thực --}}
                            @if ($payment->requires_manual_verification)
                                <div class="mb-3">
                                    <label class="text-muted small mb-2">Trạng thái xác thực</label>
                                    <div>
                                        @if ($payment->is_verified)
                                            <div class="alert alert-success mb-2">
                                                <div class="d-flex align-items-center">
                                                    <i class="fa-solid fa-shield-check fs-4 me-3"></i>
                                                    <div class="flex-grow-1">
                                                        <strong>Đã xác nhận</strong>
                                                        @if ($payment->verifier)
                                                            <div class="small mt-1">
                                                                <i class="fa-solid fa-user me-1"></i>Bởi:
                                                                {{ $payment->verifier->name }}
                                                            </div>
                                                            <div class="small">
                                                                <i
                                                                    class="fa-solid fa-clock me-1"></i>{{ $payment->verified_at->format('d/m/Y H:i') }}
                                                            </div>
                                                        @endif
                                                        @if ($payment->verification_note)
                                                            <div class="small mt-1 text-muted">
                                                                <i
                                                                    class="fa-solid fa-note-sticky me-1"></i>{{ $payment->verification_note }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="badge bg-secondary px-3 py-2">
                                                <i class="fa-solid fa-clock me-1"></i>Chưa xác nhận
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-- Mã giao dịch --}}
                            @if ($payment->transaction_id)
                                <div class="mb-3">
                                    <label class="text-muted small mb-1">Mã giao dịch</label>
                                    <div class="p-2 bg-light rounded">
                                        <code class="text-dark">{{ $payment->transaction_id }}</code>
                                    </div>
                                </div>
                            @endif

                            {{-- Số tiền --}}
                            <div class="mb-3">
                                <label class="text-muted small mb-1">Số tiền thanh toán</label>
                                <div class="fs-4 fw-bold text-success">
                                    {{ number_format($payment->amount) }}₫
                                </div>
                            </div>

                            {{-- Thời gian --}}
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="text-muted small mb-1">Tạo lúc</label>
                                    <div class="small fw-semibold">{{ $payment->created_at->format('d/m/Y H:i') }}</div>
                                </div>
                                @if ($payment->paid_at)
                                    <div class="col-6">
                                        <label class="text-muted small mb-1">Thanh toán lúc</label>
                                        <div class="small fw-semibold text-success">
                                            {{ $payment->paid_at->format('d/m/Y H:i') }}</div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fa-solid fa-file-invoice-dollar fs-1 text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">Chưa có thông tin thanh toán</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card border-0 shadow-sm action-card">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-bolt text-primary me-2"></i>Thao tác nhanh
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if ($actions['canConfirmPayment'])
                                <button type="button" class="btn btn-success w-100" data-bs-toggle="modal"
                                    data-bs-target="#confirmPaymentModal">
                                    <i class="fa-solid fa-check me-2"></i>Xác nhận thanh toán
                                </button>
                            @endif

                            @if ($actions['canRejectPayment'])
                                <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal"
                                    data-bs-target="#rejectPaymentModal">
                                    <i class="fa-solid fa-times me-2"></i>Từ chối thanh toán
                                </button>
                            @endif

                            @if ($actions['canMarkAsPaid'])
                                <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST"
                                    class="quick-status-form">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="paid">
                                    <button type="submit" class="btn btn-info w-100">
                                        <i class="fa-solid fa-credit-card me-2"></i>Đánh dấu đã thanh toán
                                    </button>
                                </form>
                            @endif

                            @if ($actions['canMarkAsShipped'])
                                <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST"
                                    class="quick-status-form">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="shipped">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fa-solid fa-truck me-2"></i>Đánh dấu đang giao
                                    </button>
                                </form>
                            @endif

                            @if ($actions['canMarkAsCompleted'])
                                <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST"
                                    class="quick-status-form">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fa-solid fa-check-circle me-2"></i>Hoàn thành đơn hàng
                                    </button>
                                </form>
                            @endif

                            @if ($actions['canCancel'])
                                <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal"
                                    data-bs-target="#cancelModal">
                                    <i class="fa-solid fa-ban me-2"></i>Hủy đơn hàng
                                </button>
                            @endif

                            @if (
                                !$actions['canConfirmPayment'] &&
                                    !$actions['canRejectPayment'] &&
                                    !$actions['canMarkAsPaid'] &&
                                    !$actions['canMarkAsShipped'] &&
                                    !$actions['canMarkAsCompleted'] &&
                                    !$actions['canCancel']
                            )
                                <div class="alert alert-info mb-0">
                                    <i class="fa-solid fa-info-circle me-2"></i>
                                    Không có thao tác khả dụng cho trạng thái hiện tại
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Payment Modal -->
    <div class="modal fade" id="confirmPaymentModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.orders.confirm-payment', $order->id) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fa-solid fa-check-circle me-2"></i>Xác nhận thanh toán
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-success border-0">
                            <div class="d-flex align-items-start">
                                <i class="fa-solid fa-info-circle fs-4 me-3 mt-1"></i>
                                <div>
                                    <strong>Xác nhận đơn hàng #{{ $order->order_number }}</strong>
                                    <p class="mb-2 small">Số tiền:
                                        <strong>{{ number_format($order->total_amount) }}₫</strong></p>
                                    @if ($payment && $payment->payment_method->value === 'cod')
                                        <p class="mb-0 small text-warning">
                                            <i class="fa-solid fa-exclamation-triangle me-1"></i>
                                            Chỉ xác nhận sau khi khách đã nhận hàng và thanh toán tiền mặt
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Mã giao dịch
                                <span class="text-muted small">(tùy chọn)</span>
                            </label>
                            <input type="text" name="transaction_id" class="form-control"
                                placeholder="Nhập mã giao dịch nếu có...">
                            <div class="form-text">
                                <i class="fa-solid fa-lightbulb me-1"></i>
                                Mã tham chiếu từ ngân hàng hoặc hệ thống thanh toán
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Ghi chú xác nhận
                                <span class="text-muted small">(tùy chọn)</span>
                            </label>
                            <textarea name="verification_note" class="form-control" rows="3"
                                placeholder="Ghi chú về việc xác nhận thanh toán..."></textarea>
                        </div>

                        <div class="alert alert-info border-0 mb-0">
                            <i class="fa-solid fa-envelope me-2"></i>
                            Email xác nhận sẽ được gửi tự động đến khách hàng
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fa-solid fa-times me-2"></i>Đóng
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fa-solid fa-check me-2"></i>Xác nhận thanh toán
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Payment Modal -->
    <div class="modal fade" id="rejectPaymentModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.orders.reject-payment', $order->id) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fa-solid fa-times-circle me-2"></i>Từ chối thanh toán
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger border-0">
                            <div class="d-flex align-items-start">
                                <i class="fa-solid fa-exclamation-triangle fs-4 me-3 mt-1"></i>
                                <div>
                                    <strong>Cảnh báo!</strong>
                                    <p class="mb-0">Từ chối thanh toán sẽ tự động hủy đơn hàng
                                        <strong>#{{ $order->order_number }}</strong></p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Lý do từ chối
                                <span class="text-danger">*</span>
                            </label>
                            <textarea name="reason" class="form-control" rows="4" required
                                placeholder="Nhập lý do từ chối thanh toán...&#10;Ví dụ: Thông tin chuyển khoản không khớp, số tiền không đúng, v.v."></textarea>
                            <div class="form-text text-danger">
                                <i class="fa-solid fa-asterisk me-1"></i>
                                Lý do sẽ được gửi đến khách hàng qua email
                            </div>
                        </div>

                        <div class="alert alert-warning border-0 mb-0">
                            <i class="fa-solid fa-undo me-2"></i>
                            Tồn kho sẽ được hoàn lại tự động
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fa-solid fa-arrow-left me-2"></i>Quay lại
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fa-solid fa-times me-2"></i>Từ chối thanh toán
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cancel Order Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.orders.cancel', $order->id) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fa-solid fa-ban me-2"></i>Hủy đơn hàng
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning border-0">
                            <div class="d-flex align-items-start">
                                <i class="fa-solid fa-exclamation-triangle fs-4 me-3 mt-1"></i>
                                <div>
                                    <strong>Xác nhận hủy đơn hàng</strong>
                                    <p class="mb-0">Bạn có chắc chắn muốn hủy đơn hàng
                                        <strong>#{{ $order->order_number }}</strong>?</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Lý do hủy đơn
                                <span class="text-danger">*</span>
                            </label>
                            <textarea name="reason" class="form-control" rows="4" required
                                placeholder="Nhập lý do hủy đơn hàng...&#10;Ví dụ: Khách yêu cầu hủy, hết hàng, địa chỉ không hợp lệ, v.v."></textarea>
                            <div class="form-text text-danger">
                                <i class="fa-solid fa-asterisk me-1"></i>
                                Lý do sẽ được ghi nhận và thông báo đến khách hàng
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col-12">
                                <div class="alert alert-info border-0 mb-0">
                                    <div class="small">
                                        <i class="fa-solid fa-check-circle me-2"></i>
                                        <strong>Tự động thực hiện:</strong>
                                    </div>
                                    <ul class="small mb-0 mt-2 ps-4">
                                        <li>Hoàn lại tồn kho sản phẩm</li>
                                        <li>Cập nhật trạng thái thanh toán</li>
                                        <li>Gửi email thông báo khách hàng</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fa-solid fa-arrow-left me-2"></i>Quay lại
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fa-solid fa-ban me-2"></i>Xác nhận hủy đơn
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Handle quick status form submissions with confirmation
            document.querySelectorAll('.quick-status-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const status = this.querySelector('input[name="status"]').value;
                    const statusLabels = {
                        'paid': 'đã thanh toán',
                        'shipped': 'đang giao hàng',
                        'completed': 'hoàn thành'
                    };

                    const statusIcons = {
                        'paid': 'credit-card',
                        'shipped': 'truck',
                        'completed': 'check-circle'
                    };

                    const statusColors = {
                        'paid': '#17a2b8',
                        'shipped': '#007bff',
                        'completed': '#28a745'
                    };

                    Swal.fire({
                        title: 'Xác nhận cập nhật',
                        html: `
                    <div class="text-start">
                        <p>Bạn có chắc muốn đánh dấu đơn hàng <strong>${statusLabels[status]}</strong>?</p>
                        <div class="alert alert-info mt-3">
                            <i class="fa-solid fa-info-circle me-2"></i>
                            <small>Email thông báo sẽ được gửi tự động đến khách hàng</small>
                        </div>
                    </div>
                `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: '<i class="fa-solid fa-check me-2"></i>Xác nhận',
                        cancelButtonText: '<i class="fa-solid fa-times me-2"></i>Hủy',
                        confirmButtonColor: statusColors[status],
                        cancelButtonColor: '#6c757d',
                        reverseButtons: true,
                        customClass: {
                            confirmButton: 'btn btn-lg',
                            cancelButton: 'btn btn-lg'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading
                            Swal.fire({
                                title: 'Đang xử lý...',
                                html: 'Vui lòng đợi',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            this.submit();
                        }
                    });
                });
            });

            // Initialize Bootstrap tooltips if any
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert:not(.payment-alert)');
            alerts.forEach(alert => {
                if (!alert.classList.contains('alert-danger') && !alert.classList.contains(
                    'alert-warning')) {
                    setTimeout(() => {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }, 5000);
                }
            });
        });
    </script>
@endpush
