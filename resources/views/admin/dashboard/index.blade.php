{{-- @extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="row text-center">
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5>Sản phẩm</h5>
                    <h2>{{ $productsCount ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5>Danh mục</h5>
                    <h2>{{ $categoriesCount ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5>Đơn hàng</h5>
                    <h2>{{ $ordersCount ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5>Người dùng</h5>
                    <h2>{{ $usersCount ?? 0 }}</h2>
                </div>
            </div>
        </div>
    </div>
@endsection --}}


@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid py-4">
        <h2 class="mb-4 text-dark fw-bold" style="font-family: 'Inter', sans-serif;">Dashboard</h2>

        <!-- Tổng quan -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm rounded-3 p-3 text-center">
                    <h6 class="text-muted mb-2">Sản phẩm</h6>
                    <h3 class="fw-bold">{{ $productsCount }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm rounded-3 p-3 text-center">
                    <h6 class="text-muted mb-2">Danh mục</h6>
                    <h3 class="fw-bold">{{ $categoriesCount }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm rounded-3 p-3 text-center">
                    <h6 class="text-muted mb-2">Đơn hàng</h6>
                    <h3 class="fw-bold">{{ $ordersCount }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm rounded-3 p-3 text-center">
                    <h6 class="text-muted mb-2">Người dùng</h6>
                    <h3 class="fw-bold">{{ $usersCount }}</h3>
                </div>
            </div>
        </div>

        <!-- Đơn hàng theo trạng thái -->
        <div class="row g-3 mb-4">
            @php
                $statuses = [
                    'Pending' => ['count' => $pendingOrders, 'color' => 'warning', 'icon' => 'fa-clock'],
                    'Paid' => ['count' => $paidOrders, 'color' => 'info', 'icon' => 'fa-credit-card'],
                    'Shipped' => ['count' => $shippedOrders, 'color' => 'primary', 'icon' => 'fa-truck'],
                    'Completed' => ['count' => $completedOrders, 'color' => 'success', 'icon' => 'fa-check-circle'],
                    'Cancelled' => ['count' => $cancelledOrders, 'color' => 'danger', 'icon' => 'fa-times-circle'],
                ];
            @endphp

            @foreach ($statuses as $name => $status)
                <div class="col-md-2">
                    <div class="card shadow-sm rounded-3 p-3 text-center border-0" style="transition: transform 0.2s;">
                        <div class="text-{{ $status['color'] }}">
                            <i class="fas {{ $status['icon'] }} fa-2x mb-2"></i>
                        </div>
                        <h6 class="text-muted mb-1">{{ $name }}</h6>
                        <h4 class="fw-bold">{{ $status['count'] }}</h4>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Doanh thu -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm rounded-3 p-3 text-center">
                    <h6 class="text-muted mb-2">Tổng doanh thu</h6>
                    <h3 class="fw-bold">{{ number_format($totalRevenue) }} đ</h3>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm rounded-3 p-3 text-center">
                    <h6 class="text-muted mb-2">Doanh thu tháng {{ now()->month }}</h6>
                    <h3 class="fw-bold">{{ number_format($monthlyRevenue) }} đ</h3>
                </div>
            </div>
        </div>

        <!-- Biểu đồ doanh thu -->
        <div class="row g-4 mb-4">
            <div class="col-md-12">
                <div class="card shadow-sm rounded-3 p-3">
                    <h6 class="text-muted mb-3">Biểu đồ doanh thu năm {{ now()->year }}</h6>
                    <canvas id="revenueChart" height="120"></canvas>
                </div>
            </div>
        </div>

        <!-- Đơn hàng gần đây -->
        <div class="row g-4">
            <div class="col-md-12">
                <div class="card shadow-sm rounded-3 p-3">
                    <h6 class="text-muted mb-3">Đơn hàng gần đây</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Mã</th>
                                    <th>Khách hàng</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentOrders as $order)
                                    <tr>
                                        <td>{{ $order->id }}</td>
                                        <td>{{ $order->user->name ?? 'N/A' }}</td>
                                        <td>{{ number_format($order->total_amount) }} đ</td>
                                        <td>{!! $order->status instanceof \App\Enums\OrderStatus ? $order->status->badge() : $order->status !!}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('revenueChart');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_keys($revenueChart)) !!},
                datasets: [{
                    label: 'Doanh thu',
                    data: {!! json_encode(array_values($revenueChart)) !!},
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointBackgroundColor: '#4f46e5'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                interaction: {
                    mode: 'nearest',
                    intersect: false
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endsection

{{-- @extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div>
                        <h3 class="mb-0">{{ number_format($totalRevenue ?? 125500000) }}</h3>
                        <small class="text-muted">Doanh thu tháng (₫)</small>
                        <div class="text-success small mt-1"><i class="bi bi-arrow-up"></i> 12.5% so với tháng trước</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                        <i class="bi bi-bag-check"></i>
                    </div>
                    <div>
                        <h3 class="mb-0">{{ $totalOrders ?? 156 }}</h3>
                        <small class="text-muted">Đơn hàng tháng này</small>
                        <div class="text-success small mt-1"><i class="bi bi-arrow-up"></i> 8.3% so với tháng trước</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-info bg-opacity-10 text-info me-3">
                        <i class="bi bi-people"></i>
                    </div>
                    <div>
                        <h3 class="mb-0">{{ $totalUsers ?? 1250 }}</h3>
                        <small class="text-muted">Khách hàng</small>
                        <div class="text-success small mt-1"><i class="bi bi-arrow-up"></i> 25 khách hàng mới</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div>
                        <h3 class="mb-0">{{ $totalProducts ?? 458 }}</h3>
                        <small class="text-muted">Sản phẩm</small>
                        <div class="text-danger small mt-1"><i class="bi bi-exclamation-triangle"></i> 12 sắp hết hàng</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card bg-warning bg-opacity-10 border-0">
                <div class="card-body text-center py-3">
                    <h4 class="text-warning mb-0">{{ $pendingOrders ?? 5 }}</h4>
                    <small class="text-muted">Chờ xác nhận</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info bg-opacity-10 border-0">
                <div class="card-body text-center py-3">
                    <h4 class="text-info mb-0">{{ $processingOrders ?? 8 }}</h4>
                    <small class="text-muted">Đang xử lý</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary bg-opacity-10 border-0">
                <div class="card-body text-center py-3">
                    <h4 class="text-primary mb-0">{{ $shippingOrders ?? 12 }}</h4>
                    <small class="text-muted">Đang giao</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success bg-opacity-10 border-0">
                <div class="card-body text-center py-3">
                    <h4 class="text-success mb-0">{{ $completedOrders ?? 131 }}</h4>
                    <small class="text-muted">Hoàn thành</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Orders -->
        <div class="col-lg-8">
            <div class="card table-card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-receipt me-2"></i>Đơn hàng gần đây</h6>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Khách hàng</th>
                                    <th>Sản phẩm</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $recentOrders = [
                                        [
                                            'code' => 'DH202401001',
                                            'customer' => 'Nguyễn Văn A',
                                            'items' => 2,
                                            'total' => 28990000,
                                            'status' => 'pending',
                                            'status_text' => 'Chờ xác nhận',
                                        ],
                                        [
                                            'code' => 'DH202401002',
                                            'customer' => 'Trần Thị B',
                                            'items' => 1,
                                            'total' => 15500000,
                                            'status' => 'processing',
                                            'status_text' => 'Đang xử lý',
                                        ],
                                        [
                                            'code' => 'DH202401003',
                                            'customer' => 'Lê Văn C',
                                            'items' => 3,
                                            'total' => 42990000,
                                            'status' => 'shipping',
                                            'status_text' => 'Đang giao',
                                        ],
                                        [
                                            'code' => 'DH202401004',
                                            'customer' => 'Phạm Thị D',
                                            'items' => 1,
                                            'total' => 8750000,
                                            'status' => 'completed',
                                            'status_text' => 'Hoàn thành',
                                        ],
                                        [
                                            'code' => 'DH202401005',
                                            'customer' => 'Hoàng Văn E',
                                            'items' => 2,
                                            'total' => 21990000,
                                            'status' => 'pending',
                                            'status_text' => 'Chờ xác nhận',
                                        ],
                                    ];
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'shipping' => 'primary',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                    ];
                                @endphp
                                @foreach ($recentOrders as $order)
                                    <tr>
                                        <td><strong>#{{ $order['code'] }}</strong></td>
                                        <td>{{ $order['customer'] }}</td>
                                        <td>{{ $order['items'] }} sản phẩm</td>
                                        <td><strong>{{ number_format($order['total']) }}₫</strong></td>
                                        <td><span
                                                class="badge badge-status bg-{{ $statusColors[$order['status']] }}">{{ $order['status_text'] }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.orders.show', $order['code']) }}"
                                                    class="btn btn-outline-primary" title="Xem"><i
                                                        class="bi bi-eye"></i></a>
                                                <button class="btn btn-outline-success" title="Xác nhận"><i
                                                        class="bi bi-check-lg"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sales Chart -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-bar-chart me-2"></i>Biểu đồ doanh thu</h6>
                    <select class="form-select form-select-sm" style="width:auto;">
                        <option>7 ngày qua</option>
                        <option selected>30 ngày qua</option>
                        <option>3 tháng qua</option>
                    </select>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="col-lg-4">
            <!-- Top Products -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-trophy me-2"></i>Sản phẩm bán chạy</h6>
                </div>
                <div class="card-body p-0">
                    @php
                        $topProducts = [
                            ['name' => 'iPhone 15 Pro Max', 'sold' => 45, 'revenue' => 1304550000],
                            ['name' => 'MacBook Air M3', 'sold' => 28, 'revenue' => 783720000],
                            ['name' => 'AirPods Pro', 'sold' => 67, 'revenue' => 401330000],
                            ['name' => 'Apple Watch Ultra', 'sold' => 23, 'revenue' => 505770000],
                            ['name' => 'iPad Pro M4', 'sold' => 15, 'revenue' => 359850000],
                        ];
                    @endphp
                    @foreach ($topProducts as $index => $product)
                        <div class="d-flex align-items-center p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="bg-light rounded d-flex align-items-center justify-content-center me-3"
                                style="width:40px;height:40px;">
                                <strong class="text-{{ $index < 3 ? 'primary' : 'muted' }}">{{ $index + 1 }}</strong>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 small">{{ $product['name'] }}</h6>
                                <small class="text-muted">{{ $product['sold'] }} đã bán</small>
                            </div>
                            <strong class="small">{{ number_format($product['revenue'] / 1000000, 1) }}M</strong>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Low Stock Alert -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Sắp hết hàng</h6>
                </div>
                <div class="card-body p-0">
                    @php
                        $lowStock = [
                            ['name' => 'iPhone 15 Pro Max - Titan Xanh', 'stock' => 3],
                            ['name' => 'AirPods Pro 2 - Trắng', 'stock' => 5],
                            ['name' => 'MacBook Pro 14 - M3 Pro', 'stock' => 2],
                        ];
                    @endphp
                    @foreach ($lowStock as $item)
                        <div class="d-flex align-items-center p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="flex-grow-1">
                                <h6 class="mb-0 small">{{ $item['name'] }}</h6>
                                <small class="text-danger">Còn {{ $item['stock'] }} sản phẩm</small>
                            </div>
                            <button class="btn btn-sm btn-outline-primary">Nhập hàng</button>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-activity me-2"></i>Hoạt động gần đây</h6>
                </div>
                <div class="card-body p-0">
                    @php
                        $activities = [
                            [
                                'icon' => 'cart-plus',
                                'color' => 'success',
                                'text' => 'Đơn hàng mới #DH001',
                                'time' => '5 phút trước',
                            ],
                            [
                                'icon' => 'person-plus',
                                'color' => 'info',
                                'text' => 'Khách hàng mới đăng ký',
                                'time' => '15 phút trước',
                            ],
                            [
                                'icon' => 'star',
                                'color' => 'warning',
                                'text' => 'Đánh giá mới 5 sao',
                                'time' => '1 giờ trước',
                            ],
                            [
                                'icon' => 'check-circle',
                                'color' => 'success',
                                'text' => 'Đơn #DH099 hoàn thành',
                                'time' => '2 giờ trước',
                            ],
                            [
                                'icon' => 'x-circle',
                                'color' => 'danger',
                                'text' => 'Đơn #DH098 bị hủy',
                                'time' => '3 giờ trước',
                            ],
                        ];
                    @endphp
                    @foreach ($activities as $act)
                        <div class="d-flex p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="bg-{{ $act['color'] }} bg-opacity-10 rounded p-2 me-3">
                                <i class="bi bi-{{ $act['icon'] }} text-{{ $act['color'] }}"></i>
                            </div>
                            <div>
                                <small class="d-block">{{ $act['text'] }}</small>
                                <small class="text-muted">{{ $act['time'] }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        new Chart(document.getElementById('salesChart'), {
            type: 'line',
            data: {
                labels: ['01', '05', '10', '15', '20', '25', '30'],
                datasets: [{
                    label: 'Doanh thu (triệu ₫)',
                    data: [12, 19, 15, 25, 22, 30, 28],
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endpush --}}
