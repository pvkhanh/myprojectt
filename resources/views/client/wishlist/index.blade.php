{{-- resources/views/client/wishlist/index.blade.php --}}
@extends('client.layouts.master')

@section('title', 'Sản phẩm yêu thích')

@push('styles')
    <style>
        .wishlist-page {
            padding: 60px 0;
            background: #f8fafc;
        }

        .page-header {
            background: white;
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 32px;
            font-weight: 800;
            color: #1e293b;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .wishlist-count {
            background: #ef4444;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 16px;
            font-weight: 700;
        }

        .btn-clear-all {
            padding: 12px 24px;
            background: #fee2e2;
            color: #ef4444;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-clear-all:hover {
            background: #ef4444;
            color: white;
        }

        .wishlist-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }

        .wishlist-item {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
            position: relative;
        }

        .wishlist-item:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }

        .item-image {
            position: relative;
            padding-top: 100%;
            overflow: hidden;
        }

        .item-image img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .wishlist-item:hover .item-image img {
            transform: scale(1.1);
        }

        .btn-remove {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 40px;
            height: 40px;
            background: white;
            border: none;
            border-radius: 50%;
            color: #ef4444;
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 2;
        }

        .btn-remove:hover {
            background: #ef4444;
            color: white;
            transform: scale(1.1);
        }

        .item-badge {
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

        .item-info {
            padding: 20px;
        }

        .item-category {
            color: #64748b;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .item-name {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 10px;
            min-height: 48px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .item-name a {
            color: inherit;
            text-decoration: none;
            transition: color 0.3s;
        }

        .item-name a:hover {
            color: var(--primary-color);
        }

        .item-rating {
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

        .item-price {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .current-price {
            font-size: 22px;
            font-weight: 800;
            color: #ef4444;
        }

        .original-price {
            font-size: 16px;
            color: #94a3b8;
            text-decoration: line-through;
        }

        .item-stock {
            font-size: 13px;
            color: #10b981;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .item-stock.out-of-stock {
            color: #ef4444;
        }

        .item-actions {
            display: flex;
            gap: 10px;
        }

        .btn-add-cart {
            flex: 1;
            padding: 12px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-add-cart:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(37, 99, 235, 0.3);
        }

        .btn-add-cart:disabled {
            background: #cbd5e1;
            cursor: not-allowed;
            transform: none;
        }

        .empty-wishlist {
            text-align: center;
            padding: 100px 20px;
            background: white;
            border-radius: 16px;
        }

        .empty-icon {
            font-size: 120px;
            color: #cbd5e1;
            margin-bottom: 30px;
        }

        .added-date {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 10px;
        }
    </style>
@endpush

@section('content')
    <div class="wishlist-page">
        <div class="container">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Yêu thích</li>
                </ol>
            </nav>

            @if (isset($wishlistItems) && count($wishlistItems) > 0)
                <!-- Page Header -->
                <div class="page-header">
                    <h1 class="page-title">
                        <i class="fas fa-heart"></i>
                        Sản phẩm yêu thích
                        <span class="wishlist-count">{{ count($wishlistItems) }}</span>
                    </h1>
                    <button class="btn-clear-all" onclick="clearAllWishlist()">
                        <i class="fas fa-trash me-2"></i>Xóa tất cả
                    </button>
                </div>

                <!-- Wishlist Grid -->
                <div class="wishlist-grid">
                    @foreach ($wishlistItems as $item)
                        <div class="wishlist-item" data-wishlist-id="{{ $item->id }}">
                            <div class="item-image">
                                <a href="{{ route('client.products.show', $item->product->id) }}">
                                    <img src="{{ $item->product->main_image ?? 'https://via.placeholder.com/300' }}"
                                        alt="{{ $item->product->name }}">
                                </a>

                                @if ($item->product->discount > 0)
                                    <span class="item-badge">-{{ $item->product->discount }}%</span>
                                @endif

                                <button class="btn-remove" onclick="removeFromWishlist({{ $item->id }})">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            <div class="item-info">
                                @if (isset($item->product->category))
                                    <div class="item-category">{{ $item->product->category->name }}</div>
                                @endif

                                <h3 class="item-name">
                                    <a href="{{ route('client.products.show', $item->product->id) }}">
                                        {{ $item->product->name }}
                                    </a>
                                </h3>

                                <div class="item-rating">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star{{ $i <= ($item->product->rating ?? 5) ? '' : '-o' }}"></i>
                                    @endfor
                                    <span class="rating-count">({{ $item->product->reviews_count ?? 0 }})</span>
                                </div>

                                <div class="item-price">
                                    <span class="current-price">
                                        {{ number_format($item->product->sale_price ?? $item->product->price) }}đ
                                    </span>
                                    @if (isset($item->product->price) && $item->product->sale_price < $item->product->price)
                                        <span class="original-price">{{ number_format($item->product->price) }}đ</span>
                                    @endif
                                </div>

                                <div class="item-stock {{ ($item->product->stock ?? 0) > 0 ? '' : 'out-of-stock' }}">
                                    <i
                                        class="fas fa-{{ ($item->product->stock ?? 0) > 0 ? 'check-circle' : 'times-circle' }} me-1"></i>
                                    {{ ($item->product->stock ?? 0) > 0 ? 'Còn hàng' : 'Hết hàng' }}
                                </div>

                                <div class="item-actions">
                                    <button class="btn-add-cart" onclick="addToCart({{ $item->product->id }})"
                                        {{ ($item->product->stock ?? 0) > 0 ? '' : 'disabled' }}>
                                        <i class="fas fa-shopping-cart"></i>
                                        Thêm vào giỏ
                                    </button>
                                </div>

                                <div class="added-date">
                                    <i class="far fa-clock me-1"></i>
                                    Thêm {{ $item->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty Wishlist -->
                <div class="empty-wishlist">
                    <div class="empty-icon">
                        <i class="fas fa-heart-broken"></i>
                    </div>
                    <h2 class="mb-3">Chưa có sản phẩm yêu thích</h2>
                    <p class="text-muted mb-4">Hãy thêm sản phẩm yêu thích để dễ dàng theo dõi và mua sắm sau!</p>
                    <a href="{{ route('client.products.index') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-shopping-bag me-2"></i>Khám phá sản phẩm
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function removeFromWishlist(wishlistId) {
            if (!confirm('Bạn có chắc muốn xóa sản phẩm này khỏi danh sách yêu thích?')) {
                return;
            }

            $.ajax({
                url: '/wishlist/remove/' + wishlistId,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('[data-wishlist-id="' + wishlistId + '"]').fadeOut(300, function() {
                            $(this).remove();

                            // Check if wishlist is empty
                            if ($('.wishlist-item').length === 0) {
                                location.reload();
                            } else {
                                // Update count
                                const count = $('.wishlist-item').length;
                                $('.wishlist-count').text(count);
                            }
                        });

                        showToast('Đã xóa khỏi danh sách yêu thích', 'success');
                    }
                },
                error: function() {
                    showToast('Có lỗi xảy ra', 'error');
                }
            });
        }

        function clearAllWishlist() {
            if (!confirm('Bạn có chắc muốn xóa tất cả sản phẩm yêu thích?')) {
                return;
            }

            $.ajax({
                url: '/wishlist/clear',
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                },
                error: function() {
                    showToast('Có lỗi xảy ra', 'error');
                }
            });
        }

        function addToCart(productId) {
            $.ajax({
                url: '/cart/add',
                method: 'POST',
                data: {
                    product_id: productId,
                    quantity: 1,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        showToast('Đã thêm vào giỏ hàng', 'success');

                        // Update cart count if exists
                        if ($('.cart-count').length) {
                            $('.cart-count').text(response.cart_count);
                        }
                    }
                },
                error: function() {
                    showToast('Có lỗi xảy ra', 'error');
                }
            });
        }

        function showToast(message, type) {
            // Implement your toast notification
            alert(message);
        }
    </script>
@endpush
