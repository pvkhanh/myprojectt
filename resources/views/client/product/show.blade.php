@extends('client.layouts.master')

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
@endpush
