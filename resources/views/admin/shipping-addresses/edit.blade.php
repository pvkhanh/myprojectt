@extends('layouts.admin')

@section('title', 'Chỉnh sửa địa chỉ giao hàng')

@section('content')
<div class="container-fluid px-4">
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-dark mb-2">
                        <i class="fa-solid fa-edit text-warning me-2"></i>
                        Chỉnh sửa địa chỉ giao hàng
                    </h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.shipping-addresses.index') }}">Địa chỉ giao hàng</a></li>
                            <li class="breadcrumb-item active">Chỉnh sửa</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('admin.shipping-addresses.show', $address->id) }}" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-2"></i>Quay lại
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.shipping-addresses.update', $address->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-location-dot text-primary me-2"></i>Thông tin địa chỉ
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info border-0 mb-4">
                            <i class="fa-solid fa-info-circle me-2"></i>
                            <strong>Lưu ý:</strong> Thay đổi địa chỉ sẽ ảnh hưởng đến thông tin giao hàng của đơn hàng #{{ $address->order_id }}
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Người nhận <span class="text-danger">*</span></label>
                                <input type="text" name="receiver_name" 
                                       class="form-control @error('receiver_name') is-invalid @enderror" 
                                       value="{{ old('receiver_name', $address->receiver_name) }}" required>
                                @error('receiver_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" name="phone" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       value="{{ old('phone', $address->phone) }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Địa chỉ chi tiết <span class="text-danger">*</span></label>
                                <textarea name="address" rows="3" 
                                          class="form-control @error('address') is-invalid @enderror" 
                                          required>{{ old('address', $address->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                                <input type="text" name="province" 
                                       class="form-control @error('province') is-invalid @enderror" 
                                       value="{{ old('province', $address->province) }}" required>
                                @error('province')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Quận/Huyện</label>
                                <input type="text" name="district" 
                                       class="form-control @error('district') is-invalid @enderror" 
                                       value="{{ old('district', $address->district) }}">
                                @error('district')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Phường/Xã</label>
                                <input type="text" name="ward" 
                                       class="form-control @error('ward') is-invalid @enderror" 
                                       value="{{ old('ward', $address->ward) }}">
                                @error('ward')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Mã bưu điện</label>
                                <input type="text" name="postal_code" 
                                       class="form-control @error('postal_code') is-invalid @enderror" 
                                       value="{{ old('postal_code', $address->postal_code) }}">
                                @error('postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-box text-primary me-2"></i>Thông tin đơn hàng
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <h4 class="fw-bold text-primary">#{{ $address->order->id }}</h4>
                            @php
                                $statusClass = match($address->order->status ?? 'pending') {
                                    'pending' => 'warning',
                                    'processing' => 'info',
                                    'shipped' => 'primary',
                                    'delivered' => 'success',
                                    'cancelled' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $statusClass }} fs-6 px-3 py-2">
                                {{ $address->order->status->label() }}
                            </span>
                        </div>

                        <div class="mb-3 pb-3 border-bottom">
                            <small class="text-muted d-block mb-1">Khách hàng</small>
                            @if($address->order->user)
                                <strong>{{ $address->order->user->username }}</strong>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </div>

                        <div class="mb-3 pb-3 border-bottom">
                            <small class="text-muted d-block mb-1">Tổng tiền</small>
                            <strong class="text-success fs-5">
                                {{ number_format($address->order->total_amount, 0, ',', '.') }}đ
                            </strong>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Ngày đặt</small>
                            <strong>{{ $address->order->created_at->format('d/m/Y H:i') }}</strong>
                        </div>

                        <a href="{{ route('admin.orders.show', $address->order->id) }}" 
                           class="btn btn-outline-primary w-100">
                            <i class="fa-solid fa-eye me-2"></i>Xem đơn hàng
                        </a>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-clock text-info me-2"></i>Lịch sử
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Ngày tạo</small>
                            <strong>{{ $address->created_at->format('d/m/Y H:i') }}</strong>
                        </div>
                        <div>
                            <small class="text-muted d-block mb-1">Cập nhật lần cuối</small>
                            <strong>{{ $address->updated_at->format('d/m/Y H:i') }}</strong>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-warning btn-lg">
                        <i class="fa-solid fa-save me-2"></i>Cập nhật
                    </button>
                    <a href="{{ route('admin.shipping-addresses.show', $address->id) }}" 
                       class="btn btn-outline-secondary btn-lg">
                        <i class="fa-solid fa-times me-2"></i>Hủy bỏ
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
    .card { border-radius: 12px; }
    .form-control:focus, .form-select:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }
</style>
@endpush
@endsection