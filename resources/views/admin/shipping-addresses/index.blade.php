@extends('layouts.admin')

@section('title', 'Quản lý địa chỉ giao hàng')

@section('content')
    <div class="container-fluid py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">
                    <i class="fas fa-truck-fast me-2 text-primary"></i>Địa chỉ giao hàng
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb small text-muted mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Địa chỉ giao hàng</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('admin.shipping-addresses.statistics') }}" class="btn btn-outline-info me-2">
                    <i class="fas fa-chart-bar me-1"></i>Thống kê
                </a>
                <a href="{{ route('admin.shipping-addresses.export', request()->all()) }}" class="btn btn-success">
                    <i class="fas fa-file-export me-1"></i>Xuất Excel
                </a>
            </div>
        </div>

        {{-- Thống kê --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card info-card bg-gradient-primary text-white shadow-sm border-0">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Tổng địa chỉ</h6>
                            <h3 class="fw-bold mb-0">{{ number_format($stats['total']) }}</h3>
                        </div>
                        <i class="fas fa-map-marked-alt fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card info-card bg-gradient-success text-white shadow-sm border-0">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Hôm nay</h6>
                            <h3 class="fw-bold mb-0">{{ number_format($stats['today']) }}</h3>
                        </div>
                        <i class="fas fa-calendar-day fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card info-card bg-gradient-info text-white shadow-sm border-0">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Tỉnh/Thành phố</h6>
                            <h3 class="fw-bold mb-0">{{ number_format($stats['provinces']) }}</h3>
                        </div>
                        <i class="fas fa-building fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bộ lọc --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tìm kiếm</label>
                        <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control"
                            placeholder="Tên, SĐT, địa chỉ, mã đơn...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Tỉnh/Thành phố</label>
                        <select name="province" class="form-select">
                            <option value="">-- Tất cả --</option>
                            @foreach ($provinces as $province)
                                <option value="{{ $province }}"
                                    {{ request('province') == $province ? 'selected' : '' }}>
                                    {{ $province }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Trạng thái đơn</label>
                        <select name="order_status" class="form-select">
                            <option value="">-- Tất cả --</option>
                            <option value="pending" {{ request('order_status') == 'pending' ? 'selected' : '' }}>Chờ xử lý
                            </option>
                            <option value="processing" {{ request('order_status') == 'processing' ? 'selected' : '' }}>Đang
                                xử lý</option>
                            <option value="shipped" {{ request('order_status') == 'shipped' ? 'selected' : '' }}>Đang giao
                            </option>
                            <option value="delivered" {{ request('order_status') == 'delivered' ? 'selected' : '' }}>Đã
                                giao</option>
                            <option value="cancelled" {{ request('order_status') == 'cancelled' ? 'selected' : '' }}>Đã hủy
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Sắp xếp</label>
                        <select name="sort_by" class="form-select">
                            <option value="latest" {{ request('sort_by') == 'latest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="oldest" {{ request('sort_by') == 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                            <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Tên A-Z</option>
                            <option value="province" {{ request('sort_by') == 'province' ? 'selected' : '' }}>Theo tỉnh
                            </option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-primary w-100">
                            <i class="fa-solid fa-filter"></i>
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.shipping-addresses.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fa-solid fa-rotate-right me-1"></i>Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Bảng danh sách --}}
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table align-middle mb-0 table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="80">Mã đơn</th>
                            <th>Người nhận</th>
                            <th>SĐT</th>
                            <th>Địa chỉ</th>
                            <th>Tỉnh/TP</th>
                            <th>Trạng thái đơn</th>
                            <th>Ngày tạo</th>
                            <th width="120" class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($addresses as $address)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.orders.show', $address->order_id) }}"
                                        class="badge bg-primary text-decoration-none">
                                        #{{ $address->order_id }}
                                    </a>
                                </td>
                                <td>
                                    <strong>{{ $address->receiver_name }}</strong>
                                    @if ($address->order->user)
                                        <br><small class="text-muted">
                                            <a href="{{ route('admin.users.show', $address->order->user->id) }}">
                                                {{ $address->order->user->username }}
                                            </a>
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-phone me-1"></i>{{ $address->phone }}
                                    </span>
                                </td>
                                <td>
                                    <div class="small" style="max-width: 300px;">
                                        <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                        {{ Str::limit($address->address, 50) }}
                                        @if ($address->ward || $address->district)
                                            <br><small class="text-muted">
                                                {{ implode(', ', array_filter([$address->ward, $address->district])) }}
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $address->province }}</span>
                                </td>
                                <td>
                                    @php
                                        $statusClass = match ($address->order->status ?? 'pending') {
                                            'pending' => 'warning',
                                            'processing' => 'info',
                                            'shipped' => 'primary',
                                            'delivered' => 'success',
                                            'cancelled' => 'danger',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusClass }}">
                                        {{ $address->order->status->label() ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ $address->created_at->format('d/m/Y') }}</small>
                                    <br><small class="text-muted">{{ $address->created_at->format('H:i') }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.shipping-addresses.show', $address->id) }}"
                                            class="btn btn-sm btn-outline-info" title="Xem">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.shipping-addresses.edit', $address->id) }}"
                                            class="btn btn-sm btn-outline-warning" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    <p class="mb-0">Không có địa chỉ giao hàng nào</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Hiển thị {{ $addresses->firstItem() ?? 0 }} - {{ $addresses->lastItem() ?? 0 }}
                    trong tổng số {{ $addresses->total() }} địa chỉ
                </div>
                {{ $addresses->links('components.pagination') }}
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            @if (session('success'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000
                });
            @endif
        </script>
    @endpush

    @push('styles')
        <style>
            .info-card {
                transition: transform 0.2s;
                border-radius: 12px;
            }

            .info-card:hover {
                transform: translateY(-5px);
            }

            .bg-gradient-primary {
                background: linear-gradient(135deg, #4f46e5 0%, #6d28d9 100%);
            }

            .bg-gradient-success {
                background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);
            }

            .bg-gradient-info {
                background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
            }

            .card {
                border-radius: 12px;
            }

            tbody tr:hover {
                background-color: #f8fafc;
            }
        </style>
    @endpush
@endsection
