@extends('layouts.admin')

@section('title', 'Chi tiết địa chỉ giao hàng')

@section('content')
    <div class="container-fluid px-4">

        {{-- Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold text-dark mb-2">
                            <i class="fa-solid fa-truck-fast text-primary me-2"></i>
                            Chi tiết địa chỉ giao hàng
                        </h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.shipping-addresses.index') }}">Địa chỉ
                                        giao hàng</a></li>
                                <li class="breadcrumb-item active">Chi tiết</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.shipping-addresses.edit', $address->id) }}" class="btn btn-warning btn-lg">
                            <i class="fa-solid fa-pen me-2"></i>Chỉnh sửa
                        </a>
                        <a href="{{ route('admin.shipping-addresses.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fa-solid fa-arrow-left me-2"></i>Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">

            {{-- LEFT COLUMN --}}
            <div class="col-lg-4">

                {{-- Order Info Card --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-box text-primary me-2"></i>Thông tin đơn hàng
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <h3 class="fw-bold text-primary mb-1">Đơn hàng #{{ $address->order->id }}</h3>
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
                            <span class="badge bg-{{ $statusClass }} fs-6 px-3 py-2">
                                {{ $address->order->status->label() }}
                            </span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div>
                                <i class="fa-solid fa-user text-primary me-2"></i>
                                <span class="text-muted">Khách hàng</span>
                            </div>
                            @if ($address->order->user)
                                <a href="{{ route('admin.users.show', $address->order->user->id) }}"
                                    class="fw-semibold text-decoration-none">
                                    {{ $address->order->user->username }}
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div>
                                <i class="fa-solid fa-money-bill text-success me-2"></i>
                                <span class="text-muted">Tổng tiền</span>
                            </div>
                            <span class="fw-bold text-success">
                                {{ number_format($address->order->total_amount, 0, ',', '.') }}đ
                            </span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div>
                                <i class="fa-solid fa-credit-card text-info me-2"></i>
                                <span class="text-muted">Thanh toán</span>
                            </div>
                            <span class="fw-semibold">{{ $address->order->payment_method ?? 'COD' }}</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fa-solid fa-calendar text-secondary me-2"></i>
                                <span class="text-muted">Ngày đặt</span>
                            </div>
                            <span class="fw-semibold">{{ $address->order->created_at->format('d/m/Y H:i') }}</span>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <a href="{{ route('admin.orders.show', $address->order->id) }}" class="btn btn-primary">
                                <i class="fa-solid fa-eye me-2"></i>Xem đơn hàng
                            </a>
                        </div>
                    </div>
                </div>

                {{-- User Info --}}
                @if ($address->order->user)
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-4">
                            @if ($address->order->user->avatar)
                                <img src="{{ $address->order->user->avatar_url }}"
                                    alt="{{ $address->order->user->username }}"
                                    class="rounded-circle border border-3 border-primary shadow mb-3"
                                    style="width: 100px; height: 100px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-gradient-primary text-white d-flex align-items-center justify-content-center shadow mx-auto mb-3"
                                    style="width: 100px; height: 100px; font-size: 2rem; font-weight: bold;">
                                    {{ $address->order->user->initials }}
                                </div>
                            @endif

                            <h5 class="fw-bold mb-1">{{ $address->order->user->full_name }}</h5>
                            <p class="text-muted mb-2 small">{{ $address->order->user->email }}</p>

                            <a href="{{ route('admin.users.show', $address->order->user->id) }}"
                                class="btn btn-outline-primary btn-sm">
                                <i class="fa-solid fa-user me-1"></i>Xem hồ sơ
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            {{-- RIGHT COLUMN --}}
            <div class="col-lg-8">

                {{-- Shipping Address Details --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-location-dot text-danger me-2"></i>Địa chỉ giao hàng
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small">Người nhận</label>
                                <div class="d-flex align-items-center">
                                    <i class="fa-solid fa-user text-primary me-2 fs-5"></i>
                                    <span class="fw-semibold fs-5">{{ $address->receiver_name }}</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small">Số điện thoại</label>
                                <div class="d-flex align-items-center">
                                    <i class="fa-solid fa-phone text-success me-2 fs-5"></i>
                                    <span class="fw-semibold fs-5">{{ $address->phone }}</span>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold text-muted small">Địa chỉ chi tiết</label>
                                <div class="alert alert-light border mb-0">
                                    <i class="fa-solid fa-map-marker-alt text-danger me-2"></i>
                                    <span class="fw-semibold fs-5">{{ $address->address }}</span>
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

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small">Ngày tạo</label>
                                <div class="d-flex align-items-center">
                                    <i class="fa-solid fa-calendar-plus text-primary me-2"></i>
                                    <span class="fw-semibold">{{ $address->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Order Items --}}
                @if ($address->order->items->isNotEmpty())
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-shopping-cart text-primary me-2"></i>
                                Sản phẩm trong đơn
                                <span class="badge bg-secondary">{{ $address->order->items->count() }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Sản phẩm</th>
                                            <th class="text-center">Số lượng</th>
                                            <th class="text-end">Đơn giá</th>
                                            <th class="text-end">Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($address->order->items as $item)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if ($item->product && $item->product->images->first())
                                                            <img src="{{ asset('storage/' . $item->product->images->first()->path) }}"
                                                                class="rounded me-2"
                                                                style="width: 50px; height: 50px; object-fit: cover;">
                                                        @endif
                                                        <div>
                                                            <strong>{{ $item->product_name }}</strong>
                                                            @if ($item->variant_name)
                                                                <br><small
                                                                    class="text-muted">{{ $item->variant_name }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-light text-dark">x{{ $item->quantity }}</span>
                                                </td>
                                                <td class="text-end">{{ number_format($item->price, 0, ',', '.') }}đ</td>
                                                <td class="text-end fw-bold">
                                                    {{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-light">
                                            <td colspan="3" class="text-end fw-bold">Tổng cộng:</td>
                                            <td class="text-end fw-bold text-success fs-5">
                                                {{ number_format($address->order->total_amount, 0, ',', '.') }}đ
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Similar Addresses --}}
                @if ($similarAddresses->isNotEmpty())
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-layer-group text-warning me-2"></i>
                                Đơn hàng khác với địa chỉ tương tự
                                <span class="badge bg-warning">{{ $similarAddresses->count() }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            @foreach ($similarAddresses as $similar)
                                <div class="border rounded p-3 mb-3 bg-light">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="fw-bold mb-1">
                                                <i class="fa-solid fa-box text-primary me-1"></i>
                                                Đơn hàng #{{ $similar->order_id }}
                                            </h6>
                                            <span class="badge bg-secondary small">
                                                {{ $similar->created_at->format('d/m/Y H:i') }}
                                            </span>
                                        </div>
                                        <a href="{{ route('admin.shipping-addresses.show', $similar->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-eye me-1"></i>Xem
                                        </a>
                                    </div>
                                    <div class="text-muted small">
                                        <div class="mb-1">
                                            <i class="fa-solid fa-user me-2"></i>
                                            <span>{{ $similar->receiver_name }}</span>
                                        </div>
                                        <div class="mb-1">
                                            <i class="fa-solid fa-phone me-2"></i>
                                            <span>{{ $similar->phone }}</span>
                                        </div>
                                        <div>
                                            <i class="fa-solid fa-location-dot me-2"></i>
                                            {{ $similar->address }}, {{ $similar->ward }}, {{ $similar->district }},
                                            {{ $similar->province }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>

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
