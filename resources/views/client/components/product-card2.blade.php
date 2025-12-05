<style>
    .product-card {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s ease;
        background: white;
        height: 100%;
    }

    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }

    .product-image-wrapper {
        position: relative;
        padding-top: 100%;
        overflow: hidden;
        background: #f8fafc;
    }

    .product-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .product-card:hover .product-image {
        transform: scale(1.1);
    }

    .product-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        z-index: 2;
    }

    .badge-sale {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
    }

    .badge-new {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
    }

    .product-actions {
        position: absolute;
        top: 15px;
        right: 15px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        opacity: 0;
        transform: translateX(20px);
        transition: all 0.3s ease;
    }

    .product-card:hover .product-actions {
        opacity: 1;
        transform: translateX(0);
    }

    .action-btn {
        width: 40px;
        height: 40px;
        background: white;
        border: none;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        transition: all 0.3s;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .action-btn:hover {
        background: var(--primary-color);
        color: white;
        transform: scale(1.1);
    }

    .action-btn.active {
        background: var(--danger-color);
        color: white;
    }

    .product-info {
        padding: 20px;
    }

    .product-category {
        color: #64748b;
        font-size: 13px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .product-title {
        font-size: 16px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 10px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 48px;
    }

    .product-title:hover {
        color: var(--primary-color);
    }

    .product-rating {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
    }

    .stars {
        color: #fbbf24;
        font-size: 14px;
    }

    .rating-count {
        color: #94a3b8;
        font-size: 13px;
    }

    .product-price {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }

    .price-current {
        font-size: 22px;
        font-weight: 700;
        color: var(--danger-color);
    }

    .price-old {
        font-size: 16px;
        color: #94a3b8;
        text-decoration: line-through;
    }

    .discount-percent {
        background: #fef2f2;
        color: var(--danger-color);
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
    }

    .add-to-cart-btn {
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: white;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
        opacity: 0;
        transform: translateY(10px);
    }

    .product-card:hover .add-to-cart-btn {
        opacity: 1;
        transform: translateY(0);
    }

    .add-to-cart-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.4);
    }

    .out-of-stock {
        background: #94a3b8;
        cursor: not-allowed;
    }
</style>

<div class="product-card">
    <!-- Product Image -->
    <div class="product-image-wrapper">
        <a href="{{ route('client.products.show', $product->id) }}">
            <img src="{{ $product->image ?? 'https://via.placeholder.com/400' }}" alt="{{ $product->name }}"
                class="product-image">
        </a>

        <!-- Badges -->
        <div class="product-badge">
            @if ($product->discount > 0)
                <span class="badge-sale">-{{ $product->discount }}%</span>
            @elseif($product->is_new)
                <span class="badge-new">MỚI</span>
            @endif
        </div>

        <!-- Quick Actions -->
        <div class="product-actions">
            <button class="action-btn wishlist-btn {{ $product->in_wishlist ? 'active' : '' }}"
                data-product-id="{{ $product->id }}" title="Thêm vào yêu thích">
                <i class="fas fa-heart"></i>
            </button>
            <button class="action-btn quick-view-btn" data-product-id="{{ $product->id }}" title="Xem nhanh">
                <i class="fas fa-eye"></i>
            </button>
            <button class="action-btn compare-btn" data-product-id="{{ $product->id }}" title="So sánh">
                <i class="fas fa-random"></i>
            </button>
        </div>
    </div>

    <!-- Product Info -->
    <div class="product-info">
        <div class="product-category">{{ $product->category->name ?? 'Chưa phân loại' }}</div>

        <a href="{{ route('client.products.show', $product->id) }}" class="text-decoration-none">
            <h3 class="product-title">{{ $product->name }}</h3>
        </a>

        <!-- Rating -->
        <div class="product-rating">
            <div class="stars">
                @for ($i = 1; $i <= 5; $i++)
                    @if ($i <= floor($product->rating))
                        <i class="fas fa-star"></i>
                    @elseif($i - 0.5 <= $product->rating)
                        <i class="fas fa-star-half-alt"></i>
                    @else
                        <i class="far fa-star"></i>
                    @endif
                @endfor
            </div>
            <span class="rating-count">({{ $product->reviews_count ?? 0 }})</span>
        </div>

        <!-- Price -->
        <div class="product-price">
            <span class="price-current">{{ number_format($product->sale_price ?? $product->price) }}đ</span>
            @if ($product->sale_price && $product->sale_price < $product->price)
                <span class="price-old">{{ number_format($product->price) }}đ</span>
                <span
                    class="discount-percent">-{{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%</span>
            @endif
        </div>

        <!-- Add to Cart Button -->
        @php
            // Tính tổng tồn kho của tất cả biến thể
            $totalStock = $product->variants->sum(fn($variant) => $variant->stockItems->sum('quantity'));
        @endphp

        @if ($totalStock > 0)
            <button class="add-to-cart-btn" data-product-id="{{ $product->id }}">
                <i class="fas fa-shopping-cart me-2"></i>Thêm vào giỏ
            </button>
        @else
            <button class="add-to-cart-btn out-of-stock" disabled>
                <i class="fas fa-times me-2"></i>Hết hàng
            </button>
        @endif

    </div>
</div>

@push('scripts')
    <script>
        // Add to cart
        $('.add-to-cart-btn').click(function() {
            const productId = $(this).data('product-id');

            $.ajax({
                url: `/client/cart/add/${productId}`,
                method: 'POST',
                data: {
                    quantity: 1,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    showToast('Đã thêm sản phẩm vào giỏ hàng!', 'success');
                    $('.badge-count').text(response.cart_count);
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    showToast('Có lỗi xảy ra!', 'danger');
                }
            });
        });


        // Wishlist
        $('.wishlist-btn').click(function() {
            const btn = $(this);
            const productId = btn.data('product-id');
            $.ajax({
                url: '/wishlist/toggle',
                method: 'POST',
                data: {
                    product_id: productId
                },
                success: function(response) {
                    btn.toggleClass('active');
                    showToast(response.message, 'success');
                }
            });
        });

        // Quick view
        $('.quick-view-btn').click(function() {
            const productId = $(this).data('product-id');
            // Open modal with product details
            window.location.href = `/product/${productId}`;
        });
    </script>
@endpush
