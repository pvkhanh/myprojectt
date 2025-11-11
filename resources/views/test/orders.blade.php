<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Order System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 0;
        }
        .test-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 30px;
            margin-bottom: 20px;
        }
        .btn-test {
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-test:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .result-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            display: none;
        }
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .status-badge:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <!-- Header -->
                <div class="test-card text-center mb-4">
                    <h1 class="mb-3">
                        <i class="fas fa-flask text-primary me-2"></i>
                        Test Order System
                    </h1>
                    <p class="text-muted">Test tạo đơn hàng và gửi mail cho: <strong>pvkhanh.tech@gmail.com</strong></p>
                </div>

                <!-- Create Order -->
                <div class="test-card">
                    <h4 class="mb-4">
                        <i class="fas fa-plus-circle text-success me-2"></i>
                        1. Tạo đơn hàng test
                    </h4>
                    <button class="btn btn-success btn-test btn-lg w-100" onclick="createOrder()">
                        <i class="fas fa-shopping-cart me-2"></i>
                        Tạo đơn hàng mới
                    </button>
                    <div id="createResult" class="result-box"></div>
                </div>

                <!-- List Orders -->
                <div class="test-card">
                    <h4 class="mb-4">
                        <i class="fas fa-list text-info me-2"></i>
                        2. Danh sách đơn hàng test
                    </h4>
                    <button class="btn btn-info btn-test btn-lg w-100" onclick="listOrders()">
                        <i class="fas fa-sync-alt me-2"></i>
                        Tải danh sách
                    </button>
                    <div id="listResult" class="result-box"></div>
                </div>

                <!-- Change Status -->
                <div class="test-card">
                    <h4 class="mb-4">
                        <i class="fas fa-exchange-alt text-warning me-2"></i>
                        3. Thay đổi trạng thái (Gửi mail)
                    </h4>
                    <div class="row g-2 mb-3">
                        <div class="col-md-8">
                            <input type="number" id="orderId" class="form-control form-control-lg" placeholder="Nhập Order ID">
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-primary btn-test w-100" onclick="loadOrderInfo()">
                                <i class="fas fa-search"></i> Tải
                            </button>
                        </div>
                    </div>
                    
                    <div id="orderInfo" style="display: none;">
                        <h5 class="mb-3">Chọn trạng thái mới:</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="status-badge bg-warning text-dark" onclick="changeStatus('pending')">
                                <i class="fas fa-clock"></i> Chờ xử lý
                            </span>
                            <span class="status-badge bg-info text-white" onclick="changeStatus('paid')">
                                <i class="fas fa-credit-card"></i> Đã thanh toán
                            </span>
                            <span class="status-badge bg-primary text-white" onclick="changeStatus('processing')">
                                <i class="fas fa-cog"></i> Đang xử lý
                            </span>
                            <span class="status-badge bg-purple text-white" onclick="changeStatus('shipped')" style="background: #764ba2;">
                                <i class="fas fa-truck"></i> Đang giao
                            </span>
                            <span class="status-badge bg-success text-white" onclick="changeStatus('delivered')">
                                <i class="fas fa-box"></i> Đã giao
                            </span>
                            <span class="status-badge bg-dark text-white" onclick="changeStatus('completed')">
                                <i class="fas fa-check-circle"></i> Hoàn thành
                            </span>
                            <span class="status-badge bg-danger text-white" onclick="changeStatus('cancelled')">
                                <i class="fas fa-ban"></i> Đã hủy
                            </span>
                        </div>
                    </div>
                    
                    <div id="statusResult" class="result-box"></div>
                </div>

                <!-- Queue Info -->
                <div class="test-card">
                    <h4 class="mb-3">
                        <i class="fas fa-info-circle text-secondary me-2"></i>
                        Lưu ý
                    </h4>
                    <div class="alert alert-info">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Để mail được gửi:</strong>
                        <ol class="mb-0 mt-2">
                            <li>Chạy queue worker: <code>php artisan queue:work</code></li>
                            <li>Kiểm tra cấu hình mail trong <code>.env</code></li>
                            <li>Mail sẽ được gửi sau vài giây (delay)</li>
                            <li>Kiểm tra inbox: <strong>pvkhanh.tech@gmail.com</strong></li>
                        </ol>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Xóa routes test trước khi deploy production!</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tạo đơn hàng
        async function createOrder() {
            const btn = event.target;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang tạo...';
            
            try {
                const response = await fetch('/test/create-order');
                const data = await response.json();
                
                const resultBox = document.getElementById('createResult');
                resultBox.style.display = 'block';
                
                if (data.success) {
                    resultBox.innerHTML = `
                        <div class="alert alert-success">
                            <h5><i class="fas fa-check-circle me-2"></i>${data.message}</h5>
                            <hr>
                            <p class="mb-2"><strong>Order ID:</strong> ${data.data.order_id}</p>
                            <p class="mb-2"><strong>Mã đơn:</strong> ${data.data.order_number}</p>
                            <p class="mb-2"><strong>Email:</strong> ${data.data.user_email}</p>
                            <p class="mb-2"><strong>Tổng tiền:</strong> ${data.data.total_amount}</p>
                            <p class="mb-2"><strong>Trạng thái:</strong> ${data.data.status}</p>
                            <p class="mb-0 text-info">${data.data.note}</p>
                            <hr>
                            <a href="${data.data.admin_url}" target="_blank" class="btn btn-sm btn-primary">
                                <i class="fas fa-external-link-alt me-1"></i>Xem chi tiết
                            </a>
                        </div>
                    `;
                } else {
                    resultBox.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                }
            } catch (error) {
                document.getElementById('createResult').innerHTML = `<div class="alert alert-danger">Lỗi: ${error.message}</div>`;
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-shopping-cart me-2"></i>Tạo đơn hàng mới';
            }
        }

        // Danh sách đơn hàng
        async function listOrders() {
            const btn = event.target;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang tải...';
            
            try {
                const response = await fetch('/test/orders');
                const data = await response.json();
                
                const resultBox = document.getElementById('listResult');
                resultBox.style.display = 'block';
                
                if (data.success) {
                    let html = `
                        <div class="alert alert-info">
                            <strong>Tổng số đơn:</strong> ${data.total_orders}
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Mã đơn</th>
                                        <th>Trạng thái</th>
                                        <th>Tổng tiền</th>
                                        <th>Ngày tạo</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    
                    data.orders.forEach(order => {
                        html += `
                            <tr>
                                <td>${order.id}</td>
                                <td><strong>${order.order_number}</strong></td>
                                <td><span class="badge bg-secondary">${order.status}</span></td>
                                <td>${order.total_amount}</td>
                                <td>${order.created_at}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="document.getElementById('orderId').value = ${order.id}; loadOrderInfo()">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="${order.admin_url}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        `;
                    });
                    
                    html += '</tbody></table></div>';
                    resultBox.innerHTML = html;
                } else {
                    resultBox.innerHTML = `<div class="alert alert-warning">${data.message}</div>`;
                }
            } catch (error) {
                document.getElementById('listResult').innerHTML = `<div class="alert alert-danger">Lỗi: ${error.message}</div>`;
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sync-alt me-2"></i>Tải danh sách';
            }
        }

        // Load order info
        function loadOrderInfo() {
            const orderId = document.getElementById('orderId').value;
            if (!orderId) {
                alert('Vui lòng nhập Order ID!');
                return;
            }
            document.getElementById('orderInfo').style.display = 'block';
        }

        // Thay đổi trạng thái
        async function changeStatus(status) {
            const orderId = document.getElementById('orderId').value;
            if (!orderId) {
                alert('Vui lòng nhập Order ID!');
                return;
            }

            const resultBox = document.getElementById('statusResult');
            resultBox.style.display = 'block';
            resultBox.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i></div>';
            
            try {
                const response = await fetch(`/test/order/${orderId}/status/${status}`);
                const data = await response.json();
                
                if (data.success) {
                    resultBox.innerHTML = `
                        <div class="alert alert-success">
                            <h5><i class="fas fa-check-circle me-2"></i>${data.message}</h5>
                            <hr>
                            <p class="mb-2"><strong>Mã đơn:</strong> ${data.data.order_number}</p>
                            <p class="mb-2"><strong>Trạng thái cũ:</strong> <span class="badge bg-secondary">${data.data.old_status}</span></p>
                            <p class="mb-2"><strong>Trạng thái mới:</strong> <span class="badge bg-success">${data.data.new_status}</span></p>
                            <p class="mb-2"><strong>Email:</strong> ${data.data.user_email}</p>
                            <p class="mb-0 text-info">${data.data.note}</p>
                        </div>
                    `;
                } else {
                    resultBox.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                }
            } catch (error) {
                resultBox.innerHTML = `<div class="alert alert-danger">Lỗi: ${error.message}</div>`;
            }
        }
    </script>
</body>
</html>