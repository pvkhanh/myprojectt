@extends('layouts.admin')

@section('title', 'Chi Tiết Thông Báo')

@push('styles')
    <style>
        .info-card {
            transition: transform 0.3s;
        }

        .info-card:hover {
            transform: translateY(-3px);
        }

        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 20px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -24px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #0d6efd;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #dee2e6;
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
                            <i class="fa-solid fa-bell text-primary me-2"></i>
                            Chi Tiết Thông Báo
                        </h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.notifications.index') }}">Thông báo</a>
                                </li>
                                <li class="breadcrumb-item active">Chi tiết #{{ $notification->id }}</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
                        </a>
                        <a href="{{ route('admin.notifications.edit', $notification->id) }}" class="btn btn-warning btn-lg">
                            <i class="fa-solid fa-pen me-2"></i> Chỉnh sửa
                        </a>
                        <button type="button" class="btn btn-danger btn-lg btn-delete-notification"
                            data-action="{{ route('admin.notifications.destroy', $notification->id) }}">
                            <i class="fa-solid fa-trash me-2"></i> Xóa
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Notification Info -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-info-circle text-primary me-2"></i>Thông tin thông báo
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <!-- Title -->
                            <div class="col-md-12">
                                <label class="form-label fw-semibold text-muted small">Tiêu đề</label>
                                <h4 class="mb-0">{{ $notification->title }}</h4>
                            </div>

                            <!-- Type & Status -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small">Loại thông báo</label>
                                <div>
                                    @php
                                        $typeConfig = [
                                            'system' => ['class' => 'primary', 'icon' => 'cog', 'text' => 'Hệ thống'],
                                            'order' => [
                                                'class' => 'success',
                                                'icon' => 'shopping-cart',
                                                'text' => 'Đơn hàng',
                                            ],
                                            'promotion' => [
                                                'class' => 'warning',
                                                'icon' => 'tag',
                                                'text' => 'Khuyến mãi',
                                            ],
                                            'activity' => ['class' => 'info', 'icon' => 'bolt', 'text' => 'Hoạt động'],
                                        ];
                                        $config = $typeConfig[$notification->type->value] ?? [
                                            'class' => 'secondary',
                                            'icon' => 'question',
                                            'text' => $notification->type->value,
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $config['class'] }} fs-5 px-4 py-2">
                                        <i class="fa-solid fa-{{ $config['icon'] }} me-2"></i>{{ $config['text'] }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small">Trạng thái</label>
                                <div>
                                    @if ($notification->is_read)
                                        <span class="badge bg-success fs-5 px-4 py-2">
                                            <i class="fa-solid fa-check-circle me-2"></i>Đã đọc
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark fs-5 px-4 py-2">
                                            <i class="fa-solid fa-envelope me-2"></i>Chưa đọc
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Message -->
                            <div class="col-md-12">
                                <label class="form-label fw-semibold text-muted small">Nội dung</label>
                                <div class="border rounded p-4 bg-light">
                                    <p class="mb-0 fs-5 text-dark" style="white-space: pre-wrap;">
                                        {{ $notification->message }}</p>
                                </div>
                            </div>

                            <!-- Variables -->
                            @php
                                $vars = is_array($notification->variables)
                                    ? $notification->variables
                                    : json_decode($notification->variables, true);
                            @endphp

                            @if (!empty($vars))
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold text-muted small">Biến động (Variables)</label>
                                    <div class="bg-dark text-light p-3 rounded">
                                        <pre class="mb-0"><code class="text-light">{{ json_encode($vars, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                    </div>
                                </div>
                            @endif


                            <!-- Timestamps -->
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-muted small">
                                    <i class="fa-solid fa-calendar-plus me-1"></i> Ngày tạo
                                </label>
                                <div class="fw-bold text-dark">{{ $notification->created_at->format('d/m/Y H:i:s') }}</div>
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-muted small">
                                    <i class="fa-solid fa-clock me-1"></i> Cập nhật lần cuối
                                </label>
                                <div class="fw-bold text-dark">{{ $notification->updated_at->format('d/m/Y H:i:s') }}</div>
                                <small class="text-muted">{{ $notification->updated_at->diffForHumans() }}</small>
                            </div>

                            @if ($notification->read_at)
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-muted small">
                                        <i class="fa-solid fa-eye me-1"></i> Đã đọc lúc
                                    </label>
                                    <div class="fw-bold text-success">{{ $notification->read_at->format('d/m/Y H:i:s') }}
                                    </div>
                                    <small class="text-muted">{{ $notification->read_at->diffForHumans() }}</small>
                                </div>
                            @endif

                            @if ($notification->expires_at)
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-muted small">
                                        <i class="fa-solid fa-hourglass-end me-1"></i> Hết hạn vào
                                    </label>
                                    <div
                                        class="fw-bold {{ $notification->expires_at->isPast() ? 'text-danger' : 'text-warning' }}">
                                        {{ $notification->expires_at->format('d/m/Y H:i:s') }}
                                    </div>
                                    <small class="text-muted">
                                        @if ($notification->expires_at->isPast())
                                            <i class="fa-solid fa-exclamation-triangle me-1"></i>Đã hết hạn
                                        @else
                                            {{ $notification->expires_at->diffForHumans() }}
                                        @endif
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- User Info -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-user text-primary me-2"></i>Người nhận
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3"
                                style="width:70px; height:70px;">
                                @if ($notification->user->avatar)
                                    <img src="{{ $notification->user->avatar_url }}"
                                        alt="{{ $notification->user->email }}" class="rounded-circle"
                                        style="width:70px; height:70px; object-fit:cover;">
                                @else
                                    <i class="fa-solid fa-user text-primary fs-2"></i>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-1 fw-bold">
                                    {{ trim(($notification->user->first_name ?? '') . ' ' . ($notification->user->last_name ?? '')) ?: 'N/A' }}
                                </h5>
                                <div class="text-muted mb-2">
                                    <i class="fa-solid fa-envelope me-1"></i>{{ $notification->user->email }}
                                </div>
                                <div class="d-flex gap-2">
                                    <span class="badge bg-info">
                                        <i class="fa-solid fa-id-badge me-1"></i>ID: {{ $notification->user->id }}
                                    </span>
                                    @if ($notification->user->phone)
                                        <span class="badge bg-secondary">
                                            <i class="fa-solid fa-phone me-1"></i>{{ $notification->user->phone }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <a href="#" class="btn btn-outline-primary">
                                    <i class="fa-solid fa-eye me-2"></i>Xem profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-bolt text-warning me-2"></i>Hành động nhanh
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.notifications.edit', $notification->id) }}"
                                class="btn btn-warning btn-lg">
                                <i class="fa-solid fa-pen me-2"></i> Chỉnh sửa
                            </a>

                            @if (!$notification->is_read)
                                <button type="button" class="btn btn-success btn-lg" id="btnSendAgain">
                                    <i class="fa-solid fa-paper-plane me-2"></i> Gửi lại
                                </button>
                            @endif

                            <button type="button" class="btn btn-info btn-lg" id="btnDuplicate">
                                <i class="fa-solid fa-copy me-2"></i> Nhân bản
                            </button>

                            <hr>

                            <button type="button" class="btn btn-danger btn-lg btn-delete-notification"
                                data-action="{{ route('admin.notifications.destroy', $notification->id) }}">
                                <i class="fa-solid fa-trash me-2"></i> Xóa thông báo
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="card border-0 shadow-sm mb-4 bg-gradient-primary text-white">
                    <div class="card-body p-4">
                        <h5 class="mb-3 fw-bold text-white">
                            <i class="fa-solid fa-chart-simple me-2"></i>Thống kê
                        </h5>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="text-white-50 small">Ngày tạo</div>
                                <div class="fw-bold">{{ $notification->created_at->format('d/m/Y') }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-white-50 small">Trạng thái</div>
                                <div class="fw-bold">{{ $notification->is_read ? 'Đã đọc' : 'Chưa đọc' }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-white-50 small">Loại</div>
                                <div class="fw-bold">{{ ucfirst($notification->type->value) }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-white-50 small">User ID</div>
                                <div class="fw-bold">#{{ $notification->user_id }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-timeline text-primary me-2"></i>Lịch sử
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="text-muted small">{{ $notification->created_at->format('d/m/Y H:i:s') }}</div>
                                <div class="fw-semibold">Thông báo được tạo</div>
                                <div class="small text-muted">Bởi Admin</div>
                            </div>

                            @if ($notification->read_at)
                                <div class="timeline-item">
                                    <div class="text-muted small">{{ $notification->read_at->format('d/m/Y H:i:s') }}
                                    </div>
                                    <div class="fw-semibold text-success">Đã được đọc</div>
                                    <div class="small text-muted">Bởi {{ $notification->user->email }}</div>
                                </div>
                            @endif

                            @if ($notification->updated_at != $notification->created_at)
                                <div class="timeline-item">
                                    <div class="text-muted small">{{ $notification->updated_at->format('d/m/Y H:i:s') }}
                                    </div>
                                    <div class="fw-semibold">Cập nhật lần cuối</div>
                                    <div class="small text-muted">Bởi Admin</div>
                                </div>
                            @endif

                            @if ($notification->expires_at)
                                <div class="timeline-item">
                                    <div class="text-muted small">{{ $notification->expires_at->format('d/m/Y H:i:s') }}
                                    </div>
                                    <div
                                        class="fw-semibold {{ $notification->expires_at->isPast() ? 'text-danger' : 'text-warning' }}">
                                        {{ $notification->expires_at->isPast() ? 'Đã hết hạn' : 'Sẽ hết hạn' }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Delete notification
            document.querySelectorAll('.btn-delete-notification').forEach(btn => {
                btn.addEventListener('click', function() {
                    const deleteUrl = this.dataset.action;

                    Swal.fire({
                        title: 'Xác nhận xóa?',
                        text: 'Bạn có chắc muốn xóa thông báo này?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Xóa',
                        cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = deleteUrl;
                            form.innerHTML = `
                        @csrf
                        @method('DELETE')
                    `;
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                });
            });

            // Send again
            document.getElementById('btnSendAgain')?.addEventListener('click', function() {
                Swal.fire({
                    title: 'Gửi lại thông báo?',
                    text: 'Thông báo sẽ được gửi lại real-time đến người dùng',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Gửi',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Implement send again logic
                        Swal.fire('Đã gửi!', 'Thông báo đã được gửi lại', 'success');
                    }
                });
            });

            // Duplicate
            document.getElementById('btnDuplicate')?.addEventListener('click', function() {
                Swal.fire({
                    title: 'Nhân bản thông báo?',
                    text: 'Tạo một bản sao của thông báo này',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Nhân bản',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href =
                            '{{ route('admin.notifications.create') }}?duplicate={{ $notification->id }}';
                    }
                });
            });
        });
    </script>
@endpush
