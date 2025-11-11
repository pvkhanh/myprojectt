{{-- segments.blade.php --}}
@extends('layouts.admin')

@section('title', 'Phân Nhóm Người Nhận')

@push('styles')
    <style>
        .segment-card {
            transition: all 0.3s;
            cursor: pointer;
        }

        .segment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .icon-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
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
                            <i class="fa-solid fa-users-between-lines text-primary me-2"></i>
                            Phân Nhóm Người Nhận
                        </h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.mails.dashboard') }}">Mail System</a>
                                </li>
                                <li class="breadcrumb-item active">Segments</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="{{ route('admin.mails.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Segments Grid -->
        <div class="row g-4">
            @foreach ($segments as $key => $segment)
                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm h-100 segment-card"
                        onclick="window.location='{{ route('admin.mails.create', ['segment' => $key]) }}'">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div
                                    class="icon-circle bg-{{ $segment['color'] }} bg-opacity-10 text-{{ $segment['color'] }} me-3">
                                    <i class="fa-solid fa-{{ $segment['icon'] }}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="fw-bold mb-1">{{ $segment['name'] }}</h5>
                                    <div class="text-muted small">Segment: {{ $key }}</div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small">Số lượng người dùng</div>
                                    <h3 class="fw-bold text-{{ $segment['color'] }} mb-0">
                                        {{ number_format($segment['count']) }}
                                    </h3>
                                </div>
                                <a href="{{ route('admin.mails.create', ['segment' => $key]) }}"
                                    class="btn btn-{{ $segment['color'] }}">
                                    <i class="fa-solid fa-envelope me-2"></i> Gửi Mail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Info Card -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">
                    <i class="fa-solid fa-info-circle text-info me-2"></i>
                    Về Phân Nhóm (Segments)
                </h5>
                <p class="mb-2">Phân nhóm giúp bạn gửi mail đến đúng đối tượng mục tiêu:</p>
                <ul class="mb-0">
                    <li><strong>Tất cả người dùng:</strong> Gửi đến toàn bộ users trong hệ thống</li>
                    <li><strong>Người dùng đã xác thực:</strong> Chỉ gửi đến users đã verify email</li>
                    <li><strong>Người dùng đang hoạt động:</strong> Users có trạng thái active</li>
                    <li><strong>Người dùng mới:</strong> Users đăng ký trong 30 ngày gần đây</li>
                    <li><strong>Khách hàng đã mua hàng:</strong> Users đã có đơn hàng</li>
                </ul>
            </div>
        </div>
    </div>
@endsection
