<style>
    .footer {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: #e2e8f0;
        padding: 60px 0 0;
        margin-top: 80px;
    }

    .footer-title {
        color: white;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        position: relative;
        padding-bottom: 10px;
    }

    .footer-title::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 50px;
        height: 3px;
        background: linear-gradient(90deg, #3b82f6, #8b5cf6);
        border-radius: 2px;
    }

    .footer-link {
        color: #cbd5e1;
        text-decoration: none;
        display: block;
        padding: 8px 0;
        transition: all 0.3s;
    }

    .footer-link:hover {
        color: #3b82f6;
        padding-left: 10px;
    }

    .footer-contact {
        display: flex;
        align-items: start;
        margin-bottom: 15px;
    }

    .footer-contact i {
        color: #3b82f6;
        margin-right: 12px;
        margin-top: 3px;
    }

    .social-links a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
        border-radius: 50%;
        margin-right: 10px;
        transition: all 0.3s;
    }

    .social-links a:hover {
        background: #3b82f6;
        color: white;
        transform: translateY(-3px);
    }

    .payment-methods {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .payment-methods img {
        height: 30px;
        background: white;
        padding: 5px 10px;
        border-radius: 5px;
    }

    .footer-bottom {
        background: rgba(0, 0, 0, 0.2);
        padding: 20px 0;
        margin-top: 40px;
        text-align: center;
        font-size: 14px;
    }

    .newsletter-form {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }

    .newsletter-form input {
        flex: 1;
        padding: 12px 20px;
        border: 1px solid #334155;
        background: rgba(255, 255, 255, 0.05);
        color: white;
        border-radius: 8px;
    }

    .newsletter-form input::placeholder {
        color: #94a3b8;
    }

    .newsletter-form button {
        padding: 12px 25px;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s;
    }

    .newsletter-form button:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
    }
</style>

<footer class="footer">
    <div class="container">
        <div class="row">
            <!-- About -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="footer-title">Về ShopX</h5>
                <p class="mb-3">Cửa hàng trực tuyến uy tín hàng đầu Việt Nam, cung cấp sản phẩm chất lượng với giá tốt
                    nhất.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="footer-title">Liên kết</h5>
                <a href="{{ route('home') }}" class="footer-link">Trang chủ</a>
                <a href="{{ route('client.products.index') }}" class="footer-link">Sản phẩm</a>
                <a href="#" class="footer-link">Về chúng tôi</a>
                <a href="#" class="footer-link">Tin tức</a>
                <a href="#" class="footer-link">Liên hệ</a>
            </div>

            <!-- Customer Service -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="footer-title">Hỗ trợ khách hàng</h5>
                <a href="#" class="footer-link">Chính sách đổi trả</a>
                <a href="#" class="footer-link">Chính sách bảo mật</a>
                <a href="#" class="footer-link">Hướng dẫn mua hàng</a>
                <a href="#" class="footer-link">Phương thức thanh toán</a>
                {{-- <a href="{{ route('order.track') }}" class="footer-link">Theo dõi đơn hàng</a> --}}
            </div>

            <!-- Contact & Newsletter -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="footer-title">Liên hệ</h5>
                <div class="footer-contact">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>123 Đường ABC, Quận XYZ, TP.HCM</span>
                </div>
                <div class="footer-contact">
                    <i class="fas fa-phone"></i>
                    <span>1900.xxxx</span>
                </div>
                <div class="footer-contact">
                    <i class="fas fa-envelope"></i>
                    <span>support@shopx.vn</span>
                </div>

                <h6 class="mt-4 mb-2" style="color: white;">Đăng ký nhận tin</h6>
                <form class="newsletter-form">
                    <input type="email" placeholder="Email của bạn" required>
                    <button type="submit">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <h6 class="mb-3" style="color: white;">Phương thức thanh toán</h6>
                <div class="payment-methods">
                    <img src="https://via.placeholder.com/60x30/4285F4/fff?text=VISA" alt="Visa">
                    <img src="https://via.placeholder.com/60x30/EB001B/fff?text=Master" alt="Mastercard">
                    <img src="https://via.placeholder.com/60x30/00457C/fff?text=Momo" alt="Momo">
                    <img src="https://via.placeholder.com/60x30/D71921/fff?text=ZaloPay" alt="ZaloPay">
                    <img src="https://via.placeholder.com/60x30/009FE3/fff?text=Bank" alt="Banking">
                </div>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container">
            <p class="mb-0">© 2024 ShopX. Tất cả quyền được bảo lưu.</p>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<button id="backToTop" class="btn btn-primary rounded-circle position-fixed"
    style="bottom: 30px; right: 30px; width: 50px; height: 50px; display: none; z-index: 999;">
    <i class="fas fa-arrow-up"></i>
</button>

<script>
    // Back to top button
    $(window).scroll(function() {
        if ($(this).scrollTop() > 300) {
            $('#backToTop').fadeIn();
        } else {
            $('#backToTop').fadeOut();
        }
    });

    $('#backToTop').click(function() {
        $('html, body').animate({
            scrollTop: 0
        }, 800);
        return false;
    });

    // Newsletter form
    $('.newsletter-form').submit(function(e) {
        e.preventDefault();
        showToast('Đăng ký nhận tin thành công!', 'success');
        $(this).find('input').val('');
    });
</script>
