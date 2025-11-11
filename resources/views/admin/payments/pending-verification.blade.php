@extends('layouts.admin')

@section('title', 'Thanh toán cần xác nhận')

@push('styles')
    <style>
        .urgent-card {
            border-left: 4px solid #ffc107;
            transition: all 0.3s;
        }

        .urgent-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        }

        .pulse-badge {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
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
                            <i class="fa-solid fa-clock text-warning me-2"></i>
                            Thanh toán cần xác nhận
                        </h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.payments.index') }}">Thanh toán</a></li>
                                <li class="breadcrumb-item active">Cần xác nhận</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if($payments->count() > 0)
            <!-- Alert -->
            <div class="alert alert-warning border-2 d-flex align-items-center mb-4" role="alert">
                <i class="fa-solid fa-exclamation-triangle fs-3 me-3"></i>
                <div>
                    <h5 class="alert-heading mb-1">Có {{ $payments->total() }} giao dịch cần xác nhận!</h5>
                    <p class="mb-0">Vui lòng xem xét và xác nhận các giao dịch dưới đây để đơn hàng được xử lý tiếp.</p>
                </div>
            </div>

            <!-- Payments List -->
            <div class="row g-4">
                @foreach($payments as $payment)
                    <div class="col-12">
                        <div class="card border-0 shadow-sm urgent-card">
                            <div class="card-body p-4">
                                <div class="row align-items-center">
                                    <!-- Payment Icon -->
                                    <div class="col-auto">
                                        <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center"
                                            style="width:70px; height:70px;">
                                            <i class="fa-solid fa-money-bill-wave text-warning fs-2"></i>
                                        </div>
                                    </div>

                                    <!-- Payment Info -->
                                    <div class="col-md-3">
                                        <div class="mb-2">
                                            <span class="badge bg-warning text-dark pulse-badge">
                                                <i class="fa-solid fa-clock me-1"></i>Chờ xác nhận
                                            </span>
                                        </div>
                                        <div class="fw-bold fs-5 mb-1">{{ $payment->transaction_id ?: 'Chưa có mã GD' }}</div>
                                        <div class="small text-muted">
                                            <i class="fa-solid fa-calendar me-1"></i>{{ $payment->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    </div>

                                    <!-- Order & Customer -->
                                    <div class="col-md-3">
                                        <label class="text-muted small d-block mb-1">Đơn hàng & Khách hàng</label>
                                        <div class="fw-bold mb-1">
                                            <a href="{{ route('admin.orders.show', $payment->order_id) }}" class="text-decoration-none">
                                                #{{ $payment->order->order_number }}
                                            </a>
                                        </div>
                                        <div class="text-muted">
                                            {{ trim(($payment->order->user->first_name ?? '') . ' ' . ($payment->order->user->last_name ?? '')) ?: 'N/A' }}
                                        </div>
                                    </div>

                                    <!-- Payment Method -->
                                    <div class="col-md-2">
                                        <label class="text-muted small d-block mb-2">Phương thức</label>
                                        @php
                                            $methodConfig = [
                                                'card' => ['icon' => 'credit-card', 'color' => 'primary', 'text' => 'Thẻ'],
                                                'bank' => ['icon' => 'university', 'color' => 'info', 'text' => 'Ngân hàng'],
                                                'cod' => ['icon' => 'money-bill-wave', 'color' => 'secondary', 'text' => 'COD'],
                                                'wallet' => ['icon' => 'wallet', 'color' => 'success', 'text' => 'Ví điện tử'],
                                            ];
                                            $method = $payment->payment_method->value;
                                            $config = $methodConfig[$method] ?? ['icon' => 'question', 'color' => 'secondary', 'text' => 'Khác'];
                                        @endphp
                                        <span class="badge bg-{{ $config['color'] }}-subtle text-{{ $config['color'] }} fs-6 px-3 py-2">
                                            <i class="fa-solid fa-{{ $config['icon'] }} me-1"></i>{{ $config['text'] }}
                                        </span>
                                    </div>

                                    <!-- Amount -->
                                    <div class="col-md-2 text-center">
                                        <label class="text-muted small d-block mb-2">Số tiền</label>
                                        <div class="fw-bold text-primary fs-4">{{ number_format($payment->amount) }}đ</div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="col-md-2 text-end">
                                        <a href="{{ route('admin.payments.verify-form', $payment->id) }}"
                                           class="btn btn-warning btn-lg w-100 mb-2">
                                            <i class="fa-solid fa-check me-2"></i>Xác nhận
                                        </a>
                                        <a href="{{ route('admin.payments.show', $payment->id) }}"
                                           class="btn btn-outline-info w-100">
                                            <i class="fa-solid fa-eye me-2"></i>Chi tiết
                                        </a>
                                    </div>
                                </div>

                                <!-- Additional Info -->
                                @if($payment->order->customer_note)
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="alert alert-info mb-0 py-2">
                                                <small>
                                                    <i class="fa-solid fa-comment-dots me-1"></i>
                                                    <strong>Ghi chú:</strong> {{ $payment->order->customer_note }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Time Warning -->
                                @php
                                    $hoursAgo = $payment->created_at->diffInHours(now());
                                @endphp
                                @if($hoursAgo > 24)
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <div class="alert alert-danger mb-0 py-2">
                                                <small>
                                                    <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                                    <strong>Cảnh báo:</strong> Giao dịch đã chờ {{ $hoursAgo }} giờ!
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($payments->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $payments->links('components.pagination') }}
                </div>
            @endif

        @else
            <!-- Empty State -->
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="text-success mb-4">
                        <i class="fa-solid fa-check-circle" style="font-size: 5rem;"></i>
                    </div>
                    <h3 class="fw-bold mb-3">Tuyệt vời! Không có giao dịch nào cần xác nhận</h3>
                    <p class="text-muted mb-4">Tất cả các giao dịch đã được xử lý hoặc xác nhận tự động.</p>
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-primary btn-lg">
                        <i class="fa-solid fa-list me-2"></i>Xem tất cả giao dịch
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Auto refresh every 60 seconds
            setTimeout(() => {
                window.location.reload();
            }, 60000);

            // Show notification if there are pending payments
            @if($payments->count() > 0)
                // You can add desktop notification here if needed
                if ("Notification" in window && Notification.permission === "granted") {
                    new Notification("Thanh toán cần xác nhận", {
                        body: "Có {{ $payments->total() }} giao dịch đang chờ xác nhận",
                        icon: "/path/to/icon.png"
                    });
                }
            @endif
        });
    </script>
@endpush
