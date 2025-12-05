{{-- resources/views/client/components/product-card.blade.php
<div class="product-card">
    <div class="product-image">
        <a href="{{ route('client.products.show', $product->id) }}">
            <img src="{{ $product->main_image ?? 'https://via.placeholder.com/300' }}" alt="{{ $product->name }}"
                class="img-fluid">
        </a>

        @if ($product->discount > 0)
            <span class="badge-sale">-{{ $product->discount }}%</span>
        @endif

        @if ($product->is_new ?? false)
            <span class="badge-new">MỚI</span>
        @endif

        <div class="product-actions">
            <button class="action-btn wishlist-btn" data-product-id="{{ $product->id }}">
                <i class="far fa-heart"></i>
            </button>
            <button class="action-btn quick-view-btn" data-product-id="{{ $product->id }}">
                <i class="far fa-eye"></i>
            </button>
        </div>
    </div>

    <div class="product-info">
        @if (isset($product->category))
            <span class="product-category">{{ $product->category->name }}</span>
        @endif

        <h3 class="product-name">
            <a href="{{ route('client.products.show', $product->id) }}">
                {{ Str::limit($product->name, 50) }}
            </a>
        </h3>

        <div class="product-rating">
            @for ($i = 1; $i <= 5; $i++)
                <i class="fas fa-star{{ $i <= ($product->rating ?? 5) ? '' : '-o' }}"></i>
            @endfor
            <span class="rating-count">({{ $product->reviews_count ?? 0 }})</span>
        </div>

        <div class="product-price">
            <span class="current-price">{{ number_format($product->sale_price ?? $product->price) }}đ</span>
            @if (isset($product->price) && $product->sale_price < $product->price)
                <span class="original-price">{{ number_format($product->price) }}đ</span>
            @endif
        </div>

        <button class="btn-add-cart" data-product-id="{{ $product->id }}">
            <i class="fas fa-shopping-cart me-1"></i>
            Thêm vào giỏ
        </button>
    </div>
</div>

<style>
    .product-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
    }

    .product-image {
        position: relative;
        padding-top: 100%;
        overflow: hidden;
    }

    .product-image img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .product-card:hover .product-image img {
        transform: scale(1.1);
    }

    .badge-sale {
        position: absolute;
        top: 15px;
        left: 15px;
        background: #ef4444;
        color: white;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 13px;
        z-index: 2;
    }

    .badge-new {
        position: absolute;
        top: 15px;
        right: 15px;
        background: #10b981;
        color: white;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 13px;
        z-index: 2;
    }

    .product-actions {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        display: flex;
        gap: 10px;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .product-card:hover .product-actions {
        opacity: 1;
    }

    .action-btn {
        width: 45px;
        height: 45px;
        background: white;
        border: none;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .action-btn:hover {
        background: var(--primary-color);
        color: white;
        transform: scale(1.1);
    }

    .product-info {
        padding: 20px;
    }

    .product-category {
        color: #64748b;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .product-name {
        font-size: 16px;
        font-weight: 600;
        margin: 10px 0;
        min-height: 48px;
    }

    .product-name a {
        color: #1e293b;
        text-decoration: none;
        transition: color 0.3s;
    }

    .product-name a:hover {
        color: var(--primary-color);
    }

    .product-rating {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 12px;
        color: #fbbf24;
        font-size: 14px;
    }

    .rating-count {
        color: #94a3b8;
        font-size: 13px;
        margin-left: 5px;
    }

    .product-price {
        margin-bottom: 15px;
    }

    .current-price {
        font-size: 22px;
        font-weight: 800;
        color: #ef4444;
        margin-right: 10px;
    }

    .original-price {
        font-size: 16px;
        color: #94a3b8;
        text-decoration: line-through;
    }

    .btn-add-cart {
        width: 100%;
        padding: 12px;
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
        cursor: pointer;
    }

    .btn-add-cart:hover {
        background: #1d4ed8;
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(37, 99, 235, 0.3);
    }
</style> --}}
{{-- resources/views/client/components/product-card.blade.php --}}
<div class="product-card">
    <div class="product-image">
        {{-- SỬA: Dùng slug thay vì id --}}
        <a href="{{ route('client.products.show', $product->slug) }}">
            <img src="{{ $product->main_image ?? 'https://via.placeholder.com/300' }}" alt="{{ $product->name }}"
                class="img-fluid">
        </a>

        @if ($product->discount > 0)
            <span class="badge-sale">-{{ $product->discount }}%</span>
        @endif

        @if ($product->is_new ?? false)
            <span class="badge-new">MỚI</span>
        @endif

        <div class="product-actions">
            <button class="action-btn wishlist-btn" data-product-id="{{ $product->id }}" title="Thêm vào yêu thích">
                <i class="far fa-heart"></i>
            </button>
            <button class="action-btn quick-view-btn" data-product-id="{{ $product->id }}" title="Xem nhanh">
                <i class="far fa-eye"></i>
            </button>
        </div>
    </div>

    <div class="product-info">
        @if (isset($product->category))
            <span class="product-category">{{ $product->category->name }}</span>
        @endif

        <h3 class="product-name">
            <a href="{{ route('client.products.show', $product->slug) }}">
                {{ Str::limit($product->name, 50) }}
            </a>
        </h3>

        <div class="product-rating">
            @for ($i = 1; $i <= 5; $i++)
                <i class="fas fa-star{{ $i <= ($product->rating ?? 5) ? '' : '-o' }}"></i>
            @endfor
            <span class="rating-count">({{ $product->reviews_count ?? 0 }})</span>
        </div>

        <div class="product-price">
            <span class="current-price">{{ number_format($product->sale_price ?? $product->price) }}₫</span>
            @if (isset($product->price) && $product->sale_price < $product->price)
                <span class="original-price">{{ number_format($product->price) }}₫</span>
            @endif
        </div>

        {{-- SỬA: Truyền product ID chính xác --}}
        <button class="btn-add-cart" data-product-id="{{ $product->id }}"
            onclick="addToCartQuick({{ $product->id }})">
            <i class="fas fa-shopping-cart me-1"></i>
            Thêm vào giỏ
        </button>
    </div>
</div>

<style>
    /* Style giữ nguyên như cũ */
    .product-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
    }

    .product-image {
        position: relative;
        padding-top: 100%;
        overflow: hidden;
    }

    .product-image img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .product-card:hover .product-image img {
        transform: scale(1.1);
    }

    .badge-sale {
        position: absolute;
        top: 15px;
        left: 15px;
        background: #ef4444;
        color: white;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 13px;
        z-index: 2;
    }

    .badge-new {
        position: absolute;
        top: 15px;
        right: 15px;
        background: #10b981;
        color: white;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 13px;
        z-index: 2;
    }

    .product-actions {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        display: flex;
        gap: 10px;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .product-card:hover .product-actions {
        opacity: 1;
    }

    .action-btn {
        width: 45px;
        height: 45px;
        background: white;
        border: none;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .action-btn:hover {
        background: var(--primary-color);
        color: white;
        transform: scale(1.1);
    }

    .product-info {
        padding: 20px;
    }

    .product-category {
        color: #64748b;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .product-name {
        font-size: 16px;
        font-weight: 600;
        margin: 10px 0;
        min-height: 48px;
    }

    .product-name a {
        color: #1e293b;
        text-decoration: none;
        transition: color 0.3s;
    }

    .product-name a:hover {
        color: var(--primary-color);
    }

    .product-rating {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 12px;
        color: #fbbf24;
        font-size: 14px;
    }

    .rating-count {
        color: #94a3b8;
        font-size: 13px;
        margin-left: 5px;
    }

    .product-price {
        margin-bottom: 15px;
    }

    .current-price {
        font-size: 22px;
        font-weight: 800;
        color: #ef4444;
        margin-right: 10px;
    }

    .original-price {
        font-size: 16px;
        color: #94a3b8;
        text-decoration: line-through;
    }

    .btn-add-cart {
        width: 100%;
        padding: 12px;
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
        cursor: pointer;
    }

    .btn-add-cart:hover {
        background: #1d4ed8;
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(37, 99, 235, 0.3);
    }

    .btn-add-cart:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>

<script>
    // Thêm vào giỏ nhanh từ product card
    function addToCartQuick(productId) {
        const btn = event.target.closest('.btn-add-cart');
        const originalHtml = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        $.ajax({
            url: `/client/cart/add/${productId}`,
            method: 'POST',
            data: {
                quantity: 1,
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
                    showToast(response.message, 'error');
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Có lỗi xảy ra!';
                showToast(message, 'error');
            },
            complete: function() {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        });
    }

    // Wishlist từ product card
    $('.wishlist-btn').click(function() {
        const btn = $(this);
        const productId = btn.data('product-id');

        $.ajax({
            url: `/client/wishlist/toggle/${productId}`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    btn.find('i').toggleClass('far fas');
                    showToast(response.message, 'success');
                }
            },
            error: function() {
                showToast('Có lỗi xảy ra', 'error');
            }
        });
    });

    // Toast function (nếu chưa có)
    function showToast(message, type = 'info') {
        if (typeof toastr !== 'undefined') {
            toastr[type](message);
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: type === 'error' ? 'error' : 'success',
                title: message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        } else {
            alert(message);
        }
    }
</script>
