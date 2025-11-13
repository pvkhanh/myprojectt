@extends('layouts.admin')

@section('title', 'Thêm địa chỉ mới')

@section('content')
    <div class="container-fluid px-4">

        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold text-dark mb-2">
                            <i class="fa-solid fa-plus-circle text-primary me-2"></i>
                            Thêm địa chỉ mới
                        </h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.user-addresses.index') }}">Địa chỉ</a>
                                </li>
                                <li class="breadcrumb-item active">Thêm mới</li>
                            </ol>
                        </nav>
                    </div>
                    <a href="{{ route('admin.user-addresses.index') }}" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-arrow-left me-2"></i>Quay lại
                    </a>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.user-addresses.store') }}" method="POST">
            @csrf

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-info-circle text-primary me-2"></i>Thông tin địa chỉ
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Người nhận <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="receiver_name"
                                        class="form-control @error('receiver_name') is-invalid @enderror"
                                        value="{{ old('receiver_name') }}" required>
                                    @error('receiver_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Số điện thoại <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="phone"
                                        class="form-control @error('phone') is-invalid @enderror"
                                        value="{{ old('phone') }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Địa chỉ chi tiết <span
                                            class="text-danger">*</span></label>
                                    <textarea name="address" rows="3" class="form-control @error('address') is-invalid @enderror" required>{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Tỉnh/Thành phố <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="province"
                                        class="form-control @error('province') is-invalid @enderror"
                                        value="{{ old('province') }}" required>
                                    @error('province')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Quận/Huyện</label>
                                    <input type="text" name="district"
                                        class="form-control @error('district') is-invalid @enderror"
                                        value="{{ old('district') }}">
                                    @error('district')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Phường/Xã</label>
                                    <input type="text" name="ward"
                                        class="form-control @error('ward') is-invalid @enderror"
                                        value="{{ old('ward') }}">
                                    @error('ward')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Mã bưu điện</label>
                                    <input type="text" name="postal_code"
                                        class="form-control @error('postal_code') is-invalid @enderror"
                                        value="{{ old('postal_code') }}">
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
                                <i class="fa-solid fa-user text-primary me-2"></i>Người dùng
                            </h5>
                        </div>
                        <div class="card-body">
                            <label class="form-label fw-semibold">Chọn người dùng <span class="text-danger">*</span></label>
                            <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                <option value="">-- Chọn người dùng --</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ old('user_id', $userId) == $user->id ? 'selected' : '' }}>
                                        {{ $user->username }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-cog text-primary me-2"></i>Cài đặt
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_default" id="is_default"
                                    value="1" {{ old('is_default') ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_default">
                                    <i class="fa-solid fa-star text-warning me-1"></i>
                                    Đặt làm địa chỉ mặc định
                                </label>
                            </div>
                            <small class="text-muted">
                                Địa chỉ mặc định sẽ được tự động chọn khi đặt hàng
                            </small>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fa-solid fa-check me-2"></i>Lưu địa chỉ
                        </button>
                        <a href="{{ route('admin.user-addresses.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fa-solid fa-times me-2"></i>Hủy bỏ
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('styles')
        <style>
            .card {
                border-radius: 12px;
            }

            .form-control:focus,
            .form-select:focus {
                border-color: #4f46e5;
                box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
            }
        </style>
    @endpush
@endsection
