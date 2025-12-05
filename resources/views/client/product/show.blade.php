{{-- @extends('client.layouts.master')

@section('title', $product->name ?? 'Chi tiết sản phẩm')

@push('styles')
    <style>
        .product-detail {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .product-images {
            position: sticky;
            top: 100px;
        }

        .main-image {
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 20px;
            position: relative;
            padding-top: 100%;
            background: #f8fafc;
        }

        .main-image img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .thumbnail-images {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }

        .thumbnail {
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            border: 3px solid transparent;
            transition: all 0.3s;
            padding-top: 100%;
            position: relative;
        }

        .thumbnail img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .thumbnail.active {
            border-color: var(--primary-color);
        }

        .thumbnail:hover {
            transform: scale(1.05);
        }

        .product-info {
            padding-left: 40px;
        }

        .product-badge-group {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .product-name {
            font-size: 32px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 15px;
            line-height: 1.3;
        }

        .product-meta {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
        }

        .rating-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .rating-stars {
            color: #fbbf24;
            font-size: 18px;
        }

        .rating-value {
            font-weight: 600;
            color: #1e293b;
        }

        .reviews-link {
            color: #3b82f6;
            text-decoration: none;
        }

        .product-price-section {
            background: #f8fafc;
            padding: 25px;
            border-radius: 16px;
            margin-bottom: 30px;
        }

        .current-price {
            font-size: 40px;
            font-weight: 800;
            color: #ef4444;
            margin-right: 15px;
        }

        .original-price {
            font-size: 24px;
            color: #94a3b8;
            text-decoration: line-through;
            margin-right: 10px;
        }

        .discount-badge {
            display: inline-block;
            background: #ef4444;
            color: white;
            padding: 8px 15px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 16px;
        }

        .stock-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            margin-top: 15px;
        }

        .in-stock {
            background: #d1fae5;
            color: #065f46;
        }

        .out-of-stock {
            background: #fee2e2;
            color: #991b1b;
        }

        .variant-section {
            margin-bottom: 30px;
        }

        .variant-title {
            font-weight: 600;
            margin-bottom: 15px;
            color: #334155;
        }

        .variant-options {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .variant-option {
            padding: 12px 24px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
            background: white;
        }

        .variant-option:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .variant-option.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .variant-option.disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .quantity-selector {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .quantity-label {
            font-weight: 600;
            color: #334155;
        }

        .quantity-control {
            display: flex;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
        }

        .qty-btn {
            width: 45px;
            height: 45px;
            border: none;
            background: #f8fafc;
            color: #475569;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .qty-btn:hover {
            background: var(--primary-color);
            color: white;
        }

        .qty-input {
            width: 70px;
            text-align: center;
            border: none;
            font-weight: 600;
            font-size: 18px;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
        }

        .btn-add-cart {
            flex: 2;
            padding: 18px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 700;
            transition: all 0.3s;
        }

        .btn-add-cart:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(37, 99, 235, 0.4);
        }

        .btn-buy-now {
            flex: 2;
            padding: 18px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 700;
            transition: all 0.3s;
        }

        .btn-buy-now:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(239, 68, 68, 0.4);
        }

        .btn-wishlist {
            width: 60px;
            height: 60px;
            border: 2px solid #e2e8f0;
            background: white;
            border-radius: 12px;
            color: #64748b;
            font-size: 24px;
            transition: all 0.3s;
        }

        .btn-wishlist:hover,
        .btn-wishlist.active {
            background: #fef2f2;
            border-color: #ef4444;
            color: #ef4444;
        }

        .product-features {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            padding: 25px;
            background: #f8fafc;
            border-radius: 12px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .feature-icon {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
        }

        .product-tabs {
            margin-top: 60px;
        }

        .nav-tabs {
            border: none;
            gap: 10px;
        }

        .nav-tabs .nav-link {
            border: none;
            background: white;
            color: #64748b;
            font-weight: 600;
            padding: 15px 30px;
            border-radius: 12px;
            transition: all 0.3s;
        }

        .nav-tabs .nav-link.active {
            background: var(--primary-color);
            color: white;
        }

        .tab-content {
            background: white;
            padding: 40px;
            border-radius: 16px;
            margin-top: 20px;
        }

        .specifications-table {
            width: 100%;
        }

        .specifications-table tr {
            border-bottom: 1px solid #e2e8f0;
        }

        .specifications-table td {
            padding: 15px 10px;
        }

        .specifications-table td:first-child {
            font-weight: 600;
            color: #475569;
            width: 30%;
        }

        .review-item {
            padding: 25px;
            background: #f8fafc;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .reviewer-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .reviewer-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .related-products {
            margin-top: 80px;
        }
    </style>
@endpush

@section('content')
    <div class="container py-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('client.products.index') }}">Sản phẩm</a></li>
                <li class="breadcrumb-item active">{{ $product->name ?? '' }}</li>
            </ol>
        </nav>

        <div class="product-detail">
            <div class="row">
                <!-- Product Images -->
                <div class="col-lg-5">
                    <div class="product-images">
                        <div class="main-image" id="mainImage">
                            <img src="{{ $product->main_image ?? 'https://via.placeholder.com/600' }}"
                                alt="{{ $product->name }}">
                        </div>
                        <div class="thumbnail-images">
                            @foreach ($product->images ?? [] as $index => $image)
                                <div class="thumbnail {{ $index === 0 ? 'active' : '' }}" data-image="{{ $image }}">
                                    <img src="{{ $image }}" alt="Thumbnail {{ $index + 1 }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="col-lg-7">
                    <div class="product-info">
                        <!-- Badges -->
                        <div class="product-badge-group">
                            @if ($product->is_new ?? false)
                                <span class="badge-new">MỚI</span>
                            @endif
                            @if ($product->discount > 0)
                                <span class="badge-sale">SALE</span>
                            @endif
                        </div>

                        <!-- Product Name -->
                        <h1 class="product-name">{{ $product->name ?? 'Tên sản phẩm' }}</h1>

                        <!-- Product Meta -->
                        <div class="product-meta">
                            <div class="rating-section">
                                <div class="rating-stars">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star{{ $i <= ($product->rating ?? 5) ? '' : '-o' }}"></i>
                                    @endfor
                                </div>
                                <span class="rating-value">{{ $product->rating ?? 5.0 }}</span>
                            </div>
                            <a href="#reviews" class="reviews-link">
                                ({{ $product->reviews_count ?? 0 }} đánh giá)
                            </a>
                            <span class="text-muted">|</span>
                            <span>Đã bán: <strong>{{ $product->sold ?? 0 }}</strong></span>
                        </div>

                        <!-- Price Section -->
                        <div class="product-price-section">
                            <div class="d-flex align-items-center flex-wrap">
                                <span class="current-price">{{ number_format($product->sale_price ?? 999000) }}đ</span>
                                @if (isset($product->price) && $product->sale_price < $product->price)
                                    <span class="original-price">{{ number_format($product->price) }}đ</span>
                                    <span
                                        class="discount-badge">-{{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%</span>
                                @endif
                            </div>
                            <div class="stock-status {{ ($product->stock ?? 0) > 0 ? 'in-stock' : 'out-of-stock' }}">
                                <i class="fas fa-check-circle"></i>
                                {{ ($product->stock ?? 0) > 0 ? 'Còn hàng' : 'Hết hàng' }}
                            </div>
                        </div>

                        <!-- Variants -->
                        @if (isset($product->variants) && count($product->variants) > 0)
                            <div class="variant-section">
                                <div class="variant-title">Màu sắc:</div>
                                <div class="variant-options">
                                    @foreach ($product->variants as $variant)
                                        <button class="variant-option {{ $loop->first ? 'active' : '' }}"
                                            data-variant-id="{{ $variant->id }}">
                                            {{ $variant->name }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Size -->
                        @if (isset($product->sizes) && count($product->sizes) > 0)
                            <div class="variant-section">
                                <div class="variant-title">Kích thước:</div>
                                <div class="variant-options">
                                    @foreach ($product->sizes as $size)
                                        <button class="variant-option" data-size-id="{{ $size->id }}">
                                            {{ $size->name }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Quantity -->
                        <div class="quantity-selector">
                            <span class="quantity-label">Số lượng:</span>
                            <div class="quantity-control">
                                <button class="qty-btn" id="decreaseQty">-</button>
                                <input type="number" class="qty-input" value="1" min="1"
                                    max="{{ $product->stock ?? 999 }}" id="quantity">
                                <button class="qty-btn" id="increaseQty">+</button>
                            </div>
                            <span class="text-muted">{{ $product->stock ?? 999 }} sản phẩm có sẵn</span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <button class="btn-add-cart" id="addToCart">
                                <i class="fas fa-shopping-cart me-2"></i>Thêm vào giỏ hàng
                            </button>
                            <button class="btn-buy-now" id="buyNow">
                                Mua ngay
                            </button>
                            <button class="btn-wishlist {{ $product->in_wishlist ?? false ? 'active' : '' }}"
                                id="toggleWishlist">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>

                        <!-- Features -->
                        <div class="product-features">
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-shipping-fast"></i>
                                </div>
                                <div>
                                    <strong>Giao hàng nhanh</strong><br>
                                    <small class="text-muted">Trong 24h</small>
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div>
                                    <strong>Bảo hành 12 tháng</strong><br>
                                    <small class="text-muted">Chính hãng</small>
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-undo"></i>
                                </div>
                                <div>
                                    <strong>Đổi trả miễn phí</strong><br>
                                    <small class="text-muted">Trong 7 ngày</small>
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-headset"></i>
                                </div>
                                <div>
                                    <strong>Hỗ trợ 24/7</strong><br>
                                    <small class="text-muted">Tư vấn miễn phí</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Tabs -->
        <div class="product-tabs">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#description">
                        Mô tả sản phẩm
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#specifications">
                        Thông số kỹ thuật
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#reviews">
                        Đánh giá ({{ $product->reviews_count ?? 0 }})
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Description -->
                <div class="tab-pane fade show active" id="description">
                    <div class="product-description">
                        {!! $product->description ?? '<p>Mô tả chi tiết sản phẩm...</p>' !!}
                    </div>
                </div>

                <!-- Specifications -->
                <div class="tab-pane fade" id="specifications">
                    <table class="specifications-table">
                        @foreach ($product->specifications ?? [] as $key => $value)
                            <tr>
                                <td>{{ $key }}</td>
                                <td>{{ $value }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>

                <!-- Reviews -->
                <div class="tab-pane fade" id="reviews">
                    @forelse($product->reviews ?? [] as $review)
                        <div class="review-item">
                            <div class="review-header">
                                <div class="reviewer-info">
                                    <div class="reviewer-avatar">
                                        {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong>{{ $review->user->name ?? 'Anonymous' }}</strong>
                                        <div class="rating-stars">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star{{ $i <= $review->rating ? '' : '-o' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                            </div>
                            <p>{{ $review->comment }}</p>
                        </div>
                    @empty
                        <p class="text-center text-muted">Chưa có đánh giá nào</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <div class="related-products">
            <div class="section-header text-center">
                <div class="section-badge">SẢN PHẨM LIÊN QUAN</div>
                <h2 class="section-title">Có Thể Bạn Quan Tâm</h2>
            </div>

            <div class="row g-4 mt-4">
                @foreach ($relatedProducts ?? [] as $relatedProduct)
                    <div class="col-lg-3 col-md-4 col-6">
                        @include('client.components.product-card', ['product' => $relatedProduct])
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let selectedVariantId = null;
        let selectedSizeId = null;

        $(document).ready(function() {
            // ====== IMAGE GALLERY ======
            $('.thumbnail').click(function() {
                $('.thumbnail').removeClass('active');
                $(this).addClass('active');
                const imageUrl = $(this).data('image');
                $('#mainImage img').attr('src', imageUrl);
            });

            // ====== QUANTITY CONTROLS ======
            $('#increaseQty').click(function() {
                const input = $('#quantity');
                const max = parseInt(input.attr('max'));
                const current = parseInt(input.val());
                if (current < max) {
                    input.val(current + 1);
                }
            });

            $('#decreaseQty').click(function() {
                const input = $('#quantity');
                const current = parseInt(input.val());
                if (current > 1) {
                    input.val(current - 1);
                }
            });

            // ====== VARIANT SELECTION ======
            $('.variant-option').click(function() {
                if ($(this).hasClass('disabled')) return;

                $(this).siblings().removeClass('active');
                $(this).addClass('active');

                selectedVariantId = $(this).data('variant-id');
                selectedSizeId = $(this).data('size-id');
            });

            // ====== ADD TO CART ======
            $('#addToCart').click(function() {
                const btn = $(this);
                const productId = {{ $product->id }};
                const quantity = parseInt($('#quantity').val());

                // Validate
                if (quantity < 1) {
                    showToast('Vui lòng chọn số lượng hợp lệ', 'error');
                    return;
                }

                // Disable button
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...');

                $.ajax({
                    url: `/client/cart/add/${productId}`, // SỬA: Truyền productId vào URL
                    method: 'POST',
                    data: {
                        quantity: quantity,
                        variant_id: selectedVariantId,
                        size_id: selectedSizeId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast(response.message, 'success');

                            // Update cart count
                            if (response.cart_count && $('.cart-count').length) {
                                $('.cart-count').text(response.cart_count);
                            }
                        } else {
                            showToast(response.message || 'Có lỗi xảy ra', 'error');
                        }
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message ||
                            'Có lỗi xảy ra, vui lòng thử lại!';
                        showToast(message, 'error');
                    },
                    complete: function() {
                        // Re-enable button
                        btn.prop('disabled', false).html(
                            '<i class="fas fa-shopping-cart me-2"></i>Thêm vào giỏ hàng');
                    }
                });
            });

            // ====== BUY NOW ======
            $('#buyNow').click(function() {
                const btn = $(this);
                const productId = {{ $product->id }};
                const quantity = parseInt($('#quantity').val());

                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...');

                $.ajax({
                    url: `/client/cart/add/${productId}`,
                    method: 'POST',
                    data: {
                        quantity: quantity,
                        variant_id: selectedVariantId,
                        size_id: selectedSizeId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            window.location.href = '{{ route('client.checkout.index') }}';
                        } else {
                            showToast(response.message, 'error');
                            btn.prop('disabled', false).html('Mua ngay');
                        }
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message || 'Có lỗi xảy ra!';
                        showToast(message, 'error');
                        btn.prop('disabled', false).html('Mua ngay');
                    }
                });
            });

            // ====== TOGGLE WISHLIST ======
            $('#toggleWishlist').click(function() {
                const btn = $(this);
                const productId = {{ $product->id }};

                $.ajax({
                    url: `/client/wishlist/toggle/${productId}`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            btn.toggleClass('active');
                            showToast(response.message, 'success');
                        }
                    },
                    error: function() {
                        showToast('Có lỗi xảy ra', 'error');
                    }
                });
            });
        });

        // ====== TOAST NOTIFICATION ======
        function showToast(message, type = 'info') {
            // Nếu bạn dùng Toastr
            if (typeof toastr !== 'undefined') {
                toastr[type](message);
                return;
            }

            // Nếu dùng SweetAlert2
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: type === 'error' ? 'error' : 'success',
                    title: type === 'error' ? 'Lỗi' : 'Thành công',
                    text: message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
                return;
            }

            // Fallback: alert
            alert(message);
        }
    </script>
@endpush --}}




@extends('client.layouts.master')

@section('title', $product->name ?? 'Chi tiết sản phẩm')

@push('styles')
    <style>
        :root {
            --primary: #ee4d2d;
            --primary-hover: #d73211;
            --secondary: #1890ff;
            --success: #00b894;
            --warning: #fdcb6e;
            --danger: #d63031;
            --dark: #2d3436;
            --gray: #636e72;
            --light-gray: #f5f5f5;
            --border: #e8e8e8;
        }

        body {
            background: #f5f5f5;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        /* ==================== PRODUCT CONTAINER ==================== */
        .product-container {
            background: white;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        /* ==================== IMAGE GALLERY ==================== */
        .image-gallery {
            padding: 15px;
        }

        .main-image-wrapper {
            position: relative;
            background: white;
            border: 1px solid var(--border);
            border-radius: 4px;
            overflow: hidden;
            aspect-ratio: 1;
            margin-bottom: 12px;
        }

        .main-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            cursor: zoom-in;
            transition: transform 0.3s ease;
        }

        .main-image:hover {
            transform: scale(1.05);
        }

        .image-badges {
            position: absolute;
            top: 10px;
            left: 10px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            z-index: 2;
        }

        .badge-item {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 2px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-new {
            background: var(--success);
            color: white;
        }

        .badge-sale {
            background: var(--primary);
            color: white;
        }

        .badge-hot {
            background: var(--warning);
            color: var(--dark);
        }

        /* Thumbnail Grid */
        .thumbnail-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
        }

        .thumbnail-item {
            aspect-ratio: 1;
            border: 2px solid transparent;
            border-radius: 4px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.2s;
        }

        .thumbnail-item:hover {
            border-color: var(--primary);
        }

        .thumbnail-item.active {
            border-color: var(--primary);
        }

        .thumbnail-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* ==================== PRODUCT INFO ==================== */
        .product-info {
            padding: 20px 24px;
        }

        /* Brand & Category */
        .product-meta-top {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
            font-size: 13px;
        }

        .meta-link {
            color: var(--secondary);
            text-decoration: none;
            transition: opacity 0.2s;
        }

        .meta-link:hover {
            opacity: 0.8;
        }

        .meta-divider {
            color: var(--border);
        }

        /* Product Name */
        .product-name {
            font-size: 20px;
            font-weight: 500;
            line-height: 1.4;
            color: var(--dark);
            margin-bottom: 16px;
        }

        /* Rating & Stats */
        .product-stats {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 16px;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .rating-number {
            color: var(--primary);
            font-size: 16px;
            font-weight: 500;
            text-decoration: underline;
            cursor: pointer;
        }

        .rating-stars {
            display: flex;
            gap: 2px;
        }

        .rating-stars i {
            color: var(--warning);
            font-size: 14px;
        }

        .stat-divider {
            width: 1px;
            height: 16px;
            background: var(--border);
        }

        .stat-label {
            color: var(--gray);
            font-size: 14px;
        }

        .stat-value {
            color: var(--dark);
            font-weight: 500;
        }

        /* Price Section */
        .price-section {
            background: #fafafa;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 24px;
        }

        .price-wrapper {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .original-price {
            font-size: 16px;
            color: var(--gray);
            text-decoration: line-through;
        }

        .current-price {
            font-size: 32px;
            color: var(--primary);
            font-weight: 500;
        }

        .discount-badge {
            background: var(--primary);
            color: white;
            padding: 4px 8px;
            border-radius: 2px;
            font-size: 12px;
            font-weight: 600;
        }

        .price-note {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 8px;
            font-size: 13px;
            color: var(--gray);
        }

        /* Variants Section */
        .variant-section {
            padding: 16px 0;
            border-bottom: 1px solid var(--border);
            margin-bottom: 16px;
        }

        .variant-label {
            color: var(--gray);
            font-size: 14px;
            margin-bottom: 12px;
        }

        .variant-options {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .variant-option {
            min-width: 80px;
            padding: 10px 16px;
            border: 1px solid var(--border);
            border-radius: 2px;
            background: white;
            color: var(--dark);
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
            position: relative;
        }

        .variant-option:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .variant-option.active {
            border-color: var(--primary);
            color: var(--primary);
            background: #fff5f3;
        }

        .variant-option.disabled {
            opacity: 0.3;
            cursor: not-allowed;
            background: #f5f5f5;
        }

        .variant-option.disabled:hover {
            border-color: var(--border);
            color: var(--dark);
        }

        /* Color variants */
        .color-variant {
            position: relative;
            width: 50px;
            height: 50px;
            border: 2px solid var(--border);
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.2s;
        }

        .color-variant.active {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px white, 0 0 0 4px var(--primary);
        }

        .color-swatch {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            overflow: hidden;
        }

        /* Quantity Section */
        .quantity-section {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 0;
            border-bottom: 1px solid var(--border);
            margin-bottom: 24px;
        }

        .quantity-label {
            color: var(--gray);
            font-size: 14px;
            min-width: 100px;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            border: 1px solid var(--border);
            border-radius: 2px;
            overflow: hidden;
        }

        .qty-btn {
            width: 36px;
            height: 36px;
            border: none;
            background: white;
            color: var(--gray);
            cursor: pointer;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qty-btn:hover:not(:disabled) {
            background: #f5f5f5;
            color: var(--dark);
        }

        .qty-btn:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }

        .qty-input {
            width: 60px;
            height: 36px;
            border: none;
            border-left: 1px solid var(--border);
            border-right: 1px solid var(--border);
            text-align: center;
            font-size: 14px;
            font-weight: 500;
        }

        .stock-info {
            color: var(--gray);
            font-size: 13px;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
        }

        .btn-action {
            flex: 1;
            height: 48px;
            border: none;
            border-radius: 2px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-add-cart {
            background: #fff0ed;
            color: var(--primary);
            border: 1px solid var(--primary);
        }

        .btn-add-cart:hover {
            background: var(--primary);
            color: white;
        }

        .btn-buy-now {
            background: var(--primary);
            color: white;
        }

        .btn-buy-now:hover {
            background: var(--primary-hover);
        }

        .btn-wishlist {
            width: 48px;
            background: white;
            border: 1px solid var(--border);
            color: var(--gray);
        }

        .btn-wishlist:hover,
        .btn-wishlist.active {
            color: var(--primary);
            border-color: var(--primary);
        }

        /* Features Grid */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            padding: 16px;
            background: #fafafa;
            border-radius: 4px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: var(--gray);
        }

        .feature-icon {
            color: var(--secondary);
            font-size: 16px;
        }

        /* ==================== SELLER INFO ==================== */
        .seller-info {
            background: white;
            border-radius: 4px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .seller-header {
            display: flex;
            align-items: center;
            gap: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 16px;
        }

        .seller-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid var(--border);
        }

        .seller-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .seller-details h4 {
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 4px;
        }

        .seller-badges {
            display: flex;
            gap: 8px;
        }

        .seller-badge {
            padding: 2px 8px;
            background: #e8f5e9;
            color: var(--success);
            border-radius: 2px;
            font-size: 11px;
            font-weight: 500;
        }

        .seller-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 16px;
        }

        .seller-stat {
            text-align: center;
        }

        .seller-stat-label {
            font-size: 12px;
            color: var(--gray);
            margin-bottom: 4px;
        }

        .seller-stat-value {
            font-size: 16px;
            font-weight: 500;
            color: var(--dark);
        }

        .seller-actions {
            display: flex;
            gap: 8px;
        }

        .btn-seller {
            flex: 1;
            height: 40px;
            border: 1px solid var(--border);
            background: white;
            border-radius: 2px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 14px;
        }

        .btn-seller:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        /* ==================== PRODUCT DETAILS TABS ==================== */
        .product-details {
            background: white;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .tabs-nav {
            display: flex;
            border-bottom: 2px solid var(--border);
        }

        .tab-link {
            padding: 16px 24px;
            color: var(--gray);
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            transition: all 0.2s;
        }

        .tab-link:hover {
            color: var(--primary);
        }

        .tab-link.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }

        .tab-content {
            padding: 24px;
        }

        .tab-pane {
            display: none;
        }

        .tab-pane.active {
            display: block;
        }

        /* Description */
        .product-description {
            line-height: 1.8;
            color: var(--dark);
        }

        .product-description img {
            max-width: 100%;
            height: auto;
            margin: 16px 0;
        }

        /* Specifications */
        .specifications-table {
            width: 100%;
            border-collapse: collapse;
        }

        .specifications-table tr {
            border-bottom: 1px solid var(--border);
        }

        .specifications-table tr:last-child {
            border-bottom: none;
        }

        .specifications-table td {
            padding: 16px 12px;
            font-size: 14px;
        }

        .specifications-table td:first-child {
            width: 200px;
            color: var(--gray);
            background: #fafafa;
        }

        .specifications-table td:last-child {
            color: var(--dark);
        }

        /* ==================== REVIEWS SECTION ==================== */
        .reviews-summary {
            display: flex;
            gap: 40px;
            padding: 24px;
            background: #fffbf8;
            border-radius: 4px;
            margin-bottom: 24px;
        }

        .rating-overview {
            text-align: center;
            padding-right: 40px;
            border-right: 1px solid var(--border);
        }

        .rating-score {
            font-size: 48px;
            font-weight: 500;
            color: var(--primary);
            margin-bottom: 8px;
        }

        .rating-out {
            color: var(--gray);
            font-size: 14px;
            margin-bottom: 12px;
        }

        .rating-bars {
            flex: 1;
        }

        .rating-bar-item {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
        }

        .rating-bar-label {
            display: flex;
            align-items: center;
            gap: 4px;
            min-width: 80px;
            font-size: 13px;
            color: var(--gray);
        }

        .rating-bar {
            flex: 1;
            height: 8px;
            background: #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
        }

        .rating-bar-fill {
            height: 100%;
            background: var(--warning);
            transition: width 0.3s;
        }

        .rating-bar-count {
            min-width: 40px;
            text-align: right;
            font-size: 13px;
            color: var(--gray);
        }

        .reviews-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .review-item {
            padding: 20px;
            border: 1px solid var(--border);
            border-radius: 4px;
        }

        .review-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .reviewer-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 500;
        }

        .reviewer-info {
            flex: 1;
        }

        .reviewer-name {
            font-weight: 500;
            margin-bottom: 4px;
        }

        .review-date {
            font-size: 12px;
            color: var(--gray);
        }

        .review-rating {
            display: flex;
            gap: 2px;
            margin-bottom: 8px;
        }

        .review-content {
            line-height: 1.6;
            color: var(--dark);
            margin-bottom: 12px;
        }

        .review-images {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .review-image {
            width: 80px;
            height: 80px;
            border-radius: 4px;
            overflow: hidden;
            cursor: pointer;
            border: 1px solid var(--border);
        }

        .review-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* ==================== RELATED PRODUCTS ==================== */
        .related-products {
            background: white;
            border-radius: 4px;
            padding: 24px;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border);
        }

        /* ==================== RESPONSIVE ==================== */
        @media (max-width: 991px) {
            .product-container {
                margin-bottom: 12px;
            }

            .product-info,
            .seller-info {
                padding: 16px;
            }

            .product-name {
                font-size: 18px;
            }

            .current-price {
                font-size: 28px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn-wishlist {
                width: 100%;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .seller-stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .reviews-summary {
                flex-direction: column;
                gap: 20px;
            }

            .rating-overview {
                border-right: none;
                border-bottom: 1px solid var(--border);
                padding-right: 0;
                padding-bottom: 20px;
            }
        }

        @media (max-width: 575px) {
            .thumbnail-grid {
                grid-template-columns: repeat(4, 1fr);
            }

            .variant-options {
                gap: 8px;
            }

            .variant-option {
                min-width: 60px;
                padding: 8px 12px;
                font-size: 13px;
            }

            .quantity-section {
                flex-wrap: wrap;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container py-3">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0" style="background: transparent; padding: 0;">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color: var(--secondary);">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('client.products.index') }}"
                        style="color: var(--secondary);">Sản phẩm</a></li>
                @if (isset($product->category))
                    <li class="breadcrumb-item"><a
                            href="{{ route('client.products.index', ['category' => $product->category->id]) }}"
                            style="color: var(--secondary);">{{ $product->category->name }}</a></li>
                @endif
                <li class="breadcrumb-item active">{{ Str::limit($product->name ?? '', 50) }}</li>
            </ol>
        </nav>

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Product Container -->
                <div class="product-container">
                    <div class="row g-0">
                        <!-- Image Gallery -->
                        <div class="col-md-5">
                            <div class="image-gallery">
                                <div class="main-image-wrapper">
                                    <div class="image-badges">
                                        @if ($product->is_new ?? false)
                                            <span class="badge-item badge-new">Mới</span>
                                        @endif
                                        @if (isset($product->discount) && $product->discount > 0)
                                            <span class="badge-item badge-sale">-{{ $product->discount }}%</span>
                                        @endif
                                        @if ($product->is_hot ?? false)
                                            <span class="badge-item badge-hot">Hot</span>
                                        @endif
                                    </div>
                                    <img src="{{ $product->main_image ?? ($product->image ?? 'https://via.placeholder.com/600') }}"
                                        alt="{{ $product->name }}" class="main-image" id="mainImage">
                                </div>

                                <div class="thumbnail-grid">
                                    @php
                                        $images = $product->images ?? [$product->main_image ?? $product->image];
                                        if (is_string($images)) {
                                            $images = json_decode($images, true) ?? [$images];
                                        }
                                    @endphp
                                    @foreach ($images as $index => $image)
                                        <div class="thumbnail-item {{ $index === 0 ? 'active' : '' }}"
                                            data-image="{{ $image }}">
                                            <img src="{{ $image }}" alt="Thumbnail {{ $index + 1 }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Product Info -->
                        <div class="col-md-7">
                            <div class="product-info">
                                <!-- Brand & Category -->
                                <div class="product-meta-top">
                                    @if (isset($product->brand))
                                        <a href="{{ route('client.products.index', ['brand' => $product->brand->id]) }}"
                                            class="meta-link">
                                            {{ $product->brand->name }}
                                        </a>
                                        <span class="meta-divider">|</span>
                                    @endif
                                    @if (isset($product->category))
                                        <a href="{{ route('client.products.index', ['category' => $product->category->id]) }}"
                                            class="meta-link">
                                            {{ $product->category->name }}
                                        </a>
                                    @endif
                                </div>

                                <!-- Product Name -->
                                <h1 class="product-name">{{ $product->name ?? 'Tên sản phẩm' }}</h1>

                                <!-- Rating & Stats -->
                                <div class="product-stats">
                                    <div class="stat-item">
                                        <span
                                            class="rating-number">{{ number_format($product->avg_rating ?? 4.8, 1) }}</span>
                                        <div class="rating-stars">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i
                                                    class="fas fa-star{{ $i <= round($product->avg_rating ?? 4.8) ? '' : '-o' }}"></i>
                                            @endfor
                                        </div>
                                    </div>

                                    <div class="stat-divider"></div>

                                    <div class="stat-item">
                                        <span class="stat-label">Đánh giá:</span>
                                        <span class="stat-value">{{ $product->reviews_count ?? 0 }}</span>
                                    </div>

                                    <div class="stat-divider"></div>

                                    <div class="stat-item">
                                        <span class="stat-label">Đã bán:</span>
                                        <span class="stat-value">{{ number_format($product->sold_count ?? 0) }}</span>
                                    </div>
                                </div>

                                <!-- Price Section -->
                                <div class="price-section">
                                    <div class="price-wrapper">
                                        @if (isset($product->price) && isset($product->sale_price) && $product->sale_price < $product->price)
                                            <span class="original-price">₫{{ number_format($product->price) }}</span>
                                            <span class="current-price">₫{{ number_format($product->sale_price) }}</span>
                                            <span
                                                class="discount-badge">{{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%
                                                GIẢM</span>
                                        @else
                                            <span
                                                class="current-price">₫{{ number_format($product->sale_price ?? ($product->price ?? 0)) }}</span>
                                        @endif
                                    </div>
                                    <div class="price-note">
                                        <i class="fas fa-shield-alt"></i>
                                        <span>Cam kết chính hãng 100%</span>
                                    </div>
                                </div>

                                <!-- Variants -->
                                @if (isset($product->variants) && count($product->variants) > 0)
                                    <div class="variant-section">
                                        <div class="variant-label">Màu sắc / Phiên bản</div>
                                        <div class="variant-options" id="variantOptions">
                                            @foreach ($product->variants as $variant)
                                                <button
                                                    class="variant-option {{ $loop->first ? 'active' : '' }} {{ ($variant->stock ?? 0) <= 0 ? 'disabled' : '' }}"
                                                    data-variant-id="{{ $variant->id }}"
                                                    data-price="{{ $variant->price ?? $product->sale_price }}"
                                                    data-stock="{{ $variant->stock ?? 0 }}"
                                                    {{ ($variant->stock ?? 0) <= 0 ? 'disabled' : '' }}>
                                                    {{ $variant->name }}
                                                    @if (($variant->stock ?? 0) <= 0)
                                                        <br><small style="color: var(--danger);">Hết hàng</small>
                                                    @endif
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Size Options -->
                                @if (isset($product->sizes) && count($product->sizes) > 0)
                                    <div class="variant-section">
                                        <div class="variant-label">Kích thước</div>
                                        <div class="variant-options" id="sizeOptions">
                                            @foreach ($product->sizes as $size)
                                                <button
                                                    class="variant-option {{ ($size->stock ?? 0) <= 0 ? 'disabled' : '' }}"
                                                    data-size-id="{{ $size->id }}"
                                                    {{ ($size->stock ?? 0) <= 0 ? 'disabled' : '' }}>
                                                    {{ $size->name }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Quantity -->
                                <div class="quantity-section">
                                    <span class="quantity-label">Số lượng</span>
                                    <div class="quantity-control">
                                        <button class="qty-btn" id="decreaseQty" type="button">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" class="qty-input" id="quantity" value="1" min="1"
                                            max="{{ $product->stock ?? 999 }}">
                                        <button class="qty-btn" id="increaseQty" type="button">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <span class="stock-info">{{ number_format($product->stock ?? 999) }} sản phẩm có
                                        sẵn</span>
                                </div>

                                <!-- Action Buttons -->
                                <div class="action-buttons">
                                    <button class="btn-action btn-add-cart" id="addToCart">
                                        <i class="fas fa-shopping-cart"></i>
                                        Thêm vào giỏ hàng
                                    </button>
                                    <button class="btn-action btn-buy-now" id="buyNow">
                                        Mua ngay
                                    </button>
                                    <button
                                        class="btn-action btn-wishlist {{ $product->in_wishlist ?? false ? 'active' : '' }}"
                                        id="toggleWishlist">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                </div>

                                <!-- Features -->
                                <div class="features-grid">
                                    <div class="feature-item">
                                        <i class="fas fa-shield-alt feature-icon"></i>
                                        <span>Bảo hành chính hãng 12 tháng</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-truck feature-icon"></i>
                                        <span>Giao hàng toàn quốc</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-undo feature-icon"></i>
                                        <span>Đổi trả trong 7 ngày</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-credit-card feature-icon"></i>
                                        <span>Thanh toán linh hoạt</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Details Tabs -->
                <div class="product-details">
                    <div class="tabs-nav">
                        <div class="tab-link active" data-tab="description">Mô tả sản phẩm</div>
                        <div class="tab-link" data-tab="specifications">Thông số kỹ thuật</div>
                        <div class="tab-link" data-tab="reviews">Đánh giá ({{ $product->reviews_count ?? 0 }})</div>
                    </div>

                    <div class="tab-content">
                        <!-- Description Tab -->
                        <div class="tab-pane active" id="description">
                            <div class="product-description">
                                {!! $product->description ?? '<p>Mô tả chi tiết sản phẩm sẽ được cập nhật...</p>' !!}
                            </div>
                        </div>

                        <!-- Specifications Tab -->
                        <div class="tab-pane" id="specifications">
                            <table class="specifications-table">
                                @php
                                    $specs = $product->specifications ?? [
                                        'Thương hiệu' => $product->brand->name ?? 'Đang cập nhật',
                                        'Xuất xứ' => $product->origin ?? 'Đang cập nhật',
                                        'Bảo hành' => $product->warranty ?? '12 tháng',
                                        'Chất liệu' => $product->material ?? 'Đang cập nhật',
                                    ];
                                    if (is_string($specs)) {
                                        $specs = json_decode($specs, true) ?? [];
                                    }
                                @endphp
                                @foreach ($specs as $key => $value)
                                    <tr>
                                        <td>{{ $key }}</td>
                                        <td>{{ $value }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>

                        <!-- Reviews Tab -->
                        <div class="tab-pane" id="reviews">
                            <!-- Reviews Summary -->
                            <div class="reviews-summary">
                                <div class="rating-overview">
                                    <div class="rating-score">{{ number_format($product->avg_rating ?? 4.8, 1) }}</div>
                                    <div class="rating-out">trên 5</div>
                                    <div class="rating-stars">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star" style="color: var(--warning); font-size: 18px;"></i>
                                        @endfor
                                    </div>
                                </div>

                                <div class="rating-bars">
                                    @php
                                        $ratingDistribution = $product->rating_distribution ?? [
                                            5 => 70,
                                            4 => 20,
                                            3 => 7,
                                            2 => 2,
                                            1 => 1,
                                        ];
                                    @endphp
                                    @for ($i = 5; $i >= 1; $i--)
                                        <div class="rating-bar-item">
                                            <div class="rating-bar-label">
                                                {{ $i }} <i class="fas fa-star"
                                                    style="color: var(--warning);"></i>
                                            </div>
                                            <div class="rating-bar">
                                                <div class="rating-bar-fill"
                                                    style="width: {{ $ratingDistribution[$i] ?? 0 }}%"></div>
                                            </div>
                                            <span class="rating-bar-count">{{ $ratingDistribution[$i] ?? 0 }}%</span>
                                        </div>
                                    @endfor
                                </div>
                            </div>

                            <!-- Reviews List -->
                            <div class="reviews-list">
                                @forelse($product->reviews ?? [] as $review)
                                    <div class="review-item">
                                        <div class="review-header">
                                            <div class="reviewer-avatar">
                                                {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <div class="reviewer-info">
                                                <div class="reviewer-name">{{ $review->user->name ?? 'Người dùng' }}</div>
                                                <div class="review-date">
                                                    {{ $review->created_at->format('d/m/Y H:i') ?? '' }}</div>
                                            </div>
                                        </div>
                                        <div class="review-rating">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star"
                                                    style="color: {{ $i <= ($review->rating ?? 5) ? 'var(--warning)' : '#e0e0e0' }};"></i>
                                            @endfor
                                        </div>
                                        <div class="review-content">
                                            {{ $review->comment ?? 'Sản phẩm rất tốt, đáng để mua!' }}
                                        </div>
                                        @if (isset($review->images) && count($review->images) > 0)
                                            <div class="review-images">
                                                @foreach ($review->images as $image)
                                                    <div class="review-image">
                                                        <img src="{{ $image }}" alt="Review image">
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-center text-muted py-5">Chưa có đánh giá nào cho sản phẩm này</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Seller Info -->
                <div class="seller-info">
                    <div class="seller-header">
                        <div class="seller-avatar">
                            <img src="{{ $product->seller->avatar ?? 'https://ui-avatars.com/api/?name=Shop' }}"
                                alt="Seller">
                        </div>
                        <div class="seller-details">
                            <h4>{{ $product->seller->name ?? 'ShopX Official' }}</h4>
                            <div class="seller-badges">
                                <span class="seller-badge">Chính hãng</span>
                            </div>
                        </div>
                    </div>

                    <div class="seller-stats">
                        <div class="seller-stat">
                            <div class="seller-stat-label">Đánh giá</div>
                            <div class="seller-stat-value">{{ number_format($product->seller->rating ?? 4.9, 1) }}</div>
                        </div>
                        <div class="seller-stat">
                            <div class="seller-stat-label">Sản phẩm</div>
                            <div class="seller-stat-value">{{ number_format($product->seller->products_count ?? 120) }}
                            </div>
                        </div>
                        <div class="seller-stat">
                            <div class="seller-stat-label">Phản hồi</div>
                            <div class="seller-stat-value">{{ $product->seller->response_rate ?? 95 }}%</div>
                        </div>
                    </div>

                    <div class="seller-actions">
                        <button class="btn-seller">
                            <i class="fas fa-comment-alt me-2"></i>Chat ngay
                        </button>
                        <button class="btn-seller">
                            <i class="fas fa-store me-2"></i>Xem Shop
                        </button>
                    </div>
                </div>

                <!-- Related Products -->
                <div class="related-products">
                    <h3 class="section-title">Sản phẩm tương tự</h3>
                    <div class="row g-3">
                        @foreach ($relatedProducts ?? [] as $relatedProduct)
                            <div class="col-12">
                                @include('client.components.product-card', ['product' => $relatedProduct])
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let selectedVariantId = null;
            let selectedSizeId = null;
            let currentPrice = {{ $product->sale_price ?? ($product->price ?? 0) }};
            let maxStock = {{ $product->stock ?? 999 }};

            // ==================== IMAGE GALLERY ====================
            $('.thumbnail-item').click(function() {
                $('.thumbnail-item').removeClass('active');
                $(this).addClass('active');
                const imageUrl = $(this).data('image');
                $('#mainImage').attr('src', imageUrl);
            });

            // ==================== TABS ====================
            $('.tab-link').click(function() {
                const tabId = $(this).data('tab');

                $('.tab-link').removeClass('active');
                $(this).addClass('active');

                $('.tab-pane').removeClass('active');
                $('#' + tabId).addClass('active');
            });

            // ==================== QUANTITY CONTROLS ====================
            $('#increaseQty').click(function() {
                const input = $('#quantity');
                const current = parseInt(input.val());
                const max = parseInt(input.attr('max'));
                if (current < max) {
                    input.val(current + 1);
                }
            });

            $('#decreaseQty').click(function() {
                const input = $('#quantity');
                const current = parseInt(input.val());
                if (current > 1) {
                    input.val(current - 1);
                }
            });

            $('#quantity').on('input', function() {
                let value = parseInt($(this).val());
                const max = parseInt($(this).attr('max'));

                if (isNaN(value) || value < 1) {
                    $(this).val(1);
                } else if (value > max) {
                    $(this).val(max);
                    showToast('Số lượng vượt quá tồn kho', 'warning');
                }
            });

            // ==================== VARIANT SELECTION ====================
            $('.variant-option').click(function() {
                if ($(this).hasClass('disabled')) return;

                $(this).siblings().removeClass('active');
                $(this).addClass('active');

                selectedVariantId = $(this).data('variant-id');
                const variantPrice = $(this).data('price');
                const variantStock = $(this).data('stock');

                if (variantPrice) {
                    currentPrice = variantPrice;
                    $('.current-price').text('₫' + new Intl.NumberFormat('vi-VN').format(variantPrice));
                }

                if (variantStock !== undefined) {
                    maxStock = variantStock;
                    $('#quantity').attr('max', variantStock);
                    $('.stock-info').text(new Intl.NumberFormat('vi-VN').format(variantStock) +
                        ' sản phẩm có sẵn');
                }
            });

            // Size selection
            $('#sizeOptions .variant-option').click(function() {
                if ($(this).hasClass('disabled')) return;

                $('#sizeOptions .variant-option').removeClass('active');
                $(this).addClass('active');

                selectedSizeId = $(this).data('size-id');
            });

            // ==================== ADD TO CART ====================
            $('#addToCart').click(function() {
                const btn = $(this);
                const productId = {{ $product->id ?? 0 }};
                const quantity = parseInt($('#quantity').val());

                if (quantity < 1 || quantity > maxStock) {
                    showToast('Số lượng không hợp lệ', 'error');
                    return;
                }

                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang xử lý...');

                $.ajax({
                    url: `/client/cart/add/${productId}`,
                    method: 'POST',
                    data: {
                        quantity: quantity,
                        variant_id: selectedVariantId,
                        size_id: selectedSizeId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast(response.message || 'Đã thêm vào giỏ hàng', 'success');

                            if (response.cart_count) {
                                $('.cart-count, .cart-badge').text(response.cart_count).show();
                            }
                        } else {
                            showToast(response.message || 'Có lỗi xảy ra', 'error');
                        }
                    },
                    error: function(xhr) {
                        let message = 'Có lỗi xảy ra, vui lòng thử lại!';

                        if (xhr.status === 401) {
                            message = 'Vui lòng đăng nhập để thêm vào giỏ hàng';
                            setTimeout(() => {
                                window.location.href = '{{ route('login') }}';
                            }, 1500);
                        } else if (xhr.responseJSON?.message) {
                            message = xhr.responseJSON.message;
                        }

                        showToast(message, 'error');
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(
                            '<i class="fas fa-shopping-cart"></i> Thêm vào giỏ hàng');
                    }
                });
            });

            // ==================== BUY NOW ====================
            $('#buyNow').click(function() {
                const btn = $(this);
                const productId = {{ $product->id ?? 0 }};
                const quantity = parseInt($('#quantity').val());

                if (quantity < 1 || quantity > maxStock) {
                    showToast('Số lượng không hợp lệ', 'error');
                    return;
                }

                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang xử lý...');

                $.ajax({
                    url: `/client/cart/add/${productId}`,
                    method: 'POST',
                    data: {
                        quantity: quantity,
                        variant_id: selectedVariantId,
                        size_id: selectedSizeId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            window.location.href = '{{ route('client.checkout.index') }}';
                        } else {
                            showToast(response.message || 'Có lỗi xảy ra', 'error');
                            btn.prop('disabled', false).html('Mua ngay');
                        }
                    },
                    error: function(xhr) {
                        let message = 'Có lỗi xảy ra!';

                        if (xhr.status === 401) {
                            message = 'Vui lòng đăng nhập để mua hàng';
                            setTimeout(() => {
                                window.location.href = '{{ route('login') }}';
                            }, 1500);
                        } else if (xhr.responseJSON?.message) {
                            message = xhr.responseJSON.message;
                        }

                        showToast(message, 'error');
                        btn.prop('disabled', false).html('Mua ngay');
                    }
                });
            });

            // ==================== WISHLIST ====================
            $('#toggleWishlist').click(function() {
                const btn = $(this);
                const productId = {{ $product->id ?? 0 }};

                $.ajax({
                    url: `/client/wishlist/toggle/${productId}`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            btn.toggleClass('active');
                            const icon = btn.find('i');
                            if (btn.hasClass('active')) {
                                icon.removeClass('far').addClass('fas');
                            } else {
                                icon.removeClass('fas').addClass('far');
                            }
                            showToast(response.message, 'success');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 401) {
                            showToast('Vui lòng đăng nhập', 'error');
                            setTimeout(() => {
                                window.location.href = '{{ route('login') }}';
                            }, 1500);
                        } else {
                            showToast('Có lỗi xảy ra', 'error');
                        }
                    }
                });
            });
        });

        // ==================== TOAST NOTIFICATION ====================
        function showToast(message, type = 'info') {
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

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: type === 'error' ? 'error' : (type === 'warning' ? 'warning' : 'success'),
                    title: type === 'error' ? 'Lỗi!' : (type === 'warning' ? 'Cảnh báo!' : 'Thành công!'),
                    text: message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                return;
            }

            const alertClass = type === 'error' ? 'danger' : (type === 'warning' ? 'warning' : 'success');
            const alertHtml = `
        <div class="alert alert-${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
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
