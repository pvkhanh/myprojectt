@extends('layouts.admin')

@section('title', 'Quản lý đánh giá sản phẩm')

@section('content')
    <div class="container-fluid py-4">

        {{-- ====== Header với Quick Actions ====== --}}
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <div class="icon-box me-3">
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-1">Quản lý đánh giá</h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb small mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Đánh giá sản phẩm</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-end">
                <div class="btn-toolbar justify-content-end gap-2">
                    <button type="button" class="btn btn-action btn-bulk" id="bulkActionsBtn" style="display:none;">
                        <i class="fas fa-tasks"></i>
                        <span>Thao tác hàng loạt</span>
                    </button>
                    <a href="{{ route('admin.reviews.trash') }}" class="btn btn-action btn-trash">
                        <i class="fas fa-trash-restore"></i>
                        <span>Thùng rác</span>
                        @if (isset($trashedCount) && $trashedCount > 0)
                            <span class="badge-count">{{ $trashedCount }}</span>
                        @endif
                    </a>
                    <button type="button" class="btn btn-action btn-refresh" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- ====== Thẻ thống kê Dashboard Style ====== --}}
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="stats-card card-total">
                    <div class="stats-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">Tổng đánh giá</div>
                        <div class="stats-value">{{ number_format($stats['total']) }}</div>
                        <div class="stats-change positive">
                            <i class="fas fa-arrow-up"></i> 100%
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stats-card card-pending">
                    <div class="stats-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">Chờ duyệt</div>
                        <div class="stats-value">{{ number_format($stats['pending']) }}</div>
                        <div class="stats-change warning">
                            <i class="fas fa-hourglass-half"></i> Cần xử lý
                        </div>
                    </div>
                    @if ($stats['pending'] > 0)
                        <div class="stats-badge pulse">{{ $stats['pending'] }}</div>
                    @endif
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stats-card card-approved">
                    <div class="stats-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">Đã duyệt</div>
                        <div class="stats-value">{{ number_format($stats['approved']) }}</div>
                        <div class="stats-change positive">
                            <i class="fas fa-check"></i>
                            {{ $stats['total'] > 0 ? round(($stats['approved'] / $stats['total']) * 100, 1) : 0 }}%
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stats-card card-rating">
                    <div class="stats-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">Điểm trung bình</div>
                        <div class="stats-value">
                            {{ $stats['avg_rating'] }}
                            <span class="stats-subvalue">/5</span>
                        </div>
                        <div class="stats-stars">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $stats['avg_rating'] ? 'active' : '' }}"></i>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====== Bộ lọc nâng cao với Tabs ====== --}}
        <div class="card filter-card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom-0">
                <ul class="nav nav-pills card-header-pills" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{ !request('status') ? 'active' : '' }}"
                            href="{{ route('admin.reviews.index') }}">
                            <i class="fas fa-list me-2"></i>Tất cả
                            <span class="badge">{{ $stats['total'] }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('status') == 'pending' ? 'active' : '' }}"
                            href="{{ route('admin.reviews.index', ['status' => 'pending']) }}">
                            <i class="fas fa-clock me-2"></i>Chờ duyệt
                            <span class="badge">{{ $stats['pending'] }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('status') == 'approved' ? 'active' : '' }}"
                            href="{{ route('admin.reviews.index', ['status' => 'approved']) }}">
                            <i class="fas fa-check-circle me-2"></i>Đã duyệt
                            <span class="badge">{{ $stats['approved'] }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('status') == 'rejected' ? 'active' : '' }}"
                            href="{{ route('admin.reviews.index', ['status' => 'rejected']) }}">
                            <i class="fas fa-times-circle me-2"></i>Từ chối
                            <span class="badge">{{ $stats['rejected'] }}</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-lg-3 col-md-4">
                        <div class="input-group input-group-custom">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control"
                                placeholder="Tìm kiếm đánh giá...">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3">
                        <select name="rating" class="form-select form-select-custom">
                            <option value="">Tất cả đánh giá</option>
                            @for ($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                                    {{ $i }} ⭐
                                    {{ $i == 5 ? 'Xuất sắc' : ($i == 4 ? 'Tốt' : ($i == 3 ? 'TB' : ($i == 2 ? 'Tệ' : 'Rất tệ'))) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-3">
                        <input type="date" name="date_from" class="form-control form-control-custom"
                            value="{{ request('date_from') }}" placeholder="Từ ngày">
                    </div>
                    <div class="col-lg-2 col-md-3">
                        <input type="date" name="date_to" class="form-control form-control-custom"
                            value="{{ request('date_to') }}" placeholder="Đến ngày">
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <select name="sort_by" class="form-select form-select-custom">
                            <option value="latest" {{ request('sort_by') == 'latest' ? 'selected' : '' }}>Mới nhất
                            </option>
                            <option value="oldest" {{ request('sort_by') == 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                            <option value="rating_high" {{ request('sort_by') == 'rating_high' ? 'selected' : '' }}>⭐ Cao
                                nhất</option>
                            <option value="rating_low" {{ request('sort_by') == 'rating_low' ? 'selected' : '' }}>⭐ Thấp
                                nhất</option>
                        </select>
                    </div>
                    <div class="col-lg-1 col-md-2">
                        <button class="btn btn-primary w-100 btn-filter">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ====== Bảng danh sách Modern ====== --}}
        <div class="card modern-table border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="form-check me-3">
                        <input type="checkbox" id="selectAll" class="form-check-input">
                        <label for="selectAll" class="form-check-label fw-semibold">Chọn tất cả</label>
                    </div>
                    <span class="text-muted" id="selectedCount">(0 đánh giá được chọn)</span>
                </div>
                <div class="table-info">
                    Hiển thị {{ $reviews->firstItem() ?? 0 }}-{{ $reviews->lastItem() ?? 0 }}
                    trong {{ $reviews->total() }} kết quả
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="40"></th>
                            <th width="260">Người đánh giá</th>
                            <th width="280">Sản phẩm</th>
                            <th width="140" class="text-center">Đánh giá</th>
                            <th>Nội dung</th>
                            <th width="120">Trạng thái</th>
                            <th width="130">Thời gian</th>
                            <th width="200" class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                            <tr class="review-row">
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input review-checkbox"
                                            value="{{ $review->id }}">
                                    </div>
                                </td>
                                <td>
                                    @if ($review->user)
                                        <div class="user-info d-flex align-items-center">
                                            <div class="user-avatar position-relative me-3">
                                                <img src="{{ $review->user->avatar_url ?? asset('images/default-avatar.png') }}"
                                                    alt="{{ $review->user->username }}" class="rounded-circle border"
                                                    width="45" height="45">
                                                @if ($review->user->orders && $review->user->orders->count() > 0)
                                                    <span
                                                        class="verified-badge position-absolute bottom-0 end-0 bg-success text-white rounded-circle p-1"
                                                        data-bs-toggle="tooltip"
                                                        title="Đã mua {{ $review->user->orders->count() }} đơn">
                                                        <i class="fas fa-check" style="font-size: 10px;"></i>
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="user-details">
                                                <div class="user-name fw-semibold">
                                                    {{ $review->user->username ?? 'Ẩn danh' }}</div>
                                                <div class="user-email text-muted small">
                                                    {{ $review->user->email ?? 'Không có email' }}</div>
                                                <div class="user-stats text-muted small mt-1">
                                                    <i class="fas fa-comment-dots"></i>
                                                    {{ $review->user->reviews->count() ?? 0 }} đánh giá
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-muted fst-italic">
                                            <i class="fas fa-user-slash me-1"></i> Người dùng không tồn tại
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    <div class="product-info">
                                        <div class="product-image">
                                            <img src="{{ $review->product->main_image_url }}"
                                                alt="{{ $review->product->name }}">
                                        </div>
                                        <div class="product-details">
                                            <a href="{{ route('admin.products.show', $review->product) }}"
                                                class="product-name">
                                                {{ Str::limit($review->product->name, 50) }}
                                            </a>
                                            <div class="product-meta">
                                                <span class="product-price">
                                                    {{ number_format($review->product->price, 0, ',', '.') }}đ
                                                </span>
                                                <span class="product-id">#{{ $review->product->id }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="rating-container">
                                        <div class="rating-stars-lg">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $review->rating ? 'filled' : '' }}"></i>
                                            @endfor
                                        </div>
                                        <div class="rating-score">{{ $review->rating }}/5</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="review-content">
                                        <p class="review-text">{{ Str::limit($review->comment, 100) }}</p>
                                        @if (strlen($review->comment) > 100)
                                            <button class="btn-read-more" onclick="showFullReview({{ $review->id }})">
                                                Xem thêm <i class="fas fa-chevron-down"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $statusConfig = match ($review->status->value) {
                                            'approved' => [
                                                'class' => 'success',
                                                'icon' => 'check-circle',
                                                'text' => 'Đã duyệt',
                                            ],
                                            'pending' => [
                                                'class' => 'warning',
                                                'icon' => 'clock',
                                                'text' => 'Chờ duyệt',
                                            ],
                                            'rejected' => [
                                                'class' => 'danger',
                                                'icon' => 'times-circle',
                                                'text' => 'Từ chối',
                                            ],
                                            default => [
                                                'class' => 'secondary',
                                                'icon' => 'question',
                                                'text' => 'Khác',
                                            ],
                                        };
                                    @endphp
                                    <span class="status-badge status-{{ $statusConfig['class'] }}">
                                        <i class="fas fa-{{ $statusConfig['icon'] }}"></i>
                                        {{ $statusConfig['text'] }}
                                    </span>
                                </td>
                                <td>
                                    <div class="time-info">
                                        <div class="time-date">
                                            <i class="far fa-calendar"></i>
                                            {{ $review->created_at->format('d/m/Y') }}
                                        </div>
                                        <div class="time-ago">{{ $review->created_at->diffForHumans() }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.reviews.show', $review) }}"
                                            class="btn-action btn-action-view" data-bs-toggle="tooltip"
                                            title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <a href="{{ route('admin.reviews.edit', $review) }}"
                                            class="btn-action btn-action-edit" data-bs-toggle="tooltip"
                                            title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        @if ($review->status->value !== 'approved')
                                            <form action="{{ route('admin.reviews.approve', $review) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn-action btn-action-approve"
                                                    data-bs-toggle="tooltip" title="Phê duyệt">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @if ($review->status->value !== 'rejected')
                                            <form action="{{ route('admin.reviews.reject', $review) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn-action btn-action-reject"
                                                    data-bs-toggle="tooltip" title="Từ chối">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <button type="button" class="btn-action btn-action-delete"
                                            onclick="confirmDelete({{ $review->id }})" data-bs-toggle="tooltip"
                                            title="Xóa">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                    <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST"
                                        class="d-none" id="deleteForm{{ $review->id }}">
                                        @csrf @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <div class="empty-icon">
                                            <i class="fas fa-comment-slash"></i>
                                        </div>
                                        <div class="empty-title">Không có đánh giá nào</div>
                                        <div class="empty-description">
                                            Chưa có đánh giá nào được tìm thấy với bộ lọc hiện tại
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="pagination-info">
                        Hiển thị <strong>{{ $reviews->firstItem() ?? 0 }}</strong> đến
                        <strong>{{ $reviews->lastItem() ?? 0 }}</strong> trong tổng số
                        <strong>{{ $reviews->total() }}</strong> đánh giá
                    </div>
                    {{ $reviews->links('components.pagination') }}
                </div>
            </div>
        </div>

    </div>

    {{-- Modal thao tác hàng loạt --}}
    <div class="modal fade" id="bulkActionsModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modern-modal">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-tasks me-2"></i>
                        Thao tác hàng loạt
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="bulkActionForm">
                        @csrf
                        <input type="hidden" name="ids" id="bulkIds">

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Chọn hành động</label>
                            <select class="form-select form-select-lg" id="bulkAction" required>
                                <option value="">-- Chọn hành động --</option>
                                <option value="approve">✅ Phê duyệt tất cả</option>
                                <option value="reject">❌ Từ chối tất cả</option>
                                <option value="delete">🗑️ Xóa tất cả</option>
                            </select>
                        </div>

                        <div class="alert alert-info d-none" id="actionInfo">
                            <i class="fas fa-info-circle me-2"></i>
                            <span id="actionText"></span>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Hủy
                    </button>
                    <button type="button" class="btn btn-primary" id="executeBulkAction">
                        <i class="fas fa-check me-2"></i>Thực hiện
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Xử lý checkbox
            const selectAllCheckbox = document.getElementById('selectAll');
            const reviewCheckboxes = document.querySelectorAll('.review-checkbox');
            const selectedCountSpan = document.getElementById('selectedCount');
            const bulkActionsBtn = document.getElementById('bulkActionsBtn');

            function updateSelectedCount() {
                const checked = document.querySelectorAll('.review-checkbox:checked').length;
                const total = reviewCheckboxes.length;

                selectedCountSpan.textContent = `(${checked} đánh giá được chọn)`;
                bulkActionsBtn.style.display = checked > 0 ? 'inline-flex' : 'none';

                if (checked === total && total > 0) {
                    selectAllCheckbox.checked = true;
                    selectAllCheckbox.indeterminate = false;
                } else if (checked > 0) {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = true;
                } else {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = false;
                }
            }

            selectAllCheckbox.addEventListener('change', function() {
                reviewCheckboxes.forEach(cb => {
                    cb.checked = this.checked;
                    cb.closest('.review-row').classList.toggle('selected', this.checked);
                });
                updateSelectedCount();
            });

            reviewCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    updateSelectedCount();
                    this.closest('.review-row').classList.toggle('selected', this.checked);
                });
            });

            // Mở modal bulk actions
            bulkActionsBtn.addEventListener('click', function() {
                const checkedIds = Array.from(document.querySelectorAll('.review-checkbox:checked'))
                    .map(cb => cb.value);

                document.getElementById('bulkIds').value = checkedIds.join(',');
                const modal = new bootstrap.Modal(document.getElementById('bulkActionsModal'));
                modal.show();
            });

            // Xử lý thay đổi action
            document.getElementById('bulkAction').addEventListener('change', function() {
                const actionInfo = document.getElementById('actionInfo');
                const actionText = document.getElementById('actionText');
                const count = document.getElementById('bulkIds').value.split(',').length;

                if (this.value) {
                    actionInfo.classList.remove('d-none');
                    const actions = {
                        'approve': `Bạn sắp phê duyệt <strong>${count} đánh giá</strong>`,
                        'reject': `Bạn sắp từ chối <strong>${count} đánh giá</strong>`,
                        'delete': `Bạn sắp xóa <strong>${count} đánh giá</strong> vào thùng rác`
                    };
                    actionText.innerHTML = actions[this.value];
                } else {
                    actionInfo.classList.add('d-none');
                }
            });

            // Thực hiện bulk action
            document.getElementById('executeBulkAction').addEventListener('click', function() {
                const action = document.getElementById('bulkAction').value;
                const ids = document.getElementById('bulkIds').value.split(',');

                if (!action) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Vui lòng chọn hành động',
                        confirmButtonColor: '#4f46e5'
                    });
                    return;
                }

                const btn = this;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';

                fetch('{{ route('admin.reviews.bulk-action') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            ids,
                            action
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: data.message,
                                confirmButtonColor: '#4f46e5',
                                timer: 2000
                            }).then(() => location.reload());
                        } else {
                            throw new Error(data.message);
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: error.message || 'Có lỗi xảy ra khi xử lý',
                            confirmButtonColor: '#4f46e5'
                        });
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-check me-2"></i>Thực hiện';
                    });
            });

            // Xác nhận xóa
            function confirmDelete(id) {
                Swal.fire({
                    title: "Xác nhận xóa?",
                    html: `
            <div class="text-center py-3">
                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                <p class="mb-0">Đánh giá sẽ được chuyển vào thùng rác</p>
            </div>
        `,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#ef4444",
                    cancelButtonColor: "#64748b",
                    confirmButtonText: '<i class="fas fa-trash me-2"></i>Xóa',
                    cancelButtonText: '<i class="fas fa-times me-2"></i>Hủy',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('deleteForm' + id).submit();
                    }
                });
            }

            // Show full review
            function showFullReview(id) {
                // Implement if needed
            }

            // Toast thông báo
            @if (session('success'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: '{{ session('error') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            // Tooltip
            document.addEventListener('DOMContentLoaded', function() {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });

                updateSelectedCount();
            });
        </script>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const checkAll = document.getElementById("checkAll");
                const checkboxes = document.querySelectorAll(".review-checkbox");
                const bulkActions = document.querySelector(".bulk-actions");
                const reviewRows = document.querySelectorAll(".review-row");

                // Chọn tất cả
                checkAll.addEventListener("change", () => {
                    checkboxes.forEach(cb => {
                        cb.checked = checkAll.checked;
                        cb.closest("tr").classList.toggle("selected", cb.checked);
                    });
                    toggleBulkActions();
                });

                // Chọn từng dòng
                checkboxes.forEach(cb => {
                    cb.addEventListener("change", () => {
                        cb.closest("tr").classList.toggle("selected", cb.checked);
                        toggleBulkActions();
                    });
                });

                // Ẩn/hiện bulk actions
                function toggleBulkActions() {
                    const checkedCount = document.querySelectorAll(".review-checkbox:checked").length;
                    bulkActions.classList.toggle("active", checkedCount > 0);
                }

                // Reload trang
                window.reloadPage = function() {
                    location.reload();
                }
            });

            // Xác nhận xóa
            function confirmDelete(id) {
                Swal.fire({
                    title: 'Xóa đánh giá?',
                    text: 'Bạn có chắc chắn muốn xóa đánh giá này không?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '<i class="fas fa-trash-alt me-2"></i>Xóa',
                    cancelButtonText: 'Hủy'
                }).then(result => {
                    if (result.isConfirmed) {
                        document.getElementById(`deleteForm${id}`).submit();
                    }
                });
            }

            // Xem toàn bộ nội dung đánh giá
            function showFullReview(id) {
                const btn = document.querySelector(`button[onclick="showFullReview(${id})"]`);
                const textElement = btn.closest('tr').querySelector('.review-text');
                const fullText = textElement.getAttribute('data-full') || textElement.textContent;

                Swal.fire({
                    title: 'Nội dung đánh giá',
                    html: `<div class="text-start p-3 border rounded bg-light">${fullText}</div>`,
                    width: 600,
                    confirmButtonText: 'Đóng',
                    confirmButtonColor: '#4f46e5',
                });
            }
        </script>
    @endpush
    @push('styles')
        <style>
            /* ===== Review Management Page Styles ===== */
            .icon-box {
                background: #eef2ff;
                color: #4f46e5;
                width: 48px;
                height: 48px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 22px;
            }

            .btn-action {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                border-radius: 8px;
                padding: 8px 14px;
                font-weight: 500;
                transition: all 0.2s ease;
            }

            .btn-action i {
                font-size: 14px;
            }

            .btn-action:hover {
                transform: translateY(-1px);
            }

            .btn-bulk {
                background-color: #f9fafb;
                color: #111827;
                border: 1px solid #e5e7eb;
            }

            .btn-bulk:hover {
                background-color: #e5e7eb;
            }

            .btn-trash {
                background-color: #fef2f2;
                color: #dc2626;
                border: 1px solid #fecaca;
                position: relative;
            }

            .badge-count {
                background-color: #ef4444;
                color: #fff;
                font-size: 11px;
                padding: 2px 6px;
                border-radius: 50%;
                position: absolute;
                top: -6px;
                right: -6px;
            }

            .btn-refresh {
                background-color: #eef2ff;
                color: #4338ca;
            }

            .btn-refresh:hover {
                background-color: #e0e7ff;
            }

            .stats-card {
                border-radius: 14px;
                padding: 18px 20px;
                display: flex;
                align-items: center;
                position: relative;
                background: #fff;
                box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
                transition: all .2s;
            }

            .stats-card:hover {
                transform: translateY(-2px);
            }

            .stats-icon {
                width: 52px;
                height: 52px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 12px;
                font-size: 20px;
                margin-right: 16px;
            }

            .card-total .stats-icon {
                background-color: #fef3c7;
                color: #d97706;
            }

            .card-pending .stats-icon {
                background-color: #ffedd5;
                color: #f97316;
            }

            .card-approved .stats-icon {
                background-color: #dcfce7;
                color: #16a34a;
            }

            .card-rating .stats-icon {
                background-color: #fef9c3;
                color: #eab308;
            }

            .stats-label {
                font-size: 14px;
                color: #6b7280;
            }

            .stats-value {
                font-size: 22px;
                font-weight: 700;
                color: #111827;
            }

            .stats-change {
                font-size: 13px;
                margin-top: 3px;
            }

            .stats-change.positive {
                color: #16a34a;
            }

            .stats-change.warning {
                color: #f59e0b;
            }

            .stats-badge.pulse {
                position: absolute;
                top: 12px;
                right: 14px;
                background-color: #f59e0b;
                color: #fff;
                font-size: 12px;
                padding: 2px 7px;
                border-radius: 999px;
                animation: pulse 1.8s infinite;
            }

            @keyframes pulse {

                0%,
                100% {
                    opacity: .7;
                    transform: scale(1);
                }

                50% {
                    opacity: 1;
                    transform: scale(1.15);
                }
            }

            .filter-card .nav-link {
                font-weight: 500;
                color: #4b5563;
                border-radius: 8px;
                padding: 8px 16px;
            }

            .filter-card .nav-link.active {
                background-color: #4f46e5;
                color: #fff;
            }

            .filter-card .form-select-custom,
            .filter-card .form-control-custom {
                border-radius: 8px;
                border-color: #e5e7eb;
            }

            .btn-filter {
                background-color: #4f46e5;
                border: none;
                color: #fff;
            }

            .modern-table .review-row.selected {
                background-color: #f9fafb;
                box-shadow: inset 2px 0 0 #4f46e5;
            }

            .user-info {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .user-avatar {
                position: relative;
                width: 48px;
                height: 48px;
            }

            .user-avatar img {
                width: 48px;
                height: 48px;
                border-radius: 50%;
                object-fit: cover;
            }

            .verified-badge {
                position: absolute;
                bottom: -2px;
                right: -2px;
                background-color: #22c55e;
                color: #fff;
                width: 18px;
                height: 18px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 10px;
            }

            .user-name {
                font-weight: 600;
            }

            .user-email {
                font-size: 13px;
                color: #6b7280;
            }

            .user-stats .stat-item {
                font-size: 12px;
                color: #6b7280;
            }

            .product-info {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .product-image img {
                width: 56px;
                height: 56px;
                border-radius: 10px;
                object-fit: cover;
            }

            .product-details .product-name {
                font-weight: 600;
                color: #111827;
                text-decoration: none;
            }

            .product-details .product-name:hover {
                text-decoration: underline;
            }

            .product-meta {
                font-size: 13px;
                color: #6b7280;
                display: flex;
                gap: 8px;
            }

            .rating-container {
                text-align: center;
            }

            .rating-stars-lg i {
                color: #facc15;
                font-size: 16px;
                margin: 0 1px;
                opacity: 0.3;
                transition: 0.2s;
            }

            .rating-stars-lg i.filled {
                opacity: 1;
            }

            .rating-score {
                font-weight: 600;
                color: #111827;
                margin-top: 3px;
            }

            .review-text {
                margin: 0;
                font-size: 14px;
                color: #374151;
            }

            .btn-read-more {
                background: none;
                border: none;
                color: #4f46e5;
                font-size: 13px;
                padding: 0;
            }

            .status-badge {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                border-radius: 999px;
                font-size: 13px;
                font-weight: 500;
                padding: 4px 10px;
            }

            .status-success {
                background: #dcfce7;
                color: #16a34a;
            }

            .status-warning {
                background: #fef9c3;
                color: #ca8a04;
            }

            .status-danger {
                background: #fee2e2;
                color: #dc2626;
            }

            .status-secondary {
                background: #f3f4f6;
                color: #4b5563;
            }

            .action-buttons .btn-action {
                border: none;
                background: none;
                color: #4b5563;
                padding: 6px;
                font-size: 14px;
            }

            .btn-action-view {
                color: #3b82f6;
            }

            .btn-action-edit {
                color: #16a34a;
            }

            .btn-action-approve {
                color: #2563eb;
            }

            .btn-action-reject {
                color: #f59e0b;
            }

            .btn-action-delete {
                color: #dc2626;
            }

            .btn-action:hover {
                transform: scale(1.1);
            }

            .empty-state {
                text-align: center;
                padding: 60px 0;
                color: #6b7280;
            }

            .empty-icon {
                font-size: 48px;
                margin-bottom: 10px;
                color: #9ca3af;
            }

            .empty-title {
                font-weight: 600;
                font-size: 18px;
            }

            .empty-description {
                font-size: 14px;
                color: #9ca3af;
            }

            /* Modal style */
            .modern-modal .modal-content {
                border-radius: 14px;
                border: none;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            }
        </style>
    @endpush
    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Tooltip Bootstrap
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                tooltipTriggerList.map(el => new bootstrap.Tooltip(el));

                // Hiệu ứng khi hover dòng
                document.querySelectorAll(".review-row").forEach(row => {
                    row.addEventListener("mouseenter", () => row.classList.add("hover"));
                    row.addEventListener("mouseleave", () => row.classList.remove("hover"));
                });
            });
        </script>
    @endpush
@endsection
