@extends('layouts.admin')

@section('title', 'Wishlist của ' . ($user->first_name ?? '') . ' ' . ($user->last_name ?? ''))

@push('styles')
    <style>
        .product-card {
            transition: all 0.3s;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px 8px 0 0;
        }

        .user-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
        }

        .action-btn {
            transition: all 0.2s;
        }

        .action-btn:hover {
            transform: scale(1.1);
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
                            Wishlist của người dùng
                        </h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.wishlists.index') }}">Wishlist</a></li>
                                <li class="breadcrumb-item active">Người dùng #{{ $user->id }}</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.wishlists.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
                        </a>
                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-primary btn-lg">
                            <i class="fa-solid fa-user me-2"></i> Xem hồ sơ
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Info Card -->
        <div class="card border-0 shadow-sm mb-4 user-header text-white">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <img src="{{ $user->avatar_url }}" alt="Avatar"
                            class="rounded-circle border border-3 border-white"
                            style="width: 100px; height: 100px; object-fit: cover;">
                    </div>
                    <div class="col">
                        <h3 class="fw-bold mb-2">
                            {{ trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: 'N/A' }}
                        </h3>
                        <div class="row g-3">
                            <div class="col-auto">
                                <i class="fa-solid fa-envelope me-2"></i>{{ $user->email }}
                            </div>
                            @if ($user->phone)
                                <div class="col-auto">
                                    <i class="fa-solid fa-phone me-2"></i>{{ $user->phone }}
                                </div>
                            @endif
                            <div class="col-auto">
                                <i class="fa-solid fa-user-tag me-2"></i>
                                <span class="badge bg-white text-primary">{{ ucfirst($user->role) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto text-center">
                        <div class="bg-white bg-opacity-25 rounded p-3">
                            <h2 class="fw-bold mb-1">{{ $wishlists->count() }}</h2>
                            <div class="small">Sản phẩm yêu thích</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wishlists Grid -->
        @if ($wishlists->isEmpty())
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fa-solid fa-heart-crack text-muted fs-1 mb-3 d-block opacity-50"></i>
                    <h4 class="text-muted mb-2">Chưa có sản phẩm yêu thích</h4>
                    <p class="text-muted mb-0">Người dùng này chưa thêm sản phẩm nào vào wishlist</p>
                </div>
            </div>
        @else
            <div class="row g-4">
                @foreach ($wishlists as $wishlist)
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="card border-0 shadow-sm product-card">
                            <!-- Product Image -->
                            <div class="position-relative">
                                <img src="{{ $wishlist->product->main_image_url }}" alt="{{ $wishlist->product->name }}"
                                    class="product-image">

                                <!-- Status Badge -->
                                <div class="position-absolute top-0 start-0 m-2">
                                    <span class="badge bg-{{ $wishlist->product->status_color }} fs-6">
                                        {{ $wishlist->product->status_label }}
                                    </span>
                                </div>

                                <!-- Stock Badge -->
                                <div class="position-absolute top-0 end-0 m-2">
                                    @if ($wishlist->product->in_stock)
                                        <span class="badge bg-success fs-6">
                                            <i class="fa-solid fa-check me-1"></i>Còn hàng
                                        </span>
                                    @else
                                        <span class="badge bg-danger fs-6">
                                            <i class="fa-solid fa-times me-1"></i>Hết hàng
                                        </span>
                                    @endif
                                </div>

                                <!-- Date Badge -->
                                <div class="position-absolute bottom-0 start-0 m-2">
                                    <span class="badge bg-dark bg-opacity-75 fs-6">
                                        <i class="fa-solid fa-calendar me-1"></i>
                                        {{ $wishlist->created_at->format('d/m/Y') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Product Info -->
                            <div class="card-body">
                                <h5 class="card-title fw-bold mb-2">
                                    <a href="{{ route('admin.products.show', $wishlist->product->id) }}"
                                        class="text-dark text-decoration-none">
                                        {{ Str::limit($wishlist->product->name, 40) }}
                                    </a>
                                </h5>

                                <!-- Price -->
                                <div class="mb-3">
                                    <h4 class="text-primary fw-bold mb-0">
                                        {{ $wishlist->product->price_range }}
                                    </h4>
                                </div>

                                <!-- Variant Info -->
                                @if ($wishlist->variant)
                                    <div class="mb-3">
                                        <small class="text-muted">Biến thể:</small>
                                        <div class="badge bg-info mt-1">{{ $wishlist->variant->name }}</div>
                                    </div>
                                @endif

                                <!-- Product Stats -->
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <div class="text-center bg-light rounded py-2">
                                            <small class="text-muted d-block">Tồn kho</small>
                                            <strong>{{ number_format($wishlist->product->total_stock) }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center bg-light rounded py-2">
                                            <small class="text-muted d-block">Đánh giá</small>
                                            <strong>
                                                <i class="fa-solid fa-star text-warning"></i>
                                                {{ $wishlist->product->average_rating }}
                                            </strong>
                                        </div>
                                    </div>
                                </div>

                                <!-- Category -->
                                <div class="mb-3">
                                    <small class="text-muted">
                                        <i class="fa-solid fa-tag me-1"></i>
                                        {{ $wishlist->product->first_category_name }}
                                    </small>
                                </div>

                                <!-- Actions -->
                                <div class="d-grid gap-2">
                                    <a href="{{ route('admin.wishlists.show', $wishlist->id) }}"
                                        class="btn btn-outline-primary btn-sm">
                                        <i class="fa-solid fa-eye me-1"></i> Xem chi tiết
                                    </a>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.products.show', $wishlist->product->id) }}"
                                            class="btn btn-outline-secondary btn-sm">
                                            <i class="fa-solid fa-box me-1"></i> Sản phẩm
                                        </a>
                                        <button type="button" class="btn btn-outline-danger btn-sm btn-delete"
                                            data-action="{{ route('admin.wishlists.destroy', $wishlist->id) }}"
                                            data-product="{{ $wishlist->product->name }}">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Added Time -->
                            <div class="card-footer bg-light border-0 text-center small text-muted">
                                <i class="fa-solid fa-clock me-1"></i>
                                Thêm {{ $wishlist->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                @endforeach
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
            });
        });
    </script>
@endpush
