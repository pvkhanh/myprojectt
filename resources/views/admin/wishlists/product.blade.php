@extends('layouts.admin')

@section('title', 'Wishlist cho: ' . $product->name)

@push('styles')
    <style>
        .product-header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border-radius: 12px;
        }

        .user-card {
            transition: all 0.3s;
        }

        .user-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .user-avatar {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
        }

        .product-main-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 12px;
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
                            <i class="fa-solid fa-heart text-danger me-2"></i>
                            Người dùng yêu thích sản phẩm
                        </h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.wishlists.index') }}">Wishlist</a></li>
                                <li class="breadcrumb-item active">Sản phẩm #{{ $product->id }}</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.wishlists.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
                        </a>
                        <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-primary btn-lg">
                            <i class="fa-solid fa-box me-2"></i> Xem sản phẩm
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Info Card -->
        <div class="card border-0 shadow-sm mb-4 product-header text-white">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <img src="{{ $product->main_image_url }}" alt="{{ $product->name }}"
                            class="product-main-image border border-3 border-white">
                    </div>
                    <div class="col">
                        <h3 class="fw-bold mb-3">{{ $product->name }}</h3>

                        <div class="row g-3 mb-3">
                            <div class="col-auto">
                                <span class="badge bg-white text-dark fs-6">
                                    {{ $product->status_label }}
                                </span>
                            </div>
                            <div class="col-auto">
                                @if ($product->in_stock)
                                    <span class="badge bg-success fs-6">
                                        <i class="fa-solid fa-check me-1"></i>Còn hàng
                                    </span>
                                @else
                                    <span class="badge bg-danger fs-6">
                                        <i class="fa-solid fa-times me-1"></i>Hết hàng
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-auto">
                                <h4 class="mb-0">{{ $product->price_range }}</h4>
                            </div>
                            <div class="col-auto">
                                <i class="fa-solid fa-box me-2"></i>
                                Tồn kho: <strong>{{ number_format($product->total_stock) }}</strong>
                            </div>
                            <div class="col-auto">
                                <i class="fa-solid fa-star text-warning me-2"></i>
                                <strong>{{ $product->average_rating }}</strong>
                                <span class="opacity-75">({{ $product->review_count }} đánh giá)</span>
                            </div>
                            <div class="col-auto">
                                <i class="fa-solid fa-tag me-2"></i>
                                {{ $product->first_category_name }}
                            </div>
                        </div>
                    </div>
                    <div class="col-auto text-center">
                        <div class="bg-white bg-opacity-25 rounded p-4">
                            <h1 class="fw-bold mb-1">{{ $wishlists->count() }}</h1>
                            <div>Người yêu thích</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Row -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-white"
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body text-center">
                        <i class="fa-solid fa-users fs-1 mb-2 opacity-50"></i>
                        <h3 class="fw-bold mb-1">{{ $wishlists->count() }}</h3>
                        <div class="small">Tổng người yêu thích</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-white"
                    style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                    <div class="card-body text-center">
                        <i class="fa-solid fa-calendar-day fs-1 mb-2 opacity-50"></i>
                        <h3 class="fw-bold mb-1">
                            {{ $wishlists->where('created_at', '>=', today())->count() }}
                        </h3>
                        <div class="small">Hôm nay</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-white"
                    style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <div class="card-body text-center">
                        <i class="fa-solid fa-calendar-week fs-1 mb-2 opacity-50"></i>
                        <h3 class="fw-bold mb-1">
                            {{ $wishlists->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count() }}
                        </h3>
                        <div class="small">Tuần này</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-white"
                    style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                    <div class="card-body text-center">
                        <i class="fa-solid fa-calendar fs-1 mb-2 opacity-50"></i>
                        <h3 class="fw-bold mb-1">
                            {{ $wishlists->whereMonth('created_at', now()->month)->count() }}
                        </h3>
                        <div class="small">Tháng này</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users List -->
        @if ($wishlists->isEmpty())
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fa-solid fa-heart-crack text-muted fs-1 mb-3 d-block opacity-50"></i>
                    <h4 class="text-muted mb-2">Chưa có người yêu thích</h4>
                    <p class="text-muted mb-0">Sản phẩm này chưa được thêm vào wishlist</p>
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fa-solid fa-users text-primary me-2"></i>
                        Danh sách người dùng yêu thích
                        <span class="badge bg-primary fs-6">{{ $wishlists->count() }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="row g-3 p-4">
                        @foreach ($wishlists as $wishlist)
                            <div class="col-lg-4 col-md-6">
                                <div class="card border shadow-sm user-card h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start mb-3">
                                            <img src="{{ $wishlist->user->avatar_url }}" alt="Avatar"
                                                class="user-avatar me-3">
                                            <div class="flex-fill">
                                                <h6 class="fw-bold mb-1">
                                                    {{ trim(($wishlist->user->first_name ?? '') . ' ' . ($wishlist->user->last_name ?? '')) ?: 'N/A' }}
                                                </h6>
                                                <p class="text-muted small mb-2">
                                                    <i class="fa-solid fa-envelope me-1"></i>
                                                    {{ $wishlist->user->email }}
                                                </p>
                                                <span class="badge bg-primary">
                                                    {{ ucfirst($wishlist->user->role) }}
                                                </span>
                                            </div>
                                        </div>

                                        @if ($wishlist->user->phone)
                                            <div class="mb-2 small">
                                                <i class="fa-solid fa-phone text-muted me-2"></i>
                                                {{ $wishlist->user->phone }}
                                            </div>
                                        @endif

                                        @if ($wishlist->variant)
                                            <div class="mb-3">
                                                <small class="text-muted">Biến thể:</small>
                                                <div class="badge bg-info mt-1">{{ $wishlist->variant->name }}</div>
                                            </div>
                                        @endif

                                        <div class="border-top pt-3 mb-3">
                                            <div class="d-flex justify-content-between text-muted small">
                                                <span>
                                                    <i class="fa-solid fa-calendar-plus me-1"></i>
                                                    Ngày thêm
                                                </span>
                                                <strong class="text-dark">
                                                    {{ $wishlist->created_at->format('d/m/Y') }}
                                                </strong>
                                            </div>
                                            <div class="d-flex justify-content-between text-muted small mt-2">
                                                <span>
                                                    <i class="fa-solid fa-clock me-1"></i>
                                                    Thời gian
                                                </span>
                                                <strong class="text-dark">
                                                    {{ $wishlist->created_at->diffForHumans() }}
                                                </strong>
                                            </div>
                                        </div>

                                        <div class="d-grid gap-2">
                                            <a href="{{ route('admin.wishlists.show', $wishlist->id) }}"
                                                class="btn btn-outline-primary btn-sm">
                                                <i class="fa-solid fa-eye me-1"></i> Xem chi tiết
                                            </a>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.wishlists.user', $wishlist->user_id) }}"
                                                    class="btn btn-outline-secondary btn-sm">
                                                    <i class="fa-solid fa-list me-1"></i> Wishlist
                                                </a>
                                                <a href="{{ route('admin.users.show', $wishlist->user_id) }}"
                                                    class="btn btn-outline-info btn-sm">
                                                    <i class="fa-solid fa-user me-1"></i> Hồ sơ
                                                </a>
                                                <button type="button" class="btn btn-outline-danger btn-sm btn-delete"
                                                    data-action="{{ route('admin.wishlists.destroy', $wishlist->id) }}"
                                                    data-user="{{ trim(($wishlist->user->first_name ?? '') . ' ' . ($wishlist->user->last_name ?? '')) }}">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Delete confirmation
            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', function() {
                    const deleteUrl = this.dataset.action;
                    const userName = this.dataset.user;

                    Swal.fire({
                        title: 'Xác nhận xóa?',
                        html: `Bạn có chắc muốn xóa wishlist của <strong>${userName}</strong>?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Xóa',
                        cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = deleteUrl;
                            form.innerHTML = `
                                @csrf
                                @method('DELETE')
                            `;
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
