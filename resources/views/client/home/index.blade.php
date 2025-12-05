{{-- resources/views/client/home/index.blade.php --}}
@extends('client.layouts.master')

@section('title', 'Trang chủ - ShopX')

@push('styles')
    <style>
        :root {
            --primary: #ee4d2d;
            --primary-dark: #d73211;
            --secondary: #1890ff;
            --success: #00b894;
            --warning: #fdcb6e;
            --dark: #2d3436;
            --gray: #636e72;
            --light: #f5f5f5;
            --white: #ffffff;
        }

        body {
            background: #f5f5f5;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        /* ==================== HERO SLIDER ==================== */
        .hero-section {
            margin-bottom: 40px;
        }

        .hero-slider-wrapper {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            height: 450px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .hero-slide {
            display: none;
            height: 100%;
            position: relative;
        }

        .hero-slide.active {
            display: flex;
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .hero-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            color: white;
            z-index: 2;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            padding: 10px 20px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
            width: fit-content;
        }

        .hero-title {
            font-size: 52px;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 20px;
            text-shadow: 0 2px 20px rgba(0, 0, 0, 0.2);
        }

        .hero-description {
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 35px;
            opacity: 0.95;
            max-width: 500px;
        }

        .hero-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn-hero {
            padding: 16px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }

        .btn-hero-primary {
            background: white;
            color: var(--primary);
        }

        .btn-hero-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 255, 255, 0.3);
        }

        .btn-hero-outline {
            background: transparent;
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.5);
        }

        .btn-hero-outline:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: white;
        }

        .hero-image {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .hero-image img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 10px 30px rgba(0, 0, 0, 0.3));
        }

        /* Slider Controls */
        .slider-controls {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 12px;
            z-index: 3;
        }

        .slider-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.4);
            cursor: pointer;
            transition: all 0.3s;
        }

        .slider-dot.active {
            background: white;
            width: 30px;
            border-radius: 6px;
        }

        .slider-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 50%;
            color: white;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.3s;
            z-index: 3;
        }

        .slider-arrow:hover {
            background: rgba(255, 255, 255, 0.4);
        }

        .slider-arrow.prev {
            left: 20px;
        }

        .slider-arrow.next {
            right: 20px;
        }

        /* ==================== CATEGORIES SECTION ==================== */
        .section {
            margin-bottom: 50px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .section-title-wrap {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .section-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .section-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--dark);
            margin: 0;
        }

        .section-subtitle {
            font-size: 14px;
            color: var(--gray);
            margin: 0;
        }

        .view-all-link {
            color: var(--secondary);
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: gap 0.3s;
        }

        .view-all-link:hover {
            gap: 10px;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
        }

        .category-card {
            background: white;
            border-radius: 12px;
            padding: 25px 20px;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .category-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            opacity: 0;
            transition: opacity 0.3s;
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-color: var(--primary);
        }

        .category-card:hover::before {
            opacity: 0.05;
        }

        .category-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 15px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: white;
            transition: transform 0.3s;
        }

        .category-card:hover .category-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .category-card:nth-child(2) .category-icon {
            background: linear-gradient(135deg, #f093fb, #f5576c);
        }

        .category-card:nth-child(3) .category-icon {
            background: linear-gradient(135deg, #4facfe, #00f2fe);
        }

        .category-card:nth-child(4) .category-icon {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
        }

        .category-card:nth-child(5) .category-icon {
            background: linear-gradient(135deg, #fa709a, #fee140);
        }

        .category-card:nth-child(6) .category-icon {
            background: linear-gradient(135deg, #30cfd0, #330867);
        }

        .category-name {
            font-size: 16px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 5px;
        }

        .category-count {
            font-size: 13px;
            color: var(--gray);
        }

        /* ==================== FLASH SALE ==================== */
        .flash-sale-section {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 50px;
            position: relative;
            overflow: hidden;
        }

        .flash-sale-section::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 300px;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(238, 77, 45, 0.05));
        }

        .flash-sale-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
            position: relative;
            z-index: 1;
        }

        .flash-sale-title {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .flash-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #ff6b6b, #ee5a6f);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .flash-title-text h3 {
            font-size: 24px;
            font-weight: 700;
            color: var(--dark);
            margin: 0;
        }

        .flash-title-text p {
            font-size: 13px;
            color: var(--gray);
            margin: 0;
        }

        .flash-timer {
            display: flex;
            gap: 10px;
        }

        .timer-box {
            background: var(--dark);
            color: white;
            padding: 12px 16px;
            border-radius: 8px;
            text-align: center;
            min-width: 60px;
        }

        .timer-number {
            font-size: 24px;
            font-weight: 700;
            display: block;
        }

        .timer-label {
            font-size: 11px;
            opacity: 0.8;
            text-transform: uppercase;
        }

        .flash-products-slider {
            position: relative;
            overflow: hidden;
        }

        .flash-products-track {
            display: flex;
            gap: 20px;
            transition: transform 0.3s ease;
        }

        .flash-product-item {
            flex: 0 0 calc(20% - 16px);
            min-width: 0;
        }

        /* ==================== PRODUCTS GRID ==================== */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }

        /* ==================== BANNERS GRID ==================== */
        .banners-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 50px;
        }

        .banner-card {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            height: 200px;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .banner-card:hover {
            transform: translateY(-5px);
        }

        .banner-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .banner-content {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to right, rgba(0, 0, 0, 0.7), transparent);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 30px;
            color: white;
        }

        .banner-title {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .banner-subtitle {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 15px;
        }

        .banner-link {
            color: white;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        /* ==================== FEATURES SECTION ==================== */
        .features-section {
            background: white;
            border-radius: 12px;
            padding: 50px 30px;
            margin-bottom: 50px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 20px;
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #e8f5ff, #d0ebff);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .feature-icon i {
            font-size: 28px;
            color: var(--secondary);
        }

        .feature-content h4 {
            font-size: 17px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
        }

        .feature-content p {
            font-size: 14px;
            color: var(--gray);
            line-height: 1.6;
            margin: 0;
        }

        /* ==================== NEWSLETTER ==================== */
        .newsletter-section {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 12px;
            padding: 60px;
            text-align: center;
            color: white;
            margin-bottom: 50px;
            position: relative;
            overflow: hidden;
        }

        .newsletter-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1), transparent);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .newsletter-content {
            position: relative;
            z-index: 1;
            max-width: 600px;
            margin: 0 auto;
        }

        .newsletter-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            backdrop-filter: blur(10px);
        }

        .newsletter-icon i {
            font-size: 36px;
        }

        .newsletter-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .newsletter-description {
            font-size: 16px;
            opacity: 0.95;
            margin-bottom: 30px;
        }

        .newsletter-form {
            display: flex;
            gap: 12px;
            max-width: 500px;
            margin: 0 auto;
        }

        .newsletter-input {
            flex: 1;
            padding: 16px 24px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            outline: none;
        }

        .newsletter-btn {
            padding: 16px 32px;
            background: var(--dark);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            white-space: nowrap;
        }

        .newsletter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        /* ==================== BRANDS SLIDER ==================== */
        .brands-section {
            background: white;
            border-radius: 12px;
            padding: 40px 30px;
            margin-bottom: 50px;
        }

        .brands-slider {
            display: flex;
            gap: 30px;
            overflow-x: auto;
            padding: 20px 0;
            scrollbar-width: none;
        }

        .brands-slider::-webkit-scrollbar {
            display: none;
        }

        .brand-item {
            flex: 0 0 150px;
            height: 80px;
            background: #f8f9fa;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
            transition: all 0.3s;
            cursor: pointer;
        }

        .brand-item:hover {
            background: white;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transform: translateY(-3px);
        }

        .brand-item img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            filter: grayscale(1);
            opacity: 0.6;
            transition: all 0.3s;
        }

        .brand-item:hover img {
            filter: grayscale(0);
            opacity: 1;
        }

        /* ==================== RESPONSIVE ==================== */
        @media (max-width: 991px) {
            .hero-slider-wrapper {
                height: 350px;
            }

            .hero-content {
                padding: 40px 30px;
            }

            .hero-title {
                font-size: 36px;
            }

            .hero-description {
                font-size: 16px;
            }

            .hero-image {
                display: none;
            }

            .section-title {
                font-size: 24px;
            }

            .categories-grid {
                grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            }

            .banners-grid {
                grid-template-columns: 1fr;
            }

            .flash-sale-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
            }

            .flash-product-item {
                flex: 0 0 calc(50% - 10px);
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .newsletter-section {
                padding: 40px 30px;
            }
        }

        @media (max-width: 575px) {
            .hero-slider-wrapper {
                height: 280px;
            }

            .hero-content {
                padding: 30px 20px;
            }

            .hero-title {
                font-size: 28px;
            }

            .hero-buttons {
                flex-direction: column;
            }

            .btn-hero {
                width: 100%;
                justify-content: center;
            }

            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .products-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .newsletter-form {
                flex-direction: column;
            }

            .newsletter-btn {
                width: 100%;
            }

            .timer-box {
                min-width: 50px;
                padding: 10px 12px;
            }

            .timer-number {
                font-size: 20px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container py-3">
        <!-- Hero Slider -->
        <div class="hero-section">
            <div class="hero-slider-wrapper">
                <!-- Slide 1 -->
                <div class="hero-slide active" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="hero-content">
                        <div class="hero-badge">
                            <i class="fas fa-star"></i>
                            <span>Bộ sưu tập mới 2024</span>
                        </div>
                        <h1 class="hero-title">
                            Khám Phá<br>
                            Phong Cách<br>
                            Của Bạn
                        </h1>
                        <p class="hero-description">
                            Hàng nghìn sản phẩm chất lượng cao với giá tốt nhất.
                            Giao hàng nhanh chóng và miễn phí đổi trả trong 30 ngày.
                        </p>
                        <div class="hero-buttons">
                            <a href="{{ route('client.products.index') }}" class="btn-hero btn-hero-primary">
                                <i class="fas fa-shopping-bag"></i>
                                Mua Ngay
                            </a>
                            <a href="#categories" class="btn-hero btn-hero-outline">
                                <i class="fas fa-arrow-right"></i>
                                Khám Phá
                            </a>
                        </div>
                    </div>
                    <div class="hero-image">
                        <img src="{{ asset('assets/images/hero-product.png') }}" alt="Hero Product">
                    </div>
                </div>

                <!-- Slide 2 -->
                <div class="hero-slide" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <div class="hero-content">
                        <div class="hero-badge">
                            <i class="fas fa-fire"></i>
                            <span>Sale Khủng</span>
                        </div>
                        <h1 class="hero-title">
                            Giảm Giá<br>
                            Lên Đến 50%
                        </h1>
                        <p class="hero-description">
                            Sự kiện flash sale đặc biệt. Cơ hội sở hữu sản phẩm
                            yêu thích với giá siêu ưu đãi. Nhanh tay kẻo lỡ!
                        </p>
                        <div class="hero-buttons">
                            <a href="{{ route('client.products.index') }}" class="btn-hero btn-hero-primary">
                                <i class="fas fa-bolt"></i>
                                Mua Ngay
                            </a>
                            <a href="#flash-sale" class="btn-hero btn-hero-outline">
                                Xem Flash Sale
                            </a>
                        </div>
                    </div>
                    <div class="hero-image">
                        <img src="{{ asset('assets/images/hero-sale.png') }}" alt="Sale">
                    </div>
                </div>

                <!-- Slide 3 -->
                <div class="hero-slide" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <div class="hero-content">
                        <div class="hero-badge">
                            <i class="fas fa-gift"></i>
                            <span>Ưu đãi đặc biệt</span>
                        </div>
                        <h1 class="hero-title">
                            Freeship<br>
                            Toàn Quốc
                        </h1>
                        <p class="hero-description">
                            Miễn phí vận chuyển cho mọi đơn hàng.
                            Giao nhanh trong 24h tại khu vực nội thành.
                        </p>
                        <div class="hero-buttons">
                            <a href="{{ route('client.products.index') }}" class="btn-hero btn-hero-primary">
                                <i class="fas fa-truck"></i>
                                Đặt Hàng Ngay
                            </a>
                            <a href="#features" class="btn-hero btn-hero-outline">
                                Tìm Hiểu Thêm
                            </a>
                        </div>
                    </div>
                    <div class="hero-image">
                        <img src="{{ asset('assets/images/hero-delivery.png') }}" alt="Delivery">
                    </div>
                </div>

                <!-- Slider Controls -->
                <button class="slider-arrow prev" id="prevSlide">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="slider-arrow next" id="nextSlide">
                    <i class="fas fa-chevron-right"></i>
                </button>

                <div class="slider-controls" id="sliderDots"></div>
            </div>
        </div>

        <!-- Categories -->
        <div class="section" id="categories">
            <div class="section-header">
                <div class="section-title-wrap">
                    <div class="section-icon">
                        <i class="fas fa-th-large"></i>
                    </div>
                    <div>
                        <h2 class="section-title">Danh Mục Nổi Bật</h2>
                        <p class="section-subtitle">Khám phá các sản phẩm đa dạng</p>
                    </div>
                </div>
                <a href="{{ route('client.products.index') }}" class="view-all-link">
                    Xem tất cả
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="categories-grid">
                @foreach ($categories ?? [] as $category)
                    <a href="{{ route('client.products.index', ['category' => $category->id]) }}" class="category-card">
                        <div class="category-icon">
                            <i class="fas fa-{{ $category->icon ?? 'shopping-bag' }}"></i>
                        </div>
                        <div class="category-name">{{ $category->name }}</div>
                        <div class="category-count">{{ $category->products_count ?? 0 }} sản phẩm</div>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Flash Sale -->
        <div class="flash-sale-section" id="flash-sale">
            <div class="flash-sale-header">
                <div class="flash-sale-title">
                    <div class="flash-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div class="flash-title-text">
                        <h3>Flash Sale</h3>
                        <p>Giảm giá cực sốc trong thời gian có hạn</p>
                    </div>
                </div>
                <div class="flash-timer">
                    <div class="timer-box">
                        <span class="timer-number" id="hours">02</span>
                        <span class="timer-label">Giờ</span>
                    </div>
                    <div class="timer-box">
                        <span class="timer-number" id="minutes">30</span>
                        <span class="timer-label">Phút</span>
                    </div>
                    <div class="timer-box">
                        <span class="timer-number" id="seconds">45</span>
                        <span class="timer-label">Giây</span>
                    </div>
                </div>
            </div>

            <div class="flash-products-slider">
                <div class="flash-products-track">
                    @foreach ($flashSaleProducts ?? ($featuredProducts ?? []) as $product)
                        <div class="flash-product-item">
                            @include('client.components.product-card', ['product' => $product])
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Promotional Banners -->
        <div class="banners-grid">
            <div class="banner-card" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                <div class="banner-content">
                    <h3 class="banner-title">Điện Thoại</h3>
                    <p class="banner-subtitle">Giảm đến 30%</p>
                    <a href="{{ route('client.products.index', ['category' => 'phones']) }}" class="banner-link">
                        Mua ngay <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="banner-card" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
                <div class="banner-content">
                    <h3 class="banner-title">Laptop</h3>
                    <p class="banner-subtitle">Trả góp 0%</p>
                    <a href="{{ route('client.products.index', ['category' => 'laptops']) }}" class="banner-link">
                        Khám phá <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="banner-card" style="background: linear-gradient(135deg, #4facfe, #00f2fe);">
                <div class="banner-content">
                    <h3 class="banner-title">Phụ Kiện</h3>
                    <p class="banner-subtitle">Mua 2 giảm 20%</p>
                    <a href="{{ route('client.products.index', ['category' => 'accessories']) }}" class="banner-link">
                        Xem ngay <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Featured Products -->
        <div class="section">
            <div class="section-header">
                <div class="section-title-wrap">
                    <div class="section-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div>
                        <h2 class="section-title">Sản Phẩm Nổi Bật</h2>
                        <p class="section-subtitle">Những sản phẩm được yêu thích nhất</p>
                    </div>
                </div>
                <a href="{{ route('client.products.index') }}" class="view-all-link">
                    Xem tất cả
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="products-grid">
                @foreach ($featuredProducts ?? [] as $product)
                    @include('client.components.product-card', ['product' => $product])
                @endforeach
            </div>
        </div>

        <!-- New Arrivals -->
        <div class="section">
            <div class="section-header">
                <div class="section-title-wrap">
                    <div class="section-icon">
                        <i class="fas fa-sparkles"></i>
                    </div>
                    <div>
                        <h2 class="section-title">Hàng Mới Về</h2>
                        <p class="section-subtitle">Cập nhật xu hướng mới nhất</p>
                    </div>
                </div>
                <a href="{{ route('client.products.index', ['sort' => 'newest']) }}" class="view-all-link">
                    Xem tất cả
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="products-grid">
                @foreach ($newProducts ?? [] as $product)
                    @include('client.components.product-card', ['product' => $product])
                @endforeach
            </div>
        </div>
    </div>

    <!-- Best Sellers Section -->
    <div style="background: white; padding: 50px 0; margin-bottom: 50px;">
        <div class="container">
            <div class="section-header">
                <div class="section-title-wrap">
                    <div class="section-icon">
                        <i class="fas fa-fire"></i>
                    </div>
                    <div>
                        <h2 class="section-title">Sản Phẩm Bán Chạy</h2>
                        <p class="section-subtitle">Top sản phẩm được khách hàng tin dùng</p>
                    </div>
                </div>
                <a href="{{ route('client.products.index', ['sort' => 'best_selling']) }}" class="view-all-link">
                    Xem tất cả
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="products-grid">
                @foreach ($bestSellers ?? [] as $product)
                    @include('client.components.product-card', ['product' => $product])
                @endforeach
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Features Section -->
        <div class="features-section" id="features">
            <div class="features-grid">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <div class="feature-content">
                        <h4>Giao Hàng Nhanh Chóng</h4>
                        <p>Giao hàng toàn quốc trong 1-3 ngày. Miễn phí vận chuyển cho đơn hàng từ 500K.</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="feature-content">
                        <h4>Bảo Hành Chính Hãng</h4>
                        <p>100% sản phẩm chính hãng với chế độ bảo hành uy tín và tốt nhất thị trường.</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-undo"></i>
                    </div>
                    <div class="feature-content">
                        <h4>Đổi Trả Miễn Phí</h4>
                        <p>Đổi trả miễn phí trong 30 ngày nếu có bất kỳ vấn đề gì về chất lượng sản phẩm.</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div class="feature-content">
                        <h4>Hỗ Trợ 24/7</h4>
                        <p>Đội ngũ chăm sóc khách hàng luôn sẵn sàng hỗ trợ bạn mọi lúc, mọi nơi.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Brands Section -->
        <div class="brands-section">
            <div class="section-header">
                <div class="section-title-wrap">
                    <div class="section-icon">
                        <i class="fas fa-copyright"></i>
                    </div>
                    <div>
                        <h2 class="section-title">Thương Hiệu Nổi Tiếng</h2>
                        <p class="section-subtitle">Đối tác chính thức của các thương hiệu hàng đầu</p>
                    </div>
                </div>
            </div>
            {{-- {{ route('newsletter.subscribe') }} --}}
            <div class="brands-slider">
                @foreach ($brands ?? [] as $brand)
                    <div class="brand-item">
                        <img src="{{ $brand->logo ?? 'https://via.placeholder.com/150x60?text=' . $brand->name }}"
                            alt="{{ $brand->name }}">
                    </div>
                @endforeach

                @if (empty($brands) || count($brands) == 0)
                    <!-- Placeholder brands -->
                    <div class="brand-item">
                        <img src="https://via.placeholder.com/150x60?text=Apple" alt="Apple">
                    </div>
                    <div class="brand-item">
                        <img src="https://via.placeholder.com/150x60?text=Samsung" alt="Samsung">
                    </div>
                    <div class="brand-item">
                        <img src="https://via.placeholder.com/150x60?text=Sony" alt="Sony">
                    </div>
                    <div class="brand-item">
                        <img src="https://via.placeholder.com/150x60?text=LG" alt="LG">
                    </div>
                    <div class="brand-item">
                        <img src="https://via.placeholder.com/150x60?text=Dell" alt="Dell">
                    </div>
                    <div class="brand-item">
                        <img src="https://via.placeholder.com/150x60?text=HP" alt="HP">
                    </div>
                @endif
            </div>
        </div>

        <!-- Newsletter -->
        <div class="newsletter-section">
            <div class="newsletter-content">
                <div class="newsletter-icon">
                    <i class="fas fa-envelope-open-text"></i>
                </div>
                <h2 class="newsletter-title">Đăng Ký Nhận Tin</h2>
                <p class="newsletter-description">
                    Nhận thông tin về sản phẩm mới và ưu đãi đặc biệt qua email
                </p>
                {{-- <form class="newsletter-form" id="newsletterForm">
                    @csrf
                    <input type="email" class="newsletter-input" placeholder="Nhập email của bạn..." name="email"
                        required>
                    <button type="submit" class="newsletter-btn">
                        <i class="fas fa-paper-plane me-2"></i>
                        Đăng Ký
                    </button>
                </form> --}}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // ==================== HERO SLIDER ====================
            let currentSlide = 0;
            const slides = $('.hero-slide');
            const totalSlides = slides.length;

            // Create dots
            const dotsContainer = $('#sliderDots');
            for (let i = 0; i < totalSlides; i++) {
                dotsContainer.append(`<div class="slider-dot ${i === 0 ? 'active' : ''}" data-slide="${i}"></div>`);
            }

            function showSlide(index) {
                if (index >= totalSlides) index = 0;
                if (index < 0) index = totalSlides - 1;

                slides.removeClass('active');
                slides.eq(index).addClass('active');

                $('.slider-dot').removeClass('active');
                $('.slider-dot').eq(index).addClass('active');

                currentSlide = index;
            }

            // Auto slide
            let autoSlideInterval = setInterval(() => {
                showSlide(currentSlide + 1);
            }, 5000);

            // Navigation
            $('#prevSlide').click(function() {
                clearInterval(autoSlideInterval);
                showSlide(currentSlide - 1);
                autoSlideInterval = setInterval(() => showSlide(currentSlide + 1), 5000);
            });

            $('#nextSlide').click(function() {
                clearInterval(autoSlideInterval);
                showSlide(currentSlide + 1);
                autoSlideInterval = setInterval(() => showSlide(currentSlide + 1), 5000);
            });

            // Dots navigation
            $(document).on('click', '.slider-dot', function() {
                clearInterval(autoSlideInterval);
                showSlide($(this).data('slide'));
                autoSlideInterval = setInterval(() => showSlide(currentSlide + 1), 5000);
            });

            // ==================== FLASH SALE TIMER ====================
            function updateTimer() {
                const now = new Date();
                const endTime = new Date();
                endTime.setHours(endTime.getHours() + 2);
                endTime.setMinutes(endTime.getMinutes() + 30);
                endTime.setSeconds(endTime.getSeconds() + 45);

                const diff = endTime - now;

                if (diff > 0) {
                    const hours = Math.floor(diff / (1000 * 60 * 60));
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                    $('#hours').text(String(hours).padStart(2, '0'));
                    $('#minutes').text(String(minutes).padStart(2, '0'));
                    $('#seconds').text(String(seconds).padStart(2, '0'));
                }
            }

            updateTimer();
            setInterval(updateTimer, 1000);

            // ==================== SMOOTH SCROLL ====================
            $('a[href^="#"]').click(function(e) {
                const target = $(this.hash);
                if (target.length) {
                    e.preventDefault();
                    $('html, body').animate({
                        scrollTop: target.offset().top - 80
                    }, 800);
                }
            });

            // ==================== SCROLL ANIMATIONS ====================
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Animate elements on scroll
            $('.category-card, .product-card, .feature-item, .banner-card').each(function() {
                this.style.opacity = '0';
                this.style.transform = 'translateY(30px)';
                this.style.transition = 'all 0.6s ease';
                observer.observe(this);
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
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
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
                            }, 1500);
                        } else {
                            showToast('Có lỗi xảy ra', 'error');
                        }
                    }
                });
            });

            // ==================== NEWSLETTER FORM ====================
            // $('#newsletterForm').submit(function(e) {
            //     e.preventDefault();

            //     const form = $(this);
            //     const email = form.find('input[name="email"]').val();
            //     const btn = form.find('button');
            //     const originalHtml = btn.html();

            //     btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang xử lý...');

            //     $.ajax({
            // url: '',
            //         method: 'POST',
            //         data: {
            //             email: email,
            //             _token: '{{ csrf_token() }}'
            //         },
            //         success: function(response) {
            //             if (response.success) {
            //                 showToast(response.message || 'Đăng ký thành công!', 'success');
            //                 form[0].reset();
            //             } else {
            //                 showToast(response.message || 'Có lỗi xảy ra', 'error');
            //             }
            //         },
            //         error: function(xhr) {
            //             const message = xhr.responseJSON?.message || 'Có lỗi xảy ra, vui lòng thử lại!';
            //             showToast(message, 'error');
            //         },
            //         complete: function() {
            //             btn.prop('disabled', false).html(originalHtml);
            //         }
            //     });
            // });

            // ==================== BRANDS SLIDER AUTO SCROLL ====================
            const brandsSlider = $('.brands-slider');
            let scrollAmount = 0;

            function autoBrandsScroll() {
                scrollAmount += 1;
                if (scrollAmount >= brandsSlider[0].scrollWidth - brandsSlider[0].clientWidth) {
                    scrollAmount = 0;
                }
                brandsSlider.scrollLeft(scrollAmount);
            }

            let brandsInterval = setInterval(autoBrandsScroll, 30);

            brandsSlider.hover(
                function() {
                    clearInterval(brandsInterval);
                },
                function() {
                    brandsInterval = setInterval(autoBrandsScroll, 30);
                }
            );
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

            const alertClass = type === 'error' ? 'danger' : 'success';
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
