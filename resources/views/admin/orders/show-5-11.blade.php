{{-- @extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng #' . $order->order_number)

@push('styles')
<style>
.order-timeline {
    position: relative;
    padding: 20px 0;
}
.timeline-item {
    text-align: center;
    position: relative;
}
.timeline-item::before {
    content: '';
    position: absolute;
    top: 35px;
    left: 50%;
    width: 100%;
    height: 3px;
    background: #e0e0e0;
    z-index: 0;
}
.timeline-item:last-child::before {
    display: none;
}
.timeline-icon {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    font-size: 24px;
    color: white;
    position: relative;
    z-index: 1;
    background: #e0e0e0;
    border: 4px solid #fff;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
.timeline-item.active .timeline-icon {
    animation: pulse 2s infinite;
}
.timeline-item.current .timeline-icon {
    transform: scale(1.2);
    box-shadow: 0 6px 20px rgba(0,0,0,0.2);
}
@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}
.timeline-label {
    font-weight: 600;
    margin-bottom: 5px;
    color: #666;
}
.timeline-item.active .timeline-label {
    color: #333;
}
.timeline-time {
    font-size: 12px;
    color: #999;
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
                    <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="btn btn-success btn-lg">
                        <i class="fa-solid fa-print me-2"></i> In hóa đơn
                    </a>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Status Timeline -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-4">
                <i class="fa-solid fa-timeline text-primary me-2"></i>Trạng thái đơn hàng
            </h5>
            <div class="order-timeline">
                @php
                    $statuses = [
                        'pending' => ['icon' => 'clock', 'label' => 'Chờ xử lý', 'color' => 'warning'],
                        'paid' => ['icon' => 'credit-card', 'label' => 'Đã thanh toán', 'color' => 'info'],
                        'shipped' => ['icon' => 'truck', 'label' => 'Đang giao', 'color' => 'primary'],
                        'completed' => ['icon' => 'check-circle', 'label' => 'Hoàn thành', 'color' => 'success'],
                    ];
                    $currentStatus = $order->status->value;
                    $currentIndex = array_search($currentStatus, array_keys($statuses));
                    if ($currentIndex === false && $currentStatus !== 'cancelled') {
                        $currentIndex = 0;
                    }
                @endphp

                <div class="row">
                    @foreach ($statuses as $key => $status)
                        @php
                            $index = array_search($key, array_keys($statuses));
                            $isActive = $currentStatus !== 'cancelled' && $index <= $currentIndex;
                            $isCurrent = $key === $currentStatus;
                        @endphp
                        <div class="col-3">
                            <div class="timeline-item {{ $isActive ? 'active' : '' }} {{ $isCurrent ? 'current' : '' }}">
                                <div class="timeline-icon bg-{{ $status['color'] }}">
                                    <i class="fa-solid fa-{{ $status['icon'] }}"></i>
                                </div>
                                <div class="timeline-label">{{ $status['label'] }}</div>
                                @if ($key === 'pending' && $order->created_at)
                                    <div class="timeline-time">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                                @elseif($key === 'paid' && $order->paid_at)
                                    <div class="timeline-time">{{ $order->paid_at->format('d/m/Y H:i') }}</div>
                                @elseif($key === 'shipped' && $order->shipped_at)
                                    <div class="timeline-time">{{ $order->shipped_at->format('d/m/Y H:i') }}</div>
                                @elseif($key === 'completed' && $order->completed_at)
                                    <div class="timeline-time">{{ $order->completed_at->format('d/m/Y H:i') }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($order->status->value === 'cancelled')
                    <div class="alert alert-danger mt-4 mb-0">
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
                @endif
            </div>
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
                                                        <i class="fa-solid fa-tag me-1"></i>{{ $item->variant->name }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="fw-semibold">{{ number_format($item->price) }}đ</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="badge bg-primary fs-6 px-3 py-2">{{ $item->quantity }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <span class="fw-bold text-primary fs-6">
                                            {{ number_format($item->price * $item->quantity) }}đ
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-end fw-semibold">Tạm tính:</td>
                                    <td class="px-4 py-3 text-end fw-bold">{{ number_format($order->subtotal) }}đ</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-end fw-semibold">Phí vận chuyển:</td>
                                    <td class="px-4 py-3 text-end fw-bold">{{ number_format($order->shipping_fee) }}đ</td>
                                </tr>
                                <tr class="table-primary">
                                    <td colspan="3" class="px-4 py-3 text-end fw-bold fs-5">TỔNG CỘNG:</td>
                                    <td class="px-4 py-3 text-end fw-bold text-primary fs-5">
                                        {{ number_format($order->total_amount) }}đ
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
                                {{ $order->shippingAddress->phone }}
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
            <div class="card border-0 shadow-sm mb-4">
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
                        <h5 class="fw-bold mb-1">{{ $order->user->name }}</h5>
                        <p class="text-muted mb-0">
                            <i class="fa-solid fa-envelope me-2"></i>{{ $order->user->email }}
                        </p>
                    </div>
                    <hr>
                    <div class="d-grid gap-2">
                        <a href="mailto:{{ $order->user->email }}" class="btn btn-outline-primary">
                            <i class="fa-solid fa-envelope me-2"></i>Gửi email
                        </a>
                        @if ($order->shippingAddress)
                        <a href="tel:{{ $order->shippingAddress->phone }}" class="btn btn-outline-success">
                            <i class="fa-solid fa-phone me-2"></i>Gọi điện
                        </a>
                        @endif
                    </div>
                    @else
                    <p class="text-muted text-center mb-0">Không có thông tin khách hàng</p>
                    @endif
                </div>
            </div>

            <!-- Payment Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fa-solid fa-credit-card text-primary me-2"></i>Thông tin thanh toán
                    </h5>
                </div>
                <div class="card-body">
                    @php $payment = $order->payments->first(); @endphp
                    @if ($payment)
                    <div class="mb-3">
                        <label class="text-muted small mb-1">Phương thức</label>
                        <div class="fw-semibold">
                            <i class="fa-solid fa-credit-card text-primary me-2"></i>
                            {{ $payment->payment_method->label() }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small mb-1">Trạng thái</label>
                        <div>
                            @if ($payment->status->value === 'success')
                                <span class="badge bg-success fs-6 px-3 py-2">
                                    <i class="fa-solid fa-check-circle me-1"></i>Đã thanh toán
                                </span>
                            @elseif($payment->status->value === 'pending')
                                <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                                    <i class="fa-solid fa-clock me-1"></i>Chờ thanh toán
                                </span>
                            @else
                                <span class="badge bg-danger fs-6 px-3 py-2">
                                    <i class="fa-solid fa-times-circle me-1"></i>Thất bại
                                </span>
                            @endif
                        </div>
                    </div>
                    @if ($payment->transaction_id)
                    <div class="mb-3">
                        <label class="text-muted small mb-1">Mã giao dịch</label>
                        <div class="fw-semibold">{{ $payment->transaction_id }}</div>
                    </div>
                    @endif
                    @if ($payment->paid_at)
                    <div class="mb-3">
                        <label class="text-muted small mb-1">Thời gian thanh toán</label>
                        <div class="fw-semibold">{{ $payment->paid_at->format('d/m/Y H:i') }}</div>
                    </div>
                    @endif
                    <hr>
                    <div class="d-grid gap-2">
                        @if ($payment->status->value === 'pending')
                        <form action="{{ route('admin.orders.confirm-payment', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fa-solid fa-check me-2"></i>Xác nhận thanh toán
                            </button>
                        </form>
                        @endif
                    </div>
                    @else
                    <p class="text-muted text-center mb-0">Chưa có thông tin thanh toán</p>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fa-solid fa-bolt text-primary me-2"></i>Thao tác nhanh
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if ($order->status->value === 'pending')
                        <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="quick-status-form">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="paid">
                            <button type="submit" class="btn btn-info w-100">
                                <i class="fa-solid fa-credit-card me-2"></i>Đánh dấu đã thanh toán
                            </button>
                        </form>
                        @endif

                        @if ($order->status->value === 'paid')
                        <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="quick-status-form">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="shipped">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fa-solid fa-truck me-2"></i>Đánh dấu đang giao
                            </button>
                        </form>
                        @endif

                        @if ($order->status->value === 'shipped')
                        <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="quick-status-form">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fa-solid fa-check-circle me-2"></i>Hoàn thành đơn hàng
                            </button>
                        </form>
                        @endif

                        @if (in_array($order->status->value, ['pending', 'paid']))
                        <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#cancelModal">
                            <i class="fa-solid fa-ban me-2"></i>Hủy đơn hàng
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.orders.cancel', $order->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa-solid fa-ban text-danger me-2"></i>Hủy đơn hàng
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fa-solid fa-exclamation-triangle me-2"></i>
                        Bạn có chắc chắn muốn hủy đơn hàng <strong>#{{ $order->order_number }}</strong>?
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Lý do hủy <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="4" required
                            placeholder="Nhập lý do hủy đơn hàng..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-ban me-2"></i>Xác nhận hủy
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
    // Quick status update with confirmation
    document.querySelectorAll('.quick-status-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const status = this.querySelector('input[name="status"]').value;
            const statusText = {
                'paid': 'đã thanh toán',
                'shipped': 'đang giao hàng',
                'completed': 'hoàn thành'
            };

            Swal.fire({
                title: 'Xác nhận',
                text: `Bạn có chắc muốn đánh dấu đơn hàng ${statusText[status]}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Xác nhận',
                cancelButtonText: 'Hủy',
                confirmButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });
});
</script>
@endpush --}}



{{--

@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng #' . $order->order_number)

@push('styles')
    <style>
        .customer-info-card {
            transition: all 0.3s ease;
        }

        .customer-info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
        }

        .payment-verification-badge {
            animation: pulse-verification 2s infinite;
        }

        @keyframes pulse-verification {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .verification-required {
            border-left: 4px solid #ffc107;
        }

        .verification-success {
            border-left: 4px solid #28a745;
        }

        .verification-failed {
            border-left: 4px solid #dc3545;
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
                        <a href="{{ route('admin.orders.customer-details', $order->id) }}" class="btn btn-info btn-lg">
                            <i class="fa-solid fa-user-circle me-2"></i> Thông tin khách
                        </a>
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
                <!-- Customer Info - Enhanced -->
                <div class="card border-0 shadow-sm mb-4 customer-info-card">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-user text-primary me-2"></i>Thông tin khách hàng
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($order->user)
                            <div class="text-center mb-3">
                                <div class="avatar-lg mx-auto mb-3">
                                    @if ($order->user->avatar_url)
                                        <img src="{{ $order->user->avatar_url }}" alt="{{ $order->user->name }}"
                                            class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
                                            style="width: 80px; height: 80px;">
                                            <i class="fa-solid fa-user text-primary fs-1"></i>
                                        </div>
                                    @endif
                                </div>
                                <h5 class="fw-bold mb-1">
                                    {{ $order->user->first_name }} {{ $order->user->last_name }}
                                </h5>
                                <p class="text-muted mb-0">
                                    <i class="fa-solid fa-envelope me-2"></i>{{ $order->user->email }}
                                </p>
                                @if ($order->user->phone)
                                    <p class="text-muted mb-0">
                                        <i class="fa-solid fa-phone me-2"></i>{{ $order->user->phone }}
                                    </p>
                                @endif
                            </div>

                            <hr>

                            <!-- Customer Quick Stats -->
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
                        @else
                            <p class="text-muted text-center mb-0">Không có thông tin khách hàng</p>
                        @endif
                    </div>
                </div>

                <!-- Payment Info - Enhanced -->
                <div
                    class="card border-0 shadow-sm mb-4
                {{ $payment && $payment->needsVerification() ? 'verification-required' : '' }}
                {{ $payment && $payment->is_verified ? 'verification-success' : '' }}">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-credit-card text-primary me-2"></i>Thanh toán
                            </h5>
                            @php $payment = $order->payments->first(); @endphp
                            @if ($payment && $payment->needsVerification())
                                <span class="badge bg-warning text-dark payment-verification-badge">
                                    <i class="fa-solid fa-clock me-1"></i>Cần xác nhận
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($payment)
                            <div class="mb-3">
                                <label class="text-muted small mb-1">Phương thức</label>
                                <div class="fw-semibold">
                                    @switch($payment->payment_method->value)
                                        @case('cod')
                                            <i class="fa-solid fa-money-bill-wave text-success me-2"></i>
                                            Thanh toán khi nhận hàng (COD)
                                        @break

                                        @case('bank')
                                            <i class="fa-solid fa-university text-info me-2"></i>
                                            Chuyển khoản ngân hàng
                                        @break

                                        @case('card')
                                            <i class="fa-solid fa-credit-card text-primary me-2"></i>
                                            Thẻ tín dụng
                                        @break

                                        @case('wallet')
                                            <i class="fa-solid fa-wallet text-warning me-2"></i>
                                            Ví điện tử
                                        @break
                                    @endswitch
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="text-muted small mb-1">Trạng thái</label>
                                <div>
                                    @if ($payment->status->value === 'success')
                                        <span class="badge bg-success fs-6 px-3 py-2">
                                            <i class="fa-solid fa-check-circle me-1"></i>Đã thanh toán
                                        </span>
                                    @elseif($payment->status->value === 'pending')
                                        <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                                            <i class="fa-solid fa-clock me-1"></i>Chờ thanh toán
                                        </span>
                                    @else
                                        <span class="badge bg-danger fs-6 px-3 py-2">
                                            <i class="fa-solid fa-times-circle me-1"></i>Thất bại
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Verification Status -->
                            @if ($payment->requires_manual_verification)
                                <div class="mb-3">
                                    <label class="text-muted small mb-1">Xác thực</label>
                                    <div>
                                        @if ($payment->is_verified)
                                            <span class="badge bg-success fs-6 px-3 py-2 mb-2">
                                                <i class="fa-solid fa-shield-check me-1"></i>Đã xác nhận
                                            </span>
                                            @if ($payment->verifier)
                                                <div class="small text-muted">
                                                    <i class="fa-solid fa-user me-1"></i>
                                                    Bởi: {{ $payment->verifier->name }}
                                                </div>
                                                <div class="small text-muted">
                                                    <i class="fa-solid fa-clock me-1"></i>
                                                    {{ $payment->verified_at->format('d/m/Y H:i') }}
                                                </div>
                                            @endif
                                        @else
                                            <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                                                <i class="fa-solid fa-exclamation-triangle me-1"></i>Chưa xác nhận
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if ($payment->transaction_id)
                                <div class="mb-3">
                                    <label class="text-muted small mb-1">Mã giao dịch</label>
                                    <div class="fw-semibold">{{ $payment->transaction_id }}</div>
                                </div>
                            @endif

                            @if ($payment->paid_at)
                                <div class="mb-3">
                                    <label class="text-muted small mb-1">Thời gian thanh toán</label>
                                    <div class="fw-semibold">{{ $payment->paid_at->format('d/m/Y H:i') }}</div>
                                </div>
                            @endif

                            @if ($payment->verification_note)
                                <div class="mb-3">
                                    <label class="text-muted small mb-1">Ghi chú xác nhận</label>
                                    <div class="p-2 bg-light rounded small">{{ $payment->verification_note }}</div>
                                </div>
                            @endif

                            <hr>

                            <!-- Payment Actions -->
                            <div class="d-grid gap-2">
                                @if ($payment->canBeVerified())
                                    <button type="button" class="btn btn-success w-100" data-bs-toggle="modal"
                                        data-bs-target="#confirmPaymentModal">
                                        <i class="fa-solid fa-check me-2"></i>Xác nhận thanh toán
                                    </button>
                                    <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal"
                                        data-bs-target="#rejectPaymentModal">
                                        <i class="fa-solid fa-times me-2"></i>Từ chối thanh toán
                                    </button>
                                @endif
                            </div>
                        @else
                            <p class="text-muted text-center mb-0">Chưa có thông tin thanh toán</p>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions (giữ nguyên code cũ) -->
            </div>
        </div>
    </div>

    <!-- Confirm Payment Modal -->
    <div class="modal fade" id="confirmPaymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.orders.confirm-payment', $order->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fa-solid fa-check-circle text-success me-2"></i>Xác nhận thanh toán
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fa-solid fa-info-circle me-2"></i>
                            Xác nhận đơn hàng <strong>#{{ $order->order_number }}</strong> đã thanh toán thành công
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mã giao dịch (tùy chọn)</label>
                            <input type="text" name="transaction_id" class="form-control"
                                placeholder="Nhập mã giao dịch nếu có..." value="{{ $payment->transaction_id ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Ghi chú xác nhận</label>
                            <textarea name="verification_note" class="form-control" rows="3"
                                placeholder="Ghi chú về việc xác nhận thanh toán..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fa-solid fa-check me-2"></i>Xác nhận
                        </button>
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
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fa-solid fa-times-circle text-danger me-2"></i>Từ chối thanh toán
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <i class="fa-solid fa-exclamation-triangle me-2"></i>
                            Từ chối thanh toán sẽ tự động hủy đơn hàng <strong>#{{ $order->order_number }}</strong>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Lý do từ chối <span class="text-danger">*</span></label>
                            <textarea name="reason" class="form-control" rows="4" required
                                placeholder="Nhập lý do từ chối thanh toán..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fa-solid fa-times me-2"></i>Từ chối
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cancel Order Modal (giữ nguyên code cũ) -->
    <div class="modal fade" id="cancelModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.orders.cancel', $order->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fa-solid fa-ban text-danger me-2"></i>Hủy đơn hàng
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fa-solid fa-exclamation-triangle me-2"></i>
                            Bạn có chắc chắn muốn hủy đơn hàng <strong>#{{ $order->order_number }}</strong>?
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Lý do hủy <span class="text-danger">*</span></label>
                            <textarea name="reason" class="form-control" rows="4" required placeholder="Nhập lý do hủy đơn hàng..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fa-solid fa-ban me-2"></i>Xác nhận hủy
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
            // Quick status update with confirmation
            document.querySelectorAll('.quick-status-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const status = this.querySelector('input[name="status"]').value;
                    const statusText = {
                        'paid': 'đã thanh toán',
                        'shipped': 'đang giao hàng',
                        'completed': 'hoàn thành'
                    };

                    Swal.fire({
                        title: 'Xác nhận',
                        text: `Bạn có chắc muốn đánh dấu đơn hàng ${statusText[status]}?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Xác nhận',
                        cancelButtonText: 'Hủy',
                        confirmButtonColor: '#3085d6'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush --}}



{{-- @extends('layouts.admin')

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
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
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
        0%, 100% { transform: scale(1.1); }
        50% { transform: scale(1.15); }
    }
    .action-btn-group {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .action-card {
        transition: transform 0.2s;
    }
    .action-card:hover {
        transform: translateY(-3px);
    }
    .payment-verification-alert {
        border-left: 4px solid #ffc107;
        animation: glow 2s ease-in-out infinite;
    }
    @keyframes glow {
        0%, 100% { box-shadow: 0 0 5px rgba(255, 193, 7, 0.3); }
        50% { box-shadow: 0 0 20px rgba(255, 193, 7, 0.6); }
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
                    <a href="{{ route('admin.orders.customer-details', $order->id) }}" class="btn btn-info btn-lg">
                        <i class="fa-solid fa-user-circle me-2"></i> Thông tin khách
                    </a>
                    <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-warning btn-lg">
                        <i class="fa-solid fa-pen me-2"></i> Chỉnh sửa
                    </a>
                    <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="btn btn-success btn-lg">
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

            @if($order->status->value === 'cancelled')
                <div class="alert alert-danger">
                    <div class="d-flex align-items-center">
                        <i class="fa-solid fa-ban fs-3 me-3"></i>
                        <div>
                            <strong>Đơn hàng đã bị hủy</strong>
                            @if($order->cancelled_at)
                                <p class="mb-0 small">Thời gian: {{ $order->cancelled_at->format('d/m/Y H:i') }}</p>
                            @endif
                            @if($order->admin_note)
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
                                ['key' => 'pending', 'icon' => 'clock', 'label' => 'Chờ xử lý', 'time' => $order->created_at],
                                ['key' => 'paid', 'icon' => 'credit-card', 'label' => 'Đã thanh toán', 'time' => $order->paid_at],
                                ['key' => 'shipped', 'icon' => 'truck', 'label' => 'Đang giao', 'time' => $order->shipped_at],
                                ['key' => 'completed', 'icon' => 'check-circle', 'label' => 'Hoàn thành', 'time' => $order->completed_at],
                            ];
                            $currentStep = array_search($order->status->value, array_column($steps, 'key'));
                        @endphp

                        @foreach($steps as $index => $step)
                            @php
                                $isCompleted = $index < $currentStep;
                                $isActive = $index === $currentStep;
                            @endphp
                            <div class="timeline-step {{ $isCompleted ? 'completed' : '' }} {{ $isActive ? 'active' : '' }}">
                                <div class="timeline-icon">
                                    <i class="fa-solid fa-{{ $step['icon'] }}"></i>
                                </div>
                                <div class="fw-semibold mb-1">{{ $step['label'] }}</div>
                                @if($step['time'])
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
                                @foreach($order->orderItems as $item)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            @if($item->product->image)
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
                                                @if($item->variant)
                                                    <div class="small text-muted">
                                                        <i class="fa-solid fa-tag me-1"></i>{{ $item->variant->name }}
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
                                    <td class="px-4 py-3 text-end fw-bold">{{ number_format($order->shipping_fee) }}₫</td>
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
            @if($order->shippingAddress)
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
            @if($order->customer_note || $order->admin_note)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fa-solid fa-note-sticky text-primary me-2"></i>Ghi chú
                    </h5>
                </div>
                <div class="card-body">
                    @if($order->customer_note)
                    <div class="mb-3">
                        <label class="text-muted small mb-1">Ghi chú của khách hàng:</label>
                        <div class="p-3 bg-light rounded">{{ $order->customer_note }}</div>
                    </div>
                    @endif
                    @if($order->admin_note)
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
                    @if($order->user)
                    <div class="text-center mb-3">
                        <div class="avatar-lg mx-auto mb-3">
                            @if($order->user->avatar_url)
                                <img src="{{ $order->user->avatar_url }}" alt="{{ $order->user->name }}"
                                     class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
                                     style="width: 80px; height: 80px;">
                                    <i class="fa-solid fa-user text-primary fs-1"></i>
                                </div>
                            @endif
                        </div>
                        <h5 class="fw-bold mb-1">
                            {{ $order->user->first_name }} {{ $order->user->last_name }}
                        </h5>
                        <p class="text-muted mb-0">
                            <i class="fa-solid fa-envelope me-2"></i>{{ $order->user->email }}
                        </p>
                        @if($order->user->phone)
                        <p class="text-muted mb-0">
                            <i class="fa-solid fa-phone me-2"></i>{{ $order->user->phone }}
                        </p>
                        @endif
                    </div>

                    <hr>

                    <!-- Customer Quick Stats -->
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
                        <a href="{{ route('admin.orders.customer-details', $order->id) }}" class="btn btn-outline-primary">
                            <i class="fa-solid fa-user-circle me-2"></i>Xem chi tiết khách hàng
                        </a>
                        <a href="mailto:{{ $order->user->email }}" class="btn btn-outline-success">
                            <i class="fa-solid fa-envelope me-2"></i>Gửi email
                        </a>
                        @if($order->shippingAddress)
                        <a href="tel:{{ $order->shippingAddress->phone }}" class="btn btn-outline-info">
                            <i class="fa-solid fa-phone me-2"></i>Gọi điện
                        </a>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Payment Info -->
            <div class="card border-0 shadow-sm mb-4 action-card">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-credit-card text-primary me-2"></i>Thanh toán
                        </h5>
                        @if($payment && $payment->needsVerification())
                            <span class="badge bg-warning text-dark">
                                <i class="fa-solid fa-clock me-1"></i>Cần xác nhận
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if($payment)
                        @if($payment->needsVerification())
                        <div class="alert alert-warning payment-verification-alert mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fa-solid fa-exclamation-triangle fs-4 me-3"></i>
                                <div>
                                    <strong>Cần xác nhận thanh toán!</strong>
                                    <p class="mb-0 small">Vui lòng xác nhận để xử lý đơn hàng</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="mb-3">
                            <label class="text-muted small mb-1">Phương thức</label>
                            <div class="fw-semibold">
                                @switch($payment->payment_method->value)
                                    @case('cod')
                                        <i class="fa-solid fa-money-bill-wave text-success me-2"></i>
                                        Thanh toán khi nhận hàng (COD)
                                        @break
                                    @case('bank')
                                        <i class="fa-solid fa-university text-info me-2"></i>
                                        Chuyển khoản ngân hàng
                                        @break
                                    @case('card')
                                        <i class="fa-solid fa-credit-card text-primary me-2"></i>
                                        Thẻ tín dụng
                                        @break
                                    @case('wallet')
                                        <i class="fa-solid fa-wallet text-warning me-2"></i>
                                        Ví điện tử
                                        @break
                                @endswitch
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted small mb-1">Trạng thái</label>
                            <div>
                                @if($payment->status->value === 'success')
                                    <span class="badge bg-success fs-6 px-3 py-2">
                                        <i class="fa-solid fa-check-circle me-1"></i>Đã thanh toán
                                    </span>
                                @elseif($payment->status->value === 'pending')
                                    <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                                        <i class="fa-solid fa-clock me-1"></i>Chờ thanh toán
                                    </span>
                                @else
                                    <span class="badge bg-danger fs-6 px-3 py-2">
                                        <i class="fa-solid fa-times-circle me-1"></i>Thất bại
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if($payment->requires_manual_verification)
                        <div class="mb-3">
                            <label class="text-muted small mb-1">Xác thực</label>
                            <div>
                                @if($payment->is_verified)
                                    <span class="badge bg-success fs-6 px-3 py-2 mb-2">
                                        <i class="fa-solid fa-shield-check me-1"></i>Đã xác nhận
                                    </span>
                                    @if($payment->verifier)
                                    <div class="small text-muted">
                                        <i class="fa-solid fa-user me-1"></i>
                                        Bởi: {{ $payment->verifier->name }}
                                    </div>
                                    <div class="small text-muted">
                                        <i class="fa-solid fa-clock me-1"></i>
                                        {{ $payment->verified_at->format('d/m/Y H:i') }}
                                    </div>
                                    @endif
                                @else
                                    <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                                        <i class="fa-solid fa-exclamation-triangle me-1"></i>Chưa xác nhận
                                    </span>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if($payment->transaction_id)
                        <div class="mb-3">
                            <label class="text-muted small mb-1">Mã giao dịch</label>
                            <div class="fw-semibold">{{ $payment->transaction_id }}</div>
                        </div>
                        @endif

                        @if($payment->paid_at)
                        <div class="mb-3">
                            <label class="text-muted small mb-1">Thời gian thanh toán</label>
                            <div class="fw-semibold">{{ $payment->paid_at->format('d/m/Y H:i') }}</div>
                        </div>
                        @endif

                        @if($payment->verification_note)
                        <div class="mb-3">
                            <label class="text-muted small mb-1">Ghi chú xác nhận</label>
                            <div class="p-2 bg-light rounded small">{{ $payment->verification_note }}</div>
                        </div>
                        @endif

                        <hr>

                        <!-- Payment Actions -->
                        <div class="d-grid gap-2">
                            @if($payment->canBeVerified())
                                <button type="button" class="btn btn-success w-100" data-bs-toggle="modal"
                                        data-bs-target="#confirmPaymentModal">
                                    <i class="fa-solid fa-check me-2"></i>Xác nhận thanh toán
                                </button>
                                <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal"
                                        data-bs-target="#rejectPaymentModal">
                                    <i class="fa-solid fa-times me-2"></i>Từ chối thanh toán
                                </button>
                            @endif
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">Chưa có thông tin thanh toán</p>
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
                        @if($order->status->value === 'pending' && $payment && $payment->status->value === 'success')
                        <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="quick-status-form">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="paid">
                            <button type="submit" class="btn btn-info w-100">
                                <i class="fa-solid fa-credit-card me-2"></i>Đánh dấu đã thanh toán
                            </button>
                        </form>
                        @endif

                        @if($order->status->value === 'paid')
                        <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="quick-status-form">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="shipped">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fa-solid fa-truck me-2"></i>Đánh dấu đang giao
                            </button>
                        </form>
                        @endif

                        @if($order->status->value === 'shipped')
                        <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="quick-status-form">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fa-solid fa-check-circle me-2"></i>Hoàn thành đơn hàng
                            </button>
                        </form>
                        @endif

                        @if($canCancel)
                        <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#cancelModal">
                            <i class="fa-solid fa-ban me-2"></i>Hủy đơn hàng
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirm Payment Modal -->
<div class="modal fade" id="confirmPaymentModal" tabindex="-1">
    <div class="modal-dialog">
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
                    <div class="alert alert-info">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        Xác nhận đơn hàng <strong>#{{ $order->order_number }}</strong> đã thanh toán thành công
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mã giao dịch (tùy chọn)</label>
                        <input type="text" name="transaction_id" class="form-control"
                               placeholder="Nhập mã giao dịch nếu có..." value="{{ $payment->transaction_id ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ghi chú xác nhận</label>
                        <textarea name="verification_note" class="form-control" rows="3"
                                  placeholder="Ghi chú về việc xác nhận thanh toán..."></textarea>
                    </div>
                    <div class="alert alert-success">
                        <i class="fa-solid fa-envelope me-2"></i>
                        Email xác nhận sẽ được gửi tự động đến khách hàng
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa-solid fa-check me-2"></i>Xác nhận
                    </button>
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
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fa-solid fa-times-circle me-2"></i>Từ chối thanh toán
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fa-solid fa-exclamation-triangle me-2"></i>
                        Từ chối thanh toán sẽ tự động hủy đơn hàng <strong>#{{ $order->order_number }}</strong>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Lý do từ chối <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="4" required
                                  placeholder="Nhập lý do từ chối thanh toán..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-times me-2"></i>Từ chối
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
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
                    <div class="alert alert-warning">
                        <i class="fa-solid fa-exclamation-triangle me-2"></i>
                        Bạn có chắc chắn muốn hủy đơn hàng <strong>#{{ $order->order_number }}</strong>?
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Lý do hủy <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="4" required
                                  placeholder="Nhập lý do hủy đơn hàng..."></textarea>
                    </div>
                    <div class="alert alert-info">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        Tồn kho sẽ được tự động hoàn lại
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-ban me-2"></i>Xác nhận hủy
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
    // Quick status update with confirmation
    document.querySelectorAll('.quick-status-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const status = this.querySelector('input[name="status"]').value;
            const statusText = {
                'paid': 'đã thanh toán',
                'shipped': 'đang giao hàng',
                'completed': 'hoàn thành'
            };

            Swal.fire({
                title: 'Xác nhận',
                html: `Bạn có chắc muốn đánh dấu đơn hàng <strong>${statusText[status]}</strong>?<br><small class="text-muted">Email thông báo sẽ được gửi tự động</small>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Xác nhận',
                cancelButtonText: 'Hủy',
                confirmButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });
});
</script>
@endpush --}}



@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<div class="container-fluid py-4">

    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Đơn hàng</a></li>
            <li class="breadcrumb-item active" aria-current="page">#{{ $order->order_number }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0"><i class="fa-solid fa-receipt me-2 text-primary"></i>Chi tiết đơn hàng #{{ $order->order_number }}</h2>
        <span class="badge bg-{{ $order->status->badge }}">{{ ucfirst($order->status->value) }}</span>
    </div>

    <div class="row g-4">

        <div class="col-lg-4">
            {{-- Order Info --}}
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold"><i class="fa-solid fa-info-circle me-2 text-primary"></i>Thông tin đơn hàng</h5>
                </div>
                <div class="card-body">
                    <p><strong>Ngày tạo:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Thanh toán:</strong> {{ $order->payment?->status?->value ?? 'Chưa thanh toán' }}</p>
                    <p><strong>Tổng tiền:</strong> {{ number_format($order->total, 0, ',', '.') }} đ</p>
                    <p><strong>Ghi chú:</strong> {{ $order->note ?? '-' }}</p>
                </div>
            </div>

            {{-- Customer Info --}}
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold"><i class="fa-solid fa-user me-2 text-primary"></i>Thông tin khách hàng</h5>
                </div>
                <div class="card-body">
                    <p><strong>Tên:</strong> {{ $order->customer_name }}</p>
                    <p><strong>Email:</strong> {{ $order->customer_email }}</p>
                    <p><strong>Điện thoại:</strong> {{ $order->customer_phone }}</p>
                    <p><strong>Địa chỉ:</strong> {{ $order->shipping_address }}</p>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="card border-0 shadow-sm action-card">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fa-solid fa-bolt text-primary me-2"></i>Thao tác nhanh
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">

                        {{-- Pending → Paid --}}
                        @if($order->status->value === 'pending')
                            @if($payment && $payment->canBeVerified())
                                <button type="button" class="btn btn-success w-100 quick-action-btn" data-bs-toggle="modal"
                                        data-bs-target="#confirmPaymentModal" title="Xác nhận thanh toán">
                                    <i class="fa-solid fa-check me-2"></i>Xác nhận thanh toán
                                </button>
                                <button type="button" class="btn btn-danger w-100 quick-action-btn" data-bs-toggle="modal"
                                        data-bs-target="#rejectPaymentModal" title="Từ chối thanh toán">
                                    <i class="fa-solid fa-times me-2"></i>Từ chối thanh toán
                                </button>
                            @elseif($payment && $payment->status->value === 'success')
                                <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="quick-status-form" title="Đánh dấu đã thanh toán">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="paid">
                                    <button type="submit" class="btn btn-info w-100">
                                        <i class="fa-solid fa-credit-card me-2"></i>Đánh dấu đã thanh toán
                                    </button>
                                </form>
                            @endif
                        @endif

                        {{-- Paid → Shipped --}}
                        @if($order->status->value === 'paid')
                            <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="quick-status-form" title="Đang giao">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="shipped">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fa-solid fa-truck me-2"></i>Đang giao
                                </button>
                            </form>
                        @endif

                        {{-- Shipped → Completed --}}
                        @if($order->status->value === 'shipped')
                            <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="quick-status-form" title="Hoàn thành">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fa-solid fa-check-circle me-2"></i>Hoàn thành
                                </button>
                            </form>
                        @endif

                        {{-- Hủy đơn --}}
                        @if(!in_array($order->status->value, ['completed', 'cancelled']) && $canCancel)
                            <button type="button" class="btn btn-danger w-100 quick-action-btn" data-bs-toggle="modal"
                                    data-bs-target="#cancelModal" title="Hủy đơn hàng">
                                <i class="fa-solid fa-ban me-2"></i>Hủy đơn
                            </button>
                        @endif

                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Order Items --}}
        <div class="col-lg-8">
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold"><i class="fa-solid fa-boxes-stacked me-2 text-primary"></i>Sản phẩm trong đơn</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Sản phẩm</th>
                                    <th>Giá</th>
                                    <th>Số lượng</th>
                                    <th>Tổng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ number_format($item->price,0,',','.') }} đ</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->price * $item->quantity,0,',','.') }} đ</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Modals --}}
@include('admin.orders.modals.confirm-payment')
@include('admin.orders.modals.reject-payment')
@include('admin.orders.modals.cancel-order')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Tooltip activation
    const tooltipTriggerList = document.querySelectorAll('.quick-action-btn, .quick-status-form');
    tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));

    // Quick status update with SweetAlert2
    document.querySelectorAll('.quick-status-form').forEach(form => {
        const btn = form.querySelector('button');
        btn.dataset.originalHtml = btn.innerHTML;

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const status = this.querySelector('input[name="status"]').value;
            const statusText = { 'paid':'đã thanh toán', 'shipped':'đang giao hàng', 'completed':'hoàn thành' };

            Swal.fire({
                title: 'Xác nhận thao tác',
                html: `Bạn có chắc muốn đánh dấu đơn hàng <strong>#{{ $order->order_number }}</strong> là <strong>${statusText[status]}</strong>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Xác nhận',
                cancelButtonText: 'Hủy',
                confirmButtonColor: '#3085d6',
                preConfirm: () => {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';
                }
            }).then((result) => {
                if(result.isConfirmed) form.submit();
                else {
                    btn.disabled = false;
                    btn.innerHTML = btn.dataset.originalHtml;
                }
            });
        });
    });
});
</script>
@endpush
