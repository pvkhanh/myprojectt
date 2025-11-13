@extends('layouts.admin')

@section('title', 'Chi tiết người dùng')

@section('content')
    <div class="container-fluid px-4">

        {{-- Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold text-dark mb-2">
                            <i class="fa-solid fa-circle-info text-primary me-2"></i>
                            Chi tiết người dùng
                        </h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Người dùng</a></li>
                                <li class="breadcrumb-item active">{{ $user->username }}</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning btn-lg">
                            <i class="fa-solid fa-pen me-2"></i>Chỉnh sửa
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fa-solid fa-arrow-left me-2"></i>Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">

            {{-- LEFT COLUMN: Avatar + Quick Actions --}}
            <div class="col-lg-4">

                {{-- Avatar Card --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body text-center py-5">
                        <div class="position-relative d-inline-block mb-3">
                            @if ($user->avatar)
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->username }}"
                                    class="rounded-circle border border-4 border-primary shadow-lg"
                                    style="width: 200px; height: 200px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-gradient-primary text-white d-flex align-items-center justify-content-center shadow-lg mx-auto"
                                    style="width: 200px; height: 200px; font-size: 4rem; font-weight: bold;">
                                    {{ $user->initials }}
                                </div>
                            @endif
                        </div>

                        <h4 class="fw-bold mb-1">{{ $user->full_name }}</h4>
                        <p class="text-muted mb-2">{{ '@' . $user->username }}</p>

                        <span
                            class="badge rounded-pill fs-6 px-3 py-2
                        @if ($user->role === 'admin') bg-danger
                        @else bg-info @endif">
                            <i class="fa-solid fa-user-shield me-1"></i>
                            {{ ucfirst($user->role) }}
                        </span>

                        @if ($user->is_active)
                            <span class="badge rounded-pill bg-success fs-6 px-3 py-2 ms-2">
                                <i class="fa-solid fa-circle-check me-1"></i>Hoạt động
                            </span>
                        @else
                            <span class="badge rounded-pill bg-secondary fs-6 px-3 py-2 ms-2">
                                <i class="fa-solid fa-ban me-1"></i>Vô hiệu hóa
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Email Actions --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-envelope text-primary me-2"></i>Hành động Email
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-resend-welcome"
                                data-user-id="{{ $user->id }}" data-user-email="{{ $user->email }}">
                                <i class="fa-solid fa-paper-plane me-2"></i>
                                Gửi Lại Welcome Email
                            </button>

                            @if (!$user->email_verified_at)
                                <button type="button" class="btn btn-outline-success btn-send-verification"
                                    data-user-id="{{ $user->id }}" data-user-email="{{ $user->email }}">
                                    <i class="fa-solid fa-envelope-circle-check me-2"></i>
                                    Gửi Email Xác Thực
                                </button>
                            @else
                                <div class="alert alert-success small mb-0 text-start">
                                    <i class="fa-solid fa-check-circle me-2"></i>
                                    Email đã xác thực vào <b>{{ $user->email_verified_at->format('d/m/Y H:i') }}</b>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Quick Stats --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-chart-simple text-primary me-2"></i>Thống kê
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div>
                                <i class="fa-solid fa-shopping-bag text-primary me-2"></i>
                                <span class="text-muted">Đơn hàng</span>
                            </div>
                            <span class="badge bg-primary fs-6">{{ $user->orders->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div>
                                <i class="fa-solid fa-location-dot text-success me-2"></i>
                                <span class="text-muted">Địa chỉ</span>
                            </div>
                            <span class="badge bg-success fs-6">{{ $user->addresses->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fa-solid fa-star text-warning me-2"></i>
                                <span class="text-muted">Đánh giá</span>
                            </div>
                            <span class="badge bg-warning fs-6">{{ $user->reviews->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: Details --}}
            <div class="col-lg-8">

                {{-- Basic Info --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-id-card text-primary me-2"></i>Thông tin cơ bản
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small">Email</label>
                                <div class="d-flex align-items-center">
                                    <i class="fa-solid fa-envelope text-primary me-2"></i>
                                    <span class="fw-semibold">{{ $user->email }}</span>
                                    @if ($user->email_verified_at)
                                        <i class="fa-solid fa-circle-check text-success ms-2" title="Đã xác thực"></i>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small">Số điện thoại</label>
                                <div class="d-flex align-items-center">
                                    <i class="fa-solid fa-phone text-success me-2"></i>
                                    <span class="fw-semibold">{{ $user->phone ?? '—' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small">Giới tính</label>
                                <div class="d-flex align-items-center">
                                    <i class="fa-solid fa-venus-mars text-info me-2"></i>
                                    <span class="fw-semibold text-capitalize">{{ $user->gender ?? '—' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small">Ngày sinh</label>
                                <div class="d-flex align-items-center">
                                    <i class="fa-solid fa-cake-candles text-warning me-2"></i>
                                    <span
                                        class="fw-semibold">{{ $user->birthday ? $user->birthday->format('d/m/Y') : '—' }}</span>
                                </div>
                            </div>
                            @if ($user->bio)
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-muted small">Giới thiệu</label>
                                    <div class="alert alert-light mb-0">
                                        <i class="fa-solid fa-quote-left text-muted me-2"></i>
                                        {{ $user->bio }}
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small">Ngày tạo</label>
                                <div class="d-flex align-items-center">
                                    <i class="fa-solid fa-calendar-plus text-primary me-2"></i>
                                    <span class="fw-semibold">{{ $user->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small">Cập nhật lần cuối</label>
                                <div class="d-flex align-items-center">
                                    <i class="fa-solid fa-clock text-secondary me-2"></i>
                                    <span class="fw-semibold">{{ $user->updated_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- User Addresses --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-map-location-dot text-primary me-2"></i>
                                Địa chỉ đã lưu
                                <span class="badge bg-primary">{{ $user->addresses->count() }}</span>
                            </h5>
                        </div>
                    </div>
                    <div class="card-body">
                        @forelse($user->addresses as $address)
                            <div
                                class="border rounded p-3 mb-3 {{ $address->is_default ? 'border-primary bg-light' : '' }}">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-bold mb-1">
                                            <i class="fa-solid fa-user text-primary me-1"></i>
                                            {{ $address->receiver_name }}
                                        </h6>
                                        @if ($address->is_default)
                                            <span class="badge bg-primary">
                                                <i class="fa-solid fa-star me-1"></i>Địa chỉ mặc định
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-muted small">
                                    <div class="mb-1">
                                        <i class="fa-solid fa-phone me-2"></i>
                                        <span class="fw-semibold">{{ $address->phone }}</span>
                                    </div>
                                    <div class="mb-1">
                                        <i class="fa-solid fa-location-dot me-2"></i>
                                        {{ $address->address }}
                                    </div>
                                    <div>
                                        <i class="fa-solid fa-map-pin me-2"></i>
                                        {{ implode(', ', array_filter([$address->ward, $address->district, $address->province])) }}
                                    </div>
                                    @if ($address->postal_code)
                                        <div class="mt-1">
                                            <i class="fa-solid fa-mailbox me-2"></i>
                                            Mã bưu điện: {{ $address->postal_code }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">
                                <i class="fa-solid fa-inbox fs-1 d-block mb-3 opacity-50"></i>
                                <p class="mb-0">Chưa có địa chỉ nào được lưu</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Shipping Addresses from Orders --}}
                @php
                    $shippingAddresses = $user
                        ->orders()
                        ->with('shippingAddress')
                        ->whereHas('shippingAddress')
                        ->latest()
                        ->take(5)
                        ->get();
                @endphp

                @if ($shippingAddresses->isNotEmpty())
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-truck-fast text-primary me-2"></i>
                                Địa chỉ giao hàng gần đây
                                <span class="badge bg-secondary">{{ $shippingAddresses->count() }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            @foreach ($shippingAddresses as $order)
                                @php $shipping = $order->shippingAddress; @endphp
                                <div class="border rounded p-3 mb-3 bg-light">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="fw-bold mb-1">
                                                <i class="fa-solid fa-user text-success me-1"></i>
                                                {{ $shipping->receiver_name }}
                                            </h6>
                                            <span class="badge bg-info small">
                                                <i class="fa-solid fa-box me-1"></i>Đơn hàng #{{ $order->id }}
                                            </span>
                                            <span class="badge bg-secondary small ms-1">
                                                {{ $order->created_at->format('d/m/Y') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-muted small">
                                        <div class="mb-1">
                                            <i class="fa-solid fa-phone me-2"></i>
                                            <span class="fw-semibold">{{ $shipping->phone }}</span>
                                        </div>
                                        <div class="mb-1">
                                            <i class="fa-solid fa-location-dot me-2"></i>
                                            {{ $shipping->address }}
                                        </div>
                                        <div>
                                            <i class="fa-solid fa-map-pin me-2"></i>
                                            {{ implode(', ', array_filter([$shipping->ward, $shipping->district, $shipping->province])) }}
                                        </div>
                                        @if ($shipping->postal_code)
                                            <div class="mt-1">
                                                <i class="fa-solid fa-mailbox me-2"></i>
                                                Mã bưu điện: {{ $shipping->postal_code }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>

    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .card {
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }
    </style>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Gửi Welcome Email
            document.querySelectorAll('.btn-resend-welcome').forEach(btn => {
                btn.addEventListener('click', function() {
                    const userId = this.dataset.userId;
                    const userEmail = this.dataset.userEmail;

                    Swal.fire({
                        title: 'Gửi Welcome Email?',
                        html: `Gửi email chào mừng đến <strong>${userEmail}</strong>?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#0d6efd',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="fa-solid fa-paper-plane me-2"></i>Gửi ngay',
                        cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Đang gửi...',
                                allowOutsideClick: false,
                                didOpen: () => Swal.showLoading()
                            });

                            fetch(`/admin/users/${userId}/resend-welcome`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': csrf
                                    }
                                })
                                .then(res => res.json())
                                .then(data => {
                                    Swal.fire({
                                        icon: data.success ? 'success' :
                                            'error',
                                        title: data.message,
                                        timer: 3000
                                    });
                                });
                        }
                    });
                });
            });

            // Gửi Email Xác thực
            document.querySelectorAll('.btn-send-verification').forEach(btn => {
                btn.addEventListener('click', function() {
                    const userId = this.dataset.userId;
                    const userEmail = this.dataset.userEmail;

                    Swal.fire({
                        title: 'Gửi Email Xác Thực?',
                        html: `Gửi email xác thực đến <strong>${userEmail}</strong>?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="fa-solid fa-envelope-circle-check me-2"></i>Gửi ngay',
                        cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Đang gửi...',
                                allowOutsideClick: false,
                                didOpen: () => Swal.showLoading()
                            });

                            fetch(`/admin/users/${userId}/send-verification`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': csrf
                                    }
                                })
                                .then(res => res.json())
                                .then(data => {
                                    Swal.fire({
                                        icon: data.success ? 'success' :
                                            'error',
                                        title: data.message,
                                        timer: 3000
                                    });
                                });
                        }
                    });
                });
            });
        });
    </script>
@endpush
