@extends('layouts.admin')

@section('title', 'Chi tiết địa chỉ')

@section('content')
    <div class="container-fluid px-4">

        {{-- Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold text-dark mb-2">
                            <i class="fa-solid fa-map-location-dot text-primary me-2"></i>
                            Chi tiết địa chỉ
                        </h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.user-addresses.index') }}">Địa chỉ</a>
                                </li>
                                <li class="breadcrumb-item active">Chi tiết</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        @if (!$address->is_default)
                            <button type="button" class="btn btn-success btn-lg" id="setDefaultBtn">
                                <i class="fa-solid fa-star me-2"></i>Đặt mặc định
                            </button>
                        @endif
                        <a href="{{ route('admin.user-addresses.edit', $address->id) }}" class="btn btn-warning btn-lg">
                            <i class="fa-solid fa-pen me-2"></i>Chỉnh sửa
                        </a>
                        <button type="button" class="btn btn-danger btn-lg" onclick="confirmDelete()">
                            <i class="fa-solid fa-trash me-2"></i>Xóa
                        </button>
                        <a href="{{ route('admin.user-addresses.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fa-solid fa-arrow-left me-2"></i>Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">

            {{-- LEFT COLUMN --}}
            <div class="col-lg-4">

                {{-- User Info Card --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body text-center py-5">
                        @if ($address->user->avatar)
                            <img src="{{ $address->user->avatar_url }}" alt="{{ $address->user->username }}"
                                class="rounded-circle border border-4 border-primary shadow-lg mb-3"
                                style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-gradient-primary text-white d-flex align-items-center justify-content-center shadow-lg mx-auto mb-3"
                                style="width: 150px; height: 150px; font-size: 3rem; font-weight: bold;">
                                {{ $address->user->initials }}
                            </div>
                        @endif

                        <h4 class="fw-bold mb-1">{{ $address->user->full_name }}</h4>
                        <p class="text-muted mb-2">{{ '@' . $address->user->username }}</p>

                        <a href="{{ route('admin.users.show', $address->user->id) }}"
                            class="btn btn-outline-primary btn-sm">
                            <i class="fa-solid fa-user me-1"></i>Xem hồ sơ
                        </a>
                    </div>
                </div>

                {{-- Status Card --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-info-circle text-primary me-2"></i>Trạng thái
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div>
                                <i class="fa-solid fa-star text-warning me-2"></i>
                                <span class="text-muted">Địa chỉ mặc định</span>
                            </div>
                            @if ($address->is_default)
                                <span class="badge bg-success">Có</span>
                            @else
                                <span class="badge bg-secondary">Không</span>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div>
                                <i class="fa-solid fa-calendar-plus text-primary me-2"></i>
                                <span class="text-muted">Ngày tạo</span>
                            </div>
                            <span class="fw-semibold">{{ $address->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fa-solid fa-clock text-secondary me-2"></i>
                                <span class="text-muted">Cập nhật</span>
                            </div>
                            <span class="fw-semibold">{{ $address->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Stats Card --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-chart-simple text-primary me-2"></i>Thống kê người dùng
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div>
                                <i class="fa-solid fa-shopping-bag text-primary me-2"></i>
                                <span class="text-muted">Đơn hàng</span>
                            </div>
                            <span class="badge bg-primary fs-6">{{ $address->user->orders->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div>
                                <i class="fa-solid fa-location-dot text-success me-2"></i>
                                <span class="text-muted">Địa chỉ đã lưu</span>
                            </div>
                            <span class="badge bg-success fs-6">{{ $address->user->addresses->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fa-solid fa-star text-warning me-2"></i>
                                <span class="text-muted">Đánh giá</span>
                            </div>
                            <span class="badge bg-warning fs-6">{{ $address->user->reviews->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN --}}
            <div class="col-lg-8">

                {{-- Address Details --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-map-marked-alt text-primary me-2"></i>Thông tin địa chỉ
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small">Người nhận</label>
                                <div class="d-flex align-items-center">
                                    <i class="fa-solid fa-user text-primary me-2"></i>
                                    <span class="fw-semibold fs-5">{{ $address->receiver_name }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small">Số điện thoại</label>
                                <div class="d-flex align-items-center">
                                    <i class="fa-solid fa-phone text-success me-2"></i>
                                    <span class="fw-semibold fs-5">{{ $address->phone }}</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold text-muted small">Địa chỉ chi tiết</label>
                                <div class="alert alert-light border mb-0">
                                    <i class="fa-solid fa-location-dot text-danger me-2"></i>
                                    <span class="fw-semibold">{{ $address->address }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-muted small">Phường/Xã</label>
                                <div class="d-flex align-items-center">
                                    <i class="fa-solid fa-map-pin text-info me-2"></i>
                                    <span class="fw-semibold">{{ $address->ward ?? '—' }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-muted small">Quận/Huyện</label>
                                <div class="d-flex align-items-center">
                                    <i class="fa-solid fa-map-pin text-info me-2"></i>
                                    <span class="fw-semibold">{{ $address->district ?? '—' }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-muted small">Tỉnh/Thành phố</label>
                                <div class="d-flex align-items-center">
                                    <i class="fa-solid fa-building text-primary me-2"></i>
                                    <span class="fw-semibold">{{ $address->province }}</span>
                                </div>
                            </div>
                            @if ($address->postal_code)
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">Mã bưu điện</label>
                                    <div class="d-flex align-items-center">
                                        <i class="fa-solid fa-mailbox text-secondary me-2"></i>
                                        <span class="fw-semibold">{{ $address->postal_code }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Related Orders --}}
                @if ($relatedOrders->isNotEmpty())
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-truck-fast text-primary me-2"></i>
                                Đơn hàng liên quan
                                <span class="badge bg-secondary">{{ $relatedOrders->count() }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            @foreach ($relatedOrders as $order)
                                @php $shipping = $order->shippingAddress; @endphp
                                <div class="border rounded p-3 mb-3 bg-light">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="fw-bold mb-1">
                                                <i class="fa-solid fa-box text-primary me-1"></i>
                                                Đơn hàng #{{ $order->id }}
                                            </h6>
                                            <span class="badge bg-info small me-1">
                                                {{ $order->status->label() }}
                                            </span>
                                            <span class="badge bg-secondary small">
                                                {{ $order->created_at->format('d/m/Y') }}
                                            </span>
                                        </div>
                                        <a href="{{ route('admin.orders.show', $order->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-eye me-1"></i>Xem
                                        </a>
                                    </div>
                                    @if ($shipping)
                                        <div class="text-muted small">
                                            <div class="mb-1">
                                                <i class="fa-solid fa-user me-2"></i>
                                                <span class="fw-semibold">{{ $shipping->receiver_name }}</span>
                                            </div>
                                            <div class="mb-1">
                                                <i class="fa-solid fa-phone me-2"></i>
                                                <span>{{ $shipping->phone }}</span>
                                            </div>
                                            <div>
                                                <i class="fa-solid fa-location-dot me-2"></i>
                                                {{ $shipping->address }}, {{ $shipping->ward }},
                                                {{ $shipping->district }}, {{ $shipping->province }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>

    <form action="{{ route('admin.user-addresses.destroy', $address->id) }}" method="POST" class="d-none"
        id="deleteForm">
        @csrf @method('DELETE')
    </form>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const csrf = '{{ csrf_token() }}';

                // Set default
                const setDefaultBtn = document.getElementById('setDefaultBtn');
                setDefaultBtn?.addEventListener('click', function() {
                    Swal.fire({
                        title: 'Đặt làm mặc định?',
                        text: 'Địa chỉ này sẽ được đặt làm mặc định cho người dùng',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#16a34a',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="fa-solid fa-star me-2"></i>Đặt làm mặc định',
                        cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`/admin/user-addresses/{{ $address->id }}/set-default`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': csrf
                                    }
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire('Thành công!', data.message, 'success')
                                            .then(() => location.reload());
                                    }
                                });
                        }
                    });
                });
            });

            function confirmDelete() {
                Swal.fire({
                    title: 'Xác nhận xóa?',
                    text: 'Địa chỉ này sẽ được đưa vào thùng rác!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-trash me-2"></i>Xóa',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('deleteForm').submit();
                    }
                });
            }

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
            .bg-gradient-primary {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }

            .card {
                transition: transform 0.2s ease;
                border-radius: 12px;
            }
        </style>
    @endpush
@endsection
