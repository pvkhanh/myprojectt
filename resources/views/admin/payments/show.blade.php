@extends('layouts.admin')

@section('title', 'Chi tiết Thanh toán #' . $payment->id)

@push('styles')
    <style>
        .info-card {
            transition: all 0.3s;
        }

        .info-card:hover {
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
        }

        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -24px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #fff;
            border: 2px solid #0d6efd;
        }

        .timeline-item.success::before {
            border-color: #198754;
        }

        .timeline-item.danger::before {
            border-color: #dc3545;
        }

        .timeline-item.warning::before {
            border-color: #ffc107;
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
                            <i class="fa-solid fa-receipt text-primary me-2"></i>
                            Chi tiết Thanh toán #{{ $payment->id }}
                        </h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.payments.index') }}">Thanh toán</a>
                                </li>
                                <li class="breadcrumb-item active">Chi tiết</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
                        </a>
                        @if ($payment->canBeVerified())
                            <a href="{{ route('admin.payments.verify-form', $payment->id) }}"
                                class="btn btn-warning btn-lg">
                                <i class="fa-solid fa-check me-2"></i> Xác nhận
                            </a>
                        @endif
                        <a href="{{ route('admin.orders.show', $payment->order_id) }}" class="btn btn-primary btn-lg">
                            <i class="fa-solid fa-shopping-cart me-2"></i> Xem đơn hàng
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column -->
            <div class="col-lg-8">
                <!-- Payment Info -->
                <div class="card border-0 shadow-sm mb-4 info-card">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-info-circle text-primary me-2"></i>Thông tin giao dịch
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Mã giao dịch</label>
                                <div class="fw-bold fs-5">{{ $payment->transaction_id ?: 'Chưa có' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Số tiền</label>
                                <div class="fw-bold fs-5 text-primary">{{ number_format($payment->amount) }}đ</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Phương thức thanh toán</label>
                                <div class="fw-semibold">
                                    @php
                                        $methodConfig = [
                                            'card' => [
                                                'icon' => 'credit-card',
                                                'color' => 'primary',
                                                'text' => 'Thẻ tín dụng',
                                            ],
                                            'bank' => [
                                                'icon' => 'university',
                                                'color' => 'info',
                                                'text' => 'Chuyển khoản ngân hàng',
                                            ],
                                            'cod' => [
                                                'icon' => 'money-bill-wave',
                                                'color' => 'secondary',
                                                'text' => 'Thanh toán khi nhận hàng',
                                            ],
                                            'wallet' => [
                                                'icon' => 'wallet',
                                                'color' => 'success',
                                                'text' => 'Ví điện tử',
                                            ],
                                        ];
                                        $method = $payment->payment_method->value;
                                        $config = $methodConfig[$method] ?? [
                                            'icon' => 'question',
                                            'color' => 'secondary',
                                            'text' => 'Khác',
                                        ];
                                    @endphp
                                    <span
                                        class="badge bg-{{ $config['color'] }}-subtle text-{{ $config['color'] }} fs-6 px-3 py-2">
                                        <i class="fa-solid fa-{{ $config['icon'] }} me-1"></i>{{ $config['text'] }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Cổng thanh toán</label>
                                <div class="fw-semibold">{{ $payment->payment_gateway ?: 'N/A' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Trạng thái</label>
                                <div>
                                    @php
                                        $statusConfig = [
                                            'pending' => [
                                                'class' => 'warning',
                                                'icon' => 'clock',
                                                'text' => 'Chờ xử lý',
                                            ],
                                            'success' => [
                                                'class' => 'success',
                                                'icon' => 'check-circle',
                                                'text' => 'Thành công',
                                            ],
                                            'failed' => [
                                                'class' => 'danger',
                                                'icon' => 'times-circle',
                                                'text' => 'Thất bại',
                                            ],
                                        ];
                                        $status = $payment->status->value;
                                        $config = $statusConfig[$status] ?? [
                                            'class' => 'secondary',
                                            'icon' => 'question',
                                            'text' => $status,
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $config['class'] }} fs-6 px-3 py-2">
                                        <i class="fa-solid fa-{{ $config['icon'] }} me-1"></i>{{ $config['text'] }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Ngày tạo</label>
                                <div class="fw-semibold">{{ $payment->created_at->format('d/m/Y H:i:s') }}</div>
                            </div>
                            @if ($payment->paid_at)
                                <div class="col-md-6">
                                    <label class="text-muted small mb-1">Ngày thanh toán</label>
                                    <div class="fw-semibold">{{ $payment->paid_at->format('d/m/Y H:i:s') }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Verification Info -->
                <div class="card border-0 shadow-sm mb-4 info-card">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-shield-halved text-primary me-2"></i>Thông tin xác nhận
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Loại xác nhận</label>
                                <div>
                                    @if (!$payment->requires_manual_verification)
                                        <span class="badge bg-success fs-6 px-3 py-2">
                                            <i class="fa-solid fa-robot me-1"></i>Tự động
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                                            <i class="fa-solid fa-user me-1"></i>Thủ công
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Trạng thái xác nhận</label>
                                <div>
                                    @if (!$payment->requires_manual_verification)
                                        <span class="badge bg-success fs-6 px-3 py-2">
                                            <i class="fa-solid fa-check-circle me-1"></i>Đã xác nhận tự động
                                        </span>
                                    @elseif($payment->is_verified)
                                        <span class="badge bg-success fs-6 px-3 py-2">
                                            <i class="fa-solid fa-check-circle me-1"></i>Đã xác nhận
                                        </span>
                                    @elseif($payment->status->value === 'failed')
                                        <span class="badge bg-danger fs-6 px-3 py-2">
                                            <i class="fa-solid fa-times-circle me-1"></i>Đã từ chối
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                                            <i class="fa-solid fa-clock me-1"></i>Chờ xác nhận
                                        </span>
                                    @endif
                                </div>
                            </div>
                            @if ($payment->is_verified && $payment->verifier)
                                <div class="col-md-6">
                                    <label class="text-muted small mb-1">Người xác nhận</label>
                                    <div class="fw-semibold">{{ $payment->verifier->name }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small mb-1">Thời gian xác nhận</label>
                                    <div class="fw-semibold">{{ $payment->verified_at->format('d/m/Y H:i:s') }}</div>
                                </div>
                            @endif
                            @if ($payment->verification_note)
                                <div class="col-12">
                                    <label class="text-muted small mb-1">Ghi chú xác nhận</label>
                                    <div class="alert alert-info mb-0">
                                        <i class="fa-solid fa-info-circle me-2"></i>{{ $payment->verification_note }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Gateway Response -->
                @if ($payment->gateway_response && is_array($payment->gateway_response) && count($payment->gateway_response) > 0)
                    <div class="card border-0 shadow-sm mb-4 info-card">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-code text-primary me-2"></i>Phản hồi từ cổng thanh toán
                            </h5>
                        </div>
                        <div class="card-body">
                            <pre class="bg-light p-3 rounded mb-0"><code>{{ json_encode($payment->gateway_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column -->
            <div class="col-lg-4">
                <!-- Order Info -->
                <div class="card border-0 shadow-sm mb-4 info-card">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-shopping-cart text-primary me-2"></i>Thông tin đơn hàng
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted small mb-1">Mã đơn hàng</label>
                            <div class="fw-bold">
                                <a href="{{ route('admin.orders.show', $payment->order_id) }}"
                                    class="text-decoration-none">
                                    #{{ $payment->order->order_number }}
                                </a>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small mb-1">Trạng thái đơn hàng</label>
                            <div>
                                @php
                                    $orderStatusConfig = [
                                        'pending' => [
                                            'class' => 'warning text-dark',
                                            'icon' => 'clock',
                                            'text' => 'Chờ xử lý',
                                        ],
                                        'paid' => [
                                            'class' => 'info',
                                            'icon' => 'credit-card',
                                            'text' => 'Đã thanh toán',
                                        ],
                                        'shipped' => ['class' => 'primary', 'icon' => 'truck', 'text' => 'Đang giao'],
                                        'completed' => [
                                            'class' => 'success',
                                            'icon' => 'check-circle',
                                            'text' => 'Hoàn thành',
                                        ],
                                        'cancelled' => ['class' => 'danger', 'icon' => 'ban', 'text' => 'Đã hủy'],
                                    ];
                                    $orderStatus = $payment->order->status->value;
                                    $config = $orderStatusConfig[$orderStatus] ?? [
                                        'class' => 'secondary',
                                        'icon' => 'question',
                                        'text' => $orderStatus,
                                    ];
                                @endphp
                                <span class="badge bg-{{ $config['class'] }} fs-6 px-3 py-2">
                                    <i class="fa-solid fa-{{ $config['icon'] }} me-1"></i>{{ $config['text'] }}
                                </span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small mb-1">Tổng tiền đơn hàng</label>
                            <div class="fw-bold text-primary fs-5">{{ number_format($payment->order->total_amount) }}đ
                            </div>
                        </div>
                        <div>
                            <label class="text-muted small mb-1">Ngày đặt hàng</label>
                            <div class="fw-semibold">{{ $payment->order->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Customer Info -->
                <div class="card border-0 shadow-sm mb-4 info-card">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-user text-primary me-2"></i>Thông tin khách hàng
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted small mb-1">Tên khách hàng</label>
                            <div class="fw-semibold">
                                {{ trim(($payment->order->user->first_name ?? '') . ' ' . ($payment->order->user->last_name ?? '')) ?: 'N/A' }}
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small mb-1">Email</label>
                            <div class="fw-semibold">
                                <i
                                    class="fa-solid fa-envelope me-1 text-muted"></i>{{ $payment->order->user->email ?? 'N/A' }}
                            </div>
                        </div>
                        @if ($payment->order->user->phone)
                            <div>
                                <label class="text-muted small mb-1">Số điện thoại</label>
                                <div class="fw-semibold">
                                    <i class="fa-solid fa-phone me-1 text-muted"></i>{{ $payment->order->user->phone }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Timeline -->
                <div class="card border-0 shadow-sm info-card">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-clock-rotate-left text-primary me-2"></i>Lịch sử
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="small text-muted">{{ $payment->created_at->format('d/m/Y H:i') }}</div>
                                <div class="fw-semibold">Tạo giao dịch</div>
                            </div>
                            @if ($payment->paid_at)
                                <div class="timeline-item success">
                                    <div class="small text-muted">{{ $payment->paid_at->format('d/m/Y H:i') }}</div>
                                    <div class="fw-semibold">Thanh toán thành công</div>
                                </div>
                            @endif
                            @if ($payment->is_verified)
                                <div class="timeline-item success">
                                    <div class="small text-muted">{{ $payment->verified_at->format('d/m/Y H:i') }}</div>
                                    <div class="fw-semibold">Đã xác nhận bởi {{ $payment->verifier->name }}</div>
                                </div>
                            @endif
                            @if ($payment->status->value === 'failed')
                                <div class="timeline-item danger">
                                    <div class="small text-muted">{{ $payment->updated_at->format('d/m/Y H:i') }}</div>
                                    <div class="fw-semibold">Giao dịch thất bại</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Tooltips
            const tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltips.map(el => new bootstrap.Tooltip(el));
        });
    </script>
@endpush
