<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'E-Commerce Store')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #6366f1;
            --secondary-color: #4f46e5;
            --accent-color: #f59e0b;
            --dark-color: #1e293b;
            --light-color: #f8fafc;
        }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: var(--light-color);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }

        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .card {
            border: none;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .product-img {
            height: 200px;
            object-fit: cover;
        }

        .wishlist-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
            background: white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .wishlist-btn:hover,
        .wishlist-btn.active {
            color: #ef4444;
        }

        .wishlist-btn.active i {
            fill: #ef4444;
        }

        .badge-cart {
            position: absolute;
            top: -5px;
            right: -10px;
            font-size: 0.7rem;
        }

        .hero-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 80px 0;
        }

        .category-card {
            cursor: pointer;
            text-align: center;
            padding: 30px;
        }

        .category-card i {
            font-size: 3rem;
            color: var(--primary-color);
        }

        .footer {
            background: var(--dark-color);
            color: white;
            padding: 50px 0 20px;
        }

        .price-old {
            text-decoration: line-through;
            color: #94a3b8;
        }

        .price-new {
            color: #ef4444;
            font-weight: 700;
            font-size: 1.25rem;
        }

        .rating {
            color: var(--accent-color);
        }

        .sidebar-filter .form-check {
            padding: 8px 0 8px 1.5rem;
        }

        .quantity-input {
            width: 120px;
        }

        .nav-link {
            position: relative;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        }
    </style>
    @stack('styles')
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand text-primary" href="{{ route('home') }}">
                <i class="bi bi-bag-heart-fill me-2"></i>ShopLaravel
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active fw-bold' : '' }}"
                            href="{{ route('home') }}">
                            <i class="bi bi-house me-1"></i>Trang chủ
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-grid me-1"></i>Danh mục
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Điện thoại</a></li>
                            <li><a class="dropdown-item" href="#">Laptop</a></li>
                            <li><a class="dropdown-item" href="#">Phụ kiện</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="#">Tất cả sản phẩm</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-fire me-1"></i>Khuyến mãi</a>
                    </li>
                </ul>
                <form class="d-flex me-3" style="width: 300px;">
                    <div class="input-group">
                        <input class="form-control" type="search" placeholder="Tìm kiếm sản phẩm...">
                        <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>
                <ul class="navbar-nav">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}"><i
                                    class="bi bi-box-arrow-in-right me-1"></i>Đăng nhập</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}"><i class="bi bi-person-plus me-1"></i>Đăng
                                ký</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link position-relative" href="{{ route('client.wishlist') }}">
                                <i class="bi bi-heart fs-5"></i>
                                <span class="badge bg-danger badge-cart">{{ $wishlistCount ?? 0 }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link position-relative" href="{{ route('client.cart') }}">
                                <i class="bi bi-cart3 fs-5"></i>
                                <span class="badge bg-danger badge-cart">{{ $cartCount ?? 0 }}</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle fs-5"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <h6 class="dropdown-header">Xin chào, {{ Auth::user()->name ?? 'User' }}</h6>
                                </li>
                                <li><a class="dropdown-item" href="{{ route('client.profile') }}"><i
                                            class="bi bi-person me-2"></i>Tài khoản</a></li>
                                <li><a class="dropdown-item" href="{{ route('client.orders') }}"><i
                                            class="bi bi-receipt me-2"></i>Lịch sử đơn hàng</a></li>
                                {{-- <li><a class="dropdown-item" href="{{ route('client.wishlist') }}"><i
                                            class="bi bi-heart me-2"></i>Yêu thích</a></li> --}}
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show m-0 rounded-0" role="alert">
            <div class="container">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-0 rounded-0" role="alert">
            <div class="container">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h5 class="mb-3"><i class="bi bi-bag-heart-fill me-2"></i>ShopLaravel</h5>
                    <p class="text-white-50">Cửa hàng trực tuyến uy tín hàng đầu Việt Nam với đa dạng sản phẩm chất
                        lượng cao.</p>
                    <div class="d-flex gap-3 mt-3">
                        <a href="#" class="text-white fs-5"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white fs-5"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-white fs-5"><i class="bi bi-youtube"></i></a>
                        <a href="#" class="text-white fs-5"><i class="bi bi-tiktok"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h6 class="mb-3">Về chúng tôi</h6>
                    <ul class="list-unstyled text-white-50">
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Giới
                                thiệu</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Tuyển
                                dụng</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Liên hệ</a>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h6 class="mb-3">Hỗ trợ</h6>
                    <ul class="list-unstyled text-white-50">
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Hướng dẫn mua
                                hàng</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Chính sách
                                đổi trả</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Bảo hành</a>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-4">
                    <h6 class="mb-3">Liên hệ</h6>
                    <ul class="list-unstyled text-white-50">
                        <li class="mb-2"><i class="bi bi-geo-alt me-2"></i>123 Nguyễn Văn A, Q.1, TP.HCM</li>
                        <li class="mb-2"><i class="bi bi-telephone me-2"></i>1900 1234</li>
                        <li class="mb-2"><i class="bi bi-envelope me-2"></i>support@shoplaravel.com</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4 border-secondary">
            <div class="text-center text-white-50">
                <small>&copy; 2024 ShopLaravel. All rights reserved.</small>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
