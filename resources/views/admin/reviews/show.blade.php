@extends('layouts.admin')

@section('title', 'Chi tiết đánh giá')

@section('content')
    <div class="container-fluid py-4">

        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.reviews.index') }}">Đánh giá</a></li>
                <li class="breadcrumb-item active">Chi tiết #{{ $review->id }}</li>
            </ol>
        </nav>

        <div class="row g-4">
            {{-- Cột trái: Thông tin đánh giá --}}
            <div class="col-lg-8">
                {{-- Card chính --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-star text-warning me-2"></i>
                                Chi tiết đánh giá
                            </h5>
                            <div>
                                @php
                                    $statusClass = match ($review->status->value) {
                                        'approved' => 'success',
                                        'pending' => 'warning',
                                        'rejected' => 'danger',
                                        default => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusClass }} fs-6">
                                    {{ ucfirst($review->status->value) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        {{-- Thông tin user --}}
                        <div class="mb-4 pb-4 border-bottom">
                            <h6 class="text-muted mb-3 fw-semibold">NGƯỜI ĐÁNH GIÁ</h6>
                            <div class="d-flex align-items-center">
                                <img src="{{ $review->user->avatar_url ?? asset('images/default-avatar.png') }}"
                                    class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                <div>
                                    <h5 class="mb-1 fw-bold">{{ $review->user->username }}</h5>
                                    <p class="text-muted mb-1">
                                        <i class="fas fa-envelope me-2"></i>{{ $review->user->email }}
                                    </p>
                                    @if ($review->user->phone)
                                        <p class="text-muted mb-0">
                                            <i class="fas fa-phone me-2"></i>{{ $review->user->phone }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Thông tin sản phẩm --}}
                        <div class="mb-4 pb-4 border-bottom">
                            <h6 class="text-muted mb-3 fw-semibold">SẢN PHẨM</h6>
                            <a href="{{ route('admin.products.show', $review->product) }}" class="text-decoration-none">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <img src="{{ $review->product->main_image_url }}" class="rounded me-3"
                                        style="width: 80px; height: 80px; object-fit: cover;">
                                    <div>
                                        <h5 class="mb-1 fw-bold text-dark">{{ $review->product->name }}</h5>
                                        <p class="text-muted mb-1">
                                            <i class="fas fa-tag me-2"></i>
                                            {{ number_format($review->product->price, 0, ',', '.') }}đ
                                        </p>
                                        <p class="text-muted mb-0">
                                            <i class="fas fa-box me-2"></i>SKU: {{ $review->product->sku ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </div>

                        {{-- Đánh giá --}}
                        <div class="mb-4 pb-4 border-bottom">
                            <h6 class="text-muted mb-3 fw-semibold">ĐÁNH GIÁ</h6>
                            <div class="text-center mb-3">
                                <div class="rating-stars-large mb-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"
                                            style="font-size: 2rem;"></i>
                                    @endfor
                                </div>
                                <h3 class="fw-bold text-warning mb-0">{{ $review->rating }}/5</h3>
                            </div>
                        </div>

                        {{-- Nội dung --}}
                        <div class="mb-4">
                            <h6 class="text-muted mb-3 fw-semibold">NỘI DUNG ĐÁNH GIÁ</h6>
                            <div class="p-4 bg-light rounded">
                                <p class="mb-0" style="line-height: 1.8; white-space: pre-wrap;">{{ $review->comment }}
                                </p>
                            </div>
                        </div>

                        {{-- Thời gian --}}
                        <div class="row g-3 text-center">
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded">
                                    <i class="fas fa-calendar-plus text-primary mb-2 d-block"></i>
                                    <small class="text-muted d-block">Ngày tạo</small>
                                    <strong>{{ $review->created_at->format('d/m/Y H:i') }}</strong>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded">
                                    <i class="fas fa-calendar-check text-success mb-2 d-block"></i>
                                    <small class="text-muted d-block">Cập nhật</small>
                                    <strong>{{ $review->updated_at->format('d/m/Y H:i') }}</strong>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded">
                                    <i class="fas fa-clock text-warning mb-2 d-block"></i>
                                    <small class="text-muted d-block">Đã đăng</small>
                                    <strong>{{ $review->created_at->diffForHumans() }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Đánh giá khác của user --}}
                @if ($userReviews->count() > 0)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-user-edit me-2 text-primary"></i>
                                Đánh giá khác của {{ $review->user->username }}
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                @foreach ($userReviews as $userReview)
                                    <a href="{{ route('admin.reviews.show', $userReview) }}"
                                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-start">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $userReview->product->main_image_url }}" class="rounded me-3"
                                                style="width: 50px; height: 50px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-1">{{ Str::limit($userReview->product->name, 50) }}</h6>
                                                <small
                                                    class="text-muted">{{ $userReview->created_at->format('d/m/Y') }}</small>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="rating-stars-small mb-1">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i
                                                        class="fas fa-star {{ $i <= $userReview->rating ? 'text-warning' : 'text-muted' }} small"></i>
                                                @endfor
                                            </div>
                                            @php
                                                $statusClass = match ($userReview->status->value) {
                                                    'approved' => 'success',
                                                    'pending' => 'warning',
                                                    'rejected' => 'danger',
                                                    default => 'secondary',
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }} small">
                                                {{ ucfirst($userReview->status->value) }}
                                            </span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Đánh giá khác của sản phẩm --}}
                @if ($productReviews->count() > 0)
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-box me-2 text-success"></i>
                                Đánh giá khác của sản phẩm
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                @foreach ($productReviews as $productReview)
                                    <a href="{{ route('admin.reviews.show', $productReview) }}"
                                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-start">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $productReview->user->avatar_url ?? asset('images/default-avatar.png') }}"
                                                class="rounded-circle me-3"
                                                style="width: 40px; height: 40px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-1">{{ $productReview->user->username }}</h6>
                                                <small
                                                    class="text-muted">{{ Str::limit($productReview->comment, 60) }}</small>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="rating-stars-small mb-1">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i
                                                        class="fas fa-star {{ $i <= $productReview->rating ? 'text-warning' : 'text-muted' }} small"></i>
                                                @endfor
                                            </div>
                                            <small
                                                class="text-muted">{{ $productReview->created_at->diffForHumans() }}</small>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Cột phải: Actions --}}
            <div class="col-lg-4">
                {{-- Card actions --}}
                <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-white">
                        <h6 class="mb-0 fw-bold">
                            <i class="fas fa-cog me-2"></i>
                            Thao tác
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if ($review->status->value !== 'approved')
                                <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-check-circle me-2"></i>Phê duyệt
                                    </button>
                                </form>
                            @endif

                            @if ($review->status->value !== 'rejected')
                                <form action="{{ route('admin.reviews.reject', $review) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-warning w-100">
                                        <i class="fas fa-times-circle me-2"></i>Từ chối
                                    </button>
                                </form>
                            @endif

                            <button type="button" class="btn btn-danger w-100"
                                onclick="confirmDelete({{ $review->id }})">
                                <i class="fas fa-trash-alt me-2"></i>Xóa đánh giá
                            </button>

                            <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-arrow-left me-2"></i>Quay lại
                            </a>
                        </div>

                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="d-none"
                            id="deleteForm{{ $review->id }}">
                            @csrf @method('DELETE')
                        </form>
                    </div>
                </div>

                {{-- Card thống kê user --}}
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0 fw-bold">
                            <i class="fas fa-chart-bar me-2 text-info"></i>
                            Thống kê người dùng
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted d-block">Tổng đánh giá</small>
                            <h4 class="fw-bold mb-0">{{ $review->user->reviews->count() }}</h4>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Đánh giá trung bình</small>
                            <h4 class="fw-bold mb-0 text-warning">
                                <i class="fas fa-star"></i>
                                {{ number_format($review->user->reviews->avg('rating'), 1) }}
                            </h4>
                        </div>
                        <div>
                            <small class="text-muted d-block">Đã mua</small>
                            <h4 class="fw-bold mb-0 text-success">
                                {{ $review->user->orders->count() }} đơn hàng
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function confirmDelete(id) {
                Swal.fire({
                    title: "Xác nhận xóa?",
                    text: "Đánh giá này sẽ được đưa vào thùng rác!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#ef4444",
                    cancelButtonColor: "#64748b",
                    confirmButtonText: '<i class="fas fa-trash me-2"></i>Xóa',
                    cancelButtonText: '<i class="fas fa-times me-2"></i>Hủy',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('deleteForm' + id).submit();
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
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: '{{ session('error') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif
        </script>
    @endpush

    @push('styles')
        <style>
            .rating-stars-large i {
                margin: 0 5px;
            }

            .card {
                border-radius: 12px;
            }

            .card-header {
                border-radius: 12px 12px 0 0 !important;
            }

            .btn {
                border-radius: 8px;
                font-weight: 600;
                padding: 12px 24px;
            }

            .list-group-item {
                border-left: 3px solid transparent;
                transition: all 0.2s ease;
            }

            .list-group-item:hover {
                border-left-color: #4f46e5;
                background-color: #f8fafc;
                transform: translateX(5px);
            }

            .sticky-top {
                z-index: 1020;
            }
        </style>
    @endpush
@endsection
