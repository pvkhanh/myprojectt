<style>
    /* Top Bar */
    .top-bar {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        color: white;
        padding: 8px 0;
        font-size: 13px;
    }

    .top-bar a {
        color: white;
        text-decoration: none;
        transition: color 0.3s;
    }

    .top-bar a:hover {
        color: #3b82f6;
    }

    /* Main Header */
    .main-header {
        background: white;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .logo {
        font-size: 28px;
        font-weight: 700;
        background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        text-decoration: none;
    }

    /* Search Bar */
    .search-wrapper {
        position: relative;
        max-width: 600px;
    }

    .search-input {
        border-radius: 50px;
        padding: 12px 50px 12px 20px;
        border: 2px solid #e2e8f0;
        transition: all 0.3s;
    }

    .search-input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .search-btn {
        position: absolute;
        right: 5px;
        top: 50%;
        transform: translateY(-50%);
        border-radius: 50%;
        width: 40px;
        height: 40px;
        border: none;
        background: var(--primary-color);
        color: white;
    }

    /* Navigation */
    .main-nav {
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
    }

    .nav-link {
        color: #475569;
        font-weight: 500;
        padding: 12px 20px;
        transition: all 0.3s;
        position: relative;
    }

    .nav-link:hover {
        color: var(--primary-color);
    }

    .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 20px;
        right: 20px;
        height: 3px;
        background: var(--primary-color);
        border-radius: 3px 3px 0 0;
    }

    /* Icons */
    .header-icon {
        position: relative;
        color: #475569;
        font-size: 22px;
        transition: all 0.3s;
    }

    .header-icon:hover {
        color: var(--primary-color);
        transform: scale(1.1);
    }

    .badge-count {
        position: absolute;
        top: -8px;
        right: -8px;
        background: var(--danger-color);
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 600;
    }

    /* Mobile Menu */
    .mobile-menu-btn {
        border: none;
        background: none;
        font-size: 24px;
        color: #475569;
    }

    /* Dropdown Menu */
    .dropdown-menu {
        border: none;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        border-radius: 12px;
        padding: 12px;
        min-width: 250px;
    }

    .dropdown-item {
        padding: 10px 15px;
        border-radius: 8px;
        transition: all 0.3s;
    }

    .dropdown-item:hover {
        background: #f1f5f9;
        color: var(--primary-color);
    }
</style>

<!-- Top Bar -->
<div class="top-bar">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <i class="fas fa-shipping-fast me-2"></i>
                Miễn phí vận chuyển cho đơn hàng từ 500.000đ
            </div>
            <div class="col-md-6 text-end">
                <a href="tel:1900xxxx" class="me-3">
                    <i class="fas fa-phone me-1"></i> 1900.xxxx
                </a>
                {{-- <a href="{{ route('order.track') }}">
                    <i class="fas fa-map-marker-alt me-1"></i> Theo dõi đơn hàng
                </a> --}}
            </div>
        </div>
    </div>
</div>

<!-- Main Header -->
<header class="main-header">
    <div class="container py-3">
        <div class="row align-items-center">
            <!-- Logo -->
            <div class="col-lg-2 col-6">
                <a href="{{ route('client.home.index') }}" class="logo">
                    <i class="fas fa-store me-2"></i>ShopX
                </a>
            </div>

            <!-- Search Bar -->
            <div class="col-lg-6 d-none d-lg-block">
                <div class="search-wrapper">
                    <form action="{{ route('client.products.index') }}" method="GET">
                        <input type="text" name="search" class="form-control search-input"
                            placeholder="Tìm kiếm sản phẩm..." value="{{ request('search') }}">
                        <button type="submit" class="search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Header Icons -->
            <div class="col-lg-4 col-6">
                <div class="d-flex align-items-center justify-content-end gap-4">
                    <!-- Wishlist -->
                    <a href="{{ route('client.wishlist.index') }}" class="header-icon position-relative">
                        <i class="far fa-heart"></i>
                        @if (auth()->check() && auth()->user()->wishlist_count > 0)
                            <span class="badge-count">{{ auth()->user()->wishlist_count }}</span>
                        @endif
                    </a>

                    <!-- Cart -->
                    <a href="{{ route('client.cart.index') }}" class="header-icon position-relative">
                        <i class="fas fa-shopping-cart"></i>
                        @if (session('cart_count', 0) > 0)
                            <span class="badge-count">{{ session('cart_count') }}</span>
                        @endif
                    </a>

                    <!-- User Account -->
                    @auth
                        <div class="dropdown">
                            <a href="#" class="header-icon dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="far fa-user"></i>
                            </a>
                            {{-- <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('client.user.profile') }}">
                                        <i class="far fa-user me-2"></i> Tài khoản
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('user.orders') }}">
                                        <i class="fas fa-box me-2"></i> Đơn hàng
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('user.addresses') }}">
                                        <i class="fas fa-map-marker-alt me-2"></i> Địa chỉ
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
                                        </button>
                                    </form>
                                </li>
                            </ul> --}}
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="header-icon">
                            <i class="far fa-user"></i>
                        </a>
                    @endauth

                    <!-- Mobile Menu -->
                    <button class="mobile-menu-btn d-lg-none" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#mobileMenu">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Search -->
        <div class="row mt-3 d-lg-none">
            <div class="col-12">
                <div class="search-wrapper">
                    <form action="{{ route('client.products.index') }}" method="GET">
                        <input type="text" name="search" class="form-control search-input"
                            placeholder="Tìm kiếm sản phẩm...">
                        <button type="submit" class="search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="main-nav d-none d-lg-block">
        <div class="container">
            <ul class="nav justify-content-center">
                <li class="nav-item">
                    {{-- <a class="nav-link {{ request()->routeIs('client.home.index') ? 'active' : '' }}"
                        href="{{ route('client.home.index') }}">
                        <i class="fas fa-home me-1"></i> Trang chủ
                    </a> --}}
                    <a class="nav-link {{ request()->routeIs('client.home.index') ? 'active' : '' }} "
                        href="{{ route('client.home.index') }}"><i class="fas fa-home me-1"></i> Trang chủ</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('product.*') ? 'active' : '' }}"
                        href="{{ route('client.products.index') }}">
                        <i class="fas fa-th-large me-1"></i> Sản phẩm
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-fire me-1"></i> Khuyến mãi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-star me-1"></i> Bán chạy
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-newspaper me-1"></i> Tin tức
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-phone-alt me-1"></i> Liên hệ
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</header>

<!-- Mobile Menu Offcanvas -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Menu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('home') }}">
                    <i class="fas fa-home me-2"></i> Trang chủ
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('client.products.index') }}">
                    <i class="fas fa-th-large me-2"></i> Sản phẩm
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-fire me-2"></i> Khuyến mãi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-star me-2"></i> Bán chạy
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-newspaper me-2"></i> Tin tức
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-phone-alt me-2"></i> Liên hệ
                </a>
            </li>
        </ul>
    </div>
</div>
