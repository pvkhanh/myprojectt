@extends('layouts.admin')

@section('title', 'Quản lý Đơn hàng')

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

        .bg-gradient-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .bg-gradient-purple {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .bg-gradient-danger {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }

        .bg-gradient-revenue {
            background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);
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
                            <i class="fa-solid fa-shopping-cart text-primary me-2"></i>
                            Quản lý Đơn hàng
                        </h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Đơn hàng</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.orders.trashed') }}" class="btn btn-outline-danger btn-lg">
                            <i class="fa-solid fa-trash-arrow-up me-2"></i> Thùng rác
                        </a>
                        <a href="{{ route('admin.orders.export', request()->query()) }}" class="btn btn-success btn-lg">
                            <i class="fa-solid fa-file-excel me-2"></i> Xuất Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm h-100 bg-gradient-primary text-white stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1 small">Tổng đơn</h6>
                                <h4 class="fw-bold mb-0">{{ number_format($stats['total']) }}</h4>
                            </div>
                            <div class="fs-2 opacity-50"><i class="fa-solid fa-shopping-bag"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6">
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

            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm h-100 bg-gradient-info text-white stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1 small">Đã thanh toán</h6>
                                <h4 class="fw-bold mb-0">{{ number_format($stats['paid']) }}</h4>
                            </div>
                            <div class="fs-2 opacity-50"><i class="fa-solid fa-credit-card"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm h-100 bg-gradient-purple text-white stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1 small">Đang giao</h6>
                                <h4 class="fw-bold mb-0">{{ number_format($stats['shipped']) }}</h4>
                            </div>
                            <div class="fs-2 opacity-50"><i class="fa-solid fa-truck"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm h-100 bg-gradient-success text-white stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1 small">Hoàn thành</h6>
                                <h4 class="fw-bold mb-0">{{ number_format($stats['completed']) }}</h4>
                            </div>
                            <div class="fs-2 opacity-50"><i class="fa-solid fa-check-circle"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm h-100 bg-gradient-danger text-white stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1 small">Đã hủy</h6>
                                <h4 class="fw-bold mb-0">{{ number_format($stats['cancelled']) }}</h4>
                            </div>
                            <div class="fs-2 opacity-50"><i class="fa-solid fa-ban"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Card -->
        <div class="card border-0 shadow-sm mb-4 bg-gradient-revenue text-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">
                            <i class="fa-solid fa-chart-line me-2"></i>Tổng doanh thu
                        </h6>
                        <h2 class="fw-bold mb-0">{{ number_format($stats['total_revenue']) }}đ</h2>
                        <p class="mb-0 mt-2 small text-white-50">Từ các đơn hàng đã hoàn thành</p>
                    </div>
                    <div class="fs-1 opacity-20"><i class="fa-solid fa-sack-dollar"></i></div>
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
                <form method="GET" action="{{ route('admin.orders.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-magnifying-glass text-muted me-1"></i> Tìm kiếm
                            </label>
                            <input type="text" name="search" class="form-control form-control-lg"
                                placeholder="Mã đơn hàng, tên khách..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-list-check text-muted me-1"></i> Trạng thái
                            </label>
                            <select name="status" class="form-select form-select-lg">
                                <option value="">Tất cả</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý
                                </option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Đã thanh toán
                                </option>
                                <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Đang giao
                                </option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn
                                    thành</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-calendar text-muted me-1"></i> Từ ngày
                            </label>
                            <input type="date" name="from" class="form-control form-control-lg"
                                value="{{ request('from') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-calendar-check text-muted me-1"></i> Đến ngày
                            </label>
                            <input type="date" name="to" class="form-control form-control-lg"
                                value="{{ request('to') }}">
                        </div>
                        <div class="col-md-3 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary btn-lg flex-fill">
                                <i class="fa-solid fa-filter me-2"></i> Lọc
                            </button>
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fa-solid fa-rotate-right"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Advanced Filters -->
                    <div class="collapse mt-3" id="advancedFilters">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Số tiền tối thiểu</label>
                                <input type="number" name="min_amount" class="form-control form-control-lg"
                                    placeholder="0" value="{{ request('min_amount') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Số tiền tối đa</label>
                                <input type="number" name="max_amount" class="form-control form-control-lg"
                                    placeholder="10,000,000" value="{{ request('max_amount') }}">
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

        <!-- Orders Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-semibold">
                    <i class="fa-solid fa-list text-primary me-2"></i>Danh sách đơn hàng
                    <span class="badge bg-primary fs-6">{{ $orders->total() }} đơn</span>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 text-center" style="width:80px;">#</th>
                                <th class="px-4 py-3">Mã đơn hàng</th>
                                <th class="px-4 py-3">Khách hàng</th>
                                <th class="px-4 py-3 text-center">Ngày đặt</th>
                                <th class="px-4 py-3 text-end">Tổng tiền</th>
                                <th class="px-4 py-3 text-center">Thanh toán</th>
                                <th class="px-4 py-3 text-center">Trạng thái</th>
                                <th class="px-4 py-3 text-center" style="width:200px;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $index => $order)
                                <tr class="border-bottom">
                                    <td class="text-center px-4">
                                        <span
                                            class="badge bg-light text-dark fs-6">{{ $orders->firstItem() + $index }}</span>
                                    </td>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3"
                                                style="width:45px; height:45px;">
                                                <i class="fa-solid fa-receipt text-primary fs-5"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">#{{ $order->order_number }}</div>
                                                <div class="small text-muted">ID: {{ $order->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4">
                                        <div class="fw-semibold text-dark">
                                            {{ trim(($order->user->first_name ?? '') . ' ' . ($order->user->last_name ?? '')) ?: 'N/A' }}
                                        </div>
                                        <div class="small text-muted">
                                            <i class="fa-solid fa-envelope me-1"></i>{{ $order->user->email ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="text-center px-4">
                                        <div class="fw-semibold text-dark">{{ $order->created_at->format('d/m/Y') }}</div>
                                        <div class="small text-muted">{{ $order->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="text-end px-4">
                                        <div class="fw-bold text-primary fs-6">{{ number_format($order->total_amount) }}đ
                                        </div>
                                        @if ($order->shipping_fee > 0)
                                            <div class="small text-muted">Ship: {{ number_format($order->shipping_fee) }}đ
                                            </div>
                                        @endif
                                    </td>
                                    {{-- <td class="text-center px-4">
                                        @php
                                            $payment = $order->payments->first();
                                            $paymentStatus = $payment ? $payment->status->value : 'pending';
                                        @endphp
                                        @if ($paymentStatus === 'success')
                                            <span class="badge bg-success fs-6 px-3 py-2">
                                                <i class="fa-solid fa-check-circle me-1"></i>Đã thanh toán
                                            </span>
                                        @elseif($paymentStatus === 'pending')
                                            <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                                                <i class="fa-solid fa-clock me-1"></i>Chờ thanh toán
                                            </span>
                                        @else
                                            <span class="badge bg-danger fs-6 px-3 py-2">
                                                <i class="fa-solid fa-times-circle me-1"></i>Thất bại
                                            </span>
                                        @endif
                                    </td> --}}

                                    <td class="text-center px-4">
                                        @php
                                            $payment = $order->payments->first();
                                            $paymentStatus = $payment?->status->value ?? 'pending';
                                            $paymentMethod = $payment?->payment_method->value ?? 'cod';
                                        @endphp

                                        {{-- 🏦 Hình thức thanh toán --}}
                                        <div class="mb-2">
                                            @switch($paymentMethod)
                                                @case('card')
                                                    <span class="text-primary d-inline-flex align-items-center fs-6">
                                                        <i class="fa-solid fa-credit-card me-1"></i>Thẻ tín dụng
                                                    </span>
                                                @break

                                                @case('bank')
                                                    <span class="text-info d-inline-flex align-items-center fs-6">
                                                        <i class="fa-solid fa-university me-1"></i>Ngân hàng
                                                    </span>
                                                @break

                                                @case('wallet')
                                                    <span class="text-success d-inline-flex align-items-center fs-6">
                                                        <i class="fa-solid fa-wallet me-1"></i>Ví điện tử
                                                    </span>
                                                @break

                                                @case('cod')
                                                    <span class="text-secondary d-inline-flex align-items-center fs-6">
                                                        <i class="fa-solid fa-money-bill-wave me-1"></i>COD
                                                    </span>
                                                @break

                                                @default
                                                    <span class="text-muted fs-6">Không rõ</span>
                                            @endswitch
                                        </div>

                                        {{-- 💰 Trạng thái thanh toán --}}
                                        @if ($paymentStatus === 'success')
                                            <span class="badge bg-success fs-6 px-3 py-2 d-inline-flex align-items-center">
                                                <i class="fa-solid fa-check-circle me-1"></i>Đã thanh toán
                                            </span>
                                        @elseif($paymentStatus === 'pending')
                                            <span
                                                class="badge bg-warning text-dark fs-6 px-3 py-2 d-inline-flex align-items-center">
                                                <i class="fa-solid fa-clock me-1"></i>Chờ thanh toán
                                            </span>
                                        @else
                                            <span class="badge bg-danger fs-6 px-3 py-2 d-inline-flex align-items-center">
                                                <i class="fa-solid fa-times-circle me-1"></i>Thất bại
                                            </span>
                                        @endif
                                    </td>


                                    <td class="text-center px-4">
                                        @php
                                            $statusConfig = [
                                                'pending' => [
                                                    'class' => 'warning',
                                                    'icon' => 'clock',
                                                    'text' => 'Chờ xử lý',
                                                ],
                                                'paid' => [
                                                    'class' => 'info',
                                                    'icon' => 'credit-card',
                                                    'text' => 'Đã thanh toán',
                                                ],
                                                'shipped' => [
                                                    'class' => 'primary',
                                                    'icon' => 'truck',
                                                    'text' => 'Đang giao',
                                                ],
                                                'completed' => [
                                                    'class' => 'success',
                                                    'icon' => 'check-circle',
                                                    'text' => 'Hoàn thành',
                                                ],
                                                'cancelled' => [
                                                    'class' => 'danger',
                                                    'icon' => 'ban',
                                                    'text' => 'Đã hủy',
                                                ],
                                            ];
                                            $status = $order->status->value;
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
                                            <a href="{{ route('admin.orders.show', $order->id) }}"
                                                class="btn btn-outline-info btn-sm action-btn" data-bs-toggle="tooltip"
                                                title="Xem chi tiết">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.orders.edit', $order->id) }}"
                                                class="btn btn-outline-warning btn-sm action-btn" data-bs-toggle="tooltip"
                                                title="Chỉnh sửa">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                            <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank"
                                                class="btn btn-outline-success btn-sm action-btn" data-bs-toggle="tooltip"
                                                title="In hóa đơn">
                                                <i class="fa-solid fa-print"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-outline-danger btn-sm action-btn btn-delete"
                                                data-action="{{ route('admin.orders.destroy', $order->id) }}"
                                                data-order="{{ $order->order_number }}" data-bs-toggle="tooltip"
                                                title="Xóa">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fa-solid fa-inbox fs-1 d-block mb-3 opacity-50"></i>
                                                <h5>Không có đơn hàng nào</h5>
                                                <p class="mb-0">Thử thay đổi bộ lọc hoặc kiểm tra lại</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($orders->hasPages())
                    <div class="card-footer bg-white border-top py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                Hiển thị {{ $orders->firstItem() }} - {{ $orders->lastItem() }} trong {{ $orders->total() }}
                                đơn hàng
                            </div>
                            <div>{{ $orders->links('components.pagination') }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endsection

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Tooltips
                const tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltips.map(el => new bootstrap.Tooltip(el));

                // Delete confirmation
                document.querySelectorAll('.btn-delete').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const deleteUrl = this.dataset.action;
                        const orderNumber = this.dataset.order;

                        Swal.fire({
                            title: 'Xác nhận xóa?',
                            html: `Bạn có chắc muốn xóa đơn hàng <strong>#${orderNumber}</strong>?<br><small class="text-muted">Đơn hàng sẽ được chuyển vào thùng rác</small>`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Xóa',
                            cancelButtonText: 'Hủy'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const form = document.createElement('form');
                                form.method = 'POST';
                                form.action = deleteUrl;
                                form.innerHTML = `
                        @csrf
                        @method('DELETE')
                    `;
                                document.body.appendChild(form);
                                form.submit();
                            }
                        });
                    });
                });
            });
        </script>
    @endpush
