{{-- @extends('layouts.admin')

@section('title', 'Chi tiết Wishlist')

@push('styles')
    <style>
        .info-card {
            transition: all 0.3s;
        }

        .info-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .product-gallery img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .product-gallery img:hover {
            transform: scale(1.05);
        }

        .main-image {
            width: 100%;
            height: 400px;
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
                            Chi tiết Wishlist
                        </h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.wishlists.index') }}">Wishlist</a></li>
                                <li class="breadcrumb-item active">Chi tiết</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.wishlists.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
                        </a>
                        <button type="button" class="btn btn-danger btn-lg btn-delete"
                            data-action="{{ route('admin.wishlists.destroy', $wishlist->id) }}"
                            data-product="{{ $wishlist->product->name }}">
                            <i class="fa-solid fa-trash me-2"></i> Xóa
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- User Information -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm info-card">
                    <div class="card-header bg-gradient text-white py-3" 
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-user me-2"></i>Thông tin người dùng
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <img src="{{ $wishlist->user->avatar_url }}" 
                                alt="Avatar" 
                                class="rounded-circle mb-3"
                                style="width: 120px; height: 120px; object-fit: cover;">
                            <h5 class="fw-bold mb-1">
                                {{ trim(($wishlist->user->first_name ?? '') . ' ' . ($wishlist->user->last_name ?? '')) ?: 'N/A' }}
                            </h5>
                            <p class="text-muted mb-0">{{ $wishlist->user->email }}</p>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">
                                <i class="fa-solid fa-phone me-2"></i>Điện thoại:
                            </span>
                            <span class="fw-semibold">{{ $wishlist->user->phone ?? 'N/A' }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">
                                <i class="fa-solid fa-user-tag me-2"></i>Vai trò:
                            </span>
                            <span class="badge bg-primary">{{ ucfirst($wishlist->user->role) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">
                                <i class="fa-solid fa-calendar me-2"></i>Ngày tham gia:
                            </span>
                            <span class="fw-semibold">{{ $wishlist->user->created_at->format('d/m/Y') }}</span>
                        </div>

                        <hr>

                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.wishlists.user', $wishlist->user_id) }}" 
                                class="btn btn-outline-primary">
                                <i class="fa-solid fa-list me-2"></i>Xem tất cả wishlist
                            </a>
                            <a href="{{ route('admin.users.show', $wishlist->user_id) }}" 
                                class="btn btn-outline-secondary">
                                <i class="fa-solid fa-user me-2"></i>Xem hồ sơ
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Information -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm info-card mb-4">
                    <div class="card-header bg-gradient text-white py-3"
                        style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-box me-2"></i>Thông tin sản phẩm
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5">
                                <img src="{{ $wishlist->product->main_image_url }}" 
                                    alt="{{ $wishlist->product->name }}"
                                    class="main-image mb-3">

                                @if($wishlist->product->images->count() > 1)
                                    <div class="product-gallery">
                                        <div class="row g-2">
                                            @foreach($wishlist->product->images->take(4) as $image)
                                                <div class="col-3">
                                                    <img src="{{ $image->url }}" alt="Product">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-7">
                                <h3 class="fw-bold mb-3">{{ $wishlist->product->name }}</h3>

                                <div class="mb-3">
                                    <span class="badge bg-{{ $wishlist->product->status_color }} fs-6 px-3 py-2">
                                        {{ $wishlist->product->status_label }}
                                    </span>
                                    @if($wishlist->product->in_stock)
                                        <span class="badge bg-success fs-6 px-3 py-2 ms-2">
                                            <i class="fa-solid fa-check me-1"></i>Còn hàng
                                        </span>
                                    @else
                                        <span class="badge bg-danger fs-6 px-3 py-2 ms-2">
                                            <i class="fa-solid fa-times me-1"></i>Hết hàng
                                        </span>
                                    @endif
                                </div>

                                <div class="mb-4">
                                    <h4 class="text-primary fw-bold">{{ $wishlist->product->price_range }}</h4>
                                </div>

                                <div class="mb-3">
                                    <h6 class="fw-semibold mb-2">
                                        <i class="fa-solid fa-align-left me-2"></i>Mô tả:
                                    </h6>
                                    <p class="text-muted">{{ $wishlist->product->description ?? 'Không có mô tả' }}</p>
                                </div>

                                @if($wishlist->variant)
                                    <div class="mb-3">
                                        <h6 class="fw-semibold mb-2">
                                            <i class="fa-solid fa-layer-group me-2"></i>Biến thể:
                                        </h6>
                                        <div class="alert alert-info mb-0">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <div class="fw-bold">{{ $wishlist->variant->name }}</div>
                                                    <div class="small">SKU: {{ $wishlist->variant->sku }}</div>
                                                </div>
                                                <div class="text-end">
                                                    <div class="fw-bold text-primary">
                                                        {{ number_format($wishlist->variant->price) }}đ
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="row g-3 mb-3">
                                    <div class="col-6">
                                        <div class="card bg-light border-0">
                                            <div class="card-body text-center">
                                                <i class="fa-solid fa-box text-primary fs-4 mb-2"></i>
                                                <div class="small text-muted">Tồn kho</div>
                                                <div class="fw-bold fs-5">{{ number_format($wishlist->product->total_stock) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card bg-light border-0">
                                            <div class="card-body text-center">
                                                <i class="fa-solid fa-star text-warning fs-4 mb-2"></i>
                                                <div class="small text-muted">Đánh giá</div>
                                                <div class="fw-bold fs-5">
                                                    {{ $wishlist->product->average_rating }} 
                                                    <span class="small text-muted">({{ $wishlist->product->review_count }})</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <a href="{{ route('admin.products.show', $wishlist->product_id) }}" 
                                        class="btn btn-primary btn-lg">
                                        <i class="fa-solid fa-eye me-2"></i>Xem chi tiết sản phẩm
                                    </a>
                                    <a href="{{ route('admin.products.edit', $wishlist->product_id) }}" 
                                        class="btn btn-outline-secondary">
                                        <i class="fa-solid fa-pen me-2"></i>Chỉnh sửa sản phẩm
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Wishlist Details -->
                <div class="card border-0 shadow-sm info-card">
                    <div class="card-header bg-gradient text-white py-3"
                        style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-info-circle me-2"></i>Chi tiết Wishlist
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="fa-solid fa-calendar-plus text-primary fs-1 mb-3"></i>
                                    <h6 class="text-muted mb-2">Ngày thêm</h6>
                                    <p class="fw-bold mb-1">{{ $wishlist->created_at->format('d/m/Y') }}</p>
                                    <p class="text-muted small mb-0">{{ $wishlist->created_at->format('H:i:s') }}</p>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="fa-solid fa-clock text-success fs-1 mb-3"></i>
                                    <h6 class="text-muted mb-2">Thời gian tồn tại</h6>
                                    <p class="fw-bold mb-0">{{ $wishlist->created_at->diffForHumans() }}</p>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="fa-solid fa-hashtag text-info fs-1 mb-3"></i>
                                    <h6 class="text-muted mb-2">ID Wishlist</h6>
                                    <p class="fw-bold mb-0">#{{ $wishlist->id }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Delete confirmation
            const deleteBtn = document.querySelector('.btn-delete');
            
            deleteBtn?.addEventListener('click', function() {
                const deleteUrl = this.dataset.action;
                const productName = this.dataset.product;

                Swal.fire({
                    title: 'Xác nhận xóa?',
                    html: `Bạn có chắc muốn xóa wishlist cho sản phẩm <strong>${productName}</strong>?`,
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

            // Image gallery lightbox
            document.querySelectorAll('.product-gallery img, .main-image').forEach(img => {
                img.addEventListener('click', function() {
                    Swal.fire({
                        imageUrl: this.src,
                        imageAlt: 'Product image',
                        showConfirmButton: false,
                        showCloseButton: true,
                        width: '80%',
                        background: 'transparent',
                        backdrop: 'rgba(0,0,0,0.9)'
                    });
                });
            });
        });
    </script>
@endpush --}}


@extends('layouts.admin')

@section('title', 'Wishlist Details')

@push('styles')
    <style>
        :root {
            --tiktok-red: #FE2C55;
            --tiktok-blue: #25F4EE;
        }

        .detail-card {
            border-radius: 20px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
            overflow: hidden;
            position: relative;
        }

        .detail-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.6s;
        }

        .detail-card:hover::before {
            left: 100%;
        }

        .detail-card:hover {
            transform: translateY(-5px);
            border-color: var(--tiktok-red);
            box-shadow: 0 15px 40px rgba(254, 44, 85, 0.2);
        }

        .user-card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px 20px 0 0;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }

        .user-card-header::before {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            top: -50px;
            right: -50px;
        }

        .user-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            transition: all 0.3s;
        }

        .user-avatar:hover {
            transform: scale(1.1) rotate(5deg);
        }

        .product-card-header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border-radius: 20px 20px 0 0;
            padding: 2rem;
        }

        .main-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 16px;
            transition: all 0.3s;
            cursor: pointer;
        }

        .main-image:hover {
            transform: scale(1.02);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        }

        .product-gallery img {
            width: 100%;
            height: 100px;
            object-fit: cover;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid #f0f0f0;
        }

        .product-gallery img:hover {
            transform: scale(1.1);
            border-color: var(--tiktok-red);
            z-index: 10;
        }

        .info-item {
            padding: 1rem;
            border-radius: 12px;
            background: #f8f9fa;
            transition: all 0.3s;
            border: 2px solid transparent;
        }

        .info-item:hover {
            background: white;
            border-color: var(--tiktok-red);
            transform: translateX(5px);
        }

        .stat-box {
            text-align: center;
            padding: 1.5rem;
            border-radius: 16px;
            background: linear-gradient(135deg, rgba(254, 44, 85, 0.1) 0%, rgba(37, 244, 238, 0.1) 100%);
            transition: all 0.3s;
        }

        .stat-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
        }

        .badge-status {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .action-button {
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s;
            border: 2px solid;
            position: relative;
            overflow: hidden;
        }

        .action-button::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.4s, height 0.4s;
        }

        .action-button:hover::before {
            width: 300px;
            height: 300px;
        }

        .action-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .price-tag {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--tiktok-red) 0%, #FF6B9D 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .timeline-item {
            position: relative;
            padding-left: 2rem;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(to bottom, var(--tiktok-red), var(--tiktok-blue));
        }

        .info-card-modern {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-slide-up {
            animation: slideInUp 0.6s ease-out;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-4">
        <!-- Header -->
        <div class="mb-4 animate-slide-up">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="fw-bold mb-2">
                        <i class="fa-solid fa-heart me-2" style="color: var(--tiktok-red);"></i>
                        Wishlist Details
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.wishlists.index') }}">Wishlists</a></li>
                            <li class="breadcrumb-item active">Details #{{ $wishlist->id }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-3">
                    <a href="{{ route('admin.wishlists.index') }}" class="btn btn-outline-secondary action-button">
                        <i class="fa-solid fa-arrow-left me-2"></i> Back
                    </a>
                    <button type="button" class="btn btn-danger action-button btn-delete"
                        data-action="{{ route('admin.wishlists.destroy', $wishlist->id) }}"
                        data-product="{{ $wishlist->product->name }}">
                        <i class="fa-solid fa-trash me-2"></i> Delete
                    </button>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- User Information -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm detail-card animate-slide-up" style="animation-delay: 0.1s;">
                    <div class="user-card-header text-white position-relative">
                        <div class="text-center position-relative z-1">
                            <img src="{{ $wishlist->user->avatar_url }}" 
                                alt="Avatar" 
                                class="user-avatar mb-3">
                            <h4 class="fw-bold mb-1">
                                {{ trim(($wishlist->user->first_name ?? '') . ' ' . ($wishlist->user->last_name ?? '')) ?: 'N/A' }}
                            </h4>
                            <p class="mb-0 opacity-75">{{ $wishlist->user->email }}</p>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="info-item mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">
                                    <i class="fa-solid fa-phone me-2" style="color: var(--tiktok-red);"></i>Phone
                                </span>
                                <span class="fw-semibold">{{ $wishlist->user->phone ?? 'N/A' }}</span>
                            </div>
                        </div>

                        <div class="info-item mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">
                                    <i class="fa-solid fa-user-tag me-2" style="color: var(--tiktok-red);"></i>Role
                                </span>
                                <span class="badge" style="background: linear-gradient(135deg, var(--tiktok-red) 0%, #FF6B9D 100%);">
                                    {{ ucfirst($wishlist->user->role) }}
                                </span>
                            </div>
                        </div>

                        <div class="info-item mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">
                                    <i class="fa-solid fa-calendar me-2" style="color: var(--tiktok-red);"></i>Joined
                                </span>
                                <span class="fw-semibold">{{ $wishlist->user->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.wishlists.user', $wishlist->user_id) }}" 
                                class="btn btn-outline-primary action-button">
                                <i class="fa-solid fa-heart me-2"></i>All Wishlists
                            </a>
                            <a href="{{ route('admin.users.show', $wishlist->user_id) }}" 
                                class="btn btn-outline-secondary action-button">
                                <i class="fa-solid fa-user me-2"></i>View Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Information -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm detail-card mb-4 animate-slide-up" style="animation-delay: 0.2s;">
                    <div class="product-card-header text-white">
                        <h4 class="fw-bold mb-0">
                            <i class="fa-solid fa-box me-2"></i>Product Information
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-5">
                                <img src="{{ $wishlist->product->main_image_url }}" 
                                    alt="{{ $wishlist->product->name }}"
                                    class="main-image mb-3">

                                @if($wishlist->product->images->count() > 1)
                                    <div class="product-gallery">
                                        <div class="row g-2">
                                            @foreach($wishlist->product->images->take(4) as $image)
                                                <div class="col-3">
                                                    <img src="{{ $image->url }}" alt="Product">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-7">
                                <h3 class="fw-bold mb-3">{{ $wishlist->product->name }}</h3>

                                <div class="mb-4">
                                    <span class="badge badge-status" style="background-color: {{ $wishlist->product->status === 'active' ? '#10b981' : '#ef4444' }};">
                                        <i class="fa-solid fa-circle me-1" style="font-size: 0.5rem;"></i>
                                        {{ $wishlist->product->status_label }}
                                    </span>
                                    @if($wishlist->product->in_stock)
                                        <span class="badge badge-status bg-success ms-2">
                                            <i class="fa-solid fa-check me-1"></i>In Stock
                                        </span>
                                    @else
                                        <span class="badge badge-status bg-danger ms-2">
                                            <i class="fa-solid fa-times me-1"></i>Out of Stock
                                        </span>
                                    @endif
                                </div>

                                <div class="mb-4">
                                    <h2 class="price-tag">{{ $wishlist->product->price_range }}</h2>
                                </div>

                                <div class="mb-4">
                                    <h6 class="fw-semibold mb-2 text-muted">
                                        <i class="fa-solid fa-align-left me-2"></i>Description
                                    </h6>
                                    <p class="text-muted">{{ $wishlist->product->description ?? 'No description available' }}</p>
                                </div>

                                @if($wishlist->variant)
                                    <div class="info-card-modern mb-4">
                                        <h6 class="fw-semibold mb-3">
                                            <i class="fa-solid fa-layer-group me-2" style="color: var(--tiktok-red);"></i>
                                            Selected Variant
                                        </h6>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="fw-bold fs-5">{{ $wishlist->variant->name }}</div>
                                                <div class="small text-muted">SKU: {{ $wishlist->variant->sku }}</div>
                                            </div>
                                            <div class="price-tag" style="font-size: 1.5rem;">
                                                {{ number_format($wishlist->variant->price) }}đ
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="row g-3 mb-4">
                                    <div class="col-4">
                                        <div class="stat-box">
                                            <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                                <i class="fa-solid fa-box"></i>
                                            </div>
                                            <div class="small text-muted mb-1">Stock</div>
                                            <div class="fw-bold fs-4">{{ number_format($wishlist->product->total_stock) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-box">
                                            <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                                                <i class="fa-solid fa-star"></i>
                                            </div>
                                            <div class="small text-muted mb-1">Rating</div>
                                            <div class="fw-bold fs-4">{{ $wishlist->product->average_rating }}</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-box">
                                            <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
                                                <i class="fa-solid fa-comment"></i>
                                            </div>
                                            <div class="small text-muted mb-1">Reviews</div>
                                            <div class="fw-bold fs-4">{{ $wishlist->product->review_count }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <a href="{{ route('admin.products.show', $wishlist->product_id) }}" 
                                        class="btn action-button" style="background: linear-gradient(135deg, var(--tiktok-red) 0%, #FF6B9D 100%); color: white; border: none;">
                                        <i class="fa-solid fa-eye me-2"></i>View Product Details
                                    </a>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <a href="{{ route('admin.products.edit', $wishlist->product_id) }}" 
                                                class="btn btn-outline-secondary action-button w-100">
                                                <i class="fa-solid fa-pen me-2"></i>Edit Product
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <a href="{{ route('admin.wishlists.product', $wishlist->product_id) }}" 
                                                class="btn btn-outline-info action-button w-100">
                                                <i class="fa-solid fa-users me-2"></i>All Fans
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Wishlist Timeline -->
                <div class="card border-0 shadow-sm detail-card animate-slide-up" style="animation-delay: 0.3s;">
                    <div class="card-header border-0 bg-transparent py-4">
                        <h5 class="mb-0 fw-bold">
                            <i class="fa-solid fa-clock-rotate-left me-2" style="color: var(--tiktok-red);"></i>
                            Wishlist Timeline
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="stat-box h-100">
                                    <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
                                        <i class="fa-solid fa-calendar-plus"></i>
                                    </div>
                                    <h6 class="text-muted mb-2">Added On</h6>
                                    <p class="fw-bold mb-1 fs-5">{{ $wishlist->created_at->format('d/m/Y') }}</p>
                                    <p class="text-muted small mb-0">{{ $wishlist->created_at->format('H:i:s') }}</p>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="stat-box h-100">
                                    <div class="stat-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
                                        <i class="fa-solid fa-clock"></i>
                                    </div>
                                    <h6 class="text-muted mb-2">Time Active</h6>
                                    <p class="fw-bold mb-0 fs-5">{{ $wishlist->created_at->diffForHumans() }}</p>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="stat-box h-100">
                                    <div class="stat-icon" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: white;">
                                        <i class="fa-solid fa-hashtag"></i>
                                    </div>
                                    <h6 class="text-muted mb-2">Wishlist ID</h6>
                                    <p class="fw-bold mb-0 fs-5">#{{ $wishlist->id }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Delete confirmation
            const deleteBtn = document.querySelector('.btn-delete');
            
            deleteBtn?.addEventListener('click', function() {
                const deleteUrl = this.dataset.action;
                const productName = this.dataset.product;

                Swal.fire({
                    title: 'Delete Wishlist?',
                    html: `Remove <strong>${productName}</strong> from wishlist?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#FE2C55',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fa-solid fa-trash me-2"></i>Delete',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'rounded-4'
                    }
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

            // Image gallery lightbox
            document.querySelectorAll('.product-gallery img, .main-image').forEach(img => {
                img.addEventListener('click', function() {
                    Swal.fire({
                        imageUrl: this.src,
                        imageAlt: 'Product image',
                        showConfirmButton: false,
                        showCloseButton: true,
                        width: '80%',
                        background: 'transparent',
                        backdrop: 'rgba(0,0,0,0.95)',
                        customClass: {
                            image: 'rounded-4'
                        }
                    });
                });
            });
        });
    </script>
@endpush