@extends('layouts.admin')

@section('title', 'Thông tin khách hàng - ' . $customer->name)

@push('styles')
<style>
.stat-card {
    transition: transform 0.3s;
    border-left: 4px solid;
}
.stat-card:hover {
    transform: translateY(-5px);
}
.order-history-item {
    transition: all 0.3s;
}
.order-history-item:hover {
    background: #f8f9fa;
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
                        <i class="fa-solid fa-user-circle text-primary me-2"></i>
                        Thông tin khách hàng
                    </h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Đơn hàng</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.orders.show', $order->id) }}">#{{ $order->order_number }}</a></li>
                            <li class="breadcrumb-item active">Khách hàng</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-secondary btn-lg">
                        <i class="fa-solid fa-arrow-left me-2"></i> Quay lại đơn hàng
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column - Customer Profile -->
        <div class="col-lg-4">
            <!-- Profile Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center p-4">
                    @if($customer->avatar_url)
                        <img src="{{ $customer->avatar_url }}" 
                             alt="{{ $customer->name }}"
                             class="rounded-circle mb-3"
                             style="width: 120px; height: 120px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center mx-auto mb-3"
                             style="width: 120px; height: 120px;">
                            <i class="fa-solid fa-user text-primary" style="font-size: 48px;"></i>
                        </div>
                    @endif
                    
                    <h4 class="fw-bold mb-1">{{ $customer->first_name }} {{ $customer->last_name }}</h4>
                    <p class="text-muted mb-3">ID: #{{ $customer->id }}</p>
                    
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        @if($customer->is_active)
                            <span class="badge bg-success px-3 py-2">
                                <i class="fa-solid fa-check-circle me-1"></i>Hoạt động
                            </span>
                        @else
                            <span class="badge bg-danger px-3 py-2">
                                <i class="fa-solid fa-ban me-1"></i>Không hoạt động
                            </span>
                        @endif
                        
                        @if($customer->email_verified_at)
                            <span class="badge bg-info px-3 py-2">
                                <i class="fa-solid fa-envelope-circle-check me-1"></i>Đã xác thực email
                            </span>
                        @endif
                    </div>

                    <hr>

                    <div class="text-start">
                        <div class="mb-3">
                            <label class="text-muted small mb-1">Email</label>
                            <div class="fw-semibold">
                                <i class="fa-solid fa-envelope text-primary me-2"></i>
                                {{ $customer->email }}
                            </div>
                        </div>
                        
                        @if($customer->phone)
                        <div class="mb-3">
                            <label class="text-muted small mb-1">Điện thoại</label>
                            <div class="fw-semibold">
                                <i class="fa-solid fa-phone text-success me-2"></i>
                                {{ $customer->phone }}
                            </div>
                        </div>
                        @endif

                        @if($customer->birthday)
                        <div class="mb-3">
                            <label class="text-muted small mb-1">Ngày sinh</label>
                            <div class="fw-semibold">
                                <i class="fa-solid fa-cake-candles text-warning me-2"></i>
                                {{ $customer->birthday->format('d/m/Y') }}
                            </div>
                        </div>
                        @endif

                        @if($customer->gender)
                        <div class="mb-3">
                            <label class="text-muted small mb-1">Giới tính</label>
                            <div class="fw-semibold">
                                <i class="fa-solid fa-venus-mars text-info me-2"></i>
                                {{ ucfirst($customer->gender) }}
                            </div>
                        </div>
                        @endif

                        <div class="mb-3">
                            <label class="text-muted small mb-1">Thành viên từ</label>
                            <div class="fw-semibold">
                                <i class="fa-solid fa-calendar text-secondary me-2"></i>
                                {{ $customer->created_at->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-grid gap-2">
                        <a href="mailto:{{ $customer->email }}" class="btn btn-outline-primary">
                            <i class="fa-solid fa-envelope me-2"></i>Gửi email
                        </a>
                        @if($customer->phone)
                        <a href="tel:{{ $customer->phone }}" class="btn btn-outline-success">
                            <i class="fa-solid fa-phone me-2"></i>Gọi điện
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Bio Card -->
            @if($customer->bio)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fa-solid fa-info-circle text-primary me-2"></i>Giới thiệu
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $customer->bio }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Statistics & Orders -->
        <div class="col-lg-8">
            <!-- Statistics Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm stat-card" style="border-color: #667eea !important;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small mb-1">Tổng đơn</div>
                                    <div class="fs-4 fw-bold text-primary">{{ number_format($customerStats['total_orders']) }}</div>
                                </div>
                                <div class="fs-2 text-primary opacity-50">
                                    <i class="fa-solid fa-shopping-cart"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm stat-card" style="border-color: #28a745 !important;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small mb-1">Hoàn thành</div>
                                    <div class="fs-4 fw-bold text-success">{{ number_format($customerStats['completed_orders']) }}</div>
                                </div>
                                <div class="fs-2 text-success opacity-50">
                                    <i class="fa-solid fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm stat-card" style="border-color: #dc3545 !important;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small mb-1">Đã hủy</div>
                                    <div class="fs-4 fw-bold text-danger">{{ number_format($customerStats['cancelled_orders']) }}</div>
                                </div>
                                <div class="fs-2 text-danger opacity-50">
                                    <i class="fa-solid fa-ban"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm stat-card" style="border-color: #ffc107 !important;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small mb-1">Tổng chi</div>
                                    <div class="fs-6 fw-bold text-warning">{{ number_format($customerStats['total_spent']) }}₫</div>
                                </div>
                                <div class="fs-2 text-warning opacity-50">
                                    <i class="fa-solid fa-sack-dollar"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Stats -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Giá trị đơn trung bình</label>
                            <div class="fs-5 fw-bold text-primary">
                                {{ number_format($customerStats['average_order_value']) }}₫
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Đơn hàng đầu tiên</label>
                            <div class="fw-semibold">
                                @if($customerStats['first_order'])
                                    {{ $customerStats['first_order']->created_at->format('d/m/Y') }}
                                    <span class="text-muted small">({{ $customerStats['first_order']->created_at->diffForHumans() }})</span>
                                @else
                                    <span class="text-muted">Chưa có</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Saved Addresses -->
            @if($customer->addresses && $customer->addresses->count() > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fa-solid fa-location-dot text-primary me-2"></i>
                        Địa chỉ đã lưu ({{ $customer->addresses->count() }})
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($customer->addresses as $address)
                    <div class="p-3 mb-2 border rounded {{ $address->is_default ? 'border-primary bg-primary bg-opacity-10' : '' }}">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="fw-semibold">{{ $address->receiver_name }}</div>
                            @if($address->is_default)
                            <span class="badge bg-primary">Mặc định</span>
                            @endif
                        </div>
                        <div class="small text-muted">
                            <i class="fa-solid fa-phone me-1"></i>{{ $address->phone }}
                        </div>
                        <div class="small">
                            <i class="fa-solid fa-map-marker-alt me-1"></i>
                            {{ $address->address }}, {{ $address->ward }}, {{ $address->district }}, {{ $address->province }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Order History -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fa-solid fa-history text-primary me-2"></i>
                        Lịch sử đơn hàng (10 đơn gần nhất)
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3">Mã đơn</th>
                                    <th class="px-4 py-3">Ngày đặt</th>
                                    <th class="px-4 py-3 text-end">Tổng tiền</th>
                                    <th class="px-4 py-3 text-center">Trạng thái</th>
                                    <th class="px-4 py-3 text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customer->orders->take(10) as $customerOrder)
                                <tr class="order-history-item">
                                    <td class="px-4 py-3">
                                        <div class="fw-semibold">#{{ $customerOrder->order_number }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div>{{ $customerOrder->created_at->format('d/m/Y') }}</div>
                                        <div class="small text-muted">{{ $customerOrder->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <div class="fw-bold text-primary">{{ number_format($customerOrder->total_amount) }}₫</div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="badge bg-{{ $customerOrder->status->color() }} px-3 py-2">
                                            {{ $customerOrder->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('admin.orders.show', $customerOrder->id) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="text-muted">Chưa có đơn hàng nào</div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($customer->orders->count() > 10)
                <div class="card-footer bg-white text-center py-3">
                    <a href="{{ route('admin.orders.index', ['search' => $customer->email]) }}" 
                       class="btn btn-outline-primary">
                        <i class="fa-solid fa-list me-2"></i>Xem tất cả đơn hàng
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection