@extends('layouts.admin')

@section('title', 'Quản lý địa chỉ người dùng')

@section('content')
    <div class="container-fluid py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">
                    <i class="fas fa-map-marked-alt me-2 text-primary"></i>Địa chỉ người dùng
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb small text-muted mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Địa chỉ người dùng</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('admin.user-addresses.trashed') }}" class="btn btn-outline-danger me-2">
                    <i class="fas fa-trash-alt me-1"></i>Thùng rác
                </a>
                <a href="{{ route('admin.user-addresses.create') }}" class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus me-2"></i>Thêm địa chỉ
                </a>
            </div>
        </div>

        {{-- Thống kê --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card info-card bg-gradient-primary text-white shadow-sm border-0">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Tổng địa chỉ</h6>
                            <h3 class="fw-bold mb-0">{{ number_format($stats['total']) }}</h3>
                        </div>
                        <i class="fas fa-map-marker-alt fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card info-card bg-gradient-success text-white shadow-sm border-0">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Địa chỉ mặc định</h6>
                            <h3 class="fw-bold mb-0">{{ number_format($stats['default']) }}</h3>
                        </div>
                        <i class="fas fa-star fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card info-card bg-gradient-info text-white shadow-sm border-0">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Người dùng có địa chỉ</h6>
                            <h3 class="fw-bold mb-0">{{ number_format($stats['active_users']) }}</h3>
                        </div>
                        <i class="fas fa-users fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bộ lọc --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tìm kiếm</label>
                        <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control"
                            placeholder="Tên, SĐT, địa chỉ...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Người dùng</label>
                        <select name="user_id" class="form-select">
                            <option value="">-- Tất cả --</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->username }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Tỉnh/Thành phố</label>
                        <select name="province" class="form-select">
                            <option value="">-- Tất cả --</option>
                            @foreach ($provinces as $province)
                                <option value="{{ $province }}"
                                    {{ request('province') == $province ? 'selected' : '' }}>
                                    {{ $province }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Mặc định</label>
                        <select name="is_default" class="form-select">
                            <option value="">-- Tất cả --</option>
                            <option value="1" {{ request('is_default') == '1' ? 'selected' : '' }}>Có</option>
                            <option value="0" {{ request('is_default') == '0' ? 'selected' : '' }}>Không</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">
                            <i class="fa-solid fa-filter me-2"></i>Lọc
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Bảng danh sách --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <input type="checkbox" id="selectAll" class="form-check-input me-2">
                    <label for="selectAll" class="fw-semibold">Chọn tất cả</label>
                    <span class="text-muted ms-2" id="selectedCount">(0 mục được chọn)</span>
                </div>
                <button type="button" class="btn btn-sm btn-danger" id="bulkDeleteBtn" style="display:none;">
                    <i class="fas fa-trash me-1"></i>Xóa đã chọn
                </button>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0 table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="30"></th>
                            <th>Người dùng</th>
                            <th>Người nhận</th>
                            <th>Số điện thoại</th>
                            <th>Địa chỉ</th>
                            <th>Tỉnh/TP</th>
                            <th>Mặc định</th>
                            <th width="150" class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($addresses as $address)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input address-checkbox"
                                        value="{{ $address->id }}">
                                </td>
                                <td>
                                    <a href="{{ route('admin.users.show', $address->user_id) }}"
                                        class="text-decoration-none">
                                        <strong>{{ $address->user->username }}</strong>
                                    </a>
                                    <small class="d-block text-muted">{{ $address->user->email }}</small>
                                </td>
                                <td>
                                    <strong>{{ $address->receiver_name }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-phone me-1"></i>{{ $address->phone }}
                                    </span>
                                </td>
                                <td>
                                    <div class="small">
                                        <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                        {{ Str::limit($address->address, 50) }}
                                    </div>
                                    @if ($address->ward || $address->district)
                                        <small class="text-muted">
                                            {{ implode(', ', array_filter([$address->ward, $address->district])) }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $address->province }}</span>
                                </td>
                                <td>
                                    @if ($address->is_default)
                                        <span class="badge bg-success">
                                            <i class="fas fa-star me-1"></i>Mặc định
                                        </span>
                                    @else
                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-set-default"
                                            data-id="{{ $address->id }}">
                                            <i class="far fa-star"></i>
                                        </button>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.user-addresses.show', $address->id) }}"
                                            class="btn btn-sm btn-outline-info" title="Xem">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.user-addresses.edit', $address->id) }}"
                                            class="btn btn-sm btn-outline-warning" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="confirmDelete({{ $address->id }})" title="Xóa">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                    <form action="{{ route('admin.user-addresses.destroy', $address->id) }}"
                                        method="POST" class="d-none" id="deleteForm{{ $address->id }}">
                                        @csrf @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    <p class="mb-0">Không có địa chỉ nào</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Hiển thị {{ $addresses->firstItem() ?? 0 }} - {{ $addresses->lastItem() ?? 0 }}
                    trong tổng số {{ $addresses->total() }} địa chỉ
                </div>
                {{ $addresses->links('components.pagination') }}
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const csrf = '{{ csrf_token() }}';

                // Checkbox handling
                const selectAll = document.getElementById('selectAll');
                const checkboxes = document.querySelectorAll('.address-checkbox');
                const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
                const selectedCount = document.getElementById('selectedCount');

                function updateSelectedCount() {
                    const checked = document.querySelectorAll('.address-checkbox:checked').length;
                    selectedCount.textContent = `(${checked} mục được chọn)`;
                    bulkDeleteBtn.style.display = checked > 0 ? 'inline-block' : 'none';

                    selectAll.checked = checked === checkboxes.length && checked > 0;
                    selectAll.indeterminate = checked > 0 && checked < checkboxes.length;
                }

                selectAll?.addEventListener('change', function() {
                    checkboxes.forEach(cb => cb.checked = this.checked);
                    updateSelectedCount();
                });

                checkboxes.forEach(cb => cb.addEventListener('change', updateSelectedCount));

                // Bulk delete
                bulkDeleteBtn?.addEventListener('click', function() {
                    const ids = Array.from(document.querySelectorAll('.address-checkbox:checked')).map(cb => cb
                        .value);

                    Swal.fire({
                        title: 'Xác nhận xóa?',
                        html: `Bạn có chắc muốn xóa <strong>${ids.length}</strong> địa chỉ đã chọn?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: '<i class="fas fa-trash me-2"></i>Xóa',
                        cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch('{{ route('admin.user-addresses.bulk-delete') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': csrf
                                    },
                                    body: JSON.stringify({
                                        ids
                                    })
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire('Thành công!', data.message, 'success')
                                            .then(() => location.reload());
                                    }
                                });
                        }
                    });
                });

                // Set default
                document.querySelectorAll('.btn-set-default').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.dataset.id;

                        fetch(`/admin/user-addresses/${id}/set-default`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrf
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: data.message,
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => location.reload());
                                }
                            });
                    });
                });
            });

            function confirmDelete(id) {
                Swal.fire({
                    title: 'Xác nhận xóa?',
                    text: 'Địa chỉ này sẽ được đưa vào thùng rác!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="fas fa-trash me-2"></i>Xóa',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('deleteForm' + id).submit();
                    }
                });
            }

            @if (session('success'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000
                });
            @endif
        </script>
    @endpush

    @push('styles')
        <style>
            .info-card {
                transition: transform 0.2s;
                border-radius: 12px;
            }

            .info-card:hover {
                transform: translateY(-5px);
            }

            .bg-gradient-primary {
                background: linear-gradient(135deg, #4f46e5 0%, #6d28d9 100%);
            }

            .bg-gradient-success {
                background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);
            }

            .bg-gradient-info {
                background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
            }

            .card {
                border-radius: 12px;
            }

            tbody tr:hover {
                background-color: #f8fafc;
            }
        </style>
    @endpush
@endsection
