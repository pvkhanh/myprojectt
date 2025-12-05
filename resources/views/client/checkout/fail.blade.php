@extends('client.layouts.master')

@section('title', 'Thanh toán thất bại')

@push('styles')
    <style>
        .fail-container {
            max-width: 700px;
            margin: 80px auto;
            padding: 0 20px;
        }

        .fail-animation {
            text-align: center;
            margin-bottom: 40px;
        }

        .fail-icon {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: shake 0.6s ease-out, pulse 2s ease-in-out 0.6s infinite;
            box-shadow: 0 20px 60px rgba(239, 68, 68, 0.4);
        }

        .fail-icon i {
            font-size: 70px;
            color: white;
            animation: scaleIn 0.5s ease-in-out 0.2s both;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            10%,
            30%,
            50%,
            70%,
            90% {
                transform: translateX(-10px);
            }

            20%,
            40%,
            60%,
            80% {
                transform: translateX(10px);
            }
        }

        @keyframes scaleIn {
            0% {
                transform: scale(0);
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
            }
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

        .fail-title {
            font-size: 36px;
            font-weight: 900;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 16px;
            animation: fadeInUp 0.6s ease-out 0.3s both;
        }

        .fail-message {
            font-size: 18px;
            color: #64748b;
            margin-bottom: 50px;
            line-height: 1.8;
            animation: fadeInUp 0.6s ease-out 0.4s both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fail-card {
            background: white;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 10px 50px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            animation: fadeInUp 0.6s ease-out 0.5s both;
        }

        .error-details {
            background: linear-gradient(135deg, #fee, #fdd);
            border-left: 5px solid #ef4444;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 30px;
        }

        .error-title {
            font-weight: 800;
            color: #991b1b;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 18px;
        }

        .error-message {
            color: #dc2626;
            font-size: 15px;
            line-height: 1.6;
            margin: 0;
        }

        .help-section {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 30px;
        }

        .help-title {
            font-size: 20px;
            font-weight: 800;
            color: #1e40af;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .help-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .help-item {
            display: flex;
            gap: 15px;
            padding: 15px 0;
            color: #1e40af;
        }

        .help-item i {
            font-size: 20px;
            margin-top: 3px;
            flex-shrink: 0;
        }

        .help-item-content {
            flex: 1;
        }

        .help-item strong {
            display: block;
            margin-bottom: 5px;
            font-size: 16px;
        }

        .help-item p {
            margin: 0;
            font-size: 14px;
            opacity: 0.9;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            animation: fadeInUp 0.6s ease-out 0.6s both;
        }

        .btn-action {
            padding: 18px 35px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 16px;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
        }

        .btn-danger:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(239, 68, 68, 0.5);
        }

        .btn-secondary {
            background: white;
            color: #64748b;
            border: 2px solid #e2e8f0;
        }

        .btn-secondary:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            transform: translateY(-3px);
        }

        .support-info {
            text-align: center;
            padding: 30px;
            background: white;
            border-radius: 16px;
            border: 2px dashed #e2e8f0;
            animation: fadeInUp 0.6s ease-out 0.7s both;
        }

        .support-title {
            font-size: 18px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 15px;
        }

        .support-contacts {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }

        .support-contact {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #667eea;
            font-weight: 600;
        }

        .support-contact i {
            font-size: 20px;
        }

        @media (max-width: 768px) {
            .fail-title {
                font-size: 28px;
            }

            .action-buttons {
                grid-template-columns: 1fr;
            }

            .support-contacts {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="fail-container">
        <div class="fail-animation">
            <div class="fail-icon">
                <i class="fas fa-times"></i>
            </div>
            <h1 class="fail-title">Thanh Toán Thất Bại</h1>
            <p class="fail-message">
                Rất tiếc, giao dịch của bạn không thể hoàn tất.<br>
                Vui lòng thử lại hoặc chọn phương thức thanh toán khác.
            </p>
        </div>

        <div class="fail-card">
            <!-- Error Details -->
            @if (session('payment_error'))
                <div class="error-details">
                    <div class="error-title">
                        <i class="fas fa-exclamation-circle"></i>
                        Chi tiết lỗi
                    </div>
                    <p class="error-message">{{ session('payment_error') }}</p>
                </div>
            @endif

            <!-- Help Section -->
            <div class="help-section">
                <div class="help-title">
                    <i class="fas fa-lightbulb"></i>
                    Một số nguyên nhân thường gặp
                </div>
                <ul class="help-list">
                    <li class="help-item">
                        <i class="fas fa-credit-card"></i>
                        <div class="help-item-content">
                            <strong>Thông tin thẻ không chính xác</strong>
                            <p>Vui lòng kiểm tra lại số thẻ, ngày hết hạn và mã CVV</p>
                        </div>
                    </li>
                    <li class="help-item">
                        <i class="fas fa-wallet"></i>
                        <div class="help-item-content">
                            <strong>Số dư không đủ</strong>
                            <p>Tài khoản của bạn có thể không đủ số dư để thực hiện giao dịch</p>
                        </div>
                    </li>
                    <li class="help-item">
                        <i class="fas fa-lock"></i>
                        <div class="help-item-content">
                            <strong>Thẻ bị khóa hoặc hạn chế</strong>
                            <p>Thẻ có thể đã bị khóa hoặc bị hạn chế giao dịch trực tuyến</p>
                        </div>
                    </li>
                    <li class="help-item">
                        <i class="fas fa-network-wired"></i>
                        <div class="help-item-content">
                            <strong>Sự cố kết nối</strong>
                            <p>Có thể do mạng không ổn định trong quá trình thanh toán</p>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                @isset($order)
                    <a href="{{ route('client.checkout.index') }}" class="btn-action btn-danger">
                        <i class="fas fa-redo"></i>
                        Thử thanh toán lại
                    </a>
                @endisset

                <a href="{{ route('client.cart.index') }}" class="btn-action btn-secondary">
                    <i class="fas fa-shopping-cart"></i>
                    Quay lại giỏ hàng
                </a>
            </div>
        </div>

        <!-- Support Info -->
        <div class="support-info">
            <div class="support-title">
                <i class="fas fa-headset me-2"></i>
                Cần hỗ trợ?
            </div>
            <p style="color: #64748b; margin-bottom: 20px;">
                Nếu bạn gặp sự cố liên tục hoặc cần hỗ trợ, đừng ngại liên hệ với chúng tôi
            </p>
            <div class="support-contacts">
                <div class="support-contact">
                    <i class="fas fa-phone"></i>
                    <span>Hotline: 1900-xxxx</span>
                </div>
                <div class="support-contact">
                    <i class="fas fa-envelope"></i>
                    <span>Email: support@yourstore.com</span>
                </div>
                <div class="support-contact">
                    <i class="fas fa-comments"></i>
                    <span>Live Chat: 24/7</span>
                </div>
            </div>
        </div>

        <!-- Alternative Payment Methods -->
        <div class="text-center mt-4" style="color: #64748b; font-size: 15px;">
            <p>
                <i class="fas fa-info-circle me-2"></i>
                Bạn cũng có thể thử các phương thức thanh toán khác như
                <strong>chuyển khoản ngân hàng</strong> hoặc
                <strong>thanh toán khi nhận hàng (COD)</strong>
            </p>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Auto scroll to top on load
        window.addEventListener('load', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Optional: Show toast notification
        @if (session('payment_error'))
            setTimeout(function() {
                console.warn('Payment Error:', '{{ session('payment_error') }}');
            }, 500);
        @endif
    </script>
@endpush
