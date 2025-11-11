{{-- @extends('layouts.admin')

@section('title', 'Quản lý Wishlist')

@push('styles')
    <style>
        .stat-card {
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .bg-gradient-pink {
            background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
        }

        .bg-gradient-purple {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        }

        .action-btn {
            transition: all 0.2s;
        }

        .action-btn:hover {
            transform: scale(1.1);
        }

        .product-thumb {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
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
                            Quản lý Wishlist
                        </h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Wishlist</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.wishlists.statistics') }}" class="btn btn-outline-primary btn-lg">
                            <i class="fa-solid fa-chart-line me-2"></i> Thống kê
                        </a>
                        <a href="{{ route('admin.wishlists.export', request()->query()) }}" class="btn btn-success btn-lg">
                            <i class="fa-solid fa-file-excel me-2"></i> Xuất Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm h-100 bg-gradient-primary text-white stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1 small">Tổng wishlist</h6>
                                <h4 class="fw-bold mb-0">{{ number_format($stats['total']) }}</h4>
                            </div>
                            <div class="fs-2 opacity-50"><i class="fa-solid fa-heart"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm h-100 bg-gradient-success text-white stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1 small">Hôm nay</h6>
                                <h4 class="fw-bold mb-0">{{ number_format($stats['today']) }}</h4>
                            </div>
                            <div class="fs-2 opacity-50"><i class="fa-solid fa-calendar-day"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm h-100 bg-gradient-info text-white stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1 small">Tuần này</h6>
                                <h4 class="fw-bold mb-0">{{ number_format($stats['this_week']) }}</h4>
                            </div>
                            <div class="fs-2 opacity-50"><i class="fa-solid fa-calendar-week"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm h-100 bg-gradient-warning text-white stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1 small">Tháng này</h6>
                                <h4 class="fw-bold mb-0">{{ number_format($stats['this_month']) }}</h4>
                            </div>
                            <div class="fs-2 opacity-50"><i class="fa-solid fa-calendar"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm h-100 bg-gradient-pink text-white stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1 small">Người dùng</h6>
                                <h4 class="fw-bold mb-0">{{ number_format($stats['total_users']) }}</h4>
                            </div>
                            <div class="fs-2 opacity-50"><i class="fa-solid fa-users"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm h-100 bg-gradient-purple text-white stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1 small">Sản phẩm</h6>
                                <h4 class="fw-bold mb-0">{{ number_format($stats['total_products']) }}</h4>
                            </div>
                            <div class="fs-2 opacity-50"><i class="fa-solid fa-box"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-semibold">
                    <i class="fa-solid fa-filter text-primary me-2"></i>Bộ lọc
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.wishlists.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-magnifying-glass text-muted me-1"></i> Tìm kiếm
                            </label>
                            <input type="text" name="search" class="form-control form-control-lg"
                                placeholder="Tên người dùng, sản phẩm..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-calendar text-muted me-1"></i> Từ ngày
                            </label>
                            <input type="date" name="from" class="form-control form-control-lg"
                                value="{{ request('from') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-calendar-check text-muted me-1"></i> Đến ngày
                            </label>
                            <input type="date" name="to" class="form-control form-control-lg"
                                value="{{ request('to') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-sort text-muted me-1"></i> Sắp xếp
                            </label>
                            <select name="sort_by" class="form-select form-select-lg">
                                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Ngày tạo</option>
                                <option value="user_id" {{ request('sort_by') == 'user_id' ? 'selected' : '' }}>Người dùng</option>
                                <option value="product_id" {{ request('sort_by') == 'product_id' ? 'selected' : '' }}>Sản phẩm</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary btn-lg flex-fill">
                                <i class="fa-solid fa-filter me-2"></i> Lọc
                            </button>
                            <a href="{{ route('admin.wishlists.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fa-solid fa-rotate-right"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Wishlists Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fa-solid fa-list text-primary me-2"></i>Danh sách Wishlist
                        <span class="badge bg-primary fs-6">{{ $wishlists->total() }} mục</span>
                    </h5>
                    <button type="button" class="btn btn-outline-danger btn-sm" id="bulkDeleteBtn" style="display:none;">
                        <i class="fa-solid fa-trash me-1"></i> Xóa đã chọn
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 text-center" style="width:60px;">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                <th class="px-4 py-3 text-center" style="width:80px;">#</th>
                                <th class="px-4 py-3">Sản phẩm</th>
                                <th class="px-4 py-3">Người dùng</th>
                                <th class="px-4 py-3">Biến thể</th>
                                <th class="px-4 py-3 text-center">Ngày thêm</th>
                                <th class="px-4 py-3 text-center" style="width:150px;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($wishlists as $index => $wishlist)
                                <tr class="border-bottom">
                                    <td class="text-center px-4">
                                        <input type="checkbox" class="form-check-input wishlist-checkbox" 
                                            value="{{ $wishlist->id }}">
                                    </td>
                                    <td class="text-center px-4">
                                        <span class="badge bg-light text-dark fs-6">{{ $wishlists->firstItem() + $index }}</span>
                                    </td>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $wishlist->product->main_image_url }}" 
                                                alt="{{ $wishlist->product->name }}" 
                                                class="product-thumb me-3">
                                            <div>
                                                <div class="fw-bold text-dark">{{ $wishlist->product->name }}</div>
                                                <div class="small text-muted">
                                                    <i class="fa-solid fa-tag me-1"></i>
                                                    {{ number_format($wishlist->product->price) }}đ
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4">
                                        <div class="fw-semibold text-dark">
                                            {{ trim(($wishlist->user->first_name ?? '') . ' ' . ($wishlist->user->last_name ?? '')) ?: 'N/A' }}
                                        </div>
                                        <div class="small text-muted">
                                            <i class="fa-solid fa-envelope me-1"></i>{{ $wishlist->user->email }}
                                        </div>
                                    </td>
                                    <td class="px-4">
                                        @if($wishlist->variant)
                                            <span class="badge bg-info">{{ $wishlist->variant->name }}</span>
                                        @else
                                            <span class="text-muted small">Không có</span>
                                        @endif
                                    </td>
                                    <td class="text-center px-4">
                                        <div class="fw-semibold text-dark">{{ $wishlist->created_at->format('d/m/Y') }}</div>
                                        <div class="small text-muted">{{ $wishlist->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="text-center px-4">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.wishlists.show', $wishlist->id) }}"
                                                class="btn btn-outline-info btn-sm action-btn" data-bs-toggle="tooltip"
                                                title="Xem chi tiết">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-outline-danger btn-sm action-btn btn-delete"
                                                data-action="{{ route('admin.wishlists.destroy', $wishlist->id) }}"
                                                data-product="{{ $wishlist->product->name }}" data-bs-toggle="tooltip"
                                                title="Xóa">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fa-solid fa-heart-crack fs-1 d-block mb-3 opacity-50"></i>
                                            <h5>Không có wishlist nào</h5>
                                            <p class="mb-0">Thử thay đổi bộ lọc hoặc kiểm tra lại</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($wishlists->hasPages())
                <div class="card-footer bg-white border-top py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Hiển thị {{ $wishlists->firstItem() }} - {{ $wishlists->lastItem() }} trong {{ $wishlists->total() }} mục
                        </div>
                        <div>{{ $wishlists->links('components.pagination') }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Tooltips
            const tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltips.map(el => new bootstrap.Tooltip(el));

            // Select all checkbox
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.wishlist-checkbox');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

            selectAll?.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                toggleBulkDelete();
            });

            checkboxes.forEach(cb => {
                cb.addEventListener('change', toggleBulkDelete);
            });

            function toggleBulkDelete() {
                const checkedCount = document.querySelectorAll('.wishlist-checkbox:checked').length;
                bulkDeleteBtn.style.display = checkedCount > 0 ? 'block' : 'none';
            }

            // Bulk delete
            bulkDeleteBtn?.addEventListener('click', function() {
                const selectedIds = Array.from(document.querySelectorAll('.wishlist-checkbox:checked'))
                    .map(cb => cb.value);

                if (selectedIds.length === 0) return;

                Swal.fire({
                    title: 'Xác nhận xóa?',
                    html: `Bạn có chắc muốn xóa <strong>${selectedIds.length}</strong> mục wishlist?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('{{ route("admin.wishlists.bulk-destroy") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ ids: selectedIds })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Đã xóa!', data.message, 'success')
                                    .then(() => location.reload());
                            } else {
                                Swal.fire('Lỗi!', data.message, 'error');
                            }
                        });
                    }
                });
            });

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
@endpush --}}


@extends('layouts.admin')

@section('title', 'Quản lý Wishlist')

@push('styles')
    <style>
        :root {
            --tiktok-red: #FE2C55;
            --tiktok-blue: #25F4EE;
            --tiktok-dark: #121212;
            --tiktok-gray: #1F1F1F;
        }

        .stat-card {
            position: relative;
            overflow: hidden;
            border-radius: 16px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.1) 50%, transparent 70%);
            transform: translateX(-100%);
            transition: transform 0.6s;
        }

        .stat-card:hover::before {
            transform: translateX(100%);
        }

        .stat-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(254, 44, 85, 0.3) !important;
        }

        .stat-icon {
            font-size: 3rem;
            opacity: 0.15;
            position: absolute;
            right: -10px;
            bottom: -10px;
            transform: rotate(-15deg);
        }

        .tiktok-gradient-1 {
            background: linear-gradient(135deg, #FE2C55 0%, #FF6B9D 100%);
        }

        .tiktok-gradient-2 {
            background: linear-gradient(135deg, #25F4EE 0%, #00D4D4 100%);
        }

        .tiktok-gradient-3 {
            background: linear-gradient(135deg, #7000FF 0%, #A855F7 100%);
        }

        .tiktok-gradient-4 {
            background: linear-gradient(135deg, #FF3D00 0%, #FF9100 100%);
        }

        .tiktok-gradient-5 {
            background: linear-gradient(135deg, #00BFA5 0%, #00E676 100%);
        }

        .tiktok-gradient-6 {
            background: linear-gradient(135deg, #2196F3 0%, #00BCD4 100%);
        }

        .filter-card {
            border-radius: 16px;
            background: #fff;
            border: 2px solid #f0f0f0;
            transition: all 0.3s;
        }

        .filter-card:hover {
            border-color: var(--tiktok-red);
            box-shadow: 0 8px 24px rgba(254, 44, 85, 0.15);
        }

        .table-container {
            border-radius: 16px;
            overflow: hidden;
            background: #fff;
        }

        .table tbody tr {
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .table tbody tr:hover {
            background: linear-gradient(90deg, rgba(254, 44, 85, 0.05) 0%, rgba(255, 255, 255, 0) 100%);
            border-left-color: var(--tiktok-red);
            transform: translateX(4px);
        }

        .product-thumb {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 12px;
            transition: all 0.3s;
            border: 2px solid #f0f0f0;
        }

        .product-thumb:hover {
            transform: scale(1.5) rotate(5deg);
            border-color: var(--tiktok-red);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 10;
        }

        .action-btn {
            border-radius: 10px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .action-btn::before {
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

        .action-btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .badge-animated {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        .header-title {
            background: linear-gradient(135deg, var(--tiktok-red) 0%, #FF6B9D 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .search-input {
            border-radius: 12px;
            border: 2px solid #f0f0f0;
            transition: all 0.3s;
        }

        .search-input:focus {
            border-color: var(--tiktok-red);
            box-shadow: 0 0 0 4px rgba(254, 44, 85, 0.1);
            transform: translateY(-2px);
        }

        .btn-tiktok {
            background: linear-gradient(135deg, var(--tiktok-red) 0%, #FF6B9D 100%);
            border: none;
            color: white;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .btn-tiktok::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .btn-tiktok:hover::before {
            left: 100%;
        }

        .btn-tiktok:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(254, 44, 85, 0.4);
        }

        .page-header {
            background: linear-gradient(135deg, rgba(254, 44, 85, 0.05) 0%, rgba(37, 244, 238, 0.05) 100%);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .skeleton-loader {
            animation: skeleton-loading 1.5s infinite;
        }

        @keyframes skeleton-loading {
            0% {
                background-position: -200px 0;
            }
            100% {
                background-position: calc(200px + 100%) 0;
            }
        }

        .empty-state {
            padding: 4rem 2rem;
            text-align: center;
        }

        .empty-state i {
            font-size: 5rem;
            opacity: 0.2;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        .checkbox-wrapper {
            position: relative;
        }

        .checkbox-wrapper input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: var(--tiktok-red);
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
            animation: pulse-dot 2s infinite;
        }

        @keyframes pulse-dot {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-4">
        <!-- Page Header -->
        <div class="page-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="fw-bold mb-2">
                        <i class="fa-solid fa-heart me-2" style="color: var(--tiktok-red);"></i>
                        <span class="header-title">Wishlist Management</span>
                    </h1>
                    <p class="text-muted mb-0">
                        <i class="fa-solid fa-chart-line me-2"></i>
                        Quản lý danh sách yêu thích của khách hàng
                    </p>
                </div>
                <div class="d-flex gap-3">
                    <a href="{{ route('admin.wishlists.statistics') }}" class="btn btn-outline-dark action-btn">
                        <i class="fa-solid fa-chart-pie me-2"></i> Analytics
                    </a>
                    <a href="{{ route('admin.wishlists.export', request()->query()) }}" class="btn btn-tiktok">
                        <i class="fa-solid fa-download me-2"></i> Export Data
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="row g-4 mb-4">
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm stat-card tiktok-gradient-1 text-white h-100">
                    <div class="card-body p-4 position-relative">
                        <i class="fa-solid fa-heart stat-icon"></i>
                        <div class="position-relative z-1">
                            <p class="text-white-50 mb-2 fw-semibold small text-uppercase">Total Wishlists</p>
                            <h2 class="fw-bold mb-0 display-6">{{ number_format($stats['total']) }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm stat-card tiktok-gradient-2 text-white h-100">
                    <div class="card-body p-4 position-relative">
                        <i class="fa-solid fa-calendar-day stat-icon"></i>
                        <div class="position-relative z-1">
                            <p class="text-white-50 mb-2 fw-semibold small text-uppercase">Today</p>
                            <h2 class="fw-bold mb-0 display-6">{{ number_format($stats['today']) }}</h2>
                            <span class="badge bg-white bg-opacity-25 mt-2">
                                <i class="fa-solid fa-arrow-up me-1"></i>New
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm stat-card tiktok-gradient-3 text-white h-100">
                    <div class="card-body p-4 position-relative">
                        <i class="fa-solid fa-calendar-week stat-icon"></i>
                        <div class="position-relative z-1">
                            <p class="text-white-50 mb-2 fw-semibold small text-uppercase">This Week</p>
                            <h2 class="fw-bold mb-0 display-6">{{ number_format($stats['this_week']) }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm stat-card tiktok-gradient-4 text-white h-100">
                    <div class="card-body p-4 position-relative">
                        <i class="fa-solid fa-calendar stat-icon"></i>
                        <div class="position-relative z-1">
                            <p class="text-white-50 mb-2 fw-semibold small text-uppercase">This Month</p>
                            <h2 class="fw-bold mb-0 display-6">{{ number_format($stats['this_month']) }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm stat-card tiktok-gradient-5 text-white h-100">
                    <div class="card-body p-4 position-relative">
                        <i class="fa-solid fa-users stat-icon"></i>
                        <div class="position-relative z-1">
                            <p class="text-white-50 mb-2 fw-semibold small text-uppercase">Active Users</p>
                            <h2 class="fw-bold mb-0 display-6">{{ number_format($stats['total_users']) }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm stat-card tiktok-gradient-6 text-white h-100">
                    <div class="card-body p-4 position-relative">
                        <i class="fa-solid fa-box stat-icon"></i>
                        <div class="position-relative z-1">
                            <p class="text-white-50 mb-2 fw-semibold small text-uppercase">Products</p>
                            <h2 class="fw-bold mb-0 display-6">{{ number_format($stats['total_products']) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-card shadow-sm mb-4 p-4">
            <form method="GET" action="{{ route('admin.wishlists.index') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">
                            <i class="fa-solid fa-magnifying-glass me-2" style="color: var(--tiktok-red);"></i>
                            Search
                        </label>
                        <input type="text" name="search" class="form-control form-control-lg search-input"
                            placeholder="Tìm kiếm người dùng, sản phẩm..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold text-dark">
                            <i class="fa-solid fa-calendar me-2" style="color: var(--tiktok-red);"></i>
                            From Date
                        </label>
                        <input type="date" name="from" class="form-control form-control-lg search-input"
                            value="{{ request('from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold text-dark">
                            <i class="fa-solid fa-calendar-check me-2" style="color: var(--tiktok-red);"></i>
                            To Date
                        </label>
                        <input type="date" name="to" class="form-control form-control-lg search-input"
                            value="{{ request('to') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold text-dark">
                            <i class="fa-solid fa-sort me-2" style="color: var(--tiktok-red);"></i>
                            Sort By
                        </label>
                        <select name="sort_by" class="form-select form-select-lg search-input">
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Newest</option>
                            <option value="user_id" {{ request('sort_by') == 'user_id' ? 'selected' : '' }}>User</option>
                            <option value="product_id" {{ request('sort_by') == 'product_id' ? 'selected' : '' }}>Product</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-tiktok flex-fill">
                            <i class="fa-solid fa-filter me-2"></i> Filter
                        </button>
                        <a href="{{ route('admin.wishlists.index') }}" class="btn btn-outline-secondary action-btn">
                            <i class="fa-solid fa-rotate-right"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Wishlists Table -->
        <div class="table-container shadow-sm">
            <div class="p-4 border-bottom bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1 fw-bold text-dark">
                            <i class="fa-solid fa-list me-2" style="color: var(--tiktok-red);"></i>
                            Wishlist Items
                        </h5>
                        <p class="text-muted small mb-0">
                            Showing {{ $wishlists->firstItem() ?? 0 }}-{{ $wishlists->lastItem() ?? 0 }} of {{ $wishlists->total() }} items
                        </p>
                    </div>
                    <button type="button" class="btn btn-danger action-btn" id="bulkDeleteBtn" style="display:none;">
                        <i class="fa-solid fa-trash-can me-2"></i> Delete Selected
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 text-center" style="width:60px;">
                                <div class="checkbox-wrapper">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </div>
                            </th>
                            <th class="px-4 py-3 text-center" style="width:80px;">#</th>
                            <th class="px-4 py-3">Product</th>
                            <th class="px-4 py-3">User</th>
                            <th class="px-4 py-3 text-center">Variant</th>
                            <th class="px-4 py-3 text-center">Added Date</th>
                            <th class="px-4 py-3 text-center" style="width:180px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($wishlists as $index => $wishlist)
                            <tr>
                                <td class="text-center px-4">
                                    <div class="checkbox-wrapper">
                                        <input type="checkbox" class="form-check-input wishlist-checkbox" 
                                            value="{{ $wishlist->id }}">
                                    </div>
                                </td>
                                <td class="text-center px-4">
                                    <span class="badge bg-dark bg-opacity-10 text-dark fs-6 fw-semibold">
                                        {{ $wishlists->firstItem() + $index }}
                                    </span>
                                </td>
                                <td class="px-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $wishlist->product->main_image_url }}" 
                                            alt="{{ $wishlist->product->name }}" 
                                            class="product-thumb">
                                        <div>
                                            <div class="fw-bold text-dark mb-1">
                                                {{ Str::limit($wishlist->product->name, 40) }}
                                            </div>
                                            <div class="text-muted small">
                                                <i class="fa-solid fa-tag me-1" style="color: var(--tiktok-red);"></i>
                                                <span class="fw-semibold" style="color: var(--tiktok-red);">
                                                    {{ number_format($wishlist->product->price) }}đ
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $wishlist->user->avatar_url }}" 
                                            alt="Avatar"
                                            style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--tiktok-red);">
                                        <div>
                                            <div class="fw-semibold text-dark">
                                                {{ trim(($wishlist->user->first_name ?? '') . ' ' . ($wishlist->user->last_name ?? '')) ?: 'N/A' }}
                                            </div>
                                            <div class="small text-muted">
                                                {{ $wishlist->user->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center px-4">
                                    @if($wishlist->variant)
                                        <span class="badge tiktok-gradient-2 text-white">
                                            {{ $wishlist->variant->name }}
                                        </span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td class="text-center px-4">
                                    <div class="fw-semibold text-dark">
                                        {{ $wishlist->created_at->format('d/m/Y') }}
                                    </div>
                                    <div class="small text-muted">
                                        {{ $wishlist->created_at->format('H:i') }}
                                    </div>
                                </td>
                                <td class="text-center px-4">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.wishlists.show', $wishlist->id) }}"
                                            class="btn btn-sm btn-outline-primary action-btn" 
                                            data-bs-toggle="tooltip" title="View Details">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.products.show', $wishlist->product_id) }}"
                                            class="btn btn-sm btn-outline-info action-btn" 
                                            data-bs-toggle="tooltip" title="View Product">
                                            <i class="fa-solid fa-box"></i>
                                        </a>
                                        <a href="{{ route('admin.wishlists.user', $wishlist->user_id) }}"
                                            class="btn btn-sm btn-outline-success action-btn" 
                                            data-bs-toggle="tooltip" title="User Wishlists">
                                            <i class="fa-solid fa-user"></i>
                                        </a>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-danger action-btn btn-delete"
                                            data-action="{{ route('admin.wishlists.destroy', $wishlist->id) }}"
                                            data-product="{{ $wishlist->product->name }}" 
                                            data-bs-toggle="tooltip" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="fa-solid fa-heart-crack text-muted d-block mb-3"></i>
                                        <h4 class="text-muted mb-2">No Wishlists Found</h4>
                                        <p class="text-muted mb-0">Try adjusting your filters or check back later</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($wishlists->hasPages())
                <div class="p-4 border-top bg-light">
                    {{ $wishlists->links('components.pagination') }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Tooltips
            const tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltips.map(el => new bootstrap.Tooltip(el));

            // Select all checkbox
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.wishlist-checkbox');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

            selectAll?.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                toggleBulkDelete();
            });

            checkboxes.forEach(cb => {
                cb.addEventListener('change', toggleBulkDelete);
            });

            function toggleBulkDelete() {
                const checkedCount = document.querySelectorAll('.wishlist-checkbox:checked').length;
                bulkDeleteBtn.style.display = checkedCount > 0 ? 'block' : 'none';
            }

            // Bulk delete
            bulkDeleteBtn?.addEventListener('click', function() {
                const selectedIds = Array.from(document.querySelectorAll('.wishlist-checkbox:checked'))
                    .map(cb => cb.value);

                if (selectedIds.length === 0) return;

                Swal.fire({
                    title: 'Delete Wishlists?',
                    html: `Are you sure you want to delete <strong>${selectedIds.length}</strong> wishlist items?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#FE2C55',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fa-solid fa-trash me-2"></i>Delete',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'rounded-4',
                        confirmButton: 'btn-tiktok'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('{{ route("admin.wishlists.bulk-destroy") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ ids: selectedIds })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonColor: '#FE2C55',
                                    customClass: {
                                        popup: 'rounded-4'
                                    }
                                }).then(() => location.reload());
                            } else {
                                Swal.fire('Error!', data.message, 'error');
                            }
                        });
                    }
                });
            });

            // Delete confirmation
            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', function() {
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
                            popup: 'rounded-4',
                            confirmButton: 'btn-tiktok'
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
            });
        });
    </script>
@endpush