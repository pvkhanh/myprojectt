@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng #' . $order->order_number)

@push('styles')
    <style>
        /* Timeline Styles - TikTok Shop Style */
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
            color: #999;
            position: relative;
            z-index: 1;
            background: #f5f5f5;
            border: 5px solid #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.4s ease;
        }

        .timeline-step.completed::before {
            background: linear-gradient(to right, #10b981, #059669);
        }

        .timeline-step.completed .timeline-icon {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            transform: scale(1);
        }

        .timeline-step.active .timeline-icon {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            transform: scale(1.15);
            animation: pulse 2s infinite;
            box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7);
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7);
            }

            70% {
                box-shadow: 0 0 0 15px rgba(59, 130, 246, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
            }
        }

        .timeline-step.pending .timeline-icon {
            background: #f5f5f5;
            color: #999;
        }

        .timeline-label {
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 5px;
            color: #1f2937;
        }

        .timeline-step.active .timeline-label {
            color: #2563eb;
        }

        .timeline-step.completed .timeline-label {
            color: #059669;
        }

        .timeline-time {
            font-size: 0.8rem;
            color: #6b7280;
        }

        .timeline-description {
            font-size: 0.75rem;
            color: #9ca3af;
            margin-top: 5px;
        }

        /* Action Buttons - TikTok Style */
        .action-card {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .action-btn {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 12px;
        }

        .action-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .action-btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-confirm-order {
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            color: white;
        }

        .btn-confirm-order:hover {
            background: linear-gradient(135deg, #059669, #047857);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
        }

        .btn-ship {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border: none;
            color: white;
        }

        .btn-ship:hover {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
        }

        .btn-complete {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            border: none;
            color: white;
        }

        .btn-complete:hover {
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(139, 92, 246, 0.4);
        }

        .btn-cancel-order {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border: none;
            color: white;
        }

        .btn-cancel-order:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.4);
        }

        /* Payment Method Badge */
        .payment-method-badge {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.95rem;
            gap: 10px;
        }

        .payment-method-badge.stripe {
            background: linear-gradient(135deg, #635bff, #5469d4);
            color: white;
        }

        .payment-method-badge.cod {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        .payment-method-badge.bank {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            color: white;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-4">
        <!-- Header -->
        {{-- <div class="row mb-4">
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
                        <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="btn btn-success">
                            <i class="fa-solid fa-print me-2"></i> In hóa đơn
                        </a>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div> --}}
        <div class="container-fluid px-4">

            {{-- ✅ THÊM PHẦN HIỂN THỊ THÔNG BÁO --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-check-circle me-2"></i>
                    <strong>Thành công!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-times-circle me-2"></i>
                    <strong>Lỗi!</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-exclamation-triangle me-2"></i>
                    <strong>Cảnh báo!</strong> {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-info-circle me-2"></i>
                    <strong>Thông tin!</strong> {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

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
                                    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Đơn hàng</a>
                                    </li>
                                    <li class="breadcrumb-item active">#{{ $order->order_number }}</li>
                                </ol>
                            </nav>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank"
                                class="btn btn-success">
                                <i class="fa-solid fa-print me-2"></i> In hóa đơn
                            </a>
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                                <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Timeline - Dynamic Based on Payment Method -->
            {{-- <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">
                        <i class="fa-solid fa-timeline text-primary me-2"></i>
                        Trạng thái đơn hàng
                        @if ($payment)
                            <span class="payment-method-badge {{ $payment->payment_method->value }} ms-3">
                                @if (in_array($payment->payment_method->value, ['card', 'stripe']))
                                    <i class="fa-brands fa-stripe"></i> Stripe
                                @elseif($payment->payment_method->value === 'cod')
                                    <i class="fa-solid fa-money-bill-wave"></i> COD
                                @elseif($payment->payment_method->value === 'bank')
                                    <i class="fa-solid fa-university"></i> Chuyển khoản
                                @endif
                            </span>
                        @endif
                    </h5>

                    @if ($order->status->value === 'cancelled')
                        <div class="alert alert-danger">
                            <div class="d-flex align-items-center">
                                <i class="fa-solid fa-ban fs-3 me-3"></i>
                                <div>
                                    <strong>Đơn hàng đã bị hủy</strong>
                                    @if ($order->cancelled_at)
                                        <p class="mb-0 small">Thời gian: {{ $order->cancelled_at->format('d/m/Y H:i') }}
                                        </p>
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
                                @foreach ($timelineSteps as $step)
                                    <div class="timeline-step {{ $step['status'] }}">
                                        <div class="timeline-icon">
                                            <i class="fa-solid fa-{{ $step['icon'] }}"></i>
                                        </div>
                                        <div class="timeline-label">{{ $step['label'] }}</div>
                                        @if ($step['time'])
                                            <div class="timeline-time">{{ $step['time']->format('d/m/Y H:i') }}</div>
                                        @endif
                                        @if ($step['description'])
                                            <div class="timeline-description">{{ $step['description'] }}</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div> --}}
            <!-- Order Timeline -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">
                        <i class="fa-solid fa-timeline text-primary me-2"></i>
                        Trạng thái đơn hàng
                        @if ($payment)
                            <span class="payment-method-badge {{ $payment->payment_method->value }} ms-3">
                                @if (in_array($payment->payment_method->value, ['card', 'stripe']))
                                    <i class="fa-brands fa-stripe"></i> Stripe
                                @elseif($payment->payment_method->value === 'cod')
                                    <i class="fa-solid fa-money-bill-wave"></i> COD
                                @elseif($payment->payment_method->value === 'bank')
                                    <i class="fa-solid fa-university"></i> Chuyển khoản
                                @endif
                            </span>
                        @endif
                    </h5>

                    @if ($order->status->value === 'cancelled')
                        <div class="alert alert-danger">
                            <div class="d-flex align-items-center">
                                <i class="fa-solid fa-ban fs-3 me-3"></i>
                                <div>
                                    <strong>Đơn hàng đã bị hủy</strong>
                                    @if ($order->cancelled_at)
                                        <p class="mb-0 small">Thời gian: {{ $order->cancelled_at->format('d/m/Y H:i') }}
                                        </p>
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
                                @foreach ($timelineSteps as $step)
                                    <div class="timeline-step {{ $step['status'] }}">
                                        <div class="timeline-icon">
                                            <i class="fa-solid fa-{{ $step['icon'] }}"></i>
                                        </div>
                                        <div class="timeline-label">{{ $step['label'] }}</div>
                                        @if ($step['time'])
                                            <div class="timeline-time">{{ $step['time']->format('d/m/Y H:i') }}</div>
                                        @endif
                                        @if ($step['description'])
                                            <div class="timeline-description">{{ $step['description'] }}</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="row g-4">
                <!-- Left Column - Order Items, Shipping, Notes -->
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
                                                            <div class="fw-semibold text-dark">{{ $item->product->name }}
                                                            </div>
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
                                                    <span
                                                        class="badge bg-primary fs-6 px-3 py-2">{{ $item->quantity }}</span>
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
                                            <td class="px-4 py-3 text-end fw-bold">{{ number_format($order->subtotal) }}₫
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="px-4 py-3 text-end fw-semibold">Phí vận chuyển:</td>
                                            <td class="px-4 py-3 text-end fw-bold">
                                                {{ number_format($order->shipping_fee) }}₫
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
                                            <a href="tel:{{ $order->shippingAddress->phone }}"
                                                class="text-decoration-none">
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
                </div>

                <!-- Right Column - Payment Info & Quick Actions -->
                <div class="col-lg-4">
                    {{-- <div class="card border-0 shadow-sm mb-4 action-card">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-bolt text-warning me-2"></i>Thao tác nhanh
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-3">
                                @if ($actions['canConfirmPayment'])
                                    <button type="button" class="btn action-btn btn-confirm-order"
                                        data-bs-toggle="modal" data-bs-target="#confirmPaymentModal">
                                        <i class="fa-solid fa-check-circle me-2"></i>
                                        @if ($payment && $payment->payment_method->value === 'bank')
                                            Xác nhận đã nhận tiền CK
                                        @else
                                            Xác nhận đã thu COD
                                        @endif
                                    </button>
                                @endif

                                @if ($actions['canRejectPayment'])
                                    <button type="button" class="btn action-btn btn-cancel-order" data-bs-toggle="modal"
                                        data-bs-target="#rejectPaymentModal">
                                        <i class="fa-solid fa-times-circle me-2"></i>Từ chối thanh toán
                                    </button>
                                @endif

                                @if ($actions['canConfirmOrder'])
                                    <form action="{{ route('admin.orders.confirm', $order->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn action-btn btn-confirm-order w-100">
                                            <i class="fa-solid fa-box-check me-2"></i>Xác nhận đơn & Chuẩn bị hàng
                                        </button>
                                    </form>
                                @endif

                                @if ($actions['canMarkAsShipped'])
                                    <button type="button" class="btn action-btn btn-ship" data-bs-toggle="modal"
                                        data-bs-target="#shipModal">
                                        <i class="fa-solid fa-truck me-2"></i>Giao cho Shipper
                                    </button>
                                @endif

                                @if ($actions['canMarkAsCompleted'])
                                    <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="btn action-btn btn-complete w-100">
                                            <i class="fa-solid fa-star me-2"></i>Hoàn thành đơn hàng
                                        </button>
                                    </form>
                                @endif

                                @if ($actions['canCancel'])
                                    <button type="button" class="btn action-btn btn-cancel-order" data-bs-toggle="modal"
                                        data-bs-target="#cancelModal">
                                        <i class="fa-solid fa-ban me-2"></i>Hủy đơn hàng
                                    </button>
                                @endif

                                @if ($actions['showShippingCode'])
                                    <div class="alert alert-info mb-0">
                                        <i class="fa-solid fa-shipping-fast me-2"></i>
                                        <strong>Mã vận đơn:</strong>
                                        <code>#SHIP-{{ $order->order_number }}</code>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div> --}}
                    <!-- Quick Actions Card -->
                    <div class="card border-0 shadow-sm mb-4 action-card">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-bolt text-warning me-2"></i>Thao tác nhanh
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-3">
                                @if ($actions['canConfirmOrder'])
                                    {{-- ✅ THÊM XÁC NHẬN TRƯỚC KHI SUBMIT --}}
                                    <form action="{{ route('admin.orders.confirm', $order->id) }}" method="POST"
                                        id="confirmOrderForm"
                                        onsubmit="return confirmAction('Bạn có chắc muốn xác nhận đơn hàng này?')">
                                        @csrf
                                        <button type="submit" class="btn action-btn btn-confirm-order w-100">
                                            <i class="fa-solid fa-box-check me-2"></i>Xác nhận đơn & Chuẩn bị hàng
                                        </button>
                                    </form>
                                @endif

                                @if ($actions['canConfirmPayment'])
                                    <button type="button" class="btn action-btn btn-confirm-order"
                                        data-bs-toggle="modal" data-bs-target="#confirmPaymentModal">
                                        <i class="fa-solid fa-check-circle me-2"></i>
                                        @if ($payment && $payment->payment_method->value === 'bank')
                                            Xác nhận đã nhận tiền CK
                                        @else
                                            Xác nhận đã thu COD
                                        @endif
                                    </button>
                                @endif

                                @if ($actions['canRejectPayment'])
                                    <button type="button" class="btn action-btn btn-cancel-order" data-bs-toggle="modal"
                                        data-bs-target="#rejectPaymentModal">
                                        <i class="fa-solid fa-times-circle me-2"></i>Từ chối thanh toán
                                    </button>
                                @endif

                                @if ($actions['canMarkAsShipped'])
                                    <button type="button" class="btn action-btn btn-ship" data-bs-toggle="modal"
                                        data-bs-target="#shipModal">
                                        <i class="fa-solid fa-truck me-2"></i>Giao cho Shipper
                                    </button>
                                @endif

                                @if ($actions['canMarkAsCompleted'])
                                    <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST"
                                        onsubmit="return confirmAction('Xác nhận đơn hàng đã hoàn thành?')">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="btn action-btn btn-complete w-100">
                                            <i class="fa-solid fa-star me-2"></i>Hoàn thành đơn hàng
                                        </button>
                                    </form>
                                @endif

                                @if ($actions['canCancel'])
                                    <button type="button" class="btn action-btn btn-cancel-order" data-bs-toggle="modal"
                                        data-bs-target="#cancelModal">
                                        <i class="fa-solid fa-ban me-2"></i>Hủy đơn hàng
                                    </button>
                                @endif

                                @if ($actions['showShippingCode'])
                                    <div class="alert alert-info mb-0">
                                        <i class="fa-solid fa-shipping-fast me-2"></i>
                                        <strong>Mã vận đơn:</strong>
                                        <code>#SHIP-{{ $order->order_number }}</code>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div class="card border-0 shadow-sm action-card">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-user text-primary me-2"></i>Khách hàng
                            </h5>
                        </div>
                        <div class="card-body">
                            @if ($order->user)
                                <div class="text-center mb-3">
                                    <div class="avatar-lg mx-auto mb-3">
                                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
                                            style="width: 70px; height: 70px;">
                                            <i class="fa-solid fa-user text-primary fs-2"></i>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold mb-1">{{ $order->user->first_name }} {{ $order->user->last_name }}
                                    </h6>
                                    <p class="text-muted small mb-0">
                                        <i class="fa-solid fa-envelope me-1"></i>{{ $order->user->email }}
                                    </p>
                                </div>

                                <div class="d-grid gap-2">
                                    <a href="mailto:{{ $order->user->email }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fa-solid fa-envelope me-2"></i>Email
                                    </a>
                                    @if ($order->shippingAddress)
                                        <a href="tel:{{ $order->shippingAddress->phone }}"
                                            class="btn btn-outline-success btn-sm">
                                            <i class="fa-solid fa-phone me-2"></i>Gọi điện
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modals -->
        <!-- Confirm Payment Modal -->
        <div class="modal fade" id="confirmPaymentModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.orders.confirm-payment', $order->id) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Xác nhận thanh toán</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Mã giao dịch (tùy chọn)</label>
                                <input type="text" name="transaction_id" class="form-control"
                                    placeholder="VD: TXN123456">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Ghi chú</label>
                                <textarea name="note" class="form-control" rows="3" placeholder="Ghi chú xác nhận..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-success">Xác nhận</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Reject Payment Modal -->
        <div class="modal fade" id="rejectPaymentModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.orders.reject-payment', $order->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="modal-header">
                            <h5 class="modal-title">Từ chối thanh toán</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Lý do từ chối <span class="text-danger">*</span></label>
                                <textarea name="reason" class="form-control" rows="4" required
                                    placeholder="VD: Thông tin chuyển khoản không chính xác..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-danger">Từ chối</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Ship Modal -->
        <div class="modal fade" id="shipModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="shipped">
                        <div class="modal-header">
                            <h5 class="modal-title">Giao cho Shipper</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Xác nhận đã giao hàng cho đơn vị vận chuyển?</p>
                            <div class="mb-3">
                                <label class="form-label">Mã vận đơn (tùy chọn)</label>
                                <input type="text" name="tracking_code" class="form-control"
                                    placeholder="VD: SPX123456">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-primary">Xác nhận giao hàng</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Cancel Modal -->
        <div class="modal fade" id="cancelModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.orders.cancel', $order->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="modal-header">
                            <h5 class="modal-title text-danger">Hủy đơn hàng</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-warning">
                                <i class="fa-solid fa-exclamation-triangle me-2"></i>
                                Hành động này sẽ hủy đơn hàng và hoàn trả kho!
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Lý do hủy <span class="text-danger">*</span></label>
                                <textarea name="reason" class="form-control" rows="4" required
                                    placeholder="VD: Khách hàng yêu cầu hủy đơn..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-danger">Xác nhận hủy</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @push('scripts')
            <script>
                // ✅ THÊM JAVASCRIPT ĐỂ XÁC NHẬN VÀ HIỂN THỊ LOADING
                function confirmAction(message) {
                    return confirm(message);
                }

                // Auto hide alerts after 5 seconds
                document.addEventListener('DOMContentLoaded', function() {
                    const alerts = document.querySelectorAll('.alert');
                    alerts.forEach(alert => {
                        setTimeout(() => {
                            const bsAlert = new bootstrap.Alert(alert);
                            bsAlert.close();
                        }, 5000);
                    });

                    // Add loading spinner to form submissions
                    const forms = document.querySelectorAll('form');
                    forms.forEach(form => {
                        form.addEventListener('submit', function(e) {
                            const submitBtn = this.querySelector('button[type="submit"]');
                            if (submitBtn) {
                                submitBtn.disabled = true;
                                submitBtn.innerHTML =
                                    '<i class="fa-solid fa-spinner fa-spin me-2"></i>Đang xử lý...';
                            }
                        });
                    });
                });
            </script>
        @endpush
    @endsection
