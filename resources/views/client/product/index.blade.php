@extends('client.layouts.master')

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
@endpush --}}
