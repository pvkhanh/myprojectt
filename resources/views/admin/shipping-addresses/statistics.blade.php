@extends('layouts.admin')

@section('title', 'Thống kê địa chỉ giao hàng')

@section('content')
    <div class="container-fluid py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">
                    <i class="fas fa-chart-bar me-2 text-primary"></i>Thống kê địa chỉ giao hàng
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb small text-muted mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.shipping-addresses.index') }}">Địa chỉ giao
                                hàng</a></li>
                        <li class="breadcrumb-item active">Thống kê</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('admin.shipping-addresses.index') }}" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-2"></i>Quay lại
                </a>
            </div>
        </div>

        <div class="row g-4">

            {{-- Top Provinces --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-gradient-primary text-white border-0 py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fas fa-map-marked-alt me-2"></i>
                            Top 10 Tỉnh/Thành phố
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tỉnh/Thành phố</th>
                                        <th class="text-end">Số lượng</th>
                                        <th class="text-end">Tỷ lệ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $total = $byProvince->sum('total'); @endphp
                                    @foreach ($byProvince as $index => $item)
                                        <tr>
                                            <td>
                                                @if ($index == 0)
                                                    <i class="fas fa-crown text-warning"></i>
                                                @elseif($index == 1)
                                                    <i class="fas fa-medal text-secondary"></i>
                                                @elseif($index == 2)
                                                    <i class="fas fa-medal text-danger"></i>
                                                @else
                                                    {{ $index + 1 }}
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $item->province }}</strong>
                                            </td>
                                            <td class="text-end">
                                                <span class="badge bg-primary">{{ number_format($item->total) }}</span>
                                            </td>
                                            <td class="text-end">
                                                <div class="progress" style="height: 20px;">
                                                    @php $percent = $total > 0 ? ($item->total / $total) * 100 : 0; @endphp
                                                    <div class="progress-bar bg-primary" role="progressbar"
                                                        style="width: {{ $percent }}%">
                                                        {{ number_format($percent, 1) }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Top Receivers --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-gradient-success text-white border-0 py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fas fa-user-tie me-2"></i>
                            Top 10 Người nhận
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Người nhận</th>
                                        <th>Số điện thoại</th>
                                        <th class="text-end">Số đơn</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($topReceivers as $index => $receiver)
                                        <tr>
                                            <td>
                                                @if ($index == 0)
                                                    <i class="fas fa-crown text-warning"></i>
                                                @elseif($index == 1)
                                                    <i class="fas fa-medal text-secondary"></i>
                                                @elseif($index == 2)
                                                    <i class="fas fa-medal text-danger"></i>
                                                @else
                                                    {{ $index + 1 }}
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $receiver->receiver_name }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">{{ $receiver->phone }}</span>
                                            </td>
                                            <td class="text-end">
                                                <span class="badge bg-success">{{ number_format($receiver->total) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Monthly Trend --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-gradient-info text-white border-0 py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fas fa-chart-line me-2"></i>
                            Xu hướng theo tháng (12 tháng gần nhất)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tháng</th>
                                        <th class="text-end">Số lượng</th>
                                        <th>Biểu đồ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $maxMonth = $byMonth->max('total');
                                    @endphp
                                    @foreach ($byMonth as $month)
                                        <tr>
                                            <td>
                                                <strong>{{ \Carbon\Carbon::parse($month->month . '-01')->format('m/Y') }}</strong>
                                            </td>
                                            <td class="text-end">
                                                <span class="badge bg-info fs-6">{{ number_format($month->total) }}</span>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 25px;">
                                                    @php
                                                        $percent =
                                                            $maxMonth > 0 ? ($month->total / $maxMonth) * 100 : 0;
                                                    @endphp
                                                    <div class="progress-bar bg-info progress-bar-striped progress-bar-animated"
                                                        role="progressbar" style="width: {{ $percent }}%">
                                                        {{ number_format($month->total) }} địa chỉ
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Summary Cards --}}
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center py-4 bg-gradient-primary text-white">
                    <div class="card-body">
                        <i class="fas fa-map-marked-alt fa-3x mb-3 opacity-75"></i>
                        <h3 class="fw-bold mb-1">{{ number_format($byProvince->sum('total')) }}</h3>
                        <p class="mb-0 text-white-50">Tổng địa chỉ</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center py-4 bg-gradient-success text-white">
                    <div class="card-body">
                        <i class="fas fa-building fa-3x mb-3 opacity-75"></i>
                        <h3 class="fw-bold mb-1">{{ $byProvince->count() }}</h3>
                        <p class="mb-0 text-white-50">Tỉnh/Thành phố</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center py-4 bg-gradient-warning text-white">
                    <div class="card-body">
                        <i class="fas fa-users fa-3x mb-3 opacity-75"></i>
                        <h3 class="fw-bold mb-1">{{ $topReceivers->count() }}</h3>
                        <p class="mb-0 text-white-50">Người nhận</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center py-4 bg-gradient-info text-white">
                    <div class="card-body">
                        <i class="fas fa-calendar fa-3x mb-3 opacity-75"></i>
                        <h3 class="fw-bold mb-1">{{ $byMonth->count() }}</h3>
                        <p class="mb-0 text-white-50">Tháng theo dõi</p>
                    </div>
                </div>
            </div>

        </div>

    </div>

    @push('styles')
        <style>
            .bg-gradient-primary {
                background: linear-gradient(135deg, #4f46e5 0%, #6d28d9 100%);
            }

            .bg-gradient-success {
                background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);
            }

            .bg-gradient-warning {
                background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
            }

            .bg-gradient-info {
                background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
            }

            .card {
                border-radius: 12px;
                transition: transform 0.2s;
            }

            .card:hover {
                transform: translateY(-5px);
            }

            tbody tr:hover {
                background-color: #f8fafc;
            }
        </style>
    @endpush
@endsection
