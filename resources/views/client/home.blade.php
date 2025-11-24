{{-- resources/views/home.blade.php --}}
@extends('client.layouts.app')

@section('title', 'Trang chủ - ShopLaravel')

@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Mua sắm thông minh<br>Giá cả hợp lý</h1>
                    <p class="lead mb-4">Khám phá hàng ngàn sản phẩm chất lượng với giá tốt nhất. Giao hàng nhanh chóng, đổi
                        trả dễ dàng.</p>
                    <div class="d-flex gap-3">
                        <a href="#products" class="btn btn-light btn-lg px-4">
                            <i class="bi bi-bag me-2"></i>Mua ngay
                        </a>
                        <a href="#categories" class="btn btn-outline-light btn-lg px-4">
                            <i class="bi bi-grid me-2"></i>Danh mục
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center mt-5 mt-lg-0">
                    <img src="{{ asset('images/hero-banner.png') }}" alt="Shopping" class="img-fluid"
                        style="max-height: 400px;">
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="py-4 bg-white">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-truck text-primary fs-2 me-3"></i>
                        <div>
                            <h6 class="mb-0">Miễn phí vận chuyển</h6>
                            <small class="text-muted">Đơn hàng từ 500K</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-shield-check text-primary fs-2 me-3"></i>
                        <div>
                            <h6 class="mb-0">Bảo hành chính hãng</h6>
                            <small class="text-muted">Đổi trả trong 30 ngày</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-credit-card text-primary fs-2 me-3"></i>
                        <div>
                            <h6 class="mb-0">Thanh toán an toàn</h6>
                            <small class="text-muted">100% bảo mật</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-headset text-primary fs-2 me-3"></i>
                        <div>
                            <h6 class="mb-0">Hỗ trợ 24/7</h6>
                            <small class="text-muted">Hotline: 1900 1234</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories -->
    <section id="categories" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Danh mục sản phẩm</h2>
                <p class="text-muted">Khám phá các danh mục phổ biến</p>
            </div>
            <div class="row g-4">
                @foreach ($categories ?? [] as $category)
                    <div class="col-6 col-md-3">
                        {{-- <a href="{{ route('category.show', $category->slug) }}" class="text-decoration-none">
                            <div class="card category-card h-100">
                                <i class="bi bi-{{ $category->icon ?? 'grid' }}"></i>
                                <h6 class="mt-3 text-dark">{{ $category->name }}</h6>
                                <small class="text-muted">{{ $category->products_count ?? 0 }} sản phẩm</small>
                            </div>
                        </a> --}}
                    </div>
                @endforeach
                <!-- Sample categories if no data -->
                <div class="col-6 col-md-3">
                    <div class="card category-card h-100"><i class="bi bi-phone"></i>
                        <h6 class="mt-3">Điện thoại</h6><small class="text-muted">150 sản phẩm</small>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card category-card h-100"><i class="bi bi-laptop"></i>
                        <h6 class="mt-3">Laptop</h6><small class="text-muted">89 sản phẩm</small>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card category-card h-100"><i class="bi bi-headphones"></i>
                        <h6 class="mt-3">Phụ kiện</h6><small class="text-muted">234 sản phẩm</small>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card category-card h-100"><i class="bi bi-watch"></i>
                        <h6 class="mt-3">Đồng hồ</h6><small class="text-muted">67 sản phẩm</small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section id="products" class="py-5 bg-white">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-0">Sản phẩm nổi bật</h2>
                    <p class="text-muted mb-0">Được yêu thích nhất tuần này</p>
                </div>
                {{-- <a href="{{ route('products.index') }}" class="btn btn-outline-primary">Xem tất cả <i
                        class="bi bi-arrow-right"></i></a> --}}
            </div>
            <div class="row g-4">
                @foreach ($featuredProducts ?? [] as $product)
                    <div class="col-6 col-md-4 col-lg-3">
                        @include('components.product-card', ['product' => $product])
                    </div>
                @endforeach
                <!-- Sample products -->
                @for ($i = 1; $i <= 8; $i++)
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card h-100 position-relative">
                            <button class="wishlist-btn" onclick="toggleWishlist({{ $i }})"
                                id="wishlist-{{ $i }}">
                                <i class="bi bi-heart"></i>
                            </button>
                            @if ($i % 3 == 0)
                                <span class="badge bg-danger position-absolute" style="top:10px;left:10px;">-20%</span>
                            @endif
                            <img src="https://via.placeholder.com/300x200?text=Product+{{ $i }}"
                                class="card-img-top product-img" alt="Product">
                            <div class="card-body">
                                <small class="text-muted">Thương hiệu</small>
                                <h6 class="card-title">Sản phẩm mẫu {{ $i }}</h6>
                                <div class="rating mb-2">
                                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                        class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                        class="bi bi-star-half"></i>
                                    <small class="text-muted">(125)</small>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    @if ($i % 3 == 0)
                                        <span class="price-old">2.500.000₫</span>
                                    @endif
                                    <span class="price-new">{{ number_format(1990000 + $i * 100000) }}₫</span>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-0 pt-0">
                                <button class="btn btn-primary w-100" onclick="addToCart({{ $i }})">
                                    <i class="bi bi-cart-plus me-1"></i>Thêm vào giỏ
                                </button>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </section>

    <!-- Sale Banner -->
    <section class="py-5" style="background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%);">
        <div class="container">
            <div class="row align-items-center text-white">
                <div class="col-lg-8">
                    <h2 class="display-5 fw-bold">🔥 Flash Sale - Giảm đến 50%</h2>
                    <p class="lead mb-0">Chỉ còn 24 giờ! Nhanh tay săn deal hot ngay hôm nay.</p>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <a href="#" class="btn btn-light btn-lg px-4">Xem ngay <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- New Arrivals -->
    <section class="py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-0">Sản phẩm mới</h2>
                    <p class="text-muted mb-0">Vừa cập nhật trong tuần</p>
                </div>
                <a href="#" class="btn btn-outline-primary">Xem tất cả <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="row g-4">
                @for ($i = 1; $i <= 4; $i++)
                    <div class="col-6 col-md-3">
                        <div class="card h-100 position-relative">
                            <button class="wishlist-btn"><i class="bi bi-heart"></i></button>
                            <span class="badge bg-success position-absolute" style="top:10px;left:10px;">Mới</span>
                            <img src="https://via.placeholder.com/300x200?text=New+{{ $i }}"
                                class="card-img-top product-img" alt="Product">
                            <div class="card-body">
                                <small class="text-muted">Thương hiệu</small>
                                <h6 class="card-title">Sản phẩm mới {{ $i }}</h6>
                                <div class="rating mb-2"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                        class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                        class="bi bi-star"></i></div>
                                <span class="price-new">{{ number_format(2990000 + $i * 200000) }}₫</span>
                            </div>
                            <div class="card-footer bg-white border-0 pt-0">
                                <button class="btn btn-primary w-100"><i class="bi bi-cart-plus me-1"></i>Thêm vào
                                    giỏ</button>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        function toggleWishlist(id) {
            const btn = document.getElementById('wishlist-' + id);
            const icon = btn.querySelector('i');
            btn.classList.toggle('active');
            icon.classList.toggle('bi-heart');
            icon.classList.toggle('bi-heart-fill');
            // AJAX call to toggle wishlist
            fetch('/wishlist/toggle/' + id, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message) console.log(data.message);
                });
        }

        function addToCart(id) {
            fetch('/cart/add/' + id, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    alert('Đã thêm vào giỏ hàng!');
                    location.reload();
                });
        }
    </script>
@endpush
