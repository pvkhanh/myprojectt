@extends('layouts.admin')

@section('title', 'Quản Lý Mail')

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
    .action-btn {
        transition: all 0.2s;
    }
    .action-btn:hover {
        transform: scale(1.1);
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
                        Quản Lý Mail
                    </h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.mails.dashboard') }}">Mail System</a></li>
                            <li class="breadcrumb-item active">Danh sách Mail</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.mails.dashboard') }}" class="btn btn-outline-info btn-lg">
                        <i class="fa-solid fa-chart-line me-2"></i> Dashboard
                    </a>
                    <a href="{{ route('admin.mails.create') }}" class="btn btn-primary btn-lg">
                        <i class="fa-solid fa-plus me-2"></i> Tạo Mail Mới
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-semibold">
                <i class="fa-solid fa-filter text-primary me-2"></i>Bộ lọc
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.mails.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-magnifying-glass text-muted me-1"></i> Tìm kiếm
                        </label>
                        <input type="text" name="search" class="form-control form-control-lg"
                            placeholder="Tiêu đề, nội dung..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-tag text-muted me-1"></i> Loại Mail
                        </label>
                        <select name="type" class="form-select form-select-lg">
                            <option value="">Tất cả</option>
                            @foreach($types as $type)
                            <option value="{{ $type->value }}" {{ request('type') == $type->value ? 'selected' : '' }}>
                                {{ ucfirst($type->value) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-circle-info text-muted me-1"></i> Trạng thái
                        </label>
                        <select name="status" class="form-select form-select-lg">
                            <option value="">Tất cả</option>
                            @foreach($statuses as $status)
                            <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>
                                {{ ucfirst($status->value) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-calendar text-muted me-1"></i> Từ ngày
                        </label>
                        <input type="date" name="date_from" class="form-control form-control-lg"
                            value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-calendar-check text-muted me-1"></i> Đến ngày
                        </label>
                        <input type="date" name="date_to" class="form-control form-control-lg"
                            value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-1 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary btn-lg flex-fill">
                            <i class="fa-solid fa-filter"></i>
                        </button>
                        <a href="{{ route('admin.mails.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fa-solid fa-rotate-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Advanced Filters -->
                <div class="collapse mt-3" id="advancedFilters">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Template Key</label>
                            <input type="text" name="template_key" class="form-control form-control-lg"
                                placeholder="welcome-email, reset-password..." value="{{ request('template_key') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Sắp xếp theo</label>
                            <select name="sort_by" class="form-select form-select-lg">
                                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Ngày tạo</option>
                                <option value="subject" {{ request('sort_by') == 'subject' ? 'selected' : '' }}>Tiêu đề</option>
                                <option value="type" {{ request('sort_by') == 'type' ? 'selected' : '' }}>Loại</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Thứ tự</label>
                            <select name="sort_order" class="form-select form-select-lg">
                                <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Giảm dần</option>
                                <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Tăng dần</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Số dòng/trang</label>
                            <select name="per_page" class="form-select form-select-lg">
                                <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15</option>
                                <option value="30" {{ request('per_page') == '30' ? 'selected' : '' }}>30</option>
                                <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="button" class="btn btn-link text-decoration-none p-0"
                        data-bs-toggle="collapse" data-bs-target="#advancedFilters">
                        <i class="fa-solid fa-chevron-down me-1"></i> Bộ lọc nâng cao
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Mails Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-semibold">
                <i class="fa-solid fa-list text-primary me-2"></i>Danh sách Mail
                <span class="badge bg-primary fs-6">{{ $mails->total() }} mail</span>
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 text-center" style="width:80px;">#</th>
                            <th class="px-4 py-3">Tiêu đề</th>
                            <th class="px-4 py-3 text-center">Loại</th>
                            <th class="px-4 py-3 text-center">Người nhận</th>
                            <th class="px-4 py-3 text-center">Đã gửi</th>
                            <th class="px-4 py-3 text-center">Ngày tạo</th>
                            <th class="px-4 py-3 text-center" style="width:250px;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mails as $index => $mail)
                        <tr class="border-bottom">
                            <td class="text-center px-4">
                                <span class="badge bg-light text-dark fs-6">{{ $mails->firstItem() + $index }}</span>
                            </td>
                            <td class="px-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3"
                                        style="width:45px; height:45px;">
                                        <i class="fa-solid fa-envelope text-primary fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ Str::limit($mail->subject, 50) }}</div>
                                        @if($mail->template_key)
                                        <div class="small text-muted">
                                            <i class="fa-solid fa-file-code me-1"></i>{{ $mail->template_key }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-center px-4">
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
                            </td>
                            <td class="text-center px-4">
                                <div class="fw-bold text-primary fs-6">{{ $mail->recipients->count() }}</div>
                                <div class="small text-muted">người nhận</div>
                            </td>
                            <td class="text-center px-4">
                                @php
                                    $sentCount = $mail->recipients->where('status', 'sent')->count();
                                    $pendingCount = $mail->recipients->where('status', 'pending')->count();
                                    $failedCount = $mail->recipients->where('status', 'failed')->count();
                                @endphp
                                <div class="d-flex flex-column gap-1">
                                    @if($sentCount > 0)
                                    <span class="badge bg-success">{{ $sentCount }} sent</span>
                                    @endif
                                    @if($pendingCount > 0)
                                    <span class="badge bg-warning text-dark">{{ $pendingCount }} pending</span>
                                    @endif
                                    @if($failedCount > 0)
                                    <span class="badge bg-danger">{{ $failedCount }} failed</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center px-4">
                                <div class="fw-semibold text-dark">{{ $mail->created_at->format('d/m/Y') }}</div>
                                <div class="small text-muted">{{ $mail->created_at->format('H:i') }}</div>
                            </td>
                            <td class="text-center px-4">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.mails.show', $mail->id) }}"
                                        class="btn btn-outline-info btn-sm action-btn"
                                        data-bs-toggle="tooltip" title="Xem chi tiết">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.mails.preview', $mail->id) }}" target="_blank"
                                        class="btn btn-outline-secondary btn-sm action-btn"
                                        data-bs-toggle="tooltip" title="Preview">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </a>
                                    <a href="{{ route('admin.mails.edit', $mail->id) }}"
                                        class="btn btn-outline-warning btn-sm action-btn"
                                        data-bs-toggle="tooltip" title="Chỉnh sửa">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    @php
                                        $pendingCount = $mail->recipients->where('status', 'pending')->count();
                                    @endphp
                                    @if($pendingCount > 0)
                                    <button type="button"
                                        class="btn btn-outline-success btn-sm action-btn btn-send"
                                        data-action="{{ route('admin.mails.send', $mail->id) }}"
                                        data-subject="{{ $mail->subject }}"
                                        data-recipients="{{ $pendingCount }}"
                                        data-bs-toggle="tooltip" title="Gửi mail">
                                        <i class="fa-solid fa-paper-plane"></i>
                                    </button>
                                    @endif
                                    <button type="button"
                                        class="btn btn-outline-danger btn-sm action-btn btn-delete"
                                        data-action="{{ route('admin.mails.destroy', $mail->id) }}"
                                        data-subject="{{ $mail->subject }}"
                                        data-bs-toggle="tooltip" title="Xóa">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fa-solid fa-inbox fs-1 d-block mb-3 opacity-50"></i>
                                    <h5>Không có mail nào</h5>
                                    <p class="mb-0">Thử thay đổi bộ lọc hoặc tạo mail mới</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($mails->hasPages())
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Hiển thị {{ $mails->firstItem() }} - {{ $mails->lastItem() }} trong {{ $mails->total() }} mail
                </div>
                <div>{{ $mails->links('components.pagination') }}</div>
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
    // Tooltips
    const tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltips.map(el => new bootstrap.Tooltip(el));

    // Send confirmation
    document.querySelectorAll('.btn-send').forEach(btn => {
        btn.addEventListener('click', function() {
            const sendUrl = this.dataset.action;
            const subject = this.dataset.subject;
            const recipients = this.dataset.recipients;

            Swal.fire({
                title: 'Xác nhận gửi mail?',
                html: `Gửi mail "<strong>${subject}</strong>" đến <strong>${recipients}</strong> người nhận?`,
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

    // Delete confirmation
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            const deleteUrl = this.dataset.action;
            const subject = this.dataset.subject;

            Swal.fire({
                title: 'Xác nhận xóa?',
                html: `Bạn có chắc muốn xóa mail "<strong>${subject}</strong>"?<br><small class="text-muted">Mail sẽ được chuyển vào thùng rác</small>`,
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
