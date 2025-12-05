{{-- resources/views/client/product/show.blade.php --}}
@extends('client.layouts.master')

@section('title', $product->name ?? 'Chi tiết sản phẩm')

@push('styles')
<style>
    :root {
        --primary-color: #3b82f6;
    }

    .product-detail-page {
        padding: 40px 0 80px;
        background: #f8fafc;
    }

    .product-detail-wrapper {
        background: white;
        border-radius: 24px;
        padding: 40px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .breadcrumb {
        background: transparent;
        padding: 0;
        margin-bottom: 30px;
    }

    .breadcrumb-item a {
        color: var(--primary-color);
        text-decoration: none;
    }

    .breadcrumb-item.active {
        color: #64748b;
    }

    .product-images-section {
        position: sticky;
        top: 100px;
    }

    .main-image-wrapper {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 20px;
        background: #f8fafc;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }

    .main-image {
        position: relative;
        padding-top: 100%;
        cursor: zoom-in;
    }

    .main-image img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .main-image:hover img {
        transform: scale(1.05);
    }

    .image-badges {
        position: absolute;
        top: 20px;
        left: 20px;
        z-index: 10;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .image-badge {
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 13px;
        text-transform: uppercase;
        backdrop-filter: blur(10px);
    }

    .badge-new {
        background: rgba(16, 185, 129, 0.95);
        color: white;
    }

    .badge-sale {
        background: rgba(239, 68, 68, 0.95);
        color: white;
    }

    .badge-hot {
        background: rgba(251, 146, 60, 0.95);
        color: white;
    }

    .image-tools {
        position: absolute;
        top: 20px;
        right: 20px;
        z-index: 10;
        display: flex;
        gap: 10px;
    }

    .tool-btn {
        width: 45px;
        height: 45px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: none;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .tool-btn:hover {
        transform: scale(1.1);
        background: var(--primary-color);
        color: white;
    }

    .thumbnail-images {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 12px;
    }

    .thumbnail-item {
        position: relative;
        padding-top: 100%;
        border-radius: 12px;
        overflow: hidden;
        cursor: pointer;
        border: 3px solid transparent;
        transition: all 0.3s;
    }

    .thumbnail-item img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .thumbnail-item:hover {
        transform: scale(1.05);
    }

    .thumbnail-item.active {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 2px var(--primary-color);
    }

    .product-info-section {
        padding-left: 40px;
    }

    .product-brand {
        display: inline-block;
        color: var(--primary-color);
        font-weight: 600;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
        text-decoration: none;
    }

    .product-name {
        font-size: 36px;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 20px;
        line-height: 1.3;
    }

    .product-meta-bar {
        display: flex;
        align-items: center;
        gap: 25px;
        padding: 20px 0;
        margin-bottom: 25px;
        border-top: 2px solid #e2e8f0;
        border-bottom: 2px solid #e2e8f0;
    }

    .rating-display {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .rating-stars {
        color: #fbbf24;
        font-size: 18px;
    }

    .rating-number {
        font-weight: 700;
        color: #1e293b;
        font-size: 18px;
    }

    .rating-count {
        color: #64748b;
        font-size: 14px;
    }

    .reviews-link {
        color: #3b82f6;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s;
    }

    .reviews-link:hover {
        color: #1d4ed8;
    }

    .price-section {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        padding: 30px;
        border-radius: 20px;
        margin-bottom: 30px;
    }

    .price-main {
        display: flex;
        align-items: baseline;
        gap: 15px;
        margin-bottom: 15px;
    }

    .current-price {
        font-size: 48px;
        font-weight: 900;
        color: #ef4444;
        line-height: 1;
    }

    .original-price {
        font-size: 28px;
        color: #94a3b8;
        text-decoration: line-through;
    }

    .discount-badge {
        display: inline-block;
        background: #ef4444;
        color: white;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 16px;
    }

    .price-savings {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #065f46;
        font-weight: 600;
    }

    .stock-info {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 20px;
        border-radius: 12px;
        font-weight: 600;
        margin-top: 15px;
    }

    .in-stock {
        background: #d1fae5;
        color: #065f46;
    }

    .low-stock {
        background: #fed7aa;
        color: #92400e;
    }

    .out-of-stock {
        background: #fee2e2;
        color: #991b1b;
    }

    .variant-section {
        margin-bottom: 30px;
    }

    .variant-label {
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .selected-variant {
        color: var(--primary-color);
        font-weight: 600;
    }

    .variant-options {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .variant-option {
        padding: 12px 24px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s;
        font-weight: 600;
        background: white;
        position: relative;
    }

    .variant-option:hover:not(.disabled) {
        border-color: var(--primary-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
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

    .variant-option.disabled::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 2px;
        background: #ef4444;
        transform: translateY(-50%) rotate(-15deg);
    }

    .quantity-section {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 30px;
    }

    .quantity-label {
        font-weight: 700;
        color: #1e293b;
        font-size: 16px;
    }

    .quantity-control {
        display: flex;
        align-items: center;
        border: 2px solid #e2e8f0;
        border-radius: 14px;
        overflow: hidden;
        background: white;
    }

    .qty-btn {
        width: 50px;
        height: 50px;
        border: none;
        background: white;
        color: #475569;
        font-size: 20px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
    }

    .qty-btn:hover {
        background: var(--primary-color);
        color: white;
    }

    .qty-input {
        width: 80px;
        text-align: center;
        border: none;
        border-left: 2px solid #e2e8f0;
        border-right: 2px solid #e2e8f0;
        font-weight: 700;
        font-size: 18px;
        height: 50px;
    }

    .action-buttons {
        display: flex;
        gap: 15px;
        margin-bottom: 30px;
    }

    .btn-action {
        flex: 1;
        padding: 18px 24px;
        border: none;
        border-radius: 14px;
        font-size: 18px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .btn-add-cart {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        flex: 2;
    }

    .btn-add-cart:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 35px rgba(59, 130, 246, 0.4);
    }

    .btn-buy-now {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        flex: 2;
    }

    .btn-buy-now:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 35px rgba(239, 68, 68, 0.4);
    }

    .btn-wishlist {
        width: 70px;
        height: 70px;
        border: 2px solid #e2e8f0;
        background: white;
        border-radius: 14px;
        color: #64748b;
        font-size: 28px;
        padding: 0;
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
        gap: 20px;
        padding: 30px;
        background: #f8fafc;
        border-radius: 16px;
        margin-bottom: 30px;
    }

    .feature-item {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .feature-icon {
        width: 50px;
        height: 50px;
        background: white;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-color);
        font-size: 24px;
        flex-shrink: 0;
    }

    .feature-text strong {
        display: block;
        color: #1e293b;
        font-size: 15px;
        margin-bottom: 3px;
    }

    .feature-text small {
        color: #64748b;
        font-size: 13px;
    }

    .product-tabs-section {
        margin-top: 60px;
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .tabs-nav {
        display: flex;
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
    }

    .tab-link {
        flex: 1;
        padding: 20px 30px;
        border: none;
        background: none;
        color: #64748b;
        font-weight: 700;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s;
        position: relative;
    }

    .tab-link:hover {
        color: var(--primary-color);
        background: white;
    }

    .tab-link.active {
        color: var(--primary-color);
        background: white;
    }

    .tab-link.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 2px;
        background: var(--primary-color);
    }

    .tab-content {
        padding: 40px;
    }

    .tab-pane {
        display: none;
    }

    .tab-pane.active {
        display: block;
        animation: fadeIn 0.3s;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .reviews-summary {
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
        padding: 40px;
        border-radius: 16px;
        margin-bottom: 40px;
        text-align: center;
    }

    .overall-rating {
        font-size: 72px;
        font-weight: 900;
        color: #1e293b;
        margin-bottom: 10px;
    }

    .review-item {
        padding: 30px;
        background: #f8fafc;
        border-radius: 16px;
        margin-bottom: 20px;
    }

    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 15px;
    }

    .reviewer-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .reviewer-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 24px;
    }

    .reviewer-name {
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 5px;
    }

    .review-date {
        color: #64748b;
        font-size: 13px;
    }

    .review-rating {
        color: #fbbf24;
        font-size: 16px;
    }

    .review-content {
        color: #475569;
        line-height: 1.7;
        margin-bottom: 15px;
    }

    .image-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.95);
        z-index: 9999;
        padding: 20px;
    }

    .image-modal.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-image {
        max-width: 90%;
        max-height: 90%;
        object-fit: contain;
    }

    .modal-close {
        position: absolute;
        top: 30px;
        right: 30px;
        width: 50px;
        height: 50px;
        background: white;
        border: none;
        border-radius: 50%;
        font-size: 24px;
        cursor: pointer;
    }

    @media (max-width: 991px) {
        .product-info-section {
            padding-left: 0;
            margin-top: 30px;
        }

        .product-name {
            font-size: 28px;
        }

        .current-price {
            font-size: 36px;
        }

        .product-features {
            grid-template-columns: 1fr;
        }

        .thumbnail-images {
            grid-template-columns: repeat(4, 1fr);
        }

        .tabs-nav {
            overflow-x: auto;
        }

        .tab-link {
            flex: none;
            white-space: nowrap;
        }
    }
</style>
@endpush

@section('content')
<div class="product-detail-page">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('client.products.index') }}">Sản phẩm</a></li>
                @if ($product->category ?? false)
                    <li class="breadcrumb-item">
                        <a href="{{ route('client.products.index', ['category' => $product->category->id]) }}">
                            {{ $product->category->name }}
                        </a>
                    </li>
                @endif
                <li class="breadcrumb-item active">{{ Str::limit($product->name ?? '', 50) }}</li>
            </ol>
        </nav>

        <div class="product-detail-wrapper">
            <div class="row">
                <!-- Product Images -->
                <div class="col-lg-6">
                    <div class="product-images-section">
                        <div class="main-image-wrapper">
                            <div class="image-badges">
                                @if ($product->is_new ?? false)
                                    <span class="image-badge badge-new">
                                        <i class="fas fa-sparkles me-1"></i>Mới
                                    </span>
                                @endif

                                @php
                                    $discountPercent = 0;
                                    if (isset($product->price) && isset($product->sale_price) && $product->sale_price < $product->price) {
                                        $discountPercent = round((($product->price - $product->sale_price) / $product->price) * 100);
                                    }
                                @endphp

                                @if ($discountPercent > 0)
                                    <span class="image-badge badge-sale">-{{ $discountPercent }}%</span>
                                @endif

                                @if ($product->is_hot ?? false)
                                    <span class="image-badge badge-hot">
                                        <i class="fas fa-fire me-1"></i>Hot
                                    </span>
                                @endif
                            </div>

                            <div class="image-tools">
                                <button class="tool-btn" id="zoomBtn" title="Phóng to">
                                    <i class="fas fa-search-plus"></i>
                                </button>
                                <button class="tool-btn btn-share" id="shareBtn" title="Chia sẻ">
                                    <i class="fas fa-share-alt"></i>
                                </button>
                            </div>

                            <div class="main-image" id="mainImage">
                                @php
                                    $mainImage = $product->main_image ?? ($product->images->first()->url ?? 'https://via.placeholder.com/600');
                                @endphp
                                <img src="{{ $mainImage }}" alt="{{ $product->name }}" id="mainImageSrc">
                            </div>
                        </div>

                        <div class="thumbnail-images">
                            @php
                                $allImages = [];
                                if ($product->main_image) {
                                    $allImages[] = $product->main_image;
                                }
                                if ($product->images && $product->images->count() > 0) {
                                    foreach ($product->images as $img) {
                                        $allImages[] = $img->url;
                                    }
                                }
                                if (empty($allImages)) {
                                    $allImages[] = 'https://via.placeholder.com/600';
                                }
                            @endphp

                            @foreach ($allImages as $index => $imageUrl)
                                <div class="thumbnail-item {{ $index === 0 ? 'active' : '' }}" data-image="{{ $imageUrl }}">
                                    <img src="{{ $imageUrl }}" alt="Product image {{ $index + 1 }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="col-lg-6">
                    <div class="product-info-section">
                        @if ($product->brand ?? false)
                            <a href="{{ route('client.products.index', ['brand' => $product->brand->id]) }}" class="product-brand">
                                <i class="fas fa-crown me-1"></i>{{ $product->brand->name }}
                            </a>
                        @endif

                        <h1 class="product-name">{{ $product->name ?? 'Tên sản phẩm' }}</h1>

                        <!-- Meta Bar -->
                        <div class="product-meta-bar">
                            <div class="rating-display">
                                <div class="rating-stars">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star{{ $i <= ($product->rating ?? 5) ? '' : '-o' }}"></i>
                                    @endfor
                                </div>
                                <span class="rating-number">{{ number_format($product->rating ?? 5, 1) }}</span>
                                <span class="rating-count">({{ $product->reviews_count ?? 0 }})</span>
                                <a href="#reviews" class="reviews-link">Xem đánh giá</a>
                            </div>

                            <div style="color: #64748b;">
                                <i class="fas fa-box"></i>
                                Đã bán: <strong>{{ $product->sold ?? 0 }}</strong>
                            </div>

                            @if ($product->sku ?? false)
                                <div style="color: #64748b; font-size: 14px;">
                                    <i class="fas fa-barcode"></i> SKU: {{ $product->sku }}
                                </div>
                            @endif
                        </div>

                        <!-- Price Section -->
                        <div class="price-section">
                            <div class="price-main">
                                <span class="current-price">
                                    {{ number_format($product->sale_price ?? ($product->price ?? 0)) }}đ
                                </span>

                                @if (isset($product->price) && isset($product->sale_price) && $product->sale_price < $product->price)
                                    <span class="original-price">{{ number_format($product->price) }}đ</span>
                                    <span class="discount-badge">-{{ $discountPercent }}%</span>
                                @endif
                            </div>

                            @if (isset($product->price) && isset($product->sale_price) && $product->sale_price < $product->price)
                                <div class="price-savings">
                                    <i class="fas fa-piggy-bank"></i>
                                    <span>Tiết kiệm: {{ number_format($product->price - $product->sale_price) }}đ</span>
                                </div>
                            @endif

                            @php
                                $totalStock = 0;
                                if ($product->variants && $product->variants->count() > 0) {
                                    foreach ($product->variants as $variant) {
                                        if ($variant->stockItems && $variant->stockItems->count() > 0) {
                                            $totalStock += $variant->stockItems->sum('quantity');
                                        }
                                    }
                                } else {
                                    $totalStock = $product->stock ?? 0;
                                }

                                $stockClass = $totalStock > 50 ? 'in-stock' : ($totalStock > 0 ? 'low-stock' : 'out-of-stock');
                                $stockText = $totalStock > 50 ? 'Còn hàng' : ($totalStock > 0 ? "Chỉ còn $totalStock sản phẩm" : 'Hết hàng');
                                $stockIcon = $totalStock > 0 ? 'check-circle' : 'times-circle';
                            @endphp

                            <div class="stock-info {{ $stockClass }}">
                                <i class="fas fa-{{ $stockIcon }}"></i>
                                <span>{{ $stockText }}</span>
                            </div>
                        </div>

                        <!-- Variants -->
                        @if ($product->variants && $product->variants->count() > 0)
                            @php
                                $variantTypes = $product->variants->groupBy('type');
                            @endphp

                            @foreach ($variantTypes as $type => $variants)
                                <div class="variant-section">
                                    <div class="variant-label">
                                        {{ ucfirst($type) }}: 
                                        <span class="selected-variant" id="selected{{ ucfirst($type) }}">
                                            {{ $variants->first()->value ?? '' }}
                                        </span>
                                    </div>
                                    <div class="variant-options">
                                        @foreach ($variants as $variant)
                                            @php
                                                $variantStock = $variant->stockItems ? $variant->stockItems->sum('quantity') : 0;
                                            @endphp
                                            <button class="variant-option {{ $loop->first ? 'active' : '' }} {{ $variantStock <= 0 ? 'disabled' : '' }}"
                                                    data-variant-id="{{ $variant->id }}"
                                                    data-variant-type="{{ $type }}"
                                                    data-variant-value="{{ $variant->value }}"
                                                    data-variant-stock="{{ $variantStock }}"
                                                    {{ $variantStock <= 0 ? 'disabled' : '' }}>
                                                {{ $variant->value }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        <!-- Quantity -->
                        @if ($totalStock > 0)
                            <div class="quantity-section">
                                <span class="quantity-label">Số lượng:</span>
                                <div class="quantity-control">
                                    <button class="qty-btn" id="decreaseQty">-</button>
                                    <input type="number" class="qty-input" id="quantity" value="1" min="1" max="{{ $totalStock }}" readonly>
                                    <button class="qty-btn" id="increaseQty">+</button>
                                </div>
                                <span style="color: #64748b; font-size: 14px;">{{ $totalStock }} sản phẩm có sẵn</span>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            @if ($totalStock > 0)
                                <button class="btn-action btn-add-cart" id="addToCart">
                                    <i class="fas fa-shopping-cart"></i>
                                    Thêm vào giỏ hàng
                                </button>
                                <button class="btn-action btn-buy-now" id="buyNow">
                                    <i class="fas fa-bolt"></i>
                                    {{-- PHẦN 2: Nối tiếp từ phần 1, thay thế dòng "Mua ng" bằng code dưới --}}

                                    Mua ngay
                                </button>
                            @else
                                <button class="btn-action btn-add-cart" disabled style="opacity: 0.5; cursor: not-allowed;">
                                    <i class="fas fa-ban"></i>
                                    Hết hàng
                                </button>
                            @endif

                            <button class="btn-action btn-wishlist {{ $product->in_wishlist ?? false ? 'active' : '' }}" 
                                    id="toggleWishlist" 
                                    data-product-id="{{ $product->id }}">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>

                        <!-- Features -->
                        <div class="product-features">
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-shipping-fast"></i>
                                </div>
                                <div class="feature-text">
                                    <strong>Giao hàng nhanh</strong>
                                    <small>Trong 24h tại HN & HCM</small>
                                </div>
                            </div>

                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div class="feature-text">
                                    <strong>Bảo hành 12 tháng</strong>
                                    <small>Chính hãng 100%</small>
                                </div>
                            </div>

                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-undo"></i>
                                </div>
                                <div class="feature-text">
                                    <strong>Đổi trả miễn phí</strong>
                                    <small>Trong vòng 30 ngày</small>
                                </div>
                            </div>

                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-headset"></i>
                                </div>
                                <div class="feature-text">
                                    <strong>Hỗ trợ 24/7</strong>
                                    <small>Tư vấn miễn phí</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Tabs -->
        <div class="product-tabs-section">
            <div class="tabs-nav">
                <button class="tab-link active" data-tab="description">
                    <i class="fas fa-align-left me-2"></i>
                    Mô tả sản phẩm
                </button>
                <button class="tab-link" data-tab="specifications">
                    <i class="fas fa-list-ul me-2"></i>
                    Thông số kỹ thuật
                </button>
                <button class="tab-link" data-tab="reviews">
                    <i class="fas fa-star me-2"></i>
                    Đánh giá ({{ $product->reviews_count ?? 0 }})
                </button>
                <button class="tab-link" data-tab="shipping">
                    <i class="fas fa-shipping-fast me-2"></i>
                    Vận chuyển
                </button>
            </div>

            <div class="tab-content">
                <!-- Description Tab -->
                <div class="tab-pane active" id="description">
                    <div class="product-description" style="line-height: 1.8; color: #475569;">
                        {!! $product->description ?? '<p>Mô tả chi tiết sản phẩm sẽ được cập nhật...</p>' !!}
                    </div>
                </div>

                <!-- Specifications Tab -->
                <div class="tab-pane" id="specifications">
                    <div class="specifications-table">
                        @if (isset($product->specifications) && is_array($product->specifications))
                            @foreach ($product->specifications as $key => $value)
                                <div style="display: flex; padding: 18px 20px; border-bottom: 1px solid #e2e8f0;">
                                    <div style="flex: 0 0 40%; font-weight: 700; color: #475569;">{{ $key }}</div>
                                    <div style="flex: 1; color: #1e293b;">{{ $value }}</div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-center text-muted py-4">Thông số kỹ thuật đang được cập nhật</p>
                        @endif
                    </div>
                </div>

                <!-- Reviews Tab -->
                <div class="tab-pane" id="reviews">
                    <!-- Reviews Summary -->
                    <div class="reviews-summary">
                        <div class="overall-rating">{{ number_format($product->rating ?? 5, 1) }}</div>
                        <div class="rating-stars" style="font-size: 32px; margin-bottom: 10px; color: #fbbf24;">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star{{ $i <= ($product->rating ?? 5) ? '' : '-o' }}"></i>
                            @endfor
                        </div>
                        <p class="mb-0"><strong>{{ $product->reviews_count ?? 0 }}</strong> đánh giá</p>
                    </div>

                    <!-- Reviews List -->
                    @if (isset($product->reviews) && $product->reviews->count() > 0)
                        @foreach ($product->reviews as $review)
                            <div class="review-item">
                                <div class="review-header">
                                    <div class="reviewer-info">
                                        <div class="reviewer-avatar">
                                            {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="reviewer-name">{{ $review->user->name ?? 'Khách hàng' }}</div>
                                            <div class="review-date">
                                                <i class="far fa-clock me-1"></i>
                                                {{ $review->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="review-rating">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star{{ $i <= $review->rating ? '' : '-o' }}"></i>
                                        @endfor
                                    </div>
                                </div>

                                <div class="review-content">
                                    {{ $review->comment }}
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-center text-muted py-5">Chưa có đánh giá nào cho sản phẩm này</p>
                    @endif

                    <!-- Write Review -->
                    @auth
                        <div style="background: #eff6ff; padding: 30px; border-radius: 16px; margin-top: 30px;">
                            <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 20px;">
                                <i class="fas fa-edit me-2"></i>
                                Viết đánh giá của bạn
                            </h3>
                            <form action="{{ route('client.reviews.store', $product->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Đánh giá của bạn</label>
                                    <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star rating-star" 
                                               data-rating="{{ $i }}"
                                               style="font-size: 32px; color: #cbd5e1; cursor: pointer; transition: all 0.2s;"></i>
                                        @endfor
                                    </div>
                                    <input type="hidden" name="rating" id="ratingValue" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nhận xét</label>
                                    <textarea class="form-control" name="comment" rows="4" 
                                              placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm..." required></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Hình ảnh (tùy chọn)</label>
                                    <input type="file" class="form-control" name="images[]" multiple accept="image/*">
                                    <small class="text-muted">Tối đa 5 ảnh, mỗi ảnh không quá 5MB</small>
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Gửi đánh giá
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <p class="mb-3">Vui lòng đăng nhập để viết đánh giá</p>
                            <a href="{{ route('login') }}" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Đăng nhập
                            </a>
                        </div>
                    @endauth
                </div>

                <!-- Shipping Tab -->
                <div class="tab-pane" id="shipping">
                    <div style="line-height: 1.8; color: #475569;">
                        <h3 style="color: #1e293b; font-size: 24px; font-weight: 700; margin: 30px 0 15px;">
                            <i class="fas fa-shipping-fast me-2"></i>Chính sách vận chuyển
                        </h3>
                        <ul style="padding-left: 25px; margin: 15px 0;">
                            <li style="margin-bottom: 10px;">Miễn phí vận chuyển cho đơn hàng trên 500.000đ</li>
                            <li style="margin-bottom: 10px;">Giao hàng nhanh trong 24h tại Hà Nội và TP.HCM</li>
                            <li style="margin-bottom: 10px;">Giao hàng toàn quốc từ 2-5 ngày</li>
                            <li style="margin-bottom: 10px;">Kiểm tra hàng trước khi thanh toán</li>
                        </ul>

                        <h3 style="color: #1e293b; font-size: 24px; font-weight: 700; margin: 30px 0 15px;">
                            <i class="fas fa-undo me-2"></i>Chính sách đổi trả
                        </h3>
                        <ul style="padding-left: 25px; margin: 15px 0;">
                            <li style="margin-bottom: 10px;">Đổi trả miễn phí trong vòng 30 ngày</li>
                            <li style="margin-bottom: 10px;">Sản phẩm còn nguyên tem mác, chưa qua sử dụng</li>
                            <li style="margin-bottom: 10px;">Hoàn tiền 100% nếu sản phẩm lỗi do nhà sản xuất</li>
                            <li style="margin-bottom: 10px;">Hỗ trợ đổi size, màu miễn phí</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if (isset($relatedProducts) && count($relatedProducts) > 0)
            <div style="margin-top: 80px;">
                <div style="text-align: center; margin-bottom: 40px;">
                    <div style="display: inline-block; background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 8px 20px; border-radius: 20px; font-weight: 700; font-size: 14px; margin-bottom: 15px;">
                        GỢI Ý
                    </div>
                    <h2 style="font-size: 36px; font-weight: 800; color: #1e293b; margin-bottom: 10px;">Sản Phẩm Liên Quan</h2>
                    <p style="color: #64748b; font-size: 16px;">Những sản phẩm tương tự bạn có thể quan tâm</p>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 25px;">
                    @foreach ($relatedProducts as $relatedProduct)
                        @include('client.components.product-card', ['product' => $relatedProduct])
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Image Zoom Modal -->
<div class="image-modal" id="imageModal">
    <button class="modal-close" id="closeModal">
        <i class="fas fa-times"></i>
    </button>
    <img src="" alt="Zoomed image" class="modal-image" id="modalImage">
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let selectedVariants = {};
    let currentStock = {{ $totalStock }};

    // Image gallery
    $('.thumbnail-item').click(function() {
        $('.thumbnail-item').removeClass('active');
        $(this).addClass('active');
        const imageUrl = $(this).data('image');
        $('#mainImageSrc').attr('src', imageUrl);
        $('#modalImage').attr('src', imageUrl);
    });

    // Image zoom
    $('#zoomBtn, #mainImage').click(function() {
        $('#imageModal').addClass('active');
    });

    $('#closeModal, #imageModal').click(function(e) {
        if (e.target === this) {
            $('#imageModal').removeClass('active');
        }
    });

    // Quantity controls
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

    // Variant selection
    $('.variant-option').click(function() {
        if ($(this).hasClass('disabled')) return;

        const type = $(this).data('variant-type');
        const value = $(this).data('variant-value');
        const variantId = $(this).data('variant-id');
        const stock = $(this).data('variant-stock');

        // Update active state for this type
        $(`.variant-option[data-variant-type="${type}"]`).removeClass('active');
        $(this).addClass('active');

        // Store selected variant
        selectedVariants[type] = variantId;

        // Update selected text
        $(`#selected${type.charAt(0).toUpperCase() + type.slice(1)}`).text(value);

        // Update quantity max
        $('#quantity').attr('max', stock);
        if (parseInt($('#quantity').val()) > stock) {
            $('#quantity').val(stock);
        }
    });

    // Add to cart
    $('#addToCart').click(function() {
        const quantity = $('#quantity').val();
        const productId = {{ $product->id ?? 0 }};

        // Collect selected variants
        let variantIds = [];
        Object.values(selectedVariants).forEach(id => {
            if (id) variantIds.push(id);
        });

        $.ajax({
            url: '{{ route("client.cart.add", $product->id) }}',
            method: 'POST',
            data: {
                product_id: productId,
                quantity: quantity,
                variant_ids: variantIds,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    showToast('Đã thêm sản phẩm vào giỏ hàng!', 'success');
                    if ($('.cart-count').length) {
                        $('.cart-count').text(response.cart_count);
                    }
                } else {
                    showToast(response.message || 'Có lỗi xảy ra', 'error');
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Có lỗi xảy ra, vui lòng thử lại';
                showToast(message, 'error');
            }
        });
    });

    // Buy now
    $('#buyNow').click(function() {
        $('#addToCart').trigger('click');
        setTimeout(function() {
            window.location.href = '{{ route("client.checkout.index") }}';
        }, 500);
    });

    // Toggle wishlist
    $('#toggleWishlist').click(function() {
        const btn = $(this);
        const productId = btn.data('product-id');

        $.ajax({
            url: '{{ route("client.wishlist.toggle") }}',
            method: 'POST',
            data: {
                product_id: productId,
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

    // Share button
    $('#shareBtn').click(function() {
        if (navigator.share) {
            navigator.share({
                title: '{{ $product->name ?? "" }}',
                text: 'Xem sản phẩm này',
                url: window.location.href
            });
        } else {
            navigator.clipboard.writeText(window.location.href);
            showToast('Đã sao chép link sản phẩm', 'success');
        }
    });

    // Tabs
    $('.tab-link').click(function() {
        const tab = $(this).data('tab');

        $('.tab-link').removeClass('active');
        $(this).addClass('active');

        $('.tab-pane').removeClass('active');
        $('#' + tab).addClass('active');
    });

    // Rating input
    $('.rating-star').click(function() {
        const rating = $(this).data('rating');
        $('#ratingValue').val(rating);

        $('.rating-star').each(function() {
            if ($(this).data('rating') <= rating) {
                $(this).css('color', '#fbbf24');
            } else {
                $(this).css('color', '#cbd5e1');
            }
        });
    });

    $('.rating-star').hover(function() {
        const rating = $(this).data('rating');
        $('.rating-star').each(function() {
            if ($(this).data('rating') <= rating) {
                $(this).css('color', '#fbbf24');
            } else {
                $(this).css('color', '#cbd5e1');
            }
        });
    });

    // Scroll to reviews
    $('a[href="#reviews"]').click(function(e) {
        e.preventDefault();
        $('.tab-link[data-tab="reviews"]').click();
        $('html, body').animate({
            scrollTop: $('.product-tabs-section').offset().top - 100
        }, 500);
    });
});

function showToast(message, type) {
    alert(message);
}
</script>
@endpush