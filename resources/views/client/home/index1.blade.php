resources/views/client/home/index.blade.php
@extends('client.layouts.master')

@section('title', 'Trang chủ - ShopX')

@push('styles')
    <style>
        /* Hero Banner */
        .hero-banner {
            position: relative;
            height: 600px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 24px;
            overflow: hidden;
            margin-bottom: 80px;
        }

        .hero-content {
            position: absolute;
            top: 50%;
            left: 8%;
            transform: translateY(-50%);
            z-index: 2;
            max-width: 600px;
            color: white;
        }

        .hero-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .hero-title {
            font-size: 64px;
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 25px;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .hero-description {
            font-size: 20px;
            line-height: 1.6;
            margin-bottom: 35px;
            opacity: 0.95;
        }

        .hero-buttons {
            display: flex;
            gap: 15px;
        }

        .btn-hero {
            padding: 18px 36px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 16px;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-hero-primary {
            background: white;
            color: #667eea;
        }

        .btn-hero-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(255, 255, 255, 0.3);
        }

        .btn-hero-secondary {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .btn-hero-secondary:hover {
            background: rgba(255, 255, 255, 0.25);
            border-color: white;
        }

        .hero-image {
            position: absolute;
            right: 0;
            bottom: 0;
            height: 100%;
            max-width: 50%;
            object-fit: contain;
        }

        /* Categories */
        .categories-section {
            margin-bottom: 80px;
        }

        .section-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-badge {
            display: inline-block;
            background: #eff6ff;
            color: var(--primary-color);
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
        }

        .section-title {
            font-size: 42px;
            font-weight: 900;
            color: #1e293b;
            margin-bottom: 15px;
        }

        .section-description {
            font-size: 18px;
            color: #64748b;
            max-width: 600px;
            margin: 0 auto;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 25px;
        }

        .category-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
            text-decoration: none;
        }

        .category-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .category-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: white;
        }

        .category-card:nth-child(2) .category-icon {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .category-card:nth-child(3) .category-icon {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .category-card:nth-child(4) .category-icon {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .category-card:nth-child(5) .category-icon {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }

        .category-card:nth-child(6) .category-icon {
            background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);
        }

        .category-name {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .category-count {
            color: #64748b;
            font-size: 14px;
        }

        /* Featured Products */
        .featured-products {
            margin-bottom: 80px;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
        }

        /* Banner CTA */
        .cta-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 24px;
            padding: 80px 60px;
            text-align: center;
            color: white;
            margin-bottom: 80px;
            position: relative;
            overflow: hidden;
        }

        .cta-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        }

        .cta-content {
            position: relative;
            z-index: 2;
            max-width: 700px;
            margin: 0 auto;
        }

        .cta-title {
            font-size: 48px;
            font-weight: 900;
            margin-bottom: 20px;
        }

        .cta-description {
            font-size: 20px;
            margin-bottom: 35px;
            opacity: 0.95;
        }

        /* New Arrivals */
        .new-arrivals {
            margin-bottom: 80px;
        }

        .arrivals-slider {
            position: relative;
        }

        /* Best Sellers */
        .best-sellers {
            margin-bottom: 80px;
            background: #f8fafc;
            padding: 80px 0;
            margin-left: -100vw;
            margin-right: -100vw;
            padding-left: 100vw;
            padding-right: 100vw;
        }

        /* Features */
        .features-section {
            margin-bottom: 80px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .feature-card {
            background: white;
            border-radius: 16px;
            padding: 35px;
            text-align: center;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: var(--primary-color);
        }

        .feature-title {
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 12px;
        }

        .feature-description {
            color: #64748b;
            line-height: 1.6;
        }

        /* Newsletter */
        .newsletter-section {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            border-radius: 24px;
            padding: 60px;
            text-align: center;
            color: white;
            margin-bottom: 60px;
        }

        .newsletter-title {
            font-size: 36px;
            font-weight: 800;
            margin-bottom: 15px;
        }

        .newsletter-description {
            font-size: 18px;
            margin-bottom: 35px;
            opacity: 0.9;
        }

        .newsletter-form {
            max-width: 500px;
            margin: 0 auto;
            display: flex;
            gap: 12px;
        }

        .newsletter-input {
            flex: 1;
            padding: 16px 24px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
        }

        .newsletter-btn {
            padding: 16px 32px;
            background: white;
            color: #1e293b;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
        }

        .newsletter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 255, 255, 0.3);
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 36px;
            }

            .hero-image {
                opacity: 0.2;
            }

            .section-title {
                font-size: 32px;
            }

            .cta-title {
                font-size: 32px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <!-- Hero Banner -->
        <div class="hero-banner">
            <div class="hero-content">
                <span class="hero-badge">
                    <i class="fas fa-star me-2"></i>
                    Bộ sưu tập mới 2024
                </span>
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
                    <a href="#categories" class="btn-hero btn-hero-secondary">
                        <i class="fas fa-arrow-right"></i>
                        Khám Phá
                    </a>
                </div>
            </div>
            <img src="{{ asset('assets/images/hero-product.png') }}" alt="Hero Product" class="hero-image">
        </div>

        <!-- Categories -->
        <div class="categories-section" id="categories">
            <div class="section-header">
                <div class="section-badge">DANH MỤC</div>
                <h2 class="section-title">Danh Mục Nổi Bật</h2>
                <p class="section-description">
                    Khám phá các danh mục sản phẩm đa dạng với hàng nghìn lựa chọn
                </p>
            </div>

            <div class="categories-grid">
                @foreach ($categories ?? [] as $category)
                    <a href="{{ route('client.products.index', ['category' => $category->id]) }}" class="category-card">
                        <div class="category-icon">
                            <i class="fas fa-{{ $category->icon ?? 'shopping-bag' }}"></i>
                        </div>
                        <h3 class="category-name">{{ $category->name }}</h3>
                        <p class="category-count">{{ $category->products_count ?? 0 }} sản phẩm</p>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Featured Products -->
        <div class="featured-products">
            <div class="section-header">
                <div class="section-badge">SẢN PHẨM HOT</div>
                <h2 class="section-title">Sản Phẩm Nổi Bật</h2>
                <p class="section-description">
                    Những sản phẩm được yêu thích nhất tháng này
                </p>
            </div>

            <div class="products-grid">
                @foreach ($featuredProducts ?? [] as $product)
                    @include('client.components.product-card', ['product' => $product])
                @endforeach
            </div>
        </div>

        <!-- CTA Banner -->
        <div class="cta-banner">
            <div class="cta-content">
                <h2 class="cta-title">Giảm Giá Lên Đến 50%</h2>
                <p class="cta-description">
                    Đăng ký thành viên ngay hôm nay để nhận ưu đãi đặc biệt
                    và miễn phí vận chuyển cho đơn hàng đầu tiên
                </p>
                <a href="{{ route('register') }}" class="btn-hero btn-hero-primary">
                    <i class="fas fa-user-plus"></i>
                    Đăng Ký Ngay
                </a>
            </div>
        </div>

        <!-- New Arrivals -->
        <div class="new-arrivals">
            <div class="section-header">
                <div class="section-badge">MỚI NHẤT</div>
                <h2 class="section-title">Hàng Mới Về</h2>
                <p class="section-description">
                    Cập nhật xu hướng mới nhất với bộ sưu tập mới
                </p>
            </div>

            <div class="products-grid">
                @foreach ($newProducts ?? [] as $product)
                    @include('client.components.product-card', ['product' => $product])
                @endforeach
            </div>
        </div>
    </div>

    <!-- Best Sellers -->
    <div class="best-sellers">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">BÁN CHẠY</div>
                <h2 class="section-title">Sản Phẩm Bán Chạy</h2>
                <p class="section-description">
                    Top sản phẩm được khách hàng tin dùng nhất
                </p>
            </div>

            <div class="products-grid">
                @foreach ($bestSellers ?? [] as $product)
                    @include('client.components.product-card', ['product' => $product])
                @endforeach
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Features -->
        <div class="features-section">
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h3 class="feature-title">Giao Hàng Nhanh</h3>
                    <p class="feature-description">
                        Giao hàng toàn quốc trong 1-3 ngày với chi phí thấp nhất
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="feature-title">Bảo Hành Chính Hãng</h3>
                    <p class="feature-description">
                        100% sản phẩm chính hãng với chế độ bảo hành tốt nhất
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-undo"></i>
                    </div>
                    <h3 class="feature-title">Đổi Trả Miễn Phí</h3>
                    <p class="feature-description">
                        Đổi trả miễn phí trong 30 ngày nếu có bất kỳ vấn đề gì
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3 class="feature-title">Hỗ Trợ 24/7</h3>
                    <p class="feature-description">
                        Đội ngũ chăm sóc khách hàng luôn sẵn sàng hỗ trợ bạn
                    </p>
                </div>
            </div>
        </div>

        <!-- Newsletter -->
        <div class="newsletter-section">
            <h2 class="newsletter-title">
                <i class="fas fa-envelope-open-text me-3"></i>
                Đăng Ký Nhận Tin
            </h2>
            <p class="newsletter-description">
                Nhận thông tin về sản phẩm mới và ưu đãi đặc biệt qua email
            </p>
            {{-- <form class="newsletter-form" action="{{ route('newsletter.subscribe') }}" method="POST">
                @csrf
                <input type="email" class="newsletter-input" placeholder="Nhập email của bạn..." name="email" required>
                <button type="submit" class="newsletter-btn">
                    <i class="fas fa-paper-plane me-2"></i>
                    Đăng Ký
                </button>
            </form> --}}
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Smooth scroll for anchor links
            $('a[href^="#"]').click(function(e) {
                e.preventDefault();
                const target = $(this.hash);
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 80
                    }, 800);
                }
            });

            // Add animation on scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -100px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.category-card, .product-card, .feature-card').forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(30px)';
                el.style.transition = 'all 0.6s ease';
                observer.observe(el);
            });
        });
    </script>
@endpush
