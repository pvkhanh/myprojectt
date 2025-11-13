@extends('layouts.admin')

@section('title', 'Dashboard Thông Báo')

@push('styles')
    <style>
        .stat-card {
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .chart-container {
            position: relative;
            height: 300px;
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
                            Dashboard Thông Báo
                        </h2>
                        <p class="text-muted mb-0">Tổng quan và thống kê hệ thống thông báo</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-primary btn-lg">
                            <i class="fa-solid fa-list me-2"></i> Danh sách
                        </a>
                        <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary btn-lg">
                            <i class="fa-solid fa-plus me-2"></i> Tạo mới
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Stats -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 stat-card"
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body p-4 text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-2">Tổng thông báo</h6>
                                <h2 class="fw-bold mb-0">{{ number_format($stats['total']) }}</h2>
                                <small class="text-white-50">Tất cả thời gian</small>
                            </div>
                            <div class="fs-1 opacity-50">
                                <i class="fa-solid fa-bell"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 stat-card"
                    style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <div class="card-body p-4 text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-2">Hôm nay</h6>
                                <h2 class="fw-bold mb-0">{{ number_format($stats['today']) }}</h2>
                                <small class="text-white-50">Thông báo mới</small>
                            </div>
                            <div class="fs-1 opacity-50">
                                <i class="fa-solid fa-calendar-day"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 stat-card"
                    style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <div class="card-body p-4 text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-2">Tuần này</h6>
                                <h2 class="fw-bold mb-0">{{ number_format($stats['this_week']) }}</h2>
                                <small class="text-white-50">7 ngày gần nhất</small>
                            </div>
                            <div class="fs-1 opacity-50">
                                <i class="fa-solid fa-calendar-week"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 stat-card"
                    style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                    <div class="card-body p-4 text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-2">Tháng này</h6>
                                <h2 class="fw-bold mb-0">{{ number_format($stats['this_month']) }}</h2>
                                <small class="text-white-50">Tháng {{ now()->format('m/Y') }}</small>
                            </div>
                            <div class="fs-1 opacity-50">
                                <i class="fa-solid fa-calendar"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Stats -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                                <i class="fa-solid fa-envelope fs-4 text-warning"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0">Chưa đọc</h6>
                                <h3 class="fw-bold mb-0">{{ number_format($stats['unread']) }}</h3>
                            </div>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-warning"
                                style="width: {{ $stats['total'] > 0 ? ($stats['unread'] / $stats['total']) * 100 : 0 }}%">
                            </div>
                        </div>
                        <small
                            class="text-muted">{{ $stats['total'] > 0 ? number_format(($stats['unread'] / $stats['total']) * 100, 1) : 0 }}%
                            của tổng</small>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                                <i class="fa-solid fa-envelope-open fs-4 text-success"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0">Đã đọc</h6>
                                <h3 class="fw-bold mb-0">{{ number_format($stats['read']) }}</h3>
                            </div>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success"
                                style="width: {{ $stats['total'] > 0 ? ($stats['read'] / $stats['total']) * 100 : 0 }}%">
                            </div>
                        </div>
                        <small
                            class="text-muted">{{ $stats['total'] > 0 ? number_format(($stats['read'] / $stats['total']) * 100, 1) : 0 }}%
                            của tổng</small>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                                <i class="fa-solid fa-clock fs-4 text-danger"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0">Hết hạn</h6>
                                <h3 class="fw-bold mb-0">{{ number_format($stats['expired']) }}</h3>
                            </div>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-danger"
                                style="width: {{ $stats['total'] > 0 ? ($stats['expired'] / $stats['total']) * 100 : 0 }}%">
                            </div>
                        </div>
                        <small
                            class="text-muted">{{ $stats['total'] > 0 ? number_format(($stats['expired'] / $stats['total']) * 100, 1) : 0 }}%
                            của tổng</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <!-- Notifications by Type -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-chart-pie text-primary me-2"></i>Phân bổ theo loại
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="chart-container">
                            <canvas id="typeChart"></canvas>
                        </div>
                        <div class="row g-3 mt-3">
                            @php
                                $typeColors = [
                                    'system' => ['bg' => 'primary', 'icon' => 'cog', 'name' => 'Hệ thống'],
                                    'order' => ['bg' => 'success', 'icon' => 'shopping-cart', 'name' => 'Đơn hàng'],
                                    'promotion' => ['bg' => 'warning', 'icon' => 'tag', 'name' => 'Khuyến mãi'],
                                    'activity' => ['bg' => 'info', 'icon' => 'bolt', 'name' => 'Hoạt động'],
                                ];
                            @endphp
                            @foreach ($stats['by_type'] as $type => $count)
                                @php $config = $typeColors[$type] ?? ['bg' => 'secondary', 'icon' => 'question', 'name' => $type]; @endphp
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-{{ $config['bg'] }} bg-opacity-10 p-2 me-2">
                                            <i class="fa-solid fa-{{ $config['icon'] }} text-{{ $config['bg'] }}"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="small text-muted">{{ $config['name'] }}</div>
                                            <div class="fw-bold">{{ number_format($count) }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Users -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-users text-primary me-2"></i>Top người nhận
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-3 py-2">#</th>
                                        <th class="px-3 py-2">Người dùng</th>
                                        <th class="px-3 py-2 text-center">Số lượng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stats['top_users'] as $index => $item)
                                        <tr>
                                            <td class="px-3 py-2">
                                                <span
                                                    class="badge bg-{{ $index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'light') }} text-dark">
                                                    {{ $index + 1 }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-2">
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-2"
                                                        style="width:35px; height:35px;">
                                                        <i class="fa-solid fa-user text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">{{ $item->user->email ?? 'N/A' }}</div>
                                                        <small class="text-muted">ID: {{ $item->user_id }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2 text-center">
                                                <span
                                                    class="badge bg-primary fs-6 px-3 py-2">{{ number_format($item->total) }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted">Chưa có dữ liệu</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Notifications -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fa-solid fa-clock text-primary me-2"></i>Thông báo gần đây
                    </h5>
                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-sm btn-outline-primary">
                        Xem tất cả <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3">Tiêu đề</th>
                                <th class="px-4 py-3">Người nhận</th>
                                <th class="px-4 py-3 text-center">Loại</th>
                                <th class="px-4 py-3 text-center">Trạng thái</th>
                                <th class="px-4 py-3 text-center">Thời gian</th>
                                <th class="px-4 py-3 text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stats['recent'] as $notification)
                                <tr class="border-bottom">
                                    <td class="px-4 py-3">
                                        <div class="fw-bold text-dark">{{ Str::limit($notification->title, 50) }}</div>
                                        <div class="small text-muted">{{ Str::limit($notification->message, 60) }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="fw-semibold">{{ $notification->user->email ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @php
                                            $typeConfig = [
                                                'system' => ['class' => 'primary', 'icon' => 'cog'],
                                                'order' => ['class' => 'success', 'icon' => 'shopping-cart'],
                                                'promotion' => ['class' => 'warning', 'icon' => 'tag'],
                                                'activity' => ['class' => 'info', 'icon' => 'bolt'],
                                            ];
                                            $config = $typeConfig[$notification->type->value] ?? [
                                                'class' => 'secondary',
                                                'icon' => 'question',
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $config['class'] }}">
                                            <i
                                                class="fa-solid fa-{{ $config['icon'] }} me-1"></i>{{ ucfirst($notification->type->value) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if ($notification->is_read)
                                            <span class="badge bg-success">Đã đọc</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Chưa đọc</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="small">{{ $notification->created_at->format('d/m/Y') }}</div>
                                        <div class="small text-muted">{{ $notification->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('admin.notifications.show', $notification->id) }}"
                                            class="btn btn-sm btn-outline-info">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Chưa có thông báo nào</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Type Distribution Chart
            const typeData = @json($stats['by_type']);
            const typeLabels = Object.keys(typeData);
            const typeValues = Object.values(typeData);

            const typeColors = {
                'system': '#0d6efd',
                'order': '#198754',
                'promotion': '#ffc107',
                'activity': '#0dcaf0'
            };

            const backgroundColors = typeLabels.map(label => typeColors[label] || '#6c757d');

            new Chart(document.getElementById('typeChart'), {
                type: 'doughnut',
                data: {
                    labels: typeLabels.map(l => l.charAt(0).toUpperCase() + l.slice(1)),
                    datasets: [{
                        data: typeValues,
                        backgroundColor: backgroundColors,
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
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
