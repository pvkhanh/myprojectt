{{-- @extends('layouts.admin')

@section('title', 'Xác nhận Thanh toán #' . $payment->id)

@push('styles')
    <style>
        .verify-card {
            border: 2px solid #ffc107;
            background: #fff9e6;
        }

        .info-badge {
            background: #fff;
            border: 1px solid #dee2e6;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
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
                            <i class="fa-solid fa-shield-halved text-warning me-2"></i>
                            Xác nhận Thanh toán
                        </h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.payments.index') }}">Thanh toán</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.payments.show', $payment->id) }}">Chi tiết</a></li>
                                <li class="breadcrumb-item active">Xác nhận</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Warning Alert -->
        <div class="alert alert-warning border-2 d-flex align-items-center mb-4" role="alert">
            <i class="fa-solid fa-exclamation-triangle fs-3 me-3"></i>
            <div>
                <h5 class="alert-heading mb-1">Chú ý quan trọng!</h5>
                <p class="mb-0">Vui lòng kiểm tra kỹ thông tin giao dịch trước khi xác nhận. Hành động này không thể hoàn tác.</p>
            </div>
        </div>

        <div class="row">
            <!-- Left Column - Payment Info -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm mb-4 verify-card">
                    <div class="card-header bg-warning bg-opacity-25 border-bottom border-warning py-3">
                        <h5 class="mb-0 fw-semibold text-dark">
                            <i class="fa-solid fa-info-circle me-2"></i>Thông tin giao dịch cần xác nhận
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="text-muted small mb-2 fw-semibold">Mã giao dịch</label>
                                <div class="info-badge">
                                    <div class="fw-bold">{{ $payment->transaction_id ?: 'Chưa có mã giao dịch' }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="text-muted small mb-2 fw-semibold">Số tiền</label>
                                <div class="info-badge">
                                    <div class="fw-bold fs-5 text-primary">
                                        <i class="fa-solid fa-money-bill-wave me-2"></i>{{ number_format($payment->amount) }}đ
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="text-muted small mb-2 fw-semibold">Phương thức thanh toán</label>
                                <div class="info-badge">
                                    @php
                                        $methodConfig = [
                                            'card' => ['icon' => 'credit-card', 'text' => 'Thẻ tín dụng'],
                                            'bank' => ['icon' => 'university', 'text' => 'Chuyển khoản ngân hàng'],
                                            'cod' => ['icon' => 'money-bill-wave', 'text' => 'Thanh toán khi nhận hàng'],
                                            'wallet' => ['icon' => 'wallet', 'text' => 'Ví điện tử'],
                                        ];
                                        $method = $payment->payment_method->value;
                                        $config = $methodConfig[$method] ?? ['icon' => 'question', 'text' => 'Khác'];
                                    @endphp
                                    <div class="fw-semibold">
                                        <i class="fa-solid fa-{{ $config['icon'] }} me-2"></i>{{ $config['text'] }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="text-muted small mb-2 fw-semibold">Cổng thanh toán</label>
                                <div class="info-badge">
                                    <div class="fw-semibold">{{ $payment->payment_gateway ?: 'N/A' }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="text-muted small mb-2 fw-semibold">Mã đơn hàng</label>
                                <div class="info-badge">
                                    <div class="fw-bold">
                                        <a href="{{ route('admin.orders.show', $payment->order_id) }}" class="text-decoration-none">
                                            #{{ $payment->order->order_number }}
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="text-muted small mb-2 fw-semibold">Ngày tạo giao dịch</label>
                                <div class="info-badge">
                                    <div class="fw-semibold">
                                        <i class="fa-solid fa-clock me-2"></i>{{ $payment->created_at->format('d/m/Y H:i:s') }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="text-muted small mb-2 fw-semibold">Khách hàng</label>
                                <div class="info-badge">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3"
                                            style="width:40px; height:40px;">
                                            <i class="fa-solid fa-user text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">
                                                {{ trim(($payment->order->user->first_name ?? '') . ' ' . ($payment->order->user->last_name ?? '')) ?: 'N/A' }}
                                            </div>
                                            <div class="small text-muted">
                                                <i class="fa-solid fa-envelope me-1"></i>{{ $payment->order->user->email ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-list text-primary me-2"></i>Chi tiết đơn hàng
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-4 py-3">Sản phẩm</th>
                                        <th class="px-4 py-3 text-center">Số lượng</th>
                                        <th class="px-4 py-3 text-end">Đơn giá</th>
                                        <th class="px-4 py-3 text-end">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payment->order->orderItems as $item)
                                        <tr class="border-bottom">
                                            <td class="px-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        @if ($item->product->image)
                                                            <img src="{{ asset('storage/' . $item->product->image) }}"
                                                                 alt="{{ $item->product->name }}"
                                                                 class="rounded"
                                                                 style="width:50px; height:50px; object-fit:cover;">
                                                        @else
                                                            <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                                 style="width:50px; height:50px;">
                                                                <i class="fa-solid fa-image text-muted"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">{{ $item->product->name }}</div>
                                                        @if ($item->variant)
                                                            <div class="small text-muted">{{ $item->variant->name }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center px-4">
                                                <span class="badge bg-light text-dark fs-6">{{ $item->quantity }}</span>
                                            </td>
                                            <td class="text-end px-4">{{ number_format($item->price) }}đ</td>
                                            <td class="text-end px-4 fw-bold">{{ number_format($item->price * $item->quantity) }}đ</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <td colspan="3" class="px-4 py-3 text-end fw-semibold">Tổng cộng:</td>
                                        <td class="px-4 py-3 text-end fw-bold text-primary fs-5">{{ number_format($payment->order->total_amount) }}đ</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Verification Form -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-lg sticky-top" style="top: 20px;">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-check-circle text-success me-2"></i>Xác nhận giao dịch
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('admin.payments.verify', $payment->id) }}" id="verifyForm">
                            @csrf

                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="fa-solid fa-clipboard-list me-1"></i> Ghi chú xác nhận
                                </label>
                                <textarea name="verification_note"
                                          class="form-control form-control-lg @error('verification_note') is-invalid @enderror"
                                          rows="4"
                                          placeholder="Nhập ghi chú (nếu có)...">{{ old('verification_note') }}</textarea>
                                @error('verification_note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Ghi chú sẽ được lưu vào lịch sử giao dịch</div>
                            </div>

                            <div class="border-top pt-4 mb-4">
                                <h6 class="fw-semibold mb-3">Checklist xác nhận:</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="check1" required>
                                    <label class="form-check-label" for="check1">
                                        Đã kiểm tra thông tin khách hàng
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="check2" required>
                                    <label class="form-check-label" for="check2">
                                        Đã xác nhận số tiền chính xác
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="check3" required>
                                    <label class="form-check-label" for="check3">
                                        Đã kiểm tra phương thức thanh toán
                                    </label>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="action" value="approve" class="btn btn-success btn-lg">
                                    <i class="fa-solid fa-check me-2"></i> Xác nhận thanh toán
                                </button>
                                <button type="button" class="btn btn-danger btn-lg" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                    <i class="fa-solid fa-times me-2"></i> Từ chối giao dịch
                                </button>
                                <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-outline-secondary btn-lg">
                                    <i class="fa-solid fa-arrow-left me-2"></i> Hủy bỏ
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Warning Box -->
                <div class="alert alert-danger border-2 mt-4" role="alert">
                    <h6 class="alert-heading fw-bold mb-2">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>Lưu ý quan trọng
                    </h6>
                    <ul class="mb-0 ps-3">
                        <li>Xác nhận thanh toán sẽ cập nhật trạng thái đơn hàng</li>
                        <li>Hành động này không thể hoàn tác</li>
                        <li>Chỉ xác nhận khi đã nhận được thanh toán</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.payments.verify', $payment->id) }}">
                    @csrf
                    <input type="hidden" name="action" value="reject">

                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="rejectModalLabel">
                            <i class="fa-solid fa-times-circle me-2"></i>Từ chối giao dịch
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fa-solid fa-exclamation-triangle me-2"></i>
                            Bạn có chắc chắn muốn từ chối giao dịch này?
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Lý do từ chối <span class="text-danger">*</span></label>
                            <textarea name="verification_note"
                                      class="form-control"
                                      rows="4"
                                      placeholder="Nhập lý do từ chối giao dịch..."
                                      required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fa-solid fa-times me-2"></i>Xác nhận từ chối
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
            const form = document.getElementById('verifyForm');

            form.addEventListener('submit', function(e) {
                const action = e.submitter.value;

                if (action === 'approve') {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Xác nhận thanh toán?',
                        html: `
                            <p class="mb-2">Bạn đang xác nhận giao dịch với số tiền:</p>
                            <h3 class="text-primary mb-3">${new Intl.NumberFormat('vi-VN').format({{ $payment->amount }})}đ</h3>
                            <p class="text-muted small mb-0">Hành động này không thể hoàn tác</p>
                        `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#198754',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Xác nhận',
                        cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                }
            });
        });
    </script>
@endpush --}}



@extends('layouts.admin')

@section('title', 'Xác nhận Thanh toán #' . $payment->id)

@push('styles')
    <style>
        .verify-card {
            border: 2px solid #ffc107;
            background: #fff9e6;
        }

        .info-badge {
            background: #fff;
            border: 1px solid #dee2e6;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-4">
        {{-- Header --}}
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-dark mb-2">
                        <i class="fa-solid fa-shield-halved text-warning me-2"></i>
                        Xác nhận Thanh toán
                    </h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.payments.index') }}">Thanh toán</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.payments.show', $payment->id) }}">Chi
                                    tiết</a></li>
                            <li class="breadcrumb-item active">Xác nhận</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-outline-secondary btn-lg">
                    <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
                </a>
            </div>
        </div>

        {{-- Cảnh báo --}}
        <div class="alert alert-warning border-2 d-flex align-items-center mb-4" role="alert">
            <i class="fa-solid fa-exclamation-triangle fs-3 me-3"></i>
            <div>
                <h5 class="alert-heading mb-1">Chú ý quan trọng!</h5>
                <p class="mb-0">Vui lòng kiểm tra kỹ thông tin giao dịch trước khi xác nhận. Hành động này không thể hoàn
                    tác.</p>
            </div>
        </div>

        <div class="row">
            {{-- Thông tin giao dịch --}}
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm mb-4 verify-card">
                    <div class="card-header bg-warning bg-opacity-25 border-bottom border-warning py-3">
                        <h5 class="mb-0 fw-semibold text-dark">
                            <i class="fa-solid fa-info-circle me-2"></i>Thông tin giao dịch cần xác nhận
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            {{-- Mã giao dịch --}}
                            <div class="col-md-6">
                                <label class="text-muted small mb-2 fw-semibold">Mã giao dịch</label>
                                <div class="info-badge fw-bold">{{ $payment->transaction_id ?: 'Chưa có mã giao dịch' }}
                                </div>
                            </div>

                            {{-- Số tiền --}}
                            <div class="col-md-6">
                                <label class="text-muted small mb-2 fw-semibold">Số tiền</label>
                                <div class="info-badge fw-bold fs-5 text-primary">
                                    <i class="fa-solid fa-money-bill-wave me-2"></i>{{ number_format($payment->amount) }}đ
                                </div>
                            </div>

                            {{-- Phương thức thanh toán --}}
                            <div class="col-md-6">
                                <label class="text-muted small mb-2 fw-semibold">Phương thức thanh toán</label>
                                <div class="info-badge">
                                    @php
                                        $methodConfig = [
                                            'card' => ['icon' => 'credit-card', 'text' => 'Thẻ tín dụng'],
                                            'bank' => ['icon' => 'university', 'text' => 'Chuyển khoản ngân hàng'],
                                            'cod' => [
                                                'icon' => 'money-bill-wave',
                                                'text' => 'Thanh toán khi nhận hàng',
                                            ],
                                            'wallet' => ['icon' => 'wallet', 'text' => 'Ví điện tử'],
                                        ];
                                        $method = $payment->payment_method->value ?? 'other';
                                        $config = $methodConfig[$method] ?? ['icon' => 'question', 'text' => 'Khác'];
                                    @endphp
                                    <div class="fw-semibold">
                                        <i class="fa-solid fa-{{ $config['icon'] }} me-2"></i>{{ $config['text'] }}
                                    </div>
                                </div>
                            </div>

                            {{-- Cổng thanh toán --}}
                            <div class="col-md-6">
                                <label class="text-muted small mb-2 fw-semibold">Cổng thanh toán</label>
                                <div class="info-badge fw-semibold">{{ $payment->payment_gateway ?: 'N/A' }}</div>
                            </div>

                            {{-- Mã đơn hàng --}}
                            <div class="col-md-6">
                                <label class="text-muted small mb-2 fw-semibold">Mã đơn hàng</label>
                                <div class="info-badge fw-bold">
                                    <a href="{{ route('admin.orders.show', $payment->order_id) }}"
                                        class="text-decoration-none">
                                        #{{ $payment->order->order_number }}
                                    </a>
                                </div>
                            </div>

                            {{-- Ngày tạo --}}
                            <div class="col-md-6">
                                <label class="text-muted small mb-2 fw-semibold">Ngày tạo giao dịch</label>
                                <div class="info-badge fw-semibold">
                                    <i class="fa-solid fa-clock me-2"></i>{{ $payment->created_at->format('d/m/Y H:i:s') }}
                                </div>
                            </div>

                            {{-- Thông tin khách hàng --}}
                            <div class="col-12">
                                <label class="text-muted small mb-2 fw-semibold">Khách hàng</label>
                                <div class="info-badge d-flex align-items-center">
                                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3"
                                        style="width:40px; height:40px;">
                                        <i class="fa-solid fa-user text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">
                                            {{ $payment->order->user->username ??
                                            trim(($payment->order->user->first_name ?? '') . ' ' . ($payment->order->user->last_name ?? '')) ?:
                                                'N/A' }}
                                        </div>

                                        <div class="small text-muted">
                                            <i
                                                class="fa-solid fa-envelope me-1"></i>{{ $payment->order->user->email ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Chi tiết đơn hàng --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-list text-primary me-2"></i>Chi tiết đơn hàng
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-4 py-3">Sản phẩm</th>
                                        <th class="px-4 py-3 text-center">SL</th>
                                        <th class="px-4 py-3 text-end">Đơn giá</th>
                                        <th class="px-4 py-3 text-end">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payment->order->orderItems as $item)
                                        <tr class="border-bottom">
                                            <td class="px-4">{{ $item->product->name }}</td>
                                            <td class="text-center px-4">{{ $item->quantity }}</td>
                                            <td class="text-end px-4">{{ number_format($item->price) }}đ</td>
                                            <td class="text-end px-4 fw-bold">
                                                {{ number_format($item->price * $item->quantity) }}đ</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <td colspan="3" class="text-end fw-semibold">Tổng cộng:</td>
                                        <td class="text-end fw-bold text-primary fs-5 px-4 py-3">
                                            {{ number_format($payment->order->total_amount) }}đ
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form xác nhận --}}
            <div class="col-lg-5">
                <div class="card border-0 shadow-lg sticky-top" style="top: 20px;">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-check-circle text-success me-2"></i>Xác nhận giao dịch
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('admin.payments.verify', $payment->id) }}" id="verifyForm">
                            @csrf

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Ghi chú xác nhận</label>
                                <textarea name="verification_note" class="form-control form-control-lg" rows="4"
                                    placeholder="Nhập ghi chú (nếu có)..."></textarea>
                                <div class="form-text">Ghi chú sẽ được lưu vào lịch sử giao dịch</div>
                            </div>

                            <div class="border-top pt-4 mb-4">
                                <h6 class="fw-semibold mb-3">Checklist xác nhận:</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" required id="check1">
                                    <label class="form-check-label" for="check1">Đã kiểm tra thông tin khách
                                        hàng</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" required id="check2">
                                    <label class="form-check-label" for="check2">Đã xác nhận số tiền chính xác</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" required id="check3">
                                    <label class="form-check-label" for="check3">Đã kiểm tra phương thức thanh
                                        toán</label>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="action" value="approve" class="btn btn-success btn-lg">
                                    <i class="fa-solid fa-check me-2"></i> Xác nhận thanh toán
                                </button>
                                <button type="button" class="btn btn-danger btn-lg" data-bs-toggle="modal"
                                    data-bs-target="#rejectModal">
                                    <i class="fa-solid fa-times me-2"></i> Từ chối giao dịch
                                </button>
                                <a href="{{ route('admin.payments.show', $payment->id) }}"
                                    class="btn btn-outline-secondary btn-lg">
                                    <i class="fa-solid fa-arrow-left me-2"></i> Hủy bỏ
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="alert alert-danger border-2 mt-4">
                    <h6 class="alert-heading fw-bold mb-2"><i class="fa-solid fa-triangle-exclamation me-2"></i>Lưu ý quan
                        trọng</h6>
                    <ul class="mb-0 ps-3">
                        <li>Xác nhận thanh toán sẽ cập nhật trạng thái đơn hàng</li>
                        <li>Hành động này không thể hoàn tác</li>
                        <li>Chỉ xác nhận khi đã nhận được thanh toán thực tế</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal từ chối --}}
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.payments.verify', $payment->id) }}">
                    @csrf
                    <input type="hidden" name="action" value="reject">

                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="rejectModalLabel"><i class="fa-solid fa-times-circle me-2"></i>Từ
                            chối giao dịch</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fa-solid fa-exclamation-triangle me-2"></i>Bạn có chắc chắn muốn từ chối giao dịch
                            này?
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Lý do từ chối <span class="text-danger">*</span></label>
                            <textarea name="verification_note" class="form-control" rows="4" required placeholder="Nhập lý do từ chối..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fa-solid fa-times me-2"></i>Xác nhận từ chối
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
            const form = document.getElementById('verifyForm');

            form.addEventListener('submit', (e) => {
                const btn = e.submitter;
                if (!btn || btn.value !== 'approve') return;

                e.preventDefault();

                Swal.fire({
                    title: 'Xác nhận thanh toán?',
                    html: `
                <p class="mb-2">Bạn đang xác nhận giao dịch với số tiền:</p>
                <h3 class="text-primary mb-3">{{ number_format($payment->amount) }}đ</h3>
                <p class="text-muted small mb-0">Hành động này không thể hoàn tác</p>
            `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Xác nhận',
                    cancelButtonText: 'Hủy'
                }).then(result => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    </script>
@endpush
