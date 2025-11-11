@extends('layouts.admin')

@section('title', 'Chi Tiết Mail')

@push('styles')
<style>
    .stat-mini-card {
        transition: transform 0.3s;
    }
    .stat-mini-card:hover {
        transform: translateY(-3px);
    }
    .action-btn {
        transition: all 0.2s;
    }
    .action-btn:hover {
        transform: scale(1.05);
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
                        <i class="fa-solid fa-envelope-open text-primary me-2"></i>
                        Chi Tiết Mail
                    </h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.mails.index') }}">Mail</a></li>
                            <li class="breadcrumb-item active">Chi tiết</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.mails.index') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
                    </a>
                    <a href="{{ route('admin.mails.preview', $mail->id) }}" target="_blank"
                       class="btn btn-outline-info btn-lg">
                        <i class="fa-solid fa-eye me-2"></i> Preview
                    </a>
                    <a href="{{ route('admin.mails.edit', $mail->id) }}" class="btn btn-warning btn-lg">
                        <i class="fa-solid fa-pen me-2"></i> Chỉnh sửa
                    </a>
                    @if($stats['pending'] > 0)
                    <button type="button" class="btn btn-success btn-lg btn-send-mail"
                        data-action="{{ route('admin.mails.send', $mail->id) }}"
                        data-recipients="{{ $stats['pending'] }}">
                        <i class="fa-solid fa-paper-plane me-2"></i> Gửi Mail ({{ $stats['pending'] }})
                    </button>
                    @endif
                    @if($stats['failed'] > 0)
                    <button type="button" class="btn btn-danger btn-lg btn-resend-failed"
                        data-action="{{ route('admin.mails.resend-failed', $mail->id) }}"
                        data-recipients="{{ $stats['failed'] }}">
                        <i class="fa-solid fa-rotate me-2"></i> Gửi lại ({{ $stats['failed'] }})
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 stat-mini-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Tổng người nhận</h6>
                            <h3 class="fw-bold text-primary mb-0">{{ number_format($stats['total']) }}</h3>
                        </div>
                        <div class="fs-2 text-primary opacity-50"><i class="fa-solid fa-users"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 stat-mini-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Đã gửi</h6>
                            <h3 class="fw-bold text-success mb-0">{{ number_format($stats['sent']) }}</h3>
                        </div>
                        <div class="fs-2 text-success opacity-50"><i class="fa-solid fa-check-circle"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 stat-mini-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Chờ gửi</h6>
                            <h3 class="fw-bold text-warning mb-0">{{ number_format($stats['pending']) }}</h3>
                        </div>
                        <div class="fs-2 text-warning opacity-50"><i class="fa-solid fa-clock"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 stat-mini-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Thất bại</h6>
                            <h3 class="fw-bold text-danger mb-0">{{ number_format($stats['failed']) }}</h3>
                        </div>
                        <div class="fs-2 text-danger opacity-50"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Mail Information -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fa-solid fa-info-circle text-primary me-2"></i>Thông tin Mail
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted small">Tiêu đề</label>
                            <div class="fs-5 fw-bold text-dark">{{ $mail->subject }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted small">Loại Mail</label>
                            <div>
                                @php
                                    $typeConfig = [
                                        'system' => ['class' => 'primary', 'icon' => 'cog', 'text' => 'System'],
                                        'user' => ['class' => 'info', 'icon' => 'user', 'text' => 'User'],
                                        'marketing' => ['class' => 'success', 'icon' => 'bullhorn', 'text' => 'Marketing']
                                    ];
                                    $config = $typeConfig[$mail->type->value] ?? ['class' => 'secondary', 'icon' => 'question', 'text' => $mail->type->value];
                                @endphp
                                <span class="badge bg-{{ $config['class'] }} fs-6 px-3 py-2">
                                    <i class="fa-solid fa-{{ $config['icon'] }} me-1"></i>{{ $config['text'] }}
                                </span>
                            </div>
                        </div>
                        @if($mail->template_key)
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted small">Template Key</label>
                            <div class="d-flex align-items-center">
                                <i class="fa-solid fa-file-code text-primary me-2"></i>
                                <code class="fs-6">{{ $mail->template_key }}</code>
                            </div>
                        </div>
                        @endif
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted small">Email Người Gửi</label>
                            <div class="d-flex align-items-center">
                                <i class="fa-solid fa-envelope text-primary me-2"></i>
                                <span>{{ $mail->sender_email }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted small">Ngày tạo</label>
                            <div class="d-flex align-items-center">
                                <i class="fa-solid fa-calendar text-primary me-2"></i>
                                <span>{{ $mail->created_at->format('d/m/Y H:i:s') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted small">Cập nhật lần cuối</label>
                            <div class="d-flex align-items-center">
                                <i class="fa-solid fa-clock text-primary me-2"></i>
                                <span>{{ $mail->updated_at->format('d/m/Y H:i:s') }}</span>
                            </div>
                        </div>
                    </div>

                    @if($mail->variables && count($mail->variables) > 0)
                    <div class="mt-4">
                        <label class="form-label fw-semibold text-muted small">Biến động (Variables)</label>
                        <div class="bg-light p-3 rounded">
                            <code class="text-dark">{{ json_encode($mail->variables, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Mail Content -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fa-solid fa-file-lines text-primary me-2"></i>Nội dung Mail
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="border rounded p-4 bg-light">
                        {!! $mail->content !!}
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fa-solid fa-bolt text-warning me-2"></i>Hành động nhanh
                    </h5>
                </div>
                <div class="card-body p-3">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.mails.preview', $mail->id) }}" target="_blank"
                           class="btn btn-outline-info btn-lg">
                            <i class="fa-solid fa-eye me-2"></i> Xem Preview
                        </a>
                        <a href="{{ route('admin.mails.edit', $mail->id) }}"
                           class="btn btn-outline-warning btn-lg">
                            <i class="fa-solid fa-pen me-2"></i> Chỉnh sửa
                        </a>
                        @if($stats['pending'] > 0)
                        <button type="button" class="btn btn-success btn-lg btn-send-mail"
                            data-action="{{ route('admin.mails.send', $mail->id) }}"
                            data-recipients="{{ $stats['pending'] }}">
                            <i class="fa-solid fa-paper-plane me-2"></i> Gửi {{ $stats['pending'] }} mail
                        </button>
                        @endif
                        @if($stats['failed'] > 0)
                        <button type="button" class="btn btn-danger btn-lg btn-resend-failed"
                            data-action="{{ route('admin.mails.resend-failed', $mail->id) }}"
                            data-recipients="{{ $stats['failed'] }}">
                            <i class="fa-solid fa-rotate me-2"></i> Gửi lại {{ $stats['failed'] }} mail
                        </button>
                        @endif
                        <hr>
                        <button type="button" class="btn btn-outline-danger btn-lg btn-delete-mail"
                            data-action="{{ route('admin.mails.destroy', $mail->id) }}">
                            <i class="fa-solid fa-trash me-2"></i> Xóa Mail
                        </button>
                    </div>
                </div>
            </div>

            <!-- Progress Chart -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fa-solid fa-chart-pie text-primary me-2"></i>Tiến độ gửi
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-success fw-semibold">Đã gửi</span>
                            <span class="badge bg-success">{{ number_format(($stats['sent'] / max($stats['total'], 1)) * 100, 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar"
                                style="width: {{ ($stats['sent'] / max($stats['total'], 1)) * 100 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-warning fw-semibold">Chờ gửi</span>
                            <span class="badge bg-warning text-dark">{{ number_format(($stats['pending'] / max($stats['total'], 1)) * 100, 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-warning" role="progressbar"
                                style="width: {{ ($stats['pending'] / max($stats['total'], 1)) * 100 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-danger fw-semibold">Thất bại</span>
                            <span class="badge bg-danger">{{ number_format(($stats['failed'] / max($stats['total'], 1)) * 100, 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-danger" role="progressbar"
                                style="width: {{ ($stats['failed'] / max($stats['total'], 1)) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recipients List -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-semibold">
                <i class="fa-solid fa-users text-primary me-2"></i>Danh sách Người Nhận
                <span class="badge bg-primary fs-6">{{ $recipients->total() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 text-center" style="width:60px;">#</th>
                            <th class="px-4 py-3">Người nhận</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3 text-center">Trạng thái</th>
                            <th class="px-4 py-3">Lỗi (nếu có)</th>
                            <th class="px-4 py-3 text-center">Cập nhật</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recipients as $index => $recipient)
                        <tr class="border-bottom">
                            <td class="text-center px-4">
                                <span class="badge bg-light text-dark">{{ $recipients->firstItem() + $index }}</span>
                            </td>
                            <td class="px-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center me-3"
                                        style="width:35px; height:35px;">
                                        <i class="fa-solid fa-user text-info"></i>
                                    </div>
                                    <div class="fw-semibold">{{ $recipient->name }}</div>
                                </div>
                            </td>
                            <td class="px-4">
                                <i class="fa-solid fa-envelope text-muted me-1"></i>
                                {{ $recipient->email }}
                            </td>
                            <td class="text-center px-4">
                                @php
                                    $statusConfig = [
                                        'sent' => ['class' => 'success', 'icon' => 'check-circle', 'text' => 'Đã gửi'],
                                        'pending' => ['class' => 'warning', 'icon' => 'clock', 'text' => 'Chờ gửi'],
                                        'failed' => ['class' => 'danger', 'icon' => 'times-circle', 'text' => 'Thất bại']
                                    ];
                                    $config = $statusConfig[$recipient->status->value] ?? ['class' => 'secondary', 'icon' => 'question', 'text' => $recipient->status->value];
                                @endphp
                                <span class="badge bg-{{ $config['class'] }} px-3 py-2">
                                    <i class="fa-solid fa-{{ $config['icon'] }} me-1"></i>{{ $config['text'] }}
                                </span>
                            </td>
                            <td class="px-4">
                                @if($recipient->error_log)
                                <span class="text-danger small">
                                    <i class="fa-solid fa-exclamation-triangle me-1"></i>
                                    {{ Str::limit($recipient->error_log, 50) }}
                                </span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center px-4">
                                <div class="small">{{ $recipient->updated_at->format('d/m/Y H:i') }}</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Chưa có người nhận nào</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($recipients->hasPages())
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Hiển thị {{ $recipients->firstItem() }} - {{ $recipients->lastItem() }} trong {{ $recipients->total() }} người nhận
                </div>
                <div>{{ $recipients->links('components.pagination') }}</div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Send mail
    document.querySelectorAll('.btn-send-mail').forEach(btn => {
        btn.addEventListener('click', function() {
            const sendUrl = this.dataset.action;
            const recipients = this.dataset.recipients;

            Swal.fire({
                title: 'Xác nhận gửi mail?',
                html: `Gửi mail đến <strong>${recipients}</strong> người nhận?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fa-solid fa-paper-plane me-2"></i>Gửi ngay',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = sendUrl;
                    form.innerHTML = `@csrf`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });

    // Resend failed
    document.querySelectorAll('.btn-resend-failed').forEach(btn => {
        btn.addEventListener('click', function() {
            const resendUrl = this.dataset.action;
            const recipients = this.dataset.recipients;

            Swal.fire({
                title: 'Gửi lại mail thất bại?',
                html: `Gửi lại mail đến <strong>${recipients}</strong> người nhận bị thất bại?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fa-solid fa-rotate me-2"></i>Gửi lại',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = resendUrl;
                    form.innerHTML = `@csrf`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });

    // Delete mail
    document.querySelectorAll('.btn-delete-mail').forEach(btn => {
        btn.addEventListener('click', function() {
            const deleteUrl = this.dataset.action;

            Swal.fire({
                title: 'Xác nhận xóa?',
                text: 'Bạn có chắc muốn xóa mail này?',
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
});
</script>
@endpush
