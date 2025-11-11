@extends('layouts.admin')

@section('title', 'Thanh toán cần xác nhận')

@push('styles')
    <style>
        .payment-pending-row {
            animation: highlight-pending 2s infinite;
        }

        @keyframes highlight-pending {

            0%,
            100% {
                background-color: transparent;
            }

            50% {
                background-color: #fff3cd;
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
                                <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Đơn hàng</a></li>
                                <li class="breadcrumb-item active">Cần xác nhận</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-lg">
                            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-warning bg-opacity-10">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small mb-1">Tổng cần xác nhận</div>
                                <div class="fs-3 fw-bold text-warning">{{ number_format($stats['total']) }}</div>
                            </div>
                            <div class="fs-1 text-warning opacity-50">
                                <i class="fa-solid fa-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small mb-1">COD</div>
                                <div class="fs-4 fw-bold text-success">{{ number_format($stats['cod']) }}</div>
                            </div>
                            <div class="fs-2 text-success opacity-50">
                                <i class="fa-solid fa-money-bill-wave"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small mb-1">Chuyển khoản</div>
                                <div class="fs-4 fw-bold text-info">{{ number_format($stats['bank_transfer']) }}</div>
                            </div>
                            <div class="fs-2 text-info opacity-50">
                                <i class="fa-solid fa-university"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert -->
        @if ($pendingPayments->count() > 0)
            <div class="alert alert-warning border-0 shadow-sm mb-4">
                <div class="d-flex align-items-center">
                    <i class="fa-solid fa-bell fs-3 me-3"></i>
                    <div>
                        <h5 class="alert-heading mb-1">Có {{ $pendingPayments->count() }} thanh toán cần xác nhận</h5>
                        <p class="mb-0">
                            Vui lòng xác nhận thanh toán để xử lý đơn hàng kịp thời
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Payments Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-semibold">
                    <i class="fa-solid fa-list text-warning me-2"></i>Danh sách thanh toán
                    <span class="badge bg-warning text-dark fs-6">{{ $pendingPayments->total() }}</span>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3">Đơn hàng</th>
                                <th class="px-4 py-3">Khách hàng</th>
                                <th class="px-4 py-3 text-center">Phương thức</th>
                                <th class="px-4 py-3 text-end">Số tiền</th>
                                <th class="px-4 py-3 text-center">Ngày tạo</th>
                                <th class="px-4 py-3 text-center" style="width: 200px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingPayments as $payment)
                                <tr class="payment-pending-row border-bottom">
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center me-3"
                                                style="width:45px; height:45px;">
                                                <i class="fa-solid fa-receipt text-warning fs-5"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">#{{ $payment->order->order_number }}</div>
                                                <div class="small text-muted">{{ $payment->order->orderItems->count() }} sản
                                                    phẩm</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="fw-semibold">{{ $payment->order->user->name ?? 'N/A' }}</div>
                                        <div class="small text-muted">
                                            <i
                                                class="fa-solid fa-envelope me-1"></i>{{ $payment->order->user->email ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @switch($payment->payment_method->value)
                                            @case('cod')
                                                <span class="badge bg-success px-3 py-2">
                                                    <i class="fa-solid fa-money-bill-wave me-1"></i>COD
                                                </span>
                                            @break

                                            @case('bank')
                                                <span class="badge bg-info px-3 py-2">
                                                    <i class="fa-solid fa-university me-1"></i>Ngân hàng
                                                </span>
                                            @break

                                            @case('wallet')
                                                <span class="badge bg-warning text-dark px-3 py-2">
                                                    <i class="fa-solid fa-wallet me-1"></i>Ví điện tử
                                                </span>
                                            @break

                                            @case('card')
                                                <span class="badge bg-primary px-3 py-2">
                                                    <i class="fa-solid fa-credit-card me-1"></i>Thẻ
                                                </span>
                                            @break
                                        @endswitch
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <div class="fw-bold text-primary fs-6">{{ number_format($payment->amount) }}₫</div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="fw-semibold">{{ $payment->created_at->format('d/m/Y') }}</div>
                                        <div class="small text-muted">{{ $payment->created_at->format('H:i') }}</div>
                                        <div class="small text-danger mt-1">
                                            <i
                                                class="fa-solid fa-clock me-1"></i>{{ $payment->created_at->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.orders.show', $payment->order->id) }}"
                                                class="btn btn-outline-primary btn-sm" data-bs-toggle="tooltip"
                                                title="Xem chi tiết">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-success btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#quickConfirmModal{{ $payment->id }}"
                                                title="Xác nhận nhanh">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#quickRejectModal{{ $payment->id }}" title="Từ chối">
                                                <i class="fa-solid fa-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Quick Confirm Modal -->
                                <div class="modal fade" id="quickConfirmModal{{ $payment->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form
                                                action="{{ route('admin.orders.confirm-payment', $payment->order->id) }}"
                                                method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h6 class="modal-title">
                                                        <i class="fa-solid fa-check-circle text-success me-2"></i>
                                                        Xác nhận thanh toán #{{ $payment->order->order_number }}
                                                    </h6>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="alert alert-info small">
                                                        <strong>Khách hàng:</strong>
                                                        {{ $payment->order->user->name ?? 'N/A' }}<br>
                                                        <strong>Số tiền:</strong>
                                                        {{ number_format($payment->amount) }}₫<br>
                                                        <strong>Phương thức:</strong>
                                                        {{ $payment->payment_method->label() }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Mã giao dịch (tùy chọn)</label>
                                                        <input type="text" name="transaction_id" class="form-control"
                                                            placeholder="Nhập mã giao dịch...">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Ghi chú</label>
                                                        <textarea name="verification_note" class="form-control" rows="2" placeholder="Ghi chú xác nhận..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Đóng</button>
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="fa-solid fa-check me-2"></i>Xác nhận
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Quick Reject Modal -->
                                <div class="modal fade" id="quickRejectModal{{ $payment->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('admin.orders.reject-payment', $payment->order->id) }}"
                                                method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h6 class="modal-title">
                                                        <i class="fa-solid fa-times-circle text-danger me-2"></i>
                                                        Từ chối thanh toán #{{ $payment->order->order_number }}
                                                    </h6>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="alert alert-danger small">
                                                        Từ chối thanh toán sẽ tự động hủy đơn hàng này
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Lý do từ chối <span
                                                                class="text-danger">*</span></label>
                                                        <textarea name="reason" class="form-control" rows="3" required placeholder="Nhập lý do từ chối..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Đóng</button>
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="fa-solid fa-times me-2"></i>Từ chối
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="text-muted">
                                                <i
                                                    class="fa-solid fa-check-circle fs-1 d-block mb-3 opacity-50 text-success"></i>
                                                <h5>Tuyệt vời!</h5>
                                                <p class="mb-0">Không có thanh toán nào cần xác nhận</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($pendingPayments->hasPages())
                    <div class="card-footer bg-white border-top py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                Hiển thị {{ $pendingPayments->firstItem() }} - {{ $pendingPayments->lastItem() }}
                                trong {{ $pendingPayments->total() }} thanh toán
                            </div>
                            <div>{{ $pendingPayments->links() }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    // Initialize tooltips
                    const tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    tooltips.map(el => new bootstrap.Tooltip(el));
                });
            </script>
        @endpush
    @endsection
