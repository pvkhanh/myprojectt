@extends('layouts.admin')

@section('title', 'Quản lý Banner')

@section('content')
<div class="container-fluid py-4">

    {{-- ====== Header ====== --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="fas fa-images me-2 text-primary"></i>Quản lý Banner</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small text-muted mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Banners</li>
                </ol>
            </nav>
        </div>
        <div>
            <button type="button" class="btn btn-secondary me-2" id="bulkActionsBtn" style="display:none;">
                <i class="fas fa-tasks me-1"></i>Thao tác hàng loạt
            </button>
            <a href="{{ route('admin.banners.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus me-2"></i>Thêm banner
            </a>
        </div>
    </div>

    {{-- ====== Statistics Cards ====== --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card info-card bg-gradient-primary text-white shadow-sm border-0">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Tổng banners</h6>
                        <h3 class="fw-bold mb-0">{{ number_format($totalBanners) }}</h3>
                    </div>
                    <i class="fas fa-images fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card info-card bg-gradient-success text-white shadow-sm border-0">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Đang hoạt động</h6>
                        <h3 class="fw-bold mb-0">{{ number_format($activeBanners) }}</h3>
                    </div>
                    <i class="fas fa-check-circle fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card info-card bg-gradient-warning text-white shadow-sm border-0">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Đã lên lịch</h6>
                        <h3 class="fw-bold mb-0">{{ number_format($scheduledBanners) }}</h3>
                    </div>
                    <i class="fas fa-calendar-alt fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card info-card bg-gradient-secondary text-white shadow-sm border-0">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Đang hiển thị</h6>
                        <h3 class="fw-bold mb-0">{{ number_format($visibleBanners) }}</h3>
                    </div>
                    <i class="fas fa-eye fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- ====== Filters ====== --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tìm kiếm</label>
                    <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control" placeholder="Tiêu đề banner...">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Trạng thái</label>
                    <select name="is_active" class="form-select">
                        <option value="">-- Tất cả --</option>
                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Không hoạt động</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Loại banner</label>
                    <select name="type" class="form-select">
                        <option value="">-- Tất cả --</option>
                        <option value="hero" {{ request('type') == 'hero' ? 'selected' : '' }}>Hero</option>
                        <option value="sidebar" {{ request('type') == 'sidebar' ? 'selected' : '' }}>Sidebar</option>
                        <option value="popup" {{ request('type') == 'popup' ? 'selected' : '' }}>Popup</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100"><i class="fas fa-search"></i> Lọc</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ====== Banners Grid ====== --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <div>
                <input type="checkbox" id="selectAll" class="form-check-input me-2">
                <label for="selectAll" class="fw-semibold">Chọn tất cả</label>
                <span class="text-muted ms-2" id="selectedCount">(0 mục được chọn)</span>
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary" id="sortableToggle">
                <i class="fas fa-arrows-alt me-1"></i>Sắp xếp thứ tự
            </button>
        </div>
        <div class="card-body">
            <div class="row g-4" id="bannersGrid">
                @forelse($banners as $banner)
                    <div class="col-md-6 col-lg-4 banner-item" data-id="{{ $banner->id }}">
                        <div class="card h-100 shadow-sm border-0 position-relative banner-card">
                            {{-- Checkbox --}}
                            <div class="position-absolute top-0 start-0 p-3" style="z-index: 10;">
                                <input type="checkbox" class="form-check-input banner-checkbox" value="{{ $banner->id }}">
                            </div>
                            
                            {{-- Drag Handle --}}
                            <div class="position-absolute top-0 end-0 p-3 drag-handle" style="cursor: move; display: none; z-index: 10;">
                                <i class="fas fa-grip-vertical text-white bg-dark bg-opacity-50 p-2 rounded"></i>
                            </div>

                            {{-- Banner Image --}}
                            <div class="banner-image-wrapper position-relative" style="height: 200px; overflow: hidden;">
                                <img src="{{ asset('storage/' . $banner->image_path) }}" class="w-100 h-100 object-fit-cover" alt="{{ $banner->title }}">
                                
                                {{-- Status Badge --}}
                                <div class="position-absolute top-0 end-0 m-3">
                                    @if($banner->is_active)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Hoạt động
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-pause-circle me-1"></i>Tạm dừng
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="card-body">
                                <h5 class="card-title fw-bold text-truncate">{{ $banner->title }}</h5>
                                
                                @if($banner->url)
                                    <p class="card-text small text-muted mb-2">
                                        <i class="fas fa-link me-1"></i>
                                        <a href="{{ $banner->url }}" target="_blank" class="text-decoration-none">
                                            {{ Str::limit($banner->url, 40) }}
                                        </a>
                                    </p>
                                @endif

                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-sort-numeric-down me-1"></i>Vị trí: {{ $banner->position ?? 0 }}
                                    </span>
                                    @if($banner->type)
                                        <span class="badge bg-info">{{ ucfirst($banner->type) }}</span>
                                    @endif
                                </div>

                                @if($banner->start_at || $banner->end_at)
                                    <div class="small text-muted mb-2">
                                        <i class="fas fa-calendar me-1"></i>
                                        @if($banner->start_at)
                                            Từ: {{ $banner->start_at->format('d/m/Y') }}
                                        @endif
                                        @if($banner->end_at)
                                            - Đến: {{ $banner->end_at->format('d/m/Y') }}
                                        @endif
                                    </div>
                                @endif

                                <div class="d-flex gap-2 mt-3">
                                    <button class="btn btn-sm btn-outline-{{ $banner->is_active ? 'warning' : 'success' }} flex-fill toggle-status" data-id="{{ $banner->id }}">
                                        <i class="fas fa-{{ $banner->is_active ? 'pause' : 'play' }} me-1"></i>
                                        {{ $banner->is_active ? 'Tạm dừng' : 'Kích hoạt' }}
                                    </button>
                                    <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete({{ $banner->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" class="d-none" id="deleteForm{{ $banner->id }}">
                            @csrf @method('DELETE')
                        </form>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-images fa-3x mb-3 d-block"></i>
                            <p class="mb-0">Chưa có banner nào</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Hiển thị {{ $banners->firstItem() ?? 0 }} - {{ $banners->lastItem() ?? 0 }} trong tổng số {{ $banners->total() }} banners
            </div>
            {{ $banners->links('components.pagination') }}
        </div>
    </div>

</div>

{{-- Bulk Actions Modal --}}
<div class="modal fade" id="bulkActionsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thao tác hàng loạt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="bulkActionForm">
                    @csrf
                    <input type="hidden" name="ids" id="bulkIds">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Chọn hành động</label>
                        <select class="form-select" id="bulkAction" required>
                            <option value="">-- Chọn --</option>
                            <option value="activate">Kích hoạt</option>
                            <option value="deactivate">Tạm dừng</option>
                            <option value="delete">Xóa banners</option>
                        </select>
                    </div>
                    <div class="alert alert-warning d-none" id="deleteWarning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Bạn có chắc muốn xóa <strong><span id="deleteCount"></span></strong> banners đã chọn?
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="executeBulkAction">Thực hiện</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    // Checkbox logic
    const selectAllCheckbox = document.getElementById('selectAll');
    const bannerCheckboxes = document.querySelectorAll('.banner-checkbox');
    const selectedCountSpan = document.getElementById('selectedCount');
    const bulkActionsBtn = document.getElementById('bulkActionsBtn');

    function updateSelectedCount() {
        const checked = document.querySelectorAll('.banner-checkbox:checked').length;
        const total = bannerCheckboxes.length;
        selectedCountSpan.textContent = `(${checked}/${total} mục được chọn)`;
        bulkActionsBtn.style.display = checked > 0 ? 'inline-block' : 'none';

        if (checked === total && total > 0) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else if (checked > 0) {
            selectAllCheckbox.indeterminate = true;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        }
    }

    selectAllCheckbox.addEventListener('change', function() {
        bannerCheckboxes.forEach(cb => {
            cb.checked = this.checked;
            cb.closest('.banner-card').classList.toggle('border-primary', this.checked);
        });
        updateSelectedCount();
    });

    bannerCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            updateSelectedCount();
            this.closest('.banner-card').classList.toggle('border-primary', this.checked);
        });
    });

    // Sortable
    let sortableInstance = null;
    document.getElementById('sortableToggle').addEventListener('click', function() {
        const dragHandles = document.querySelectorAll('.drag-handle');
        
        if (sortableInstance) {
            sortableInstance.destroy();
            sortableInstance = null;
            this.classList.remove('active');
            this.innerHTML = '<i class="fas fa-arrows-alt me-1"></i>Sắp xếp thứ tự';
            dragHandles.forEach(h => h.style.display = 'none');
        } else {
            dragHandles.forEach(h => h.style.display = 'block');
            this.classList.add('active', 'btn-primary');
            this.innerHTML = '<i class="fas fa-check me-1"></i>Lưu thứ tự';
            
            sortableInstance = Sortable.create(document.getElementById('bannersGrid'), {
                animation: 150,
                handle: '.drag-handle',
                onEnd: function() {
                    savePositions();
                }
            });
        }
    });

    function savePositions() {
        const items = document.querySelectorAll('.banner-item');
        const positions = {};
        items.forEach((item, index) => {
            positions[item.dataset.id] = index + 1;
        });

        fetch('{{ route("admin.banners.update-positions") }}', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            body: JSON.stringify({positions})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({toast: true, position: 'top-end', icon: 'success', title: 'Vị trí đã được cập nhật!', showConfirmButton: false, timer: 2000});
            }
        });
    }

    // Toggle status
    document.querySelectorAll('.toggle-status').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            
            fetch(`/admin/banners/${id}/toggle-status`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'}
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({toast: true, position: 'top-end', icon: 'success', title: 'Trạng thái đã được cập nhật!', showConfirmButton: false, timer: 2000})
                    .then(() => location.reload());
                }
            });
        });
    });

    // Bulk actions
    bulkActionsBtn.addEventListener('click', function() {
        const checkedIds = Array.from(document.querySelectorAll('.banner-checkbox:checked')).map(cb => cb.value);
        document.getElementById('bulkIds').value = checkedIds.join(',');
        document.getElementById('deleteCount').textContent = checkedIds.length;
        const modal = new bootstrap.Modal(document.getElementById('bulkActionsModal'));
        modal.show();
    });

    document.getElementById('bulkAction').addEventListener('change', function() {
        const deleteWarning = document.getElementById('deleteWarning');
        deleteWarning.classList.toggle('d-none', this.value !== 'delete');
    });

    document.getElementById('executeBulkAction').addEventListener('click', function() {
        const action = document.getElementById('bulkAction').value;
        const ids = document.getElementById('bulkIds').value.split(',');
        if (!action) return Swal.fire({icon: 'error', title: 'Lỗi', text: 'Vui lòng chọn hành động'});

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';

        const url = '{{ route("admin.banners.bulk-delete") }}';
        const data = {ids, action, _token: '{{ csrf_token() }}'};

        fetch(url, {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({icon: 'success', title: 'Thành công!', text: data.message, timer: 2000}).then(() => location.reload());
            }
        });
    });

    function confirmDelete(id) {
        Swal.fire({
            title: "Xác nhận xóa?",
            text: "Banner này sẽ bị xóa vĩnh viễn!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#ef4444",
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
        }).then(result => {
            if (result.isConfirmed) document.getElementById('deleteForm' + id).submit();
        });
    }

    @if(session('success'))
        Swal.fire({toast: true, position: 'top-end', icon: 'success', title: '{{ session("success") }}', showConfirmButton: false, timer: 3000});
    @endif

    document.addEventListener('DOMContentLoaded', updateSelectedCount);
</script>
@endpush

@push('styles')
<style>
    body, table { font-family: 'Inter', 'Roboto', sans-serif; }
    .info-card { transition: all 0.3s ease; border-radius: 12px; }
    .info-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); }
    .bg-gradient-primary { background: linear-gradient(135deg, #4f46e5 0%, #6d28d9 100%); }
    .bg-gradient-success { background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%); }
    .bg-gradient-warning { background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%); }
    .bg-gradient-secondary { background: linear-gradient(135deg, #64748b 0%, #94a3b8 100%); }
    .banner-card { transition: all 0.3s ease; border: 2px solid transparent; }
    .banner-card:hover { box-shadow: 0 8px 20px rgba(0,0,0,0.12); transform: translateY(-3px); }
    .banner-card.border-primary { border-color: #4f46e5 !important; }
    .banner-image-wrapper img { transition: transform 0.3s ease; }
    .banner-card:hover .banner-image-wrapper img { transform: scale(1.05); }
    .card { border: none; border-radius: 12px; }
</style>
@endpush

@endsection