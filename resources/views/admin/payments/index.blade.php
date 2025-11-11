@extends('layouts.admin')

@section('title', 'Quản lý Thanh toán')

@push('styles')
    <style>
        .stat-card {
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .bg-gradient-danger {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }

        .action-btn {
            transition: all 0.2s;
        }

        .action-btn:hover {
            transform: scale(1.1);
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
                            <i class="fa-solid fa-credit-card text-primary me-2"></i>
                            Quản lý Thanh toán
                        </h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Thanh toán</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.payments.pending-verification') }}" class="btn btn-warning btn-lg">
                            <i class="fa-solid fa-clock me-2"></i> Cần xác nhận
                            @if ($stats['pending_verification'] > 0)
                                <span class="badge bg-danger">{{ $stats['pending_verification'] }}</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.payments.export') }}" class="btn btn-success btn-lg">
                            <i class="fa-solid fa-file-excel me-2"></i> Xuất Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 bg-gradient-primary text-white stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1 small">Tổng giao dịch</h6>
                                <h4 class="fw-bold mb-0">{{ number_format($stats['total']) }}</h4>
                            </div>
                            <div class="fs-2 opacity-50"><i class="fa-solid fa-receipt"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 bg-gradient-success text-white stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1 small">Thành công</h6>
                                <h4 class="fw-bold mb-0">{{ number_format($stats['success']) }}</h4>
                            </div>
                            <div class="fs-2 opacity-50"><i class="fa-solid fa-check-circle"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 bg-gradient-warning text-white stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1 small">Chờ xử lý</h6>
                                <h4 class="fw-bold mb-0">{{ number_format($stats['pending']) }}</h4>
                            </div>
                            <div class="fs-2 opacity-50"><i class="fa-solid fa-clock"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 bg-gradient-danger text-white stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1 small">Thất bại</h6>
                                <h4 class="fw-bold mb-0">{{ number_format($stats['failed']) }}</h4>
                            </div>
                            <div class="fs-2 opacity-50"><i class="fa-solid fa-times-circle"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-semibold">
                    <i class="fa-solid fa-filter text-primary me-2"></i>Bộ lọc
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.payments.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-magnifying-glass text-muted me-1"></i> Tìm kiếm
                            </label>
                            <input type="text" name="search" class="form-control form-control-lg"
                                placeholder="Mã GD, Mã đơn hàng..." value="{{ request('search') }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-list-check text-muted me-1"></i> Trạng thái
                            </label>
                            <select name="status" class="form-select form-select-lg">
                                <option value="">Tất cả</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý
                                </option>
                                <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Thành công
                                </option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Thất bại
                                </option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-wallet text-muted me-1"></i> Phương thức
                            </label>
                            <select name="payment_method" class="form-select form-select-lg">
                                <option value="">Tất cả</option>
                                <option value="cod" {{ request('payment_method') == 'cod' ? 'selected' : '' }}>COD
                                </option>
                                <option value="bank" {{ request('payment_method') == 'bank' ? 'selected' : '' }}>Ngân
                                    hàng</option>
                                <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Thẻ tín
                                    dụng</option>
                                <option value="wallet" {{ request('payment_method') == 'wallet' ? 'selected' : '' }}>Ví
                                    điện tử</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-shield-halved text-muted me-1"></i> Xác nhận
                            </label>
                            <select name="verification" class="form-select form-select-lg">
                                <option value="">Tất cả</option>
                                <option value="pending" {{ request('verification') == 'pending' ? 'selected' : '' }}>Chờ
                                    xác nhận</option>
                                <option value="verified" {{ request('verification') == 'verified' ? 'selected' : '' }}>Đã
                                    xác nhận</option>
                                <option value="auto" {{ request('verification') == 'auto' ? 'selected' : '' }}>Tự động
                                </option>
                            </select>
                        </div>

                        <div class="col-md-3 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary btn-lg flex-fill">
                                <i class="fa-solid fa-filter me-2"></i> Lọc
                            </button>
                            <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fa-solid fa-rotate-right"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Advanced Filters -->
                    <div class="collapse mt-3" id="advancedFilters">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Từ ngày</label>
                                <input type="date" name="from" class="form-control form-control-lg"
                                    value="{{ request('from') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Đến ngày</label>
                                <input type="date" name="to" class="form-control form-control-lg"
                                    value="{{ request('to') }}">
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="button" class="btn btn-link text-decoration-none p-0" data-bs-toggle="collapse"
                            data-bs-target="#advancedFilters">
                            <i class="fa-solid fa-chevron-down me-1"></i> Bộ lọc nâng cao
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Payments Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-semibold">
                    <i class="fa-solid fa-list text-primary me-2"></i>Danh sách giao dịch
                    <span class="badge bg-primary fs-6">{{ $payments->total() }} giao dịch</span>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 text-center" style="width:80px;">#</th>
                                <th class="px-4 py-3">Mã giao dịch</th>
                                <th class="px-4 py-3">Đơn hàng</th>
                                <th class="px-4 py-3">Khách hàng</th>
                                <th class="px-4 py-3 text-center">Phương thức</th>
                                <th class="px-4 py-3 text-end">Số tiền</th>
                                <th class="px-4 py-3 text-center">Xác nhận</th>
                                <th class="px-4 py-3 text-center">Trạng thái</th>
                                <th class="px-4 py-3 text-center" style="width:150px;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $index => $payment)
                                <tr class="border-bottom {{ request('highlight') == $payment->id ? 'table-success' : '' }}">
                                    <td class="text-center px-4">
                                        <span
                                            class="badge bg-light text-dark fs-6">{{ $payments->firstItem() + $index }}</span>
                                    </td>

                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3"
                                                style="width:45px; height:45px;">
                                                <i class="fa-solid fa-money-bill-wave text-primary fs-5"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $payment->transaction_id ?: 'N/A' }}
                                                </div>
                                                <div class="small text-muted">ID: {{ $payment->id }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4">
                                        <div class="fw-semibold text-primary">
                                            <a href="{{ route('admin.orders.show', $payment->order_id) }}"
                                                class="text-decoration-none">
                                                {{-- #{{ $payment->order->order_number }} --}}
                                                {{ $payment->order?->order_number ?? 'N/A' }}

                                            </a>
                                        </div>
                                        <div class="small text-muted">{{ $payment->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    </td>

                                    <td class="px-4">
                                        <div class="fw-semibold text-dark">
                                            {{ trim(($payment->order->user->first_name ?? '') . ' ' . ($payment->order->user->last_name ?? '')) ?: 'N/A' }}
                                        </div>
                                        <div class="small text-muted">
                                            <i
                                                class="fa-solid fa-envelope me-1"></i>{{ $payment->order->user->email ?? 'N/A' }}
                                        </div>
                                    </td>

                                    <td class="text-center px-4">
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
                                                    'text' => 'Ngân hàng',
                                                ],
                                                'cod' => [
                                                    'icon' => 'money-bill-wave',
                                                    'color' => 'secondary',
                                                    'text' => 'COD',
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
                                        <span class="text-{{ $config['color'] }} d-inline-flex align-items-center">
                                            <i class="fa-solid fa-{{ $config['icon'] }} me-1"></i>{{ $config['text'] }}
                                        </span>
                                    </td>

                                    <td class="text-end px-4">
                                        <div class="fw-bold text-primary fs-6">{{ number_format($payment->amount) }}đ
                                        </div>
                                    </td>

                                    <td class="text-center px-4">
                                        @if (!$payment->requires_manual_verification)
                                            <span class="badge bg-success fs-6 px-3 py-2">
                                                <i class="fa-solid fa-robot me-1"></i>Tự động
                                            </span>
                                        @elseif($payment->is_verified)
                                            <span class="badge bg-success fs-6 px-3 py-2">
                                                <i class="fa-solid fa-check-circle me-1"></i>Đã xác nhận
                                            </span>
                                        @elseif($payment->status->value === 'failed')
                                            <span class="badge bg-danger fs-6 px-3 py-2">
                                                <i class="fa-solid fa-times-circle me-1"></i>Từ chối
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                                                <i class="fa-solid fa-clock me-1"></i>Chờ xác nhận
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-center px-4">
                                        @php
                                            $statusConfig = [
                                                'pending' => [
                                                    'class' => 'warning text-dark',
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
                                    </td>

                                    <td class="text-center px-4">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.payments.show', $payment->id) }}"
                                                class="btn btn-outline-info btn-sm action-btn" data-bs-toggle="tooltip"
                                                title="Xem chi tiết">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            @if ($payment->canBeVerified())
                                                <a href="{{ route('admin.payments.verify-form', $payment->id) }}"
                                                    class="btn btn-outline-warning btn-sm action-btn"
                                                    data-bs-toggle="tooltip" title="Xác nhận">
                                                    <i class="fa-solid fa-check"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fa-solid fa-inbox fs-1 d-block mb-3 opacity-50"></i>
                                            <h5>Không có giao dịch nào</h5>
                                            <p class="mb-0">Thử thay đổi bộ lọc hoặc kiểm tra lại</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($payments->hasPages())
                <div class="card-footer bg-white border-top py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Hiển thị {{ $payments->firstItem() }} - {{ $payments->lastItem() }} trong
                            {{ $payments->total() }} giao dịch
                        </div>
                        <div>{{ $payments->links('components.pagination') }}</div>
                    </div>
                </div>
            @endif
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
