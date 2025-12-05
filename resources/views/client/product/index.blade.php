{{-- @extends('client.layouts.master')

@section('title', 'Danh sách sản phẩm')

@push('styles')
    <style>
        .filters-sidebar {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 100px;
        }

        .filter-title {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e2e8f0;
        }

        .filter-group {
            margin-bottom: 30px;
        }

        .filter-group-title {
            font-size: 15px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-option {
            display: flex;
            align-items: center;
            padding: 10px 0;
        }

        .filter-checkbox {
            width: 20px;
            height: 20px;
            margin-right: 10px;
            cursor: pointer;
        }

        .filter-label {
            flex: 1;
            cursor: pointer;
            font-size: 14px;
            color: #475569;
        }

        .filter-count {
            color: #94a3b8;
            font-size: 13px;
        }

        .price-range-inputs {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .price-input {
            flex: 1;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
        }

        .filter-btn {
            width: 100%;
            padding: 12px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 20px;
            transition: all 0.3s;
        }

        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
        }

        .clear-filters {
            width: 100%;
            padding: 10px;
            background: #f1f5f9;
            color: #64748b;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            margin-top: 10px;
        }

        .products-header {
            background: white;
            border-radius: 16px;
            padding: 20px 25px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .view-toggle {
            display: flex;
            gap: 10px;
        }

        .view-btn {
            width: 40px;
            height: 40px;
            border: 2px solid #e2e8f0;
            background: white;
            border-radius: 8px;
            color: #64748b;
            transition: all 0.3s;
            cursor: pointer;
        }

        .view-btn.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .sort-select {
            padding: 10px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
        }

        .active-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .filter-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 15px;
            background: #dbeafe;
            color: var(--primary-color);
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
        }

        .filter-tag .remove {
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.3s;
        }

        .filter-tag .remove:hover {
            opacity: 1;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
        }

        .list-view .products-grid {
            grid-template-columns: 1fr;
        }

        .no-products {
            text-align: center;
            padding: 80px 20px;
        }

        .no-products-icon {
            font-size: 80px;
            color: #cbd5e1;
            margin-bottom: 20px;
        }

        .mobile-filter-btn {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            padding: 15px 30px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.4);
            z-index: 999;
            display: none;
        }

        @media (max-width: 991px) {
            .mobile-filter-btn {
                display: block;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container py-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active">Sản phẩm</li>
            </ol>
        </nav>

        <div class="row">
            <!-- Filters Sidebar -->
            <div class="col-lg-3 d-none d-lg-block">
                <div class="filters-sidebar">
                    <h3 class="filter-title">
                        <i class="fas fa-filter me-2"></i>Bộ Lọc
                    </h3>

                    <!-- Categories -->
                    <div class="filter-group">
                        <h4 class="filter-group-title">
                            <i class="fas fa-folder"></i> Danh mục
                        </h4>
                        @foreach ($categories ?? [] as $category)
                            <label class="filter-option">
                                <input type="checkbox" class="filter-checkbox" name="category[]"
                                    value="{{ $category->id }}">
                                <span class="filter-label">{{ $category->name }}</span>
                                <span class="filter-count">({{ $category->products_count }})</span>
                            </label>
                        @endforeach
                    </div>

                    <!-- Price Range -->
                    <div class="filter-group">
                        <h4 class="filter-group-title">
                            <i class="fas fa-tags"></i> Khoảng giá
                        </h4>
                        <div class="price-range-inputs">
                            <input type="number" class="price-input" placeholder="Từ" id="priceMin">
                            <span>-</span>
                            <input type="number" class="price-input" placeholder="Đến" id="priceMax">
                        </div>
                    </div>

                    <!-- Rating -->
                    <div class="filter-group">
                        <h4 class="filter-group-title">
                            <i class="fas fa-star"></i> Đánh giá
                        </h4>
                        @for ($i = 5; $i >= 1; $i--)
                            <label class="filter-option">
                                <input type="checkbox" class="filter-checkbox" name="rating[]" value="{{ $i }}">
                                <span class="filter-label">
                                    @for ($j = 0; $j < $i; $j++)
                                        <i class="fas fa-star text-warning"></i>
                                    @endfor
                                    @for ($j = $i; $j < 5; $j++)
                                        <i class="far fa-star text-warning"></i>
                                    @endfor
                                </span>
                            </label>
                        @endfor
                    </div>

                    <!-- Brands -->
                    <div class="filter-group">
                        <h4 class="filter-group-title">
                            <i class="fas fa-copyright"></i> Thương hiệu
                        </h4>
                        @foreach ($brands ?? [] as $brand)
                            <label class="filter-option">
                                <input type="checkbox" class="filter-checkbox" name="brand[]" value="{{ $brand->id }}">
                                <span class="filter-label">{{ $brand->name }}</span>
                                <span class="filter-count">({{ $brand->products_count }})</span>
                            </label>
                        @endforeach
                    </div>

                    <button class="filter-btn">
                        <i class="fas fa-search me-2"></i>Áp dụng
                    </button>
                    <button class="clear-filters">
                        <i class="fas fa-times me-2"></i>Xóa bộ lọc
                    </button>
                </div>
            </div>

            <!-- Products Section -->
            <div class="col-lg-9">
                <!-- Products Header -->
                <div class="products-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h2 class="mb-0">
                                <strong>{{ $products->total() ?? 0 }}</strong> sản phẩm
                            </h2>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center justify-content-end gap-3">
                                <!-- View Toggle -->
                                <div class="view-toggle d-none d-md-flex">
                                    <button class="view-btn active" data-view="grid">
                                        <i class="fas fa-th"></i>
                                    </button>
                                    <button class="view-btn" data-view="list">
                                        <i class="fas fa-list"></i>
                                    </button>
                                </div>

                                <!-- Sort -->
                                <select class="sort-select" id="sortBy">
                                    <option value="newest">Mới nhất</option>
                                    <option value="price_asc">Giá: Thấp → Cao</option>
                                    <option value="price_desc">Giá: Cao → Thấp</option>
                                    <option value="popular">Phổ biến nhất</option>
                                    <option value="rating">Đánh giá cao</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Filters -->
                <div class="active-filters" id="activeFilters" style="display: none;">
                    <!-- Dynamically populated -->
                </div>

                <!-- Products Grid -->
                <div class="products-grid" id="productsGrid">
                    @forelse($products ?? [] as $product)
                        @include('client.components.product-card', ['product' => $product])
                    @empty
                        <div class="no-products">
                            <div class="no-products-icon">
                                <i class="fas fa-box-open"></i>
                            </div>
                            <h3>Không tìm thấy sản phẩm</h3>
                            <p class="text-muted">Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm</p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if (isset($products) && $products->hasPages())
                    <div class="mt-5">
                        {{ $products->links('components.pagination') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Mobile Filter Button -->
    <button class="mobile-filter-btn" data-bs-toggle="offcanvas" data-bs-target="#mobileFilters">
        <i class="fas fa-filter me-2"></i>Bộ lọc
    </button>

    <!-- Mobile Filters Offcanvas -->
    <div class="offcanvas offcanvas-start" id="mobileFilters">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Bộ lọc</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <!-- Same filter content as sidebar -->
            <div class="filters-sidebar" style="position: static; box-shadow: none;">
                <!-- Copy filter content here -->
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            // ====== VIEW TOGGLE ======
            $('.view-btn').click(function() {
                $('.view-btn').removeClass('active');
                $(this).addClass('active');
                const view = $(this).data('view');
                if (view === 'list') {
                    $('#productsGrid').addClass('list-view');
                } else {
                    $('#productsGrid').removeClass('list-view');
                }
            });

            // ====== FILTER FUNCTIONALITY ======
            $('.filter-btn').click(function() {
                applyFilters();
            });

            $('.clear-filters').click(function() {
                $('.filter-checkbox').prop('checked', false);
                $('#priceMin, #priceMax').val('');
                applyFilters();
            });

            $('#sortBy').change(function() {
                applyFilters();
            });

            function applyFilters() {
                const filters = {
                    categories: $('input[name="category[]"]:checked').map(function() {
                        return $(this).val();
                    }).get(),
                    brands: $('input[name="brand[]"]:checked').map(function() {
                        return $(this).val();
                    }).get(),
                    rating: $('input[name="rating[]"]:checked').map(function() {
                        return $(this).val();
                    }).get(),
                    price_min: $('#priceMin').val(),
                    price_max: $('#priceMax').val(),
                    sort: $('#sortBy').val()
                };

                const params = new URLSearchParams();
                Object.keys(filters).forEach(key => {
                    const value = filters[key];
                    if (Array.isArray(value) && value.length > 0) {
                        value.forEach(v => params.append(key + '[]', v));
                    } else if (value) {
                        params.append(key, value);
                    }
                });

                window.location.href = '{{ route('client.products.index') }}?' + params.toString();
            }

            // ====== ADD TO CART FROM PRODUCT CARD ======
            $(document).on('click', '.add-to-cart-btn', function(e) {
                e.preventDefault();

                const btn = $(this);
                const productId = btn.data('product-id');
                const originalHtml = btn.html();

                // Disable button và hiển thị loading
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang thêm...');

                $.ajax({
                    url: `/client/cart/add/${productId}`,
                    method: 'POST',
                    data: {
                        quantity: 1,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast(response.message, 'success');

                            // Cập nhật cart count
                            if (response.cart_count) {
                                $('.cart-count').text(response.cart_count);
                                $('.cart-badge').text(response.cart_count).show();
                            }
                        } else {
                            showToast(response.message || 'Có lỗi xảy ra', 'error');
                        }
                    },
                    error: function(xhr) {
                        let message = 'Có lỗi xảy ra, vui lòng thử lại!';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.status === 401) {
                            message = 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng';
                            setTimeout(() => {
                                window.location.href = '{{ route('login') }}';
                            }, 2000);
                        }

                        showToast(message, 'error');
                    },
                    complete: function() {
                        // Re-enable button
                        btn.prop('disabled', false).html(originalHtml);
                    }
                });
            });

            // ====== WISHLIST TOGGLE ======
            $(document).on('click', '.wishlist-btn', function(e) {
                e.preventDefault();

                const btn = $(this);
                const productId = btn.data('product-id');
                const icon = btn.find('i');

                $.ajax({
                    url: `/client/wishlist/toggle/${productId}`,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Toggle icon
                            if (icon.hasClass('far')) {
                                icon.removeClass('far').addClass('fas');
                            } else {
                                icon.removeClass('fas').addClass('far');
                            }

                            showToast(response.message, 'success');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 401) {
                            showToast('Vui lòng đăng nhập để thêm vào yêu thích', 'error');
                            setTimeout(() => {
                                window.location.href = '{{ route('login') }}';
                            }, 2000);
                        } else {
                            showToast('Có lỗi xảy ra', 'error');
                        }
                    }
                });
            });

            // ====== QUICK VIEW (Optional) ======
            $(document).on('click', '.quick-view-btn', function(e) {
                e.preventDefault();
                const productId = $(this).data('product-id');
                // TODO: Implement quick view modal
                console.log('Quick view for product:', productId);
            });
        });

        // ====== TOAST NOTIFICATION ======
        function showToast(message, type = 'info') {
            // Nếu có Toastr
            if (typeof toastr !== 'undefined') {
                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    positionClass: 'toast-top-right',
                    timeOut: 3000
                };
                toastr[type](message);
                return;
            }

            // Nếu có SweetAlert2
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: type === 'error' ? 'error' : 'success',
                    title: type === 'error' ? 'Lỗi!' : 'Thành công!',
                    text: message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                return;
            }

            // Fallback: Bootstrap alert
            const alertClass = type === 'error' ? 'danger' : 'success';
            const alertHtml = `
            <div class="alert alert-${alertClass} alert-dismissible fade show position-fixed top-0 end-0 m-3" 
                 style="z-index: 9999; min-width: 300px;" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
            $('body').append(alertHtml);

            setTimeout(() => {
                $('.alert').alert('close');
            }, 3000);
        }
    </script>
@endpush
{{-- 
@push('scripts')
    <script>
        // View toggle
        $('.view-btn').click(function() {
            $('.view-btn').removeClass('active');
            $(this).addClass('active');
            const view = $(this).data('view');
            if (view === 'list') {
                $('#productsGrid').addClass('list-view');
            } else {
                $('#productsGrid').removeClass('list-view');
            }
        });

        // Filter functionality
        $('.filter-btn').click(function() {
            applyFilters();
        });

        $('.clear-filters').click(function() {
            $('.filter-checkbox').prop('checked', false);
            $('#priceMin, #priceMax').val('');
            applyFilters();
        });

        function applyFilters() {
            const filters = {
                categories: $('input[name="category[]"]:checked').map(function() {
                    return $(this).val();
                }).get(),
                brands: $('input[name="brand[]"]:checked').map(function() {
                    return $(this).val();
                }).get(),
                rating: $('input[name="rating[]"]:checked').map(function() {
                    return $(this).val();
                }).get(),
                price_min: $('#priceMin').val(),
                price_max: $('#priceMax').val(),
                sort: $('#sortBy').val()
            };

            // Update URL and reload products
            const params = new URLSearchParams(filters);
            window.location.href = '{{ route('client.products.index') }}?' + params.toString();
        }

        // Sort change
        $('#sortBy').change(function() {
            applyFilters();
        });
    </script>
@endpush --}} --}}




@extends('client.layouts.master')

@section('title', 'Danh sách sản phẩm')

@push('styles')
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --secondary: #f59e0b;
            --success: #10b981;
            --danger: #ef4444;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border: #e2e8f0;
            --bg-light: #f8fafc;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.12);
            --radius: 12px;
            --radius-lg: 16px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: var(--bg-light);
            color: var(--text-primary);
        }

        /* ==================== BREADCRUMB ==================== */
        .custom-breadcrumb {
            background: white;
            padding: 15px 0;
            margin-bottom: 30px;
            border-bottom: 1px solid var(--border);
        }

        .custom-breadcrumb a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: color 0.3s;
        }

        .custom-breadcrumb a:hover {
            color: var(--primary);
        }

        /* ==================== FILTERS SIDEBAR ==================== */
        .filters-sidebar {
            background: white;
            border-radius: var(--radius-lg);
            padding: 25px;
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 100px;
            max-height: calc(100vh - 120px);
            overflow-y: auto;
        }

        .filters-sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .filters-sidebar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .filters-sidebar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .filters-sidebar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .filter-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--border);
        }

        .filter-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
        }

        .filter-title i {
            color: var(--primary);
        }

        .reset-filters-link {
            color: var(--text-secondary);
            font-size: 13px;
            text-decoration: none;
            transition: all 0.3s;
            cursor: pointer;
        }

        .reset-filters-link:hover {
            color: var(--danger);
        }

        .filter-group {
            margin-bottom: 30px;
            padding-bottom: 25px;
            border-bottom: 1px solid var(--border);
        }

        .filter-group:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .filter-group-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            cursor: pointer;
            user-select: none;
        }

        .filter-group-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
        }

        .filter-group-title i {
            color: var(--primary);
            font-size: 14px;
        }

        .filter-collapse-icon {
            color: var(--text-secondary);
            font-size: 12px;
            transition: transform 0.3s;
        }

        .filter-group.collapsed .filter-collapse-icon {
            transform: rotate(-90deg);
        }

        .filter-group-content {
            max-height: 300px;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .filter-group.collapsed .filter-group-content {
            max-height: 0;
        }

        .filter-option {
            display: flex;
            align-items: center;
            padding: 10px 8px;
            border-radius: 8px;
            transition: background 0.2s;
            cursor: pointer;
        }

        .filter-option:hover {
            background: var(--bg-light);
        }

        .filter-checkbox {
            width: 18px;
            height: 18px;
            margin-right: 12px;
            cursor: pointer;
            accent-color: var(--primary);
        }

        .filter-label {
            flex: 1;
            font-size: 14px;
            color: var(--text-primary);
            cursor: pointer;
            line-height: 1.4;
        }

        .filter-count {
            color: var(--text-secondary);
            font-size: 13px;
            background: var(--bg-light);
            padding: 2px 8px;
            border-radius: 10px;
        }

        /* Price Range */
        .price-range-inputs {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .price-input {
            flex: 1;
            padding: 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .price-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .price-separator {
            color: var(--text-secondary);
            font-weight: 500;
        }

        /* Rating Stars */
        .star-rating {
            display: flex;
            gap: 3px;
        }

        .star-rating i {
            font-size: 14px;
        }

        /* Filter Buttons */
        .filter-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 25px;
        }

        .filter-btn {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: var(--radius);
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .filter-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
        }

        .filter-btn:active {
            transform: translateY(0);
        }

        .clear-filters {
            width: 100%;
            padding: 12px;
            background: white;
            color: var(--text-secondary);
            border: 2px solid var(--border);
            border-radius: var(--radius);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }

        .clear-filters:hover {
            background: var(--bg-light);
            border-color: var(--danger);
            color: var(--danger);
        }

        /* ==================== PRODUCTS SECTION ==================== */
        .products-section {
            min-height: 600px;
        }

        /* Products Header */
        .products-header {
            background: white;
            border-radius: var(--radius-lg);
            padding: 20px 25px;
            margin-bottom: 25px;
            box-shadow: var(--shadow-sm);
        }

        .products-header h2 {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            color: var(--text-primary);
        }

        .products-header h2 strong {
            color: var(--primary);
        }

        /* View Toggle */
        .view-toggle {
            display: flex;
            gap: 8px;
            background: var(--bg-light);
            padding: 4px;
            border-radius: 8px;
        }

        .view-btn {
            width: 40px;
            height: 40px;
            border: none;
            background: transparent;
            border-radius: 6px;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .view-btn:hover {
            background: white;
            color: var(--primary);
        }

        .view-btn.active {
            background: var(--primary);
            color: white;
            box-shadow: var(--shadow-sm);
        }

        /* Sort Select */
        .sort-select {
            padding: 10px 15px;
            border: 2px solid var(--border);
            border-radius: var(--radius);
            font-size: 14px;
            color: var(--text-primary);
            cursor: pointer;
            transition: all 0.3s;
            background: white;
        }

        .sort-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        /* Active Filters */
        .active-filters {
            background: white;
            border-radius: var(--radius);
            padding: 15px 20px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-sm);
        }

        .active-filters-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 12px;
        }

        .filter-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .filter-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            background: linear-gradient(135deg, #dbeafe 0%, #e0e7ff 100%);
            color: var(--primary);
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .filter-tag:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }

        .filter-tag .remove {
            cursor: pointer;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            font-weight: bold;
            transition: all 0.3s;
        }

        .filter-tag .remove:hover {
            transform: scale(1.2) rotate(90deg);
        }

        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
            transition: opacity 0.3s;
        }

        .products-grid.loading {
            opacity: 0.6;
            pointer-events: none;
        }

        /* List View */
        .list-view .products-grid {
            grid-template-columns: 1fr;
        }

        /* No Products */
        .no-products {
            text-align: center;
            padding: 80px 20px;
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
        }

        .no-products-icon {
            font-size: 80px;
            color: #cbd5e1;
            margin-bottom: 20px;
        }

        .no-products h3 {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 10px;
        }

        .no-products p {
            color: var(--text-secondary);
            font-size: 15px;
        }

        .no-products-btn {
            margin-top: 20px;
            padding: 12px 30px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: var(--radius);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .no-products-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        /* Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
        }

        .loading-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid var(--border);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Mobile Filter Button */
        .mobile-filter-btn {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            padding: 16px 35px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 15px;
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.4);
            z-index: 999;
            display: none;
            cursor: pointer;
            transition: all 0.3s;
        }

        .mobile-filter-btn:hover {
            transform: translateX(-50%) translateY(-3px);
            box-shadow: 0 15px 40px rgba(37, 99, 235, 0.5);
        }

        /* Pagination */
        .pagination-wrapper {
            margin-top: 40px;
            display: flex;
            justify-content: center;
        }

        /* ==================== RESPONSIVE ==================== */
        @media (max-width: 1400px) {
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            }
        }

        @media (max-width: 991px) {
            .mobile-filter-btn {
                display: block;
            }

            .filters-sidebar {
                position: static;
                max-height: none;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 20px;
            }
        }

        @media (max-width: 768px) {
            .products-header {
                padding: 15px 20px;
            }

            .products-header h2 {
                font-size: 20px;
            }

            .products-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }

            .view-toggle {
                display: none !important;
            }
        }

        @media (max-width: 480px) {
            .products-grid {
                grid-template-columns: 1fr;
            }

            .filter-tag {
                font-size: 12px;
                padding: 6px 12px;
            }
        }

        /* ==================== OFFCANVAS CUSTOM ==================== */
        .offcanvas-custom {
            max-width: 85%;
            width: 380px;
        }

        .offcanvas-header {
            border-bottom: 1px solid var(--border);
            padding: 20px 25px;
        }

        .offcanvas-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .offcanvas-body {
            padding: 25px;
        }

        /* ==================== ANIMATIONS ==================== */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeInUp 0.5s ease-out;
        }

        /* ==================== UTILITIES ==================== */
        .badge-new {
            position: absolute;
            top: 10px;
            left: 10px;
            background: var(--danger);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            z-index: 2;
        }

        .badge-sale {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--secondary);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            z-index: 2;
        }
    </style>
@endpush

@section('content')
    <!-- Breadcrumb -->
    <div class="custom-breadcrumb">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}">
                            <i class="fas fa-home me-1"></i>Trang chủ
                        </a>
                    </li>
                    <li class="breadcrumb-item active">Sản phẩm</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container py-4">
        <div class="row">
            <!-- Filters Sidebar -->
            <div class="col-lg-3 d-none d-lg-block">
                <div class="filters-sidebar animate-fade-in">
                    <div class="filter-header">
                        <h3 class="filter-title">
                            <i class="fas fa-sliders-h"></i>
                            Bộ Lọc
                        </h3>
                        <a class="reset-filters-link" id="resetFiltersLink">
                            <i class="fas fa-redo-alt"></i>
                            Đặt lại
                        </a>
                    </div>

                    <!-- Categories Filter -->
                    @if (isset($categories) && count($categories) > 0)
                        <div class="filter-group">
                            <div class="filter-group-header">
                                <h4 class="filter-group-title">
                                    <i class="fas fa-folder"></i>
                                    Danh mục
                                </h4>
                                <i class="fas fa-chevron-down filter-collapse-icon"></i>
                            </div>
                            <div class="filter-group-content">
                                @foreach ($categories as $category)
                                    <label class="filter-option">
                                        <input type="checkbox" class="filter-checkbox" name="category[]"
                                            value="{{ $category->id }}">
                                        <span class="filter-label">{{ $category->name }}</span>
                                        <span class="filter-count">{{ $category->products_count ?? 0 }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Price Range Filter -->
                    <div class="filter-group">
                        <div class="filter-group-header">
                            <h4 class="filter-group-title">
                                <i class="fas fa-tags"></i>
                                Khoảng giá
                            </h4>
                            <i class="fas fa-chevron-down filter-collapse-icon"></i>
                        </div>
                        <div class="filter-group-content">
                            <div class="price-range-inputs">
                                <input type="number" class="price-input" placeholder="Từ" id="priceMin" min="0">
                                <span class="price-separator">-</span>
                                <input type="number" class="price-input" placeholder="Đến" id="priceMax" min="0">
                            </div>
                            <small class="text-muted d-block mt-2" style="font-size: 12px;">
                                <i class="fas fa-info-circle"></i> Đơn vị: VNĐ
                            </small>
                        </div>
                    </div>

                    <!-- Rating Filter -->
                    <div class="filter-group">
                        <div class="filter-group-header">
                            <h4 class="filter-group-title">
                                <i class="fas fa-star"></i>
                                Đánh giá
                            </h4>
                            <i class="fas fa-chevron-down filter-collapse-icon"></i>
                        </div>
                        <div class="filter-group-content">
                            @for ($i = 5; $i >= 1; $i--)
                                <label class="filter-option">
                                    <input type="checkbox" class="filter-checkbox" name="rating[]"
                                        value="{{ $i }}">
                                    <span class="filter-label">
                                        <span class="star-rating">
                                            @for ($j = 0; $j < $i; $j++)
                                                <i class="fas fa-star text-warning"></i>
                                            @endfor
                                            @for ($j = $i; $j < 5; $j++)
                                                <i class="far fa-star text-warning"></i>
                                            @endfor
                                        </span>
                                        <span class="ms-2">{{ $i }} sao trở lên</span>
                                    </span>
                                </label>
                            @endfor
                        </div>
                    </div>

                    <!-- Brands Filter -->
                    @if (isset($brands) && count($brands) > 0)
                        <div class="filter-group">
                            <div class="filter-group-header">
                                <h4 class="filter-group-title">
                                    <i class="fas fa-copyright"></i>
                                    Thương hiệu
                                </h4>
                                <i class="fas fa-chevron-down filter-collapse-icon"></i>
                            </div>
                            <div class="filter-group-content">
                                @foreach ($brands as $brand)
                                    <label class="filter-option">
                                        <input type="checkbox" class="filter-checkbox" name="brand[]"
                                            value="{{ $brand->id }}">
                                        <span class="filter-label">{{ $brand->name }}</span>
                                        <span class="filter-count">{{ $brand->products_count ?? 0 }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Filter Actions -->
                    <div class="filter-actions">
                        <button class="filter-btn" id="applyFiltersBtn">
                            <i class="fas fa-search"></i>
                            Áp dụng lọc
                        </button>
                        <button class="clear-filters" id="clearFiltersBtn">
                            <i class="fas fa-times"></i>
                            Xóa bộ lọc
                        </button>
                    </div>
                </div>
            </div>

            <!-- Products Section -->
            <div class="col-lg-9">
                <!-- Products Header -->
                <div class="products-header animate-fade-in">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <h2>
                                <strong id="totalProducts">{{ $products->total() ?? 0 }}</strong> sản phẩm
                            </h2>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center justify-content-md-end gap-3">
                                <!-- View Toggle -->
                                <div class="view-toggle d-none d-md-flex">
                                    <button class="view-btn active" data-view="grid" title="Lưới">
                                        <i class="fas fa-th"></i>
                                    </button>
                                    <button class="view-btn" data-view="list" title="Danh sách">
                                        <i class="fas fa-list"></i>
                                    </button>
                                </div>

                                <!-- Sort Select -->
                                <select class="sort-select" id="sortBy">
                                    <option value="newest">Mới nhất</option>
                                    <option value="price_asc">Giá: Thấp → Cao</option>
                                    <option value="price_desc">Giá: Cao → Thấp</option>
                                    <option value="popular">Phổ biến nhất</option>
                                    <option value="rating">Đánh giá cao</option>
                                    <option value="name_asc">Tên: A → Z</option>
                                    <option value="name_desc">Tên: Z → A</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Filters -->
                <div class="active-filters" id="activeFilters" style="display: none;">
                    <div class="active-filters-title">
                        <i class="fas fa-filter me-2"></i>Bộ lọc đang áp dụng:
                    </div>
                    <div class="filter-tags" id="filterTags">
                        <!-- Dynamic filter tags -->
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="products-section">
                    <div class="products-grid" id="productsGrid">
                        @forelse($products ?? [] as $product)
                            @include('client.components.product-card', ['product' => $product])
                        @empty
                            <div class="no-products">
                                <div class="no-products-icon">
                                    <i class="fas fa-box-open"></i>
                                </div>
                                <h3>Không tìm thấy sản phẩm</h3>
                                <p>Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm của bạn</p>
                                <button class="no-products-btn" id="clearAllFilters">
                                    <i class="fas fa-redo-alt me-2"></i>Xóa tất cả bộ lọc
                                </button>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if (isset($products) && $products->hasPages())
                        <div class="pagination-wrapper">
                            {{ $products->appends(request()->query())->links('components.pagination') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Filter Button -->
    <button class="mobile-filter-btn" data-bs-toggle="offcanvas" data-bs-target="#mobileFilters">
        <i class="fas fa-filter me-2"></i>Bộ lọc & Sắp xếp
    </button>

    <!-- Mobile Filters Offcanvas -->
    <div class="offcanvas offcanvas-start offcanvas-custom" id="mobileFilters" tabindex="-1">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">
                <i class="fas fa-sliders-h me-2"></i>Bộ Lọc
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <!-- Sort for Mobile -->
            <div class="filter-group">
                <h4 class="filter-group-title">
                    <i class="fas fa-sort"></i>
                    Sắp xếp
                </h4>
                <select class="sort-select w-100" id="sortByMobile">
                    <option value="newest">Mới nhất</option>
                    <option value="price_asc">Giá: Thấp → Cao</option>
                    <option value="price_desc">Giá: Cao → Thấp</option>
                    <option value="popular">Phổ biến nhất</option>
                    <option value="rating">Đánh giá cao</option>
                    <option value="name_asc">Tên: A → Z</option>
                    <option value="name_desc">Tên: Z → A</option>
                </select>
            </div>

            <!-- Categories -->
            @if (isset($categories) && count($categories) > 0)
                <div class="filter-group">
                    <h4 class="filter-group-title">
                        <i class="fas fa-folder"></i>
                        Danh mục
                    </h4>
                    @foreach ($categories as $category)
                        <label class="filter-option">
                            <input type="checkbox" class="filter-checkbox" name="category_mobile[]"
                                value="{{ $category->id }}">
                            <span class="filter-label">{{ $category->name }}</span>
                            <span class="filter-count">{{ $category->products_count ?? 0 }}</span>
                        </label>
                    @endforeach
                </div>
            @endif

            <!-- Price Range -->
            <div class="filter-group">
                <h4 class="filter-group-title">
                    <i class="fas fa-tags"></i>
                    Khoảng giá
                </h4>
                <div class="price-range-inputs">
                    <input type="number" class="price-input" placeholder="Từ" id="priceMinMobile" min="0">
                    <span class="price-separator">-</span>
                    <input type="number" class="price-input" placeholder="Đến" id="priceMaxMobile" min="0">
                </div>
            </div>

            <!-- Rating -->
            <div class="filter-group">
                <h4 class="filter-group-title">
                    <i class="fas fa-star"></i>
                    Đánh giá
                </h4>
                @for ($i = 5; $i >= 1; $i--)
                    <label class="filter-option">
                        <input type="checkbox" class="filter-checkbox" name="rating_mobile[]"
                            value="{{ $i }}">
                        <span class="filter-label">
                            <span class="star-rating">
                                @for ($j = 0; $j < $i; $j++)
                                    <i class="fas fa-star text-warning"></i>
                                @endfor
                                @for ($j = $i; $j < 5; $j++)
                                    <i class="far fa-star text-warning"></i>
                                @endfor
                            </span>
                            <span class="ms-2">{{ $i }} sao trở lên</span>
                        </span>
                    </label>
                @endfor
            </div>

            <!-- Brands -->
            @if (isset($brands) && count($brands) > 0)
                <div class="filter-group">
                    <h4 class="filter-group-title">
                        <i class="fas fa-copyright"></i>
                        Thương hiệu
                    </h4>
                    @foreach ($brands as $brand)
                        <label class="filter-option">
                            <input type="checkbox" class="filter-checkbox" name="brand_mobile[]"
                                value="{{ $brand->id }}">
                            <span class="filter-label">{{ $brand->name }}</span>
                            <span class="filter-count">{{ $brand->products_count ?? 0 }}</span>
                        </label>
                    @endforeach
                </div>
            @endif

            <!-- Mobile Filter Actions -->
            <div class="filter-actions">
                <button class="filter-btn" id="applyFiltersMobile" data-bs-dismiss="offcanvas">
                    <i class="fas fa-check"></i>
                    Áp dụng
                </button>
                <button class="clear-filters" id="clearFiltersMobile">
                    <i class="fas fa-times"></i>
                    Xóa bộ lọc
                </button>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // ==================== INITIALIZATION ====================
            initializeFiltersFromURL();
            updateActiveFilters();

            // ==================== FILTER GROUP COLLAPSE ====================
            $('.filter-group-header').click(function() {
                $(this).closest('.filter-group').toggleClass('collapsed');
            });

            // ==================== VIEW TOGGLE ====================
            $('.view-btn').click(function() {
                $('.view-btn').removeClass('active');
                $(this).addClass('active');
                const view = $(this).data('view');

                if (view === 'list') {
                    $('#productsGrid').parent().addClass('list-view');
                } else {
                    $('#productsGrid').parent().removeClass('list-view');
                }

                // Save preference to localStorage
                localStorage.setItem('productView', view);
            });

            // Restore view preference
            const savedView = localStorage.getItem('productView');
            if (savedView === 'list') {
                $('.view-btn[data-view="list"]').click();
            }

            // ==================== SORT ====================
            $('#sortBy, #sortByMobile').change(function() {
                const value = $(this).val();
                // Sync both selects
                $('#sortBy, #sortByMobile').val(value);
                applyFilters();
            });

            // ==================== FILTER BUTTONS ====================
            $('#applyFiltersBtn, #applyFiltersMobile').click(function() {
                applyFilters();
            });

            $('#clearFiltersBtn, #clearFiltersMobile, #clearAllFilters').click(function() {
                clearAllFilters();
            });

            $('#resetFiltersLink').click(function(e) {
                e.preventDefault();
                clearAllFilters();
            });

            // ==================== APPLY FILTERS ====================
            function applyFilters() {
                showLoading();

                const params = new URLSearchParams();

                // Categories (desktop and mobile)
                $('input[name="category[]"]:checked, input[name="category_mobile[]"]:checked').each(function() {
                    params.append('categories[]', $(this).val());
                });

                // Brands (desktop and mobile)
                $('input[name="brand[]"]:checked, input[name="brand_mobile[]"]:checked').each(function() {
                    params.append('brands[]', $(this).val());
                });

                // Rating (desktop and mobile)
                $('input[name="rating[]"]:checked, input[name="rating_mobile[]"]:checked').each(function() {
                    params.append('rating[]', $(this).val());
                });

                // Price range
                const priceMin = $('#priceMin').val() || $('#priceMinMobile').val();
                const priceMax = $('#priceMax').val() || $('#priceMaxMobile').val();

                if (priceMin) params.append('price_min', priceMin);
                if (priceMax) params.append('price_max', priceMax);

                // Sort
                const sort = $('#sortBy').val() || $('#sortByMobile').val();
                if (sort && sort !== 'newest') {
                    params.append('sort', sort);
                }

                // Navigate to filtered URL
                const url = '{{ route('client.products.index') }}' + (params.toString() ? '?' + params.toString() :
                    '');
                window.location.href = url;
            }

            // ==================== CLEAR FILTERS ====================
            function clearAllFilters() {
                // Uncheck all checkboxes
                $('.filter-checkbox').prop('checked', false);

                // Clear price inputs
                $('#priceMin, #priceMax, #priceMinMobile, #priceMaxMobile').val('');

                // Reset sort
                $('#sortBy, #sortByMobile').val('newest');

                // Redirect to clean URL
                window.location.href = '{{ route('client.products.index') }}';
            }

            // ==================== INITIALIZE FILTERS FROM URL ====================
            function initializeFiltersFromURL() {
                const urlParams = new URLSearchParams(window.location.search);

                // Categories
                const categories = urlParams.getAll('categories[]');
                categories.forEach(cat => {
                    $(`input[name="category[]"][value="${cat}"], input[name="category_mobile[]"][value="${cat}"]`)
                        .prop('checked', true);
                });

                // Brands
                const brands = urlParams.getAll('brands[]');
                brands.forEach(brand => {
                    $(`input[name="brand[]"][value="${brand}"], input[name="brand_mobile[]"][value="${brand}"]`)
                        .prop('checked', true);
                });

                // Rating
                const ratings = urlParams.getAll('rating[]');
                ratings.forEach(rating => {
                    $(`input[name="rating[]"][value="${rating}"], input[name="rating_mobile[]"][value="${rating}"]`)
                        .prop('checked', true);
                });

                // Price
                if (urlParams.has('price_min')) {
                    const priceMin = urlParams.get('price_min');
                    $('#priceMin, #priceMinMobile').val(priceMin);
                }
                if (urlParams.has('price_max')) {
                    const priceMax = urlParams.get('price_max');
                    $('#priceMax, #priceMaxMobile').val(priceMax);
                }

                // Sort
                if (urlParams.has('sort')) {
                    const sort = urlParams.get('sort');
                    $('#sortBy, #sortByMobile').val(sort);
                }
            }

            // ==================== ACTIVE FILTERS DISPLAY ====================
            function updateActiveFilters() {
                const container = $('#filterTags');
                container.empty();
                let hasFilters = false;

                // Category filters
                $('input[name="category[]"]:checked').each(function() {
                    hasFilters = true;
                    const label = $(this).siblings('.filter-label').text().trim();
                    addFilterTag('Danh mục', label, 'category', $(this).val());
                });

                // Brand filters
                $('input[name="brand[]"]:checked').each(function() {
                    hasFilters = true;
                    const label = $(this).siblings('.filter-label').text().trim();
                    addFilterTag('Thương hiệu', label, 'brand', $(this).val());
                });

                // Rating filters
                $('input[name="rating[]"]:checked').each(function() {
                    hasFilters = true;
                    const rating = $(this).val();
                    addFilterTag('Đánh giá', `${rating} sao trở lên`, 'rating', rating);
                });

                // Price filter
                const priceMin = $('#priceMin').val();
                const priceMax = $('#priceMax').val();
                if (priceMin || priceMax) {
                    hasFilters = true;
                    const priceText = formatPrice(priceMin) + ' - ' + formatPrice(priceMax);
                    addFilterTag('Giá', priceText, 'price', null);
                }

                $('#activeFilters').toggle(hasFilters);
            }

            function addFilterTag(type, label, filterType, value) {
                const tag = $(`
                    <span class="filter-tag" data-filter="${filterType}" data-value="${value || ''}">
                        <strong>${type}:</strong> ${label}
                        <span class="remove">×</span>
                    </span>
                `);
                $('#filterTags').append(tag);
            }

            function formatPrice(price) {
                if (!price) return '0đ';
                return parseInt(price).toLocaleString('vi-VN') + 'đ';
            }

            // Remove individual filter
            $(document).on('click', '.filter-tag .remove', function() {
                const tag = $(this).parent();
                const filterType = tag.data('filter');
                const value = tag.data('value');

                if (filterType === 'category') {
                    $(`input[name="category[]"][value="${value}"], input[name="category_mobile[]"][value="${value}"]`)
                        .prop('checked', false);
                } else if (filterType === 'brand') {
                    $(`input[name="brand[]"][value="${value}"], input[name="brand_mobile[]"][value="${value}"]`)
                        .prop('checked', false);
                } else if (filterType === 'rating') {
                    $(`input[name="rating[]"][value="${value}"], input[name="rating_mobile[]"][value="${value}"]`)
                        .prop('checked', false);
                } else if (filterType === 'price') {
                    $('#priceMin, #priceMax, #priceMinMobile, #priceMaxMobile').val('');
                }

                applyFilters();
            });

            // ==================== SYNC MOBILE & DESKTOP FILTERS ====================
            $('input[name="category_mobile[]"]').change(function() {
                const value = $(this).val();
                const isChecked = $(this).is(':checked');
                $(`input[name="category[]"][value="${value}"]`).prop('checked', isChecked);
            });

            $('input[name="category[]"]').change(function() {
                const value = $(this).val();
                const isChecked = $(this).is(':checked');
                $(`input[name="category_mobile[]"][value="${value}"]`).prop('checked', isChecked);
            });

            // Similar sync for brands and ratings
            $('input[name="brand_mobile[]"]').change(function() {
                const value = $(this).val();
                const isChecked = $(this).is(':checked');
                $(`input[name="brand[]"][value="${value}"]`).prop('checked', isChecked);
            });

            $('input[name="brand[]"]').change(function() {
                const value = $(this).val();
                const isChecked = $(this).is(':checked');
                $(`input[name="brand_mobile[]"][value="${value}"]`).prop('checked', isChecked);
            });

            $('input[name="rating_mobile[]"]').change(function() {
                const value = $(this).val();
                const isChecked = $(this).is(':checked');
                $(`input[name="rating[]"][value="${value}"]`).prop('checked', isChecked);
            });

            $('input[name="rating[]"]').change(function() {
                const value = $(this).val();
                const isChecked = $(this).is(':checked');
                $(`input[name="rating_mobile[]"][value="${value}"]`).prop('checked', isChecked);
            });

            // Sync price inputs
            $('#priceMin').on('input', function() {
                $('#priceMinMobile').val($(this).val());
            });

            $('#priceMinMobile').on('input', function() {
                $('#priceMin').val($(this).val());
            });

            $('#priceMax').on('input', function() {
                $('#priceMaxMobile').val($(this).val());
            });

            $('#priceMaxMobile').on('input', function() {
                $('#priceMax').val($(this).val());
            });

            // ==================== PRICE VALIDATION ====================
            $('#priceMin, #priceMax, #priceMinMobile, #priceMaxMobile').on('input', function() {
                const min = parseFloat($('#priceMin').val() || $('#priceMinMobile').val()) || 0;
                const max = parseFloat($('#priceMax').val() || $('#priceMaxMobile').val()) || Infinity;

                if (min > max && max !== Infinity) {
                    $(this).addClass('is-invalid');
                } else {
                    $('#priceMin, #priceMax, #priceMinMobile, #priceMaxMobile').removeClass('is-invalid');
                }
            });

            // ==================== ADD TO CART ====================
            $(document).on('click', '.add-to-cart-btn', function(e) {
                e.preventDefault();

                const btn = $(this);
                const productId = btn.data('product-id');
                const originalHtml = btn.html();

                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

                $.ajax({
                    url: `/client/cart/add/${productId}`,
                    method: 'POST',
                    data: {
                        quantity: 1,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast(response.message, 'success');

                            if (response.cart_count) {
                                $('.cart-count, .cart-badge').text(response.cart_count).show();
                            }
                        } else {
                            showToast(response.message || 'Có lỗi xảy ra', 'error');
                        }
                    },
                    error: function(xhr) {
                        let message = 'Có lỗi xảy ra, vui lòng thử lại!';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.status === 401) {
                            message = 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng';
                            setTimeout(() => {
                                window.location.href = '{{ route('login') }}';
                            }, 2000);
                        }

                        showToast(message, 'error');
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(originalHtml);
                    }
                });
            });

            // ==================== WISHLIST TOGGLE ====================
            $(document).on('click', '.wishlist-btn', function(e) {
                e.preventDefault();

                const btn = $(this);
                const productId = btn.data('product-id');
                const icon = btn.find('i');

                $.ajax({
                    url: `/client/wishlist/toggle/${productId}`,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            icon.toggleClass('far fas');
                            showToast(response.message, 'success');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 401) {
                            showToast('Vui lòng đăng nhập để thêm vào yêu thích', 'error');
                            setTimeout(() => {
                                window.location.href = '{{ route('login') }}';
                            }, 2000);
                        } else {
                            showToast('Có lỗi xảy ra', 'error');
                        }
                    }
                });
            });

            // ==================== LOADING OVERLAY ====================
            function showLoading() {
                $('#loadingOverlay').addClass('active');
            }

            function hideLoading() {
                $('#loadingOverlay').removeClass('active');
            }

            // ==================== TOAST NOTIFICATIONS ====================
            function showToast(message, type = 'info') {
                // Using Toastr if available
                if (typeof toastr !== 'undefined') {
                    toastr.options = {
                        closeButton: true,
                        progressBar: true,
                        positionClass: 'toast-top-right',
                        timeOut: 3000,
                        showMethod: 'fadeIn',
                        hideMethod: 'fadeOut'
                    };
                    toastr[type](message);
                    return;
                }

                // Using SweetAlert2 if available
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: type === 'error' ? 'error' : 'success',
                        title: type === 'error' ? 'Lỗi!' : 'Thành công!',
                        text: message,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
                    return;
                }

                // Fallback: Bootstrap alert
                const alertClass = type === 'error' ? 'danger' : 'success';
                const icon = type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle';
                const alertHtml = `
                    <div class="alert alert-${alertClass} alert-dismissible fade show position-fixed top-0 end-0 m-3"
                         style="z-index: 9999; min-width: 300px; box-shadow: var(--shadow-lg);" role="alert">
                        <i class="fas ${icon} me-2"></i>${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                $('body').append(alertHtml);

                setTimeout(() => {
                    $('.alert').alert('close');
                }, 3000);
            }

            // ==================== SMOOTH SCROLL TO TOP ====================
            $(window).scroll(function() {
                if ($(this).scrollTop() > 300) {
                    $('.mobile-filter-btn').css('bottom', '80px');
                } else {
                    $('.mobile-filter-btn').css('bottom', '20px');
                }
            });
        });
    </script>
@endpush
