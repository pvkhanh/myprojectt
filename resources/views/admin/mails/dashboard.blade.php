@extends('layouts.admin')

@section('title', 'Mail Dashboard')

@push('styles')
<style>
    .stat-card {
        transition: transform 0.3s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .bg-gradient-success {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }
    .bg-gradient-warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    .bg-gradient-danger {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
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
                        <i class="fa-solid fa-envelope text-primary me-2"></i>
                        Mail Dashboard
                    </h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Mail System</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.mails.segments') }}" class="btn btn-outline-info btn-lg">
                        <i class="fa-solid fa-users-between-lines me-2"></i> Segments
                    </a>
                    <a href="{{ route('admin.mails.templates') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="fa-solid fa-file-lines me-2"></i> Templates
                    </a>
                    <a href="{{ route('admin.mails.create') }}" class="btn btn-primary btn-lg">
                        <i class="fa-solid fa-plus me-2"></i> Tạo Mail Mới
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 bg-gradient-primary text-white stat-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Tổng Mail</h6>
                            <h3 class="fw-bold mb-0">{{ number_format($totalMails) }}</h3>
                        </div>
                        <div class="fs-1 opacity-50"><i class="fa-solid fa-envelope"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 bg-gradient-success text-white stat-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Đã Gửi Hôm Nay</h6>
                            <h3 class="fw-bold mb-0">{{ number_format($sentToday) }}</h3>
                        </div>
                        <div class="fs-1 opacity-50"><i class="fa-solid fa-paper-plane"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 bg-gradient-warning text-white stat-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Tổng Người Nhận</h6>
                            <h3 class="fw-bold mb-0">{{ number_format($totalRecipients) }}</h3>
                        </div>
                        <div class="fs-1 opacity-50"><i class="fa-solid fa-users"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 bg-gradient-danger text-white stat-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Thất Bại Hôm Nay</h6>
                            <h3 class="fw-bold mb-0">{{ number_format($failedToday) }}</h3>
                        </div>
                        <div class="fs-1 opacity-50"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <!-- 7 Days Chart -->
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fa-solid fa-chart-line text-primary me-2"></i>Thống Kê 7 Ngày Qua
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="last7DaysChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mail Types -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fa-solid fa-chart-pie text-primary me-2"></i>Phân Loại Mail
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="mailTypesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Mails & Top Templates -->
    <div class="row g-3 mb-4">
        <!-- Recent Mails -->
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-clock-rotate-left text-primary me-2"></i>Mail Gần Đây
                        </h5>
                        <a href="{{ route('admin.mails.index') }}" class="btn btn-sm btn-outline-primary">
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
                                    <th class="px-4 py-3 text-center">Loại</th>
                                    <th class="px-4 py-3 text-center">Người nhận</th>
                                    <th class="px-4 py-3 text-center">Ngày tạo</th>
                                    <th class="px-4 py-3 text-center">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentMails as $mail)
                                <tr class="border-bottom">
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3"
                                                style="width:40px; height:40px;">
                                                <i class="fa-solid fa-envelope text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark">{{ Str::limit($mail->subject, 50) }}</div>
                                                @if($mail->template_key)
                                                <div class="small text-muted">Template: {{ $mail->template_key }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center px-4">
                                        @php
                                            $typeConfig = [
                                                'system' => ['class' => 'primary', 'text' => 'System'],
                                                'user' => ['class' => 'info', 'text' => 'User'],
                                                'marketing' => ['class' => 'success', 'text' => 'Marketing']
                                            ];
                                            $config = $typeConfig[$mail->type->value] ?? ['class' => 'secondary', 'text' => $mail->type->value];
                                        @endphp
                                        <span class="badge bg-{{ $config['class'] }}">{{ $config['text'] }}</span>
                                    </td>
                                    <td class="text-center px-4">
                                        <span class="badge bg-light text-dark">{{ $mail->recipients->count() }}</span>
                                    </td>
                                    <td class="text-center px-4">
                                        <div class="small">{{ $mail->created_at->format('d/m/Y H:i') }}</div>
                                    </td>
                                    <td class="text-center px-4">
                                        <a href="{{ route('admin.mails.show', $mail->id) }}"
                                           class="btn btn-sm btn-outline-info">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Chưa có mail nào</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Templates -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fa-solid fa-fire text-danger me-2"></i>Templates Phổ Biến
                    </h5>
                </div>
                <div class="card-body">
                    @forelse($topTemplates as $template)
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <div class="fw-semibold text-dark">{{ $template->template_key }}</div>
                            <div class="small text-muted">Sử dụng {{ $template->usage_count }} lần</div>
                        </div>
                        <div class="text-end">
                            <div class="badge bg-primary fs-6">{{ $template->usage_count }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="fa-solid fa-inbox fs-3 d-block mb-2 opacity-50"></i>
                        Chưa có template nào
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 7 Days Chart
    const last7DaysData = @json($last7Days);
    const ctx1 = document.getElementById('last7DaysChart').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: last7DaysData.map(d => d.date),
            datasets: [
                {
                    label: 'Đã gửi',
                    data: last7DaysData.map(d => d.sent),
                    borderColor: '#43e97b',
                    backgroundColor: 'rgba(67, 233, 123, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Thất bại',
                    data: last7DaysData.map(d => d.failed),
                    borderColor: '#fa709a',
                    backgroundColor: 'rgba(250, 112, 154, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Mail Types Chart
    const mailByType = @json($mailByType);
    const ctx2 = document.getElementById('mailTypesChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: mailByType.map(m => m.type.charAt(0).toUpperCase() + m.type.slice(1)),
            datasets: [{
                data: mailByType.map(m => m.total),
                backgroundColor: ['#667eea', '#4facfe', '#43e97b']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endpush
