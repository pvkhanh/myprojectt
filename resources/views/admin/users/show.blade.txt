@extends('layouts.admin')

@section('title', 'Chi tiết người dùng')

@push('styles')
<style>
    .user-avatar {
        font-size: 6rem;
        color: #adb5bd;
    }
    .info-table th {
        width: 35%;
        background: #f8f9fa;
    }
    .card-section {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 0.3rem 1rem rgba(0,0,0,0.08);
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    {{-- Tiêu đề trang --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary mb-0">
            <i class="fa-solid fa-circle-info me-2"></i> Thông tin người dùng
        </h3>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>

    {{-- Thông tin người dùng --}}
    <div class="row g-4">
        {{-- Cột trái: Avatar + Role + Hành động --}}
        <div class="col-lg-4">
            <div class="card card-section text-center">
                <div class="card-body py-5">
                    <i class="fa-solid fa-circle-user user-avatar mb-3"></i>
                    <h5 class="fw-bold mb-1">{{ $user->username }}</h5>
                    <span class="badge rounded-pill
                        @if ($user->role === 'admin') bg-danger
                        @elseif($user->role === 'staff') bg-warning text-dark
                        @else bg-secondary @endif">
                        <i class="fa-solid fa-user-shield me-1"></i>
                        {{ ucfirst($user->role) }}
                    </span>

                    <hr class="my-4">

                    {{-- Email Actions --}}
                    <h6 class="fw-semibold mb-3">
                        <i class="fa-solid fa-envelope text-primary me-2"></i> Hành động Email
                    </h6>
                    <div class="d-grid gap-2">
                        {{-- Gửi lại Welcome Email --}}
                        <button type="button" class="btn btn-outline-primary btn-resend-welcome"
                                data-user-id="{{ $user->id }}"
                                data-user-email="{{ $user->email }}">
                            <i class="fa-solid fa-paper-plane me-2"></i>
                            Gửi Lại Welcome Email
                        </button>

                        {{-- Gửi Email xác thực --}}
                        @if(!$user->email_verified_at)
                            <button type="button" class="btn btn-outline-success btn-send-verification"
                                    data-user-id="{{ $user->id }}"
                                    data-user-email="{{ $user->email }}">
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
        </div>

        {{-- Cột phải: Thông tin chi tiết --}}
        <div class="col-lg-8">
            <div class="card card-section">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fa-solid fa-id-card me-2 text-primary"></i>
                        Thông tin chi tiết
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered align-middle info-table mb-0">
                        <tr>
                            <th>Email</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th>Ngày tạo</th>
                            <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Cập nhật lần cuối</th>
                            <td>{{ $user->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>

                    <div class="mt-4 text-end">
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning">
                            <i class="fa-solid fa-pen-to-square me-1"></i> Chỉnh sửa
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
