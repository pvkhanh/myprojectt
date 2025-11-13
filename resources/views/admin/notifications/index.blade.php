@extends('layouts.admin')

@section('title', 'Quản Lý Thông Báo')

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
    .bg-gradient-info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
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
                        <i class="fa-solid fa-bell text-primary me-2"></i>
                        Quản Lý Thông Báo
                    </h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Thông báo</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.notifications.dashboard') }}" class="btn btn-outline-info btn-lg">
                        <i class="fa-solid fa-chart-line me-2"></i> Dashboard
                    </a>
                    <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary btn-lg">
                        <i class="fa-solid fa-plus me-2"></i> Tạo Thông Báo
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100 bg-gradient-primary text-white stat-card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1 small">Tổng số</h6>
                            <h4 class="fw-bold mb-0">{{ number_format($stats['total']) }}</h4>
                        </div>
                        <div class="fs-2 opacity-50"><i class="fa-solid fa-bell"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100 bg-gradient-info text-white stat-card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1 small">Hôm nay</h6>
                            <h4 class="fw-bold mb-0">{{ number_format($stats['today']) }}</h4>
                        </div>
                        <div class="fs-2 opacity-50"><i class="fa-solid fa-calendar-day"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100 bg-gradient-warning text-white stat-card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1 small">Chưa đọc</h6>
                            <h4 class="fw-bold mb-0">{{ number_format($stats['unread']) }}</h4>
                        </div>
                        <div class="fs-2 opacity-50"><i class="fa-solid fa-envelope"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100 bg-gradient-success text-white stat-card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1 small">Đã đọc</h6>
                            <h4 class="fw-bold mb-0">{{ number_format($stats['read']) }}</h4>
                        </div>
                        <div class="fs-2 opacity-50"><i class="fa-solid fa-envelope-open"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100 bg-gradient-danger text-white stat-card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1 small">Hết hạn</h6>
                            <h4 class="fw-bold mb-0">{{ number_format($stats['expired']) }}</h4>
                        </div>
                        <div class="fs-2 opacity-50"><i class="fa-solid fa-clock"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100 bg-light stat-card">
                <div class="card-body p-3">
                    <h6 class="text-muted mb-2 small">Theo loại</h6>
                    <div class="d-flex flex-column gap-1">
                        @foreach($stats['by_type'] as $type => $count)
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted">{{ ucfirst($type) }}</span>
                            <span class="badge bg-secondary">{{ $count }}</span>
                        </div>
                        @endforeach
                    </div>
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
            <form method="GET" action="{{ route('admin.notifications.index') }}">
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
                            <i class="fa-solid fa-tag text-muted me-1"></i> Loại
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
                            <i class="fa-solid fa-eye text-muted me-1"></i> Trạng thái
                        </label>
                        <select name="is_read" class="form-select form-select-lg">
                            <option value="">Tất cả</option>
                            <option value="0" {{ request('is_read') === '0' ? 'selected' : '' }}>Chưa đọc</option>
                            <option value="1" {{ request('is_read') === '1' ? 'selected' : '' }}>Đã đọc</option>
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
                        <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fa-solid fa-rotate-right"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Notifications Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">
                    <i class="fa-solid fa-list text-primary me-2"></i>Danh sách Thông báo
                    <span class="badge bg-primary fs-6">{{ $notifications->total() }} thông báo</span>
                </h5>
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-success btn-sm" id="btnBulkSend">
                        <i class="fa-solid fa-paper-plane me-1"></i> Gửi đã chọn
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm" id="btnBulkDelete">
                        <i class="fa-solid fa-trash me-1"></i> Xóa đã chọn
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 text-center" style="width:50px;">
                                <input type="checkbox" class="form-check-input" id="checkAll">
                            </th>
                            <th class="px-4 py-3 text-center" style="width:80px;">#</th>
                            <th class="px-4 py-3">Tiêu đề</th>
                            <th class="px-4 py-3">Người nhận</th>
                            <th class="px-4 py-3 text-center">Loại</th>
                            <th class="px-4 py-3 text-center">Trạng thái</th>
                            <th class="px-4 py-3 text-center">Ngày tạo</th>
                            <th class="px-4 py-3 text-center" style="width:200px;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifications as $index => $notification)
                        <tr class="border-bottom">
                            <td class="text-center px-4">
                                <input type="checkbox" class="form-check-input notification-checkbox" value="{{ $notification->id }}">
                            </td>
                            <td class="text-center px-4">
                                <span class="badge bg-light text-dark fs-6">{{ $notifications->firstItem() + $index }}</span>
                            </td>
                            <td class="px-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3"
                                        style="width:45px; height:45px;">
                                        <i class="fa-solid fa-bell text-primary fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ Str::limit($notification->title, 50) }}</div>
                                        <div class="small text-muted">{{ Str::limit($notification->message, 60) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center me-2"
                                        style="width:35px; height:35px;">
                                        <i class="fa-solid fa-user text-info"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark">
                                            {{ trim(($notification->user->first_name ?? '') . ' ' . ($notification->user->last_name ?? '')) ?: 'N/A' }}
                                        </div>
                                        <div class="small text-muted">{{ $notification->user->email ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center px-4">
                                @php
                                    $typeConfig = [
                                        'system' => ['class' => 'primary', 'icon' => 'cog', 'text' => 'Hệ thống'],
                                        'order' => ['class' => 'success', 'icon' => 'shopping-cart', 'text' => 'Đơn hàng'],
                                        'promotion' => ['class' => 'warning', 'icon' => 'tag', 'text' => 'Khuyến mãi'],
                                        'activity' => ['class' => 'info', 'icon' => 'bolt', 'text' => 'Hoạt động']
                                    ];
                                    $config = $typeConfig[$notification->type->value] ?? ['class' => 'secondary', 'icon' => 'question', 'text' => $notification->type->value];
                                @endphp
                                <span class="badge bg-{{ $config['class'] }} fs-6 px-3 py-2">
                                    <i class="fa-solid fa-{{ $config['icon'] }} me-1"></i>{{ $config['text'] }}
                                </span>
                            </td>
                            <td class="text-center px-4">
                                @if($notification->is_read)
                                <span class="badge bg-success fs-6 px-3 py-2">
                                    <i class="fa-solid fa-check-circle me-1"></i>Đã đọc
                                </span>
                                @else
                                <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                                    <i class="fa-solid fa-envelope me-1"></i>Chưa đọc
                                </span>
                                @endif
                            </td>
                            <td class="text-center px-4">
                                <div class="fw-semibold text-dark">{{ $notification->created_at->format('d/m/Y') }}</div>
                                <div class="small text-muted">{{ $notification->created_at->format('H:i') }}</div>
                            </td>
                            <td class="text-center px-4">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.notifications.show', $notification->id) }}"
                                        class="btn btn-outline-info btn-sm action-btn"
                                        data-bs-toggle="tooltip" title="Xem chi tiết">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.notifications.edit', $notification->id) }}"
                                        class="btn btn-outline-warning btn-sm action-btn"
                                        data-bs-toggle="tooltip" title="Chỉnh sửa">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <button type="button"
                                        class="btn btn-outline-danger btn-sm action-btn btn-delete"
                                        data-action="{{ route('admin.notifications.destroy', $notification->id) }}"
                                        data-title="{{ $notification->title }}"
                                        data-bs-toggle="tooltip" title="Xóa">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fa-solid fa-inbox fs-1 d-block mb-3 opacity-50"></i>
                                    <h5>Không có thông báo nào</h5>
                                    <p class="mb-0">Thử thay đổi bộ lọc hoặc tạo thông báo mới</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($notifications->hasPages())
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Hiển thị {{ $notifications->firstItem() }} - {{ $notifications->lastItem() }} trong {{ $notifications->total() }} thông báo
                </div>
                <div>{{ $notifications->links('components.pagination') }}</div>
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

    // Check all
    const checkAll = document.getElementById('checkAll');
    const checkboxes = document.querySelectorAll('.notification-checkbox');
    
    if (checkAll) {
        checkAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    }

    // Bulk send
    document.getElementById('btnBulkSend')?.addEventListener('click', function() {
        const selected = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        if (selected.length === 0) {
            Swal.fire('Thông báo', 'Vui lòng chọn ít nhất 1 thông báo', 'warning');
            return;
        }

        Swal.fire({
            title: 'Xác nhận gửi?',
            html: `Gửi <strong>${selected.length}</strong> thông báo đã chọn?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Gửi',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("admin.notifications.bulk-send") }}';
                form.innerHTML = `
                    @csrf
                    ${selected.map(id => `<input type="hidden" name="notification_ids[]" value="${id}">`).join('')}
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    });

    // Bulk delete
    document.getElementById('btnBulkDelete')?.addEventListener('click', function() {
        const selected = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        if (selected.length === 0) {
            Swal.fire('Thông báo', 'Vui lòng chọn ít nhất 1 thông báo', 'warning');
            return;
        }

        Swal.fire({
            title: 'Xác nhận xóa?',
            html: `Xóa <strong>${selected.length}</strong> thông báo đã chọn?`,
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
                form.action = '{{ route("admin.notifications.bulk-delete") }}';
                form.innerHTML = `
                    @csrf
                    @method('DELETE')
                    ${selected.map(id => `<input type="hidden" name="notification_ids[]" value="${id}">`).join('')}
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    });

    // Delete single
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            const deleteUrl = this.dataset.action;
            const title = this.dataset.title;

            Swal.fire({
                title: 'Xác nhận xóa?',
                html: `Bạn có chắc muốn xóa thông báo "<strong>${title}</strong>"?`,
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