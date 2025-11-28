<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Email Templates</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 0;
        }

        .container {
            max-width: 1200px;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }

        .template-card {
            transition: all 0.3s ease;
            cursor: pointer;
            height: 100%;
        }

        .template-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .template-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .btn-preview {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border: none;
            color: white;
        }

        .btn-preview:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
        }

        .btn-send {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            color: white;
        }

        .btn-send:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
        }

        .order-selector {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .alert-custom {
            border-radius: 10px;
            border: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="text-center text-white mb-4">
            <h1 class="display-4 fw-bold mb-2">📧 Email Template Tester</h1>
            <p class="lead">Preview và test các email template của hệ thống</p>
        </div>

        <!-- Order Selector -->
        <div class="order-selector">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <label class="form-label fw-bold">Chọn đơn hàng để test:</label>
                    <select id="orderSelect" class="form-select form-select-lg">
                        <option value="">Loading orders...</option>
                    </select>
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-success btn-lg" onclick="createTestOrder()">
                        <i class="fas fa-plus-circle me-2"></i>Tạo đơn test
                    </button>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        <div id="alertContainer"></div>

        <!-- Email Templates Grid -->
        <div class="row g-4">
            <!-- Order Confirmation -->
            <div class="col-md-6 col-lg-4">
                <div class="card template-card">
                    <div class="card-body text-center">
                        <div class="template-icon">✅</div>
                        <h5 class="card-title fw-bold">Đặt hàng thành công</h5>
                        <p class="text-muted">order-confirmation</p>
                        <p class="small">Gửi khi khách đặt hàng xong</p>
                        <div class="d-grid gap-2 mt-3">
                            <button class="btn btn-preview" onclick="previewEmail('order-confirmation')">
                                <i class="fas fa-eye me-2"></i>Preview
                            </button>
                            <button class="btn btn-send" onclick="sendTestEmail('order-confirmation')">
                                <i class="fas fa-paper-plane me-2"></i>Gửi Test
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Preparing -->
            <div class="col-md-6 col-lg-4">
                <div class="card template-card">
                    <div class="card-body text-center">
                        <div class="template-icon">📦</div>
                        <h5 class="card-title fw-bold">Đang chuẩn bị hàng</h5>
                        <p class="text-muted">order-preparing</p>
                        <p class="small">Gửi khi admin xác nhận đơn</p>
                        <div class="d-grid gap-2 mt-3">
                            <button class="btn btn-preview" onclick="previewEmail('order-preparing')">
                                <i class="fas fa-eye me-2"></i>Preview
                            </button>
                            <button class="btn btn-send" onclick="sendTestEmail('order-preparing')">
                                <i class="fas fa-paper-plane me-2"></i>Gửi Test
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Paid -->
            <div class="col-md-6 col-lg-4">
                <div class="card template-card">
                    <div class="card-body text-center">
                        <div class="template-icon">💳</div>
                        <h5 class="card-title fw-bold">Thanh toán thành công</h5>
                        <p class="text-muted">order-paid</p>
                        <p class="small">Gửi khi xác nhận thanh toán</p>
                        <div class="d-grid gap-2 mt-3">
                            <button class="btn btn-preview" onclick="previewEmail('order-paid')">
                                <i class="fas fa-eye me-2"></i>Preview
                            </button>
                            <button class="btn btn-send" onclick="sendTestEmail('order-paid')">
                                <i class="fas fa-paper-plane me-2"></i>Gửi Test
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Shipped -->
            <div class="col-md-6 col-lg-4">
                <div class="card template-card">
                    <div class="card-body text-center">
                        <div class="template-icon">🚚</div>
                        <h5 class="card-title fw-bold">Đang giao hàng</h5>
                        <p class="text-muted">order-shipped</p>
                        <p class="small">Gửi khi giao cho shipper</p>
                        <div class="d-grid gap-2 mt-3">
                            <button class="btn btn-preview" onclick="previewEmail('order-shipped')">
                                <i class="fas fa-eye me-2"></i>Preview
                            </button>
                            <button class="btn btn-send" onclick="sendTestEmail('order-shipped')">
                                <i class="fas fa-paper-plane me-2"></i>Gửi Test
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Completed -->
            <div class="col-md-6 col-lg-4">
                <div class="card template-card">
                    <div class="card-body text-center">
                        <div class="template-icon">🎉</div>
                        <h5 class="card-title fw-bold">Hoàn thành</h5>
                        <p class="text-muted">order-completed</p>
                        <p class="small">Gửi khi giao hàng thành công</p>
                        <div class="d-grid gap-2 mt-3">
                            <button class="btn btn-preview" onclick="previewEmail('order-completed')">
                                <i class="fas fa-eye me-2"></i>Preview
                            </button>
                            <button class="btn btn-send" onclick="sendTestEmail('order-completed')">
                                <i class="fas fa-paper-plane me-2"></i>Gửi Test
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Cancelled -->
            <div class="col-md-6 col-lg-4">
                <div class="card template-card">
                    <div class="card-body text-center">
                        <div class="template-icon">😢</div>
                        <h5 class="card-title fw-bold">Đơn hàng đã hủy</h5>
                        <p class="text-muted">order-cancelled</p>
                        <p class="small">Gửi khi hủy đơn hàng</p>
                        <div class="d-grid gap-2 mt-3">
                            <button class="btn btn-preview" onclick="previewEmail('order-cancelled')">
                                <i class="fas fa-eye me-2"></i>Preview
                            </button>
                            <button class="btn btn-send" onclick="sendTestEmail('order-cancelled')">
                                <i class="fas fa-paper-plane me-2"></i>Gửi Test
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="text-center mt-5">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-lg">
                <i class="fas fa-arrow-left me-2"></i>Quay lại Dashboard
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load orders on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadOrders();
        });

        // Load orders
        async function loadOrders() {
            try {
                const response = await fetch('/test/orders/json'); // sửa đường dẫn
                const data = await response.json();

                const select = document.getElementById('orderSelect');
                select.innerHTML = '<option value="">-- Chọn đơn hàng --</option>';

                data.orders.forEach(order => {
                    const option = document.createElement('option');
                    option.value = order.id;
                    option.textContent = `#${order.order_number} - ${order.user} - ${order.total_amount}`;
                    select.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading orders:', error);
                showAlert('danger', 'Không thể load đơn hàng: ' + error.message);
            }
        }


        // Create test order
        async function createTestOrder() {
            try {
                showAlert('info', 'Đang tạo đơn hàng test...');

                const response = await fetch('/test/create-order');
                const data = await response.json();

                if (data.success) {
                    showAlert('success', `Tạo đơn hàng thành công! #${data.order_number}`);
                    loadOrders();
                } else {
                    showAlert('danger', 'Có lỗi xảy ra khi tạo đơn hàng');
                }
            } catch (error) {
                showAlert('danger', 'Lỗi: ' + error.message);
            }
        }

        // Preview email
        function previewEmail(template) {
            const orderId = document.getElementById('orderSelect').value;
            if (!orderId) {
                showAlert('warning', 'Vui lòng chọn đơn hàng trước!');
                return;
            }

            const url = `/test/emails/preview/${template}`;
            window.open(url, '_blank');
        }

        // Send test email
        async function sendTestEmail(template) {
            const orderId = document.getElementById('orderSelect').value;
            if (!orderId) {
                showAlert('warning', 'Vui lòng chọn đơn hàng trước!');
                return;
            }

            try {
                showAlert('info', `Đang gửi email ${template}...`);

                const response = await fetch(`/test/emails/send-test/${orderId}/${template}`);
                const data = await response.json();

                if (data.success) {
                    showAlert('success', `✅ Email đã được gửi đến queue!
                        <br>Recipient: ${data.recipient}
                        <br>Template: ${data.template}`);
                } else {
                    showAlert('danger', 'Lỗi: ' + data.message);
                }
            } catch (error) {
                showAlert('danger', 'Lỗi: ' + error.message);
            }
        }

        // Show alert
        function showAlert(type, message) {
            const container = document.getElementById('alertContainer');
            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-custom alert-dismissible fade show`;
            alert.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            container.appendChild(alert);

            // Auto remove after 5 seconds
            setTimeout(() => {
                alert.remove();
            }, 5000);
        }
    </script>
</body>

</html>
