{{-- @extends('layouts.admin')

@section('title', 'Thống kê Wishlist')

@push('styles')
    <style>
        .chart-card {
            transition: all 0.3s;
        }

        .chart-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .top-item {
            transition: all 0.2s;
            cursor: pointer;
        }

        .top-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }

        .rank-badge {
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: bold;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold text-dark mb-2">
                            <i class="fa-solid fa-chart-line text-primary me-2"></i>
                            Thống kê Wishlist
                        </h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.wishlists.index') }}">Wishlist</a></li>
                                <li class="breadcrumb-item active">Thống kê</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.wishlists.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
                        </a>
                        <button type="button" class="btn btn-success btn-lg" onclick="window.print()">
                            <i class="fa-solid fa-print me-2"></i> In báo cáo
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-white" 
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Tổng wishlist</h6>
                                <h3 class="fw-bold mb-0">{{ number_format($stats['total']) }}</h3>
                            </div>
                            <div class="fs-1 opacity-50">
                                <i class="fa-solid fa-heart"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-white" 
                    style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Sản phẩm phổ biến</h6>
                                <h3 class="fw-bold mb-0">{{ $stats['top_products']->count() }}</h3>
                            </div>
                            <div class="fs-1 opacity-50">
                                <i class="fa-solid fa-fire"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-white" 
                    style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Người dùng tích cực</h6>
                                <h3 class="fw-bold mb-0">{{ $stats['top_users']->count() }}</h3>
                            </div>
                            <div class="fs-1 opacity-50">
                                <i class="fa-solid fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-white" 
                    style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">TB mỗi người</h6>
                                <h3 class="fw-bold mb-0">
                                    {{ $stats['top_users']->count() > 0 ? number_format($stats['total'] / $stats['top_users']->count(), 1) : 0 }}
                                </h3>
                            </div>
                            <div class="fs-1 opacity-50">
                                <i class="fa-solid fa-chart-pie"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Chart - Wishlist by Day -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm chart-card">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-chart-line text-primary me-2"></i>
                            Biểu đồ Wishlist 30 ngày qua
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="wishlistChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Products -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm chart-card h-100">
                    <div class="card-header bg-gradient text-white py-3"
                        style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-trophy me-2"></i>
                            Top 10 Sản phẩm được yêu thích
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach ($stats['top_products'] as $index => $item)
                                <div class="list-group-item top-item border-0 px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="rank-badge me-3
                                            @if ($index === 0) bg-warning text-dark
                                            @elseif($index === 1) bg-secondary text-white
                                            @elseif($index === 2) bg-danger text-white
                                            @else bg-light text-dark
                                            @endif">
                                            {{ $index + 1 }}
                                        </div>
                                        <div class="flex-fill">
                                            <div class="fw-bold text-dark">{{ Str::limit($item->name, 30) }}</div>
                                            <div class="small text-muted">
                                                <i class="fa-solid fa-heart text-danger me-1"></i>
                                                {{ $item->total }} lượt yêu thích
                                            </div>
                                        </div>
                                        @if ($index < 3)
                                            <i class="fa-solid fa-medal text-warning fs-4"></i>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Users -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm chart-card">
                    <div class="card-header bg-gradient text-white py-3"
                        style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-users me-2"></i>
                            Top 10 Người dùng tích cực
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-4 py-3 text-center" style="width: 60px;">#</th>
                                        <th class="px-4 py-3">Email</th>
                                        <th class="px-4 py-3 text-center">Số wishlist</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stats['top_users'] as $index => $user)
                                        <tr>
                                            <td class="text-center px-4">
                                                <div class="rank-badge
                                                    @if ($index === 0) bg-warning text-dark
                                                    @elseif($index === 1) bg-secondary text-white
                                                    @elseif($index === 2) bg-danger text-white
                                                    @else bg-light text-dark
                                                    @endif">
                                                    {{ $index + 1 }}
                                                </div>
                                            </td>
                                            <td class="px-4">
                                                <div class="fw-semibold text-dark">{{ $user->email }}</div>
                                            </td>
                                            <td class="text-center px-4">
                                                <span class="badge bg-primary fs-6">{{ $user->total }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Stats -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm chart-card">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-chart-pie text-primary me-2"></i>
                            Phân tích chi tiết
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="pieChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Line Chart - Wishlist by Day
            const lineCtx = document.getElementById('wishlistChart').getContext('2d');
            const lineChart = new Chart(lineCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($stats['by_day']->pluck('date')) !!},
                    datasets: [{
                        label: 'Số wishlist',
                        data: {!! json_encode($stats['by_day']->pluck('count')) !!},
                        borderColor: 'rgb(102, 126, 234)',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });

            // Pie Chart - Top Products Distribution
            const pieCtx = document.getElementById('pieChart').getContext('2d');
            const topProductsData = {!! json_encode($stats['top_products']->take(5)) !!};
            
            const pieChart = new Chart(pieCtx, {
                type: 'doughnut',
                data: {
                    labels: topProductsData.map(item => item.name.substring(0, 20) + '...'),
                    datasets: [{
                        data: topProductsData.map(item => item.total),
                        backgroundColor: [
                            'rgba(102, 126, 234, 0.8)',
                            'rgba(240, 147, 251, 0.8)',
                            'rgba(67, 233, 123, 0.8)',
                            'rgba(79, 172, 254, 0.8)',
                            'rgba(255, 154, 158, 0.8)'
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.parsed + ' wishlist';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush --}}



@extends('layouts.admin')

@section('title', 'Wishlist Analytics')

@push('styles')
    <style>
        :root {
            --tiktok-red: #FE2C55;
            --tiktok-blue: #25F4EE;
        }

        .analytics-header {
            background: linear-gradient(135deg, rgba(254, 44, 85, 0.1) 0%, rgba(37, 244, 238, 0.1) 100%);
            border-radius: 24px;
            padding: 3rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .analytics-header::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(254, 44, 85, 0.1) 0%, transparent 70%);
            top: -150px;
            right: -150px;
            animation: pulse 4s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.5;
            }

            50% {
                transform: scale(1.2);
                opacity: 0.8;
            }
        }

        .stat-card-analytics {
            border-radius: 20px;
            padding: 2rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-card-analytics::after {
            content: '';
            position: absolute;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            bottom: -75px;
            right: -75px;
        }

        .stat-card-analytics:hover {
            transform: translateY(-10px) rotate(-2deg);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
        }

        .chart-container {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
        }

        .chart-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .leaderboard-item {
            padding: 1.5rem;
            border-radius: 16px;
            background: white;
            margin-bottom: 1rem;
            transition: all 0.3s;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .leaderboard-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(to bottom, var(--tiktok-red), var(--tiktok-blue));
            transform: scaleY(0);
            transition: transform 0.3s;
        }

        .leaderboard-item:hover::before {
            transform: scaleY(1);
        }

        .leaderboard-item:hover {
            transform: translateX(10px);
            background: linear-gradient(90deg, rgba(254, 44, 85, 0.05) 0%, white 50%);
            border-color: var(--tiktok-red);
        }

        .rank-badge {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
            position: relative;
        }

        .rank-1 {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            box-shadow: 0 5px 15px rgba(255, 215, 0, 0.4);
            animation: shine 2s infinite;
        }

        .rank-2 {
            background: linear-gradient(135deg, #C0C0C0 0%, #808080 100%);
            box-shadow: 0 5px 15px rgba(192, 192, 192, 0.4);
        }

        .rank-3 {
            background: linear-gradient(135deg, #CD7F32 0%, #8B4513 100%);
            box-shadow: 0 5px 15px rgba(205, 127, 50, 0.4);
        }

        .rank-other {
            background: linear-gradient(135deg, #f0f0f0 0%, #d0d0d0 100%);
        }

        @keyframes shine {

            0%,
            100% {
                box-shadow: 0 5px 15px rgba(255, 215, 0, 0.4);
            }

            50% {
                box-shadow: 0 5px 25px rgba(255, 215, 0, 0.8);
            }
        }

        .medal-icon {
            position: absolute;
            top: -5px;
            right: -5px;
            font-size: 1.5rem;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
        }

        .trend-up {
            color: #10b981;
            animation: bounce 1s infinite;
        }

        .trend-down {
            color: #ef4444;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        .user-row {
            transition: all 0.3s;
        }

        .user-row:hover {
            background: linear-gradient(90deg, rgba(37, 244, 238, 0.05) 0%, white 100%);
            transform: translateX(5px);
        }

        .progress-bar-animated {
            background: linear-gradient(90deg, var(--tiktok-red), var(--tiktok-blue), var(--tiktok-red));
            background-size: 200% 100%;
            animation: gradient-shift 3s ease infinite;
        }

        @keyframes gradient-shift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-4">
        <!-- Analytics Header -->
        <div class="analytics-header position-relative">
            <div class="row align-items-center position-relative z-1">
                <div class="col-lg-8">
                    <h1 class="fw-bold mb-3">
                        <i class="fa-solid fa-chart-line me-3" style="color: var(--tiktok-red);"></i>
                        Wishlist Analytics Dashboard
                    </h1>
                    <p class="text-muted mb-0 fs-5">
                        Comprehensive insights into user preferences and trending products
                    </p>
                </div>
                <div class="col-lg-4 text-end">
                    <a href="{{ route('admin.wishlists.index') }}" class="btn btn-outline-dark btn-lg me-2"
                        style="border-radius: 12px;">
                        <i class="fa-solid fa-arrow-left me-2"></i> Back
                    </a>
                    <button type="button" class="btn btn-lg" onclick="window.print()"
                        style="background: linear-gradient(135deg, var(--tiktok-red) 0%, #FF6B9D 100%); color: white; border: none; border-radius: 12px;">
                        <i class="fa-solid fa-print me-2"></i> Print Report
                    </button>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="stat-card-analytics text-white"
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="position-relative z-1">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <p class="text-white-50 mb-2 fw-semibold text-uppercase small">Total Wishlists</p>
                                <h2 class="fw-bold display-4 mb-0">{{ number_format($stats['total']) }}</h2>
                            </div>
                            <div class="fs-1 opacity-50">
                                <i class="fa-solid fa-heart"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-white bg-opacity-25 px-3 py-2">
                                <i class="fa-solid fa-arrow-up me-1"></i> All Time
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card-analytics text-white"
                    style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <div class="position-relative z-1">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <p class="text-white-50 mb-2 fw-semibold text-uppercase small">Top Products</p>
                                <h2 class="fw-bold display-4 mb-0">{{ $stats['top_products']->count() }}</h2>
                            </div>
                            <div class="fs-1 opacity-50">
                                <i class="fa-solid fa-fire"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-white bg-opacity-25 px-3 py-2">
                                <i class="fa-solid fa-trophy me-1"></i> Trending
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card-analytics text-white"
                    style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                    <div class="position-relative z-1">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <p class="text-white-50 mb-2 fw-semibold text-uppercase small">Active Users</p>
                                <h2 class="fw-bold display-4 mb-0">{{ $stats['top_users']->count() }}</h2>
                            </div>
                            <div class="fs-1 opacity-50">
                                <i class="fa-solid fa-users"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-white bg-opacity-25 px-3 py-2">
                                <i class="fa-solid fa-user-check me-1"></i> Engaged
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card-analytics text-white"
                    style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <div class="position-relative z-1">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <p class="text-white-50 mb-2 fw-semibold text-uppercase small">Avg Per User</p>
                                <h2 class="fw-bold display-4 mb-0">
                                    {{ $stats['top_users']->count() > 0 ? number_format($stats['total'] / $stats['top_users']->count(), 1) : 0 }}
                                </h2>
                            </div>
                            <div class="fs-1 opacity-50">
                                <i class="fa-solid fa-chart-pie"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-white bg-opacity-25 px-3 py-2">
                                <i class="fa-solid fa-calculator me-1"></i> Average
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Chart - Wishlist Trend -->
            <div class="col-lg-8">
                <div class="chart-container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="fw-bold mb-1">
                                <i class="fa-solid fa-chart-line me-2" style="color: var(--tiktok-red);"></i>
                                30-Day Wishlist Trend
                            </h4>
                            <p class="text-muted small mb-0">Daily wishlist additions over the past month</p>
                        </div>
                        <div class="badge bg-light text-dark px-3 py-2">
                            <i class="fa-solid fa-calendar me-1"></i> Last 30 Days
                        </div>
                    </div>
                    <canvas id="wishlistChart" height="300"></canvas>
                </div>
            </div>

            <!-- Top Products Leaderboard -->
            <div class="col-lg-4">
                <div class="chart-container h-100">
                    <h4 class="fw-bold mb-4">
                        <i class="fa-solid fa-trophy me-2" style="color: var(--tiktok-red);"></i>
                        Top 10 Products
                    </h4>
                    <div style="max-height: 500px; overflow-y: auto;">
                        @foreach ($stats['top_products'] as $index => $item)
                            <div class="leaderboard-item">
                                <div class="d-flex align-items-center gap-3">
                                    <div
                                        class="rank-badge {{ $index === 0 ? 'rank-1' : ($index === 1 ? 'rank-2' : ($index === 2 ? 'rank-3' : 'rank-other')) }} text-white position-relative">
                                        {{ $index + 1 }}
                                        @if ($index < 3)
                                            <span class="medal-icon">🏆</span>
                                        @endif
                                    </div>
                                    <div class="flex-fill">
                                        <div class="fw-bold text-dark mb-1">{{ Str::limit($item->name, 30) }}</div>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fa-solid fa-heart text-danger"></i>
                                            <span class="fw-semibold"
                                                style="color: var(--tiktok-red);">{{ $item->total }}</span>
                                            <span class="text-muted small">wishlists</span>
                                        </div>
                                        <div class="progress mt-2" style="height: 6px; border-radius: 10px;">
                                            <div class="progress-bar progress-bar-animated"
                                                style="width: {{ ($item->total / $stats['top_products']->max('total')) * 100 }}%; background: linear-gradient(90deg, var(--tiktok-red), var(--tiktok-blue));">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Top Users Table -->
            <div class="col-lg-6">
                <div class="chart-container">
                    <h4 class="fw-bold mb-4">
                        <i class="fa-solid fa-users me-2" style="color: var(--tiktok-red);"></i>
                        Most Active Users
                    </h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3" style="border-radius: 12px 0 0 0;">Rank</th>
                                    <th class="py-3">Email</th>
                                    <th class="py-3 text-center" style="border-radius: 0 12px 0 0;">Wishlists</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stats['top_users'] as $index => $user)
                                    <tr class="user-row">
                                        <td class="py-3">
                                            <div class="rank-badge {{ $index === 0 ? 'rank-1' : ($index === 1 ? 'rank-2' : ($index === 2 ? 'rank-3' : 'rank-other')) }} text-white"
                                                style="width: 35px; height: 35px; font-size: 0.9rem;">
                                                {{ $index + 1 }}
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <div class="fw-semibold text-dark">{{ $user->email }}</div>
                                        </td>
                                        <td class="text-center py-3">
                                            <span class="badge px-3 py-2"
                                                style="background: linear-gradient(135deg, var(--tiktok-red) 0%, #FF6B9D 100%); color: white; border-radius: 10px;">
                                                {{ $user->total }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pie Chart -->
            <div class="col-lg-6">
                <div class="chart-container">
                    <h4 class="fw-bold mb-4">
                        <i class="fa-solid fa-chart-pie me-2" style="color: var(--tiktok-red);"></i>
                        Top 5 Product Distribution
                    </h4>
                    <canvas id="pieChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const colors = [
                'rgba(254, 44, 85, 0.8)',
                'rgba(37, 244, 238, 0.8)',
                'rgba(112, 0, 255, 0.8)',
                'rgba(255, 107, 157, 0.8)',
                'rgba(67, 233, 123, 0.8)'
            ];

            // Line Chart
            const lineCtx = document.getElementById('wishlistChart').getContext('2d');
            const lineChart = new Chart(lineCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode(
                        $stats['by_day']->pluck('date')->map(function ($date) {
                            return date('M d', strtotime($date));
                        }),
                    ) !!},
                    datasets: [{
                        label: 'Wishlists Added',
                        data: {!! json_encode($stats['by_day']->pluck('count')) !!},
                        borderColor: '#FE2C55',
                        backgroundColor: 'rgba(254, 44, 85, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 3,
                        pointRadius: 5,
                        pointBackgroundColor: '#FE2C55',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            borderRadius: 8,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: {
                                    size: 12
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 11
                                }
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // Pie Chart
            const pieCtx = document.getElementById('pieChart').getContext('2d');
            const topProductsData = {!! json_encode($stats['top_products']->take(5)) !!};

            const pieChart = new Chart(pieCtx, {
                type: 'doughnut',
                data: {
                    labels: topProductsData.map(item => item.name.substring(0, 25) + '...'),
                    datasets: [{
                        data: topProductsData.map(item => item.total),
                        backgroundColor: colors,
                        borderWidth: 3,
                        borderColor: '#fff',
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                font: {
                                    size: 13,
                                    weight: '600'
                                },
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            borderRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                                    return ` ${context.label}: ${context.parsed} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
