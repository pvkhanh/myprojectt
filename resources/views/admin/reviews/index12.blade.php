@extends('layouts.admin')

@section('title', 'Quản lý đánh giá sản phẩm')

@section('content')
    <div class="container-fluid py-4">

        {{-- ====== Breadcrumb & Header ====== --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">
                    <i class="fas fa-star-half-alt me-2 text-warning"></i>Quản lý đánh giá
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb small text-muted mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Đánh giá sản phẩm</li>
                    </ol>
                </nav>
            </div>
            <div>
                <button type="button" class="btn btn-secondary me-2" id="bulkActionsBtn" style="display:none;">
                    <i class="fas fa-tasks me-1"></i>Thao tác hàng loạt
                </button>
                <a href="{{ route('admin.reviews.trash') }}" class="btn btn-outline-danger me-2">
                    <i class="fas fa-trash-alt me-1"></i>Thùng rác
                    @if (isset($trashedCount) && $trashedCount > 0)
                        <span class="badge bg-danger ms-1">{{ $trashedCount }}</span>
                    @endif
                </a>
                <button type="button" class="btn btn-outline-primary" onclick="window.location.reload()">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>

        {{-- ====== Thẻ thống kê ====== --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="card info-card bg-gradient-primary text-white shadow-sm border-0">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Tổng đánh giá</h6>
                            <h3 class="fw-bold mb-0">{{ number_format($stats['total']) }}</h3>
                            <small class="text-white-75">100%</small>
                        </div>
                        <i class="fas fa-comments fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card info-card bg-gradient-warning text-white shadow-sm border-0">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Chờ duyệt</h6>
                            <h3 class="fw-bold mb-0">{{ number_format($stats['pending']) }}</h3>
                            <small class="text-white-75">Cần xử lý</small>
                        </div>
                        <div class="position-relative">
                            <i class="fas fa-clock fa-2x opacity-75"></i>
                            @if ($stats['pending'] > 0)
                                <span class="badge-pulse"></span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card info-card bg-gradient-success text-white shadow-sm border-0">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Đã duyệt</h6>
                            <h3 class="fw-bold mb-0">{{ number_format($stats['approved']) }}</h3>
                            <small class="text-white-75">
                                {{ $stats['total'] > 0 ? round(($stats['approved'] / $stats['total']) * 100, 1) : 0 }}%
                            </small>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card info-card bg-gradient-rating text-white shadow-sm border-0">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Điểm TB</h6>
                            <h3 class="fw-bold mb-0">
                                {{ $stats['avg_rating'] }}
                                <span class="fs-6">/5</span>
                            </h3>
                            <div class="rating-stars-sm">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $stats['avg_rating'] ? 'active' : '' }}"></i>
                                @endfor
                            </div>
                        </div>
                        <i class="fas fa-star fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====== Bộ lọc nâng cao ====== --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <ul class="nav nav-pills card-header-pills mb-0" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{ !request('status') ? 'active' : '' }}"
                            href="{{ route('admin.reviews.index') }}">
                            <i class="fas fa-list me-1"></i>Tất cả
                            <span class="badge">{{ $stats['total'] }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('status') == 'pending' ? 'active' : '' }}"
                            href="{{ route('admin.reviews.index', ['status' => 'pending']) }}">
                            <i class="fas fa-clock me-1"></i>Chờ duyệt
                            <span class="badge">{{ $stats['pending'] }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('status') == 'approved' ? 'active' : '' }}"
                            href="{{ route('admin.reviews.index', ['status' => 'approved']) }}">
                            <i class="fas fa-check-circle me-1"></i>Đã duyệt
                            <span class="badge">{{ $stats['approved'] }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('status') == 'rejected' ? 'active' : '' }}"
                            href="{{ route('admin.reviews.index', ['status' => 'rejected']) }}">
                            <i class="fas fa-times-circle me-1"></i>Từ chối
                            <span class="badge">{{ $stats['rejected'] }}</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tìm kiếm</label>
                        <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control"
                            placeholder="Tìm theo nội dung, tên KH...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Đánh giá</label>
                        <select name="rating" class="form-select">
                            <option value="">-- Tất cả --</option>
                            @for ($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                                    {{ $i }} ⭐
                                    @if ($i == 5)
                                        Xuất sắc
                                    @elseif($i == 4)
                                        Tốt
                                    @elseif($i == 3)
                                        Trung bình
                                    @elseif($i == 2)
                                        Tệ
                                    @else
                                        Rất tệ
                                    @endif
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Từ ngày</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Đến ngày</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Sắp xếp</label>
                        <select name="sort_by" class="form-select">
                            <option value="latest" {{ request('sort_by') == 'latest' ? 'selected' : '' }}>Mới nhất
                            </option>
                            <option value="oldest" {{ request('sort_by') == 'oldest' ? 'selected' : '' }}>Cũ nhất
                            </option>
                            <option value="rating_high" {{ request('sort_by') == 'rating_high' ? 'selected' : '' }}>⭐
                                Cao nhất</option>
                            <option value="rating_low" {{ request('sort_by') == 'rating_low' ? 'selected' : '' }}>⭐
                                Thấp nhất</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-primary w-100"><i class="fas fa-search"></i></button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ====== Bảng danh sách ====== --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <input type="checkbox" id="selectAll" class="form-check-input me-2">
                    <label for="selectAll" class="fw-semibold">Chọn tất cả</label>
                    <span class="text-muted ms-2" id="selectedCount">(0 mục được chọn)</span>
                </div>
                <div class="text-muted small">
                    Hiển thị {{ $reviews->firstItem() ?? 0 }}-{{ $reviews->lastItem() ?? 0 }}
                    trong {{ $reviews->total() }} kết quả
                </div>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0 table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="30"></th>
                            <th width="200">Người đánh giá</th>
                            <th width="220">Sản phẩm</th>
                            <th width="120" class="text-center">Đánh giá</th>
                            <th>Nội dung</th>
                            <th width="110">Trạng thái</th>
                            <th width="120">Thời gian</th>
                            <th width="140" class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input review-checkbox"
                                        value="{{ $review->id }}">
                                </td>
                                <td>
                                    @if ($review->user)
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="position-relative">
                                                <img src="{{ $review->user->avatar_url ?? asset('images/default-avatar.png') }}"
                                                    class="rounded-circle" width="42" height="42"
                                                    style="object-fit: cover;">
                                                @if ($review->user->orders && $review->user->orders->count() > 0)
                                                    <span class="verified-badge" data-bs-toggle="tooltip"
                                                        title="Đã mua {{ $review->user->orders->count() }} đơn">
                                                        <i class="fas fa-check"></i>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1 min-w-0">
                                                <div class="fw-semibold text-truncate">
                                                    {{ $review->user->username ?? 'Ẩn danh' }}
                                                </div>
                                                <div class="text-muted small text-truncate">
                                                    {{ $review->user->email ?? 'Không có email' }}
                                                </div>
                                                <div class="text-muted small">
                                                    <i class="fas fa-comment-dots"></i>
                                                    {{ $review->user->reviews->count() ?? 0 }} đánh giá
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-muted fst-italic">
                                            <i class="fas fa-user-slash"></i> Người dùng không tồn tại
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $review->product->main_image_url ?? asset('images/default-product.png') }}"
                                            class="rounded shadow-sm" width="50" height="50"
                                            style="object-fit: cover;">
                                        <div class="flex-grow-1 min-w-0">
                                            <a href="{{ route('admin.products.show', $review->product) }}"
                                                class="text-decoration-none text-dark fw-semibold d-block text-truncate"
                                                style="max-width: 150px;">
                                                {{ $review->product->name }}
                                            </a>
                                            <div class="text-muted small">
                                                {{ number_format($review->product->price, 0, ',', '.') }}đ
                                                <span class="text-secondary">・#{{ $review->product->id }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-center">
                                    <div class="rating-display">
                                        <div class="rating-stars-table mb-1">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i
                                                    class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                        </div>
                                        <div class="fw-bold text-dark">{{ $review->rating }}/5</div>
                                    </div>
                                </td>

                                <td>
                                    <div class="review-content-preview">
                                        <p class="mb-0 text-truncate-2">{{ $review->comment }}</p>
                                        @if (strlen($review->comment) > 100)
                                            <button class="btn btn-link btn-sm p-0 text-decoration-none"
                                                onclick="showFullReview({{ $review->id }}, `{{ addslashes($review->comment) }}`)">
                                                <small>Xem thêm <i class="fas fa-chevron-down"></i></small>
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
                                    <span class="badge-status {{ $statusConfig['class'] }}">
                                        <i class="fas fa-{{ $statusConfig['icon'] }}"></i>
                                        {{ $statusConfig['text'] }}
                                    </span>
                                </td>

                                <td>
                                    <div class="text-muted small">
                                        <div><i class="far fa-calendar"></i> {{ $review->created_at->format('d/m/Y') }}
                                        </div>
                                        <div class="text-secondary">{{ $review->created_at->diffForHumans() }}</div>
                                    </div>
                                </td>

                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.reviews.show', $review) }}"
                                            class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Xem">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        @if ($review->status->value !== 'approved')
                                            <form action="{{ route('admin.reviews.approve', $review) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success"
                                                    data-bs-toggle="tooltip" title="Duyệt">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @if ($review->status->value !== 'rejected')
                                            <form action="{{ route('admin.reviews.reject', $review) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-warning"
                                                    data-bs-toggle="tooltip" title="Từ chối">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="confirmDelete({{ $review->id }})" data-bs-toggle="tooltip"
                                            title="Xóa">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                    <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST"
                                        class="d-none" id="deleteForm{{ $review->id }}">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="fas fa-comment-slash fa-3x mb-3 d-block"></i>
                                    <p class="mb-0">Không có đánh giá nào</p>
                                    <small>Chưa có đánh giá nào được tìm thấy với bộ lọc hiện tại</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Hiển thị <strong>{{ $reviews->firstItem() ?? 0 }}</strong> đến
                    <strong>{{ $reviews->lastItem() ?? 0 }}</strong> trong tổng số
                    <strong>{{ $reviews->total() }}</strong> đánh giá
                </div>
                {{ $reviews->links('components.pagination') }}
            </div>
        </div>

    </div>

    {{-- Modal thao tác hàng loạt --}}
    <div class="modal fade" id="bulkActionsModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-tasks me-2"></i>Thao tác hàng loạt
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="bulkActionForm">
                        @csrf
                        <input type="hidden" name="ids" id="bulkIds">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Chọn hành động</label>
                            <select class="form-select form-select-lg" id="bulkAction" required>
                                <option value="">-- Chọn --</option>
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

                selectedCountSpan.textContent = `(${checked}/${total} mục được chọn)`;
                bulkActionsBtn.style.display = checked > 0 ? 'inline-block' : 'none';

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
                    cb.closest('tr').classList.toggle('table-active', this.checked);
                });
                updateSelectedCount();
            });

            reviewCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    updateSelectedCount();
                    this.closest('tr').classList.toggle('table-active', this.checked);
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
            function showFullReview(id, content) {
                Swal.fire({
                    title: '<i class="fas fa-comment-dots text-primary me-2"></i>Nội dung đánh giá chi tiết',
                    html: `
                        <div class="text-start p-4 border rounded-3 bg-light shadow-sm" style="max-height: 400px; overflow-y: auto;">
                            <div class="review-full-content">
                                <i class="fas fa-quote-left text-primary opacity-25 fs-3 mb-2"></i>
                                <p class="mb-0 lh-lg" style="white-space: pre-wrap;">${content}</p>
                                <i class="fas fa-quote-right text-primary opacity-25 fs-3 mt-2 float-end"></i>
                            </div>
                        </div>
                    `,
                    width: 700,
                    confirmButtonText: '<i class="fas fa-times me-2"></i>Đóng',
                    confirmButtonColor: '#4f46e5',
                    customClass: {
                        popup: 'animated fadeIn',
                        title: 'fs-5 fw-bold',
                        htmlContainer: 'pt-2',
                        confirmButton: 'btn btn-primary px-4'
                    }
                });
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
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
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
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });
            @endif

            // Tooltip initialization
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize Bootstrap tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl, {
                        trigger: 'hover',
                        delay: {
                            show: 500,
                            hide: 100
                        }
                    });
                });

                // Initialize selected count
                updateSelectedCount();

                // Add smooth scroll behavior
                document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                    anchor.addEventListener('click', function(e) {
                        e.preventDefault();
                        const target = document.querySelector(this.getAttribute('href'));
                        if (target) {
                            target.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }
                    });
                });

                // Add loading state to forms
                document.querySelectorAll('form').forEach(form => {
                    form.addEventListener('submit', function(e) {
                        const submitBtn = this.querySelector('[type="submit"]');
                        if (submitBtn && !submitBtn.disabled) {
                            submitBtn.disabled = true;
                            const originalText = submitBtn.innerHTML;
                            submitBtn.innerHTML =
                                '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';

                            // Re-enable after 5 seconds as fallback
                            setTimeout(() => {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalText;
                            }, 5000);
                        }
                    });
                });
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Ctrl/Cmd + K: Focus search
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    const searchInput = document.querySelector('input[name="keyword"]');
                    if (searchInput) {
                        searchInput.focus();
                        searchInput.select();
                    }
                }

                // Ctrl/Cmd + A: Select all (when focus is on table)
                if ((e.ctrlKey || e.metaKey) && e.key === 'a') {
                    const activeElement = document.activeElement;
                    if (activeElement.tagName === 'TABLE' || activeElement.closest('table')) {
                        e.preventDefault();
                        selectAllCheckbox.click();
                    }
                }

                // Escape: Clear selection
                if (e.key === 'Escape') {
                    if (document.querySelectorAll('.review-checkbox:checked').length > 0) {
                        reviewCheckboxes.forEach(cb => {
                            cb.checked = false;
                            cb.closest('tr').classList.remove('table-active');
                        });
                        selectAllCheckbox.checked = false;
                        selectAllCheckbox.indeterminate = false;
                        updateSelectedCount();
                    }
                }
            });

            // Auto-refresh notification (optional)
            let autoRefreshInterval = null;

            function startAutoRefresh(minutes = 5) {
                if (autoRefreshInterval) {
                    clearInterval(autoRefreshInterval);
                }

                autoRefreshInterval = setInterval(() => {
                    // Check for new pending reviews
                    fetch('{{ route('admin.reviews.check-new') }}', {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.new_count > 0) {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'info',
                                    title: `Có ${data.new_count} đánh giá mới cần xử lý`,
                                    showConfirmButton: true,
                                    confirmButtonText: 'Tải lại',
                                    timer: 10000,
                                    timerProgressBar: true
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.reload();
                                    }
                                });
                            }
                        })
                        .catch(error => console.error('Auto-refresh error:', error));
                }, minutes * 60 * 1000);
            }

            // Uncomment to enable auto-refresh
            // startAutoRefresh(5);

            // Handle row click for quick view
            document.querySelectorAll('tbody tr').forEach(row => {
                row.addEventListener('dblclick', function(e) {
                    // Ignore if clicking on checkbox or buttons
                    if (e.target.closest('.form-check-input, .btn, .btn-group, a')) {
                        return;
                    }

                    const checkbox = this.querySelector('.review-checkbox');
                    if (checkbox) {
                        const reviewId = checkbox.value;
                        const comment = this.querySelector('.review-content-preview p')?.textContent;
                        if (comment) {
                            showFullReview(reviewId, comment);
                        }
                    }
                });
            });

            // Add visual feedback for actions
            function addActionFeedback(button, success = true) {
                const icon = button.querySelector('i');
                if (icon) {
                    const originalClass = icon.className;
                    icon.className = success ? 'fas fa-check' : 'fas fa-times';
                    button.classList.add(success ? 'btn-success' : 'btn-danger');

                    setTimeout(() => {
                        icon.className = originalClass;
                        button.classList.remove('btn-success', 'btn-danger');
                    }, 1000);
                }
            }

            // Print functionality
            function printReviews() {
                window.print();
            }

            // Export to CSV functionality
            function exportToCSV() {
                const rows = [];
                const headers = ['ID', 'Người đánh giá', 'Sản phẩm', 'Đánh giá', 'Nội dung', 'Trạng thái', 'Ngày tạo'];
                rows.push(headers.join(','));

                document.querySelectorAll('tbody tr').forEach(row => {
                    const cells = row.querySelectorAll('td');
                    if (cells.length > 1) {
                        const rowData = [
                            row.querySelector('.review-checkbox')?.value || '',
                            cells[1]?.textContent.trim().replace(/\s+/g, ' ') || '',
                            cells[2]?.textContent.trim().replace(/\s+/g, ' ') || '',
                            cells[3]?.textContent.trim() || '',
                            cells[4]?.textContent.trim().replace(/\s+/g, ' ').replace(/"/g, '""') || '',
                            cells[5]?.textContent.trim() || '',
                            cells[6]?.textContent.trim().replace(/\s+/g, ' ') || ''
                        ];
                        rows.push(rowData.map(cell => `"${cell}"`).join(','));
                    }
                });

                const csvContent = '\uFEFF' + rows.join('\n'); // Add BOM for UTF-8
                const blob = new Blob([csvContent], {
                    type: 'text/csv;charset=utf-8;'
                });
                const link = document.createElement('a');
                const url = URL.createObjectURL(blob);

                link.setAttribute('href', url);
                link.setAttribute('download', `reviews_${new Date().toISOString().split('T')[0]}.csv`);
                link.style.visibility = 'hidden';

                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }

            // Add keyboard hint
            function showKeyboardHints() {
                Swal.fire({
                    title: '<i class="fas fa-keyboard me-2"></i>Phím tắt',
                    html: `
                        <div class="text-start">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><kbd class="bg-dark text-white px-2 py-1 rounded">Ctrl</kbd> + <kbd class="bg-dark text-white px-2 py-1 rounded">K</kbd></span>
                                    <span class="text-muted">Tìm kiếm</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><kbd class="bg-dark text-white px-2 py-1 rounded">Ctrl</kbd> + <kbd class="bg-dark text-white px-2 py-1 rounded">A</kbd></span>
                                    <span class="text-muted">Chọn tất cả</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><kbd class="bg-dark text-white px-2 py-1 rounded">Esc</kbd></span>
                                    <span class="text-muted">Hủy chọn</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><kbd class="bg-dark text-white px-2 py-1 rounded">Double Click</kbd></span>
                                    <span class="text-muted">Xem chi tiết</span>
                                </div>
                            </div>
                        </div>
                    `,
                    confirmButtonText: 'Đóng',
                    confirmButtonColor: '#4f46e5',
                    width: 500
                });
            }

            // Add keyboard hint button (optional - add to HTML if needed)
            // <button onclick="showKeyboardHints()" class="btn btn-sm btn-outline-secondary">
            //     <i class="fas fa-keyboard"></i>
            // </button>
        </script>
    @endpush

    @push('styles')
        <style>
            /* ===== Global Styles ===== */
            body,
            table {
                font-family: 'Inter', 'Roboto', sans-serif;
                font-size: 15px;
                color: #1e293b;
            }

            /* ===== Info Cards ===== */
            .info-card {
                transition: transform 0.2s ease, box-shadow 0.2s ease;
                border-radius: 12px;
                overflow: hidden;
            }

            .info-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            }

            .bg-gradient-primary {
                background: linear-gradient(135deg, #4f46e5 0%, #6d28d9 100%);
            }

            .bg-gradient-success {
                background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);
            }

            .bg-gradient-warning {
                background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
            }

            .bg-gradient-rating {
                background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
            }

            .text-white-75 {
                color: rgba(255, 255, 255, 0.75);
            }

            /* Badge pulse animation */
            .badge-pulse {
                position: absolute;
                top: -5px;
                right: -5px;
                width: 12px;
                height: 12px;
                background: #ef4444;
                border-radius: 50%;
                animation: pulse 2s infinite;
            }

            @keyframes pulse {

                0%,
                100% {
                    opacity: 0.7;
                    transform: scale(1);
                    box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
                }

                50% {
                    opacity: 1;
                    transform: scale(1.2);
                    box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
                }
            }

            /* Rating stars small */
            .rating-stars-sm i {
                font-size: 14px;
                margin: 0 1px;
            }

            .rating-stars-sm i.active {
                color: #fef3c7;
            }

            /* ===== Nav Pills ===== */
            .nav-pills .nav-link {
                font-weight: 500;
                color: #64748b;
                border-radius: 8px;
                padding: 8px 16px;
                transition: all 0.2s ease;
                display: flex;
                align-items: center;
                gap: 4px;
            }

            .nav-pills .nav-link .badge {
                background: #e2e8f0;
                color: #64748b;
                padding: 2px 8px;
                border-radius: 12px;
                font-size: 12px;
                font-weight: 600;
            }

            .nav-pills .nav-link.active {
                background: linear-gradient(135deg, #4f46e5 0%, #6d28d9 100%);
                color: white;
            }

            .nav-pills .nav-link.active .badge {
                background: rgba(255, 255, 255, 0.2);
                color: white;
            }

            .nav-pills .nav-link:hover:not(.active) {
                background: #f1f5f9;
            }

            /* ===== Table ===== */
            table {
                width: 100%;
                border-collapse: separate;
                border-spacing: 0;
            }

            thead {
                background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.03em;
                color: #475569;
                font-size: 0.8rem;
            }

            thead th {
                padding: 14px 16px;
                border-bottom: 2px solid #e2e8f0;
            }

            tbody td {
                padding: 14px 16px;
                border-bottom: 1px solid #f1f5f9;
                vertical-align: middle;
            }

            tbody tr {
                transition: all 0.2s ease;
                cursor: pointer;
            }

            tbody tr:hover {
                background-color: #f8fafc;
            }

            tbody tr.table-active {
                background-color: #eff6ff;
                border-left: 3px solid #4f46e5;
            }

            /* ===== User Avatar ===== */
            .verified-badge {
                position: absolute;
                bottom: -2px;
                right: -2px;
                background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
                color: white;
                width: 18px;
                height: 18px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 9px;
                border: 2px solid white;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            /* ===== Rating Display ===== */
            .rating-display {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 4px;
            }

            .rating-stars-table i {
                font-size: 14px;
                margin: 0 1px;
            }

            /* ===== Review Content Preview ===== */
            .review-content-preview {
                max-width: 300px;
            }

            .text-truncate-2 {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
                line-height: 1.5;
                max-height: 3em;
            }

            /* ===== Status Badges ===== */
            .badge-status {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 6px 12px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
                white-space: nowrap;
            }

            .badge-status.success {
                background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
                color: #166534;
            }

            .badge-status.warning {
                background: linear-gradient(135deg, #fef9c3 0%, #fef08a 100%);
                color: #854d0e;
            }

            .badge-status.danger {
                background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
                color: #991b1b;
            }

            .badge-status.secondary {
                background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
                color: #475569;
            }

            /* ===== Buttons ===== */
            .btn {
                border-radius: 8px;
                font-weight: 600;
                transition: all 0.2s ease;
            }

            .btn:hover {
                transform: translateY(-2px);
            }

            .btn-primary {
                background: linear-gradient(135deg, #4f46e5 0%, #6d28d9 100%);
                border: none;
            }

            .btn-group .btn {
                padding: 6px 10px;
            }

            /* ===== Card ===== */
            .card {
                border: none;
                border-radius: 12px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
            }

            .card-header {
                background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
                border-bottom: 2px solid #e2e8f0;
                border-radius: 12px 12px 0 0 !important;
                padding: 16px 20px;
            }

            .card-footer {
                background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
                border-top: 2px solid #e2e8f0;
                border-radius: 0 0 12px 12px;
                padding: 16px 20px;
            }

            /* ===== Form Controls ===== */
            .form-control,
            .form-select {
                border: 2px solid #e2e8f0;
                border-radius: 8px;
                transition: all 0.2s ease;
            }

            .form-control:focus,
            .form-select:focus {
                border-color: #4f46e5;
                box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
            }

            /* ===== Modal ===== */
            .modal-content {
                border-radius: 16px;
                border: none;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            }

            .modal-header {
                background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
                border-bottom: 2px solid #e2e8f0;
                border-radius: 16px 16px 0 0;
                padding: 20px 24px;
            }

            .modal-body {
                padding: 24px;
            }

            .modal-footer {
                background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
                border-top: 2px solid #e2e8f0;
                border-radius: 0 0 16px 16px;
                padding: 16px 24px;
            }

            /* ===== Pagination ===== */
            .pagination {
                gap: 0.5rem;
            }

            .page-link {
                border-radius: 8px;
                border: 2px solid #e2e8f0;
                color: #475569;
                font-weight: 600;
                transition: all 0.2s ease;
                padding: 8px 12px;
            }

            .page-link:hover {
                background: #4f46e5;
                color: white;
                border-color: #4f46e5;
                transform: translateY(-2px);
            }

            .page-item.active .page-link {
                background: linear-gradient(135deg, #4f46e5 0%, #6d28d9 100%);
                border-color: #4f46e5;
            }

            .page-item.disabled .page-link {
                background: #f1f5f9;
                border-color: #e2e8f0;
                color: #94a3b8;
            }

            /* ===== Animations ===== */
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .card {
                animation: fadeIn 0.5s ease;
            }

            /* ===== Utilities ===== */
            .min-w-0 {
                min-width: 0;
            }

            .text-secondary {
                color: #94a3b8 !important;
            }

            /* ===== Review Full Content Modal ===== */
            .review-full-content {
                position: relative;
                padding: 20px;
                line-height: 1.8;
                color: #334155;
            }

            .review-full-content p {
                margin: 10px 0;
            }

            /* ===== Keyboard Shortcuts ===== */
            kbd {
                padding: 4px 8px;
                font-size: 11px;
                font-weight: 600;
                border-radius: 4px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            /* ===== Print Styles ===== */
            @media print {

                .btn,
                .form-check-input,
                .card-header,
                .card-footer,
                .breadcrumb,
                .nav-pills {
                    display: none !important;
                }

                .card {
                    box-shadow: none;
                    border: 1px solid #e2e8f0;
                }

                tbody tr {
                    page-break-inside: avoid;
                }
            }

            /* ===== Responsive ===== */
            @media (max-width: 768px) {
                .info-card .card-body {
                    padding: 16px;
                }

                .info-card h3 {
                    font-size: 1.5rem;
                }

                .info-card i {
                    font-size: 1.5rem;
                }

                .table {
                    font-size: 0.85rem;
                }

                thead th,
                tbody td {
                    padding: 10px 12px;
                }

                .review-content-preview {
                    max-width: 200px;
                }
            }

            /* ===== SweetAlert2 Custom ===== */
            .swal2-popup {
                border-radius: 16px;
                font-family: 'Inter', 'Roboto', sans-serif;
            }

            .swal2-title {
                color: #1e293b;
                font-weight: 700;
            }

            .swal2-html-container {
                color: #475569;
            }

            .swal2-confirm {
                background: linear-gradient(135deg, #4f46e5 0%, #6d28d9 100%) !important;
                border-radius: 8px;
                padding: 10px 24px;
                font-weight: 600;
            }

            .swal2-cancel {
                background: #94a3b8 !important;
                border-radius: 8px;
                padding: 10px 24px;
                font-weight: 600;
            }

            /* Animated class for modal */
            .animated.fadeIn {
                animation: fadeIn 0.3s ease;
            }

            /* ===== Checkbox Styles ===== */
            .form-check-input {
                width: 18px;
                height: 18px;
                border: 2px solid #cbd5e1;
                border-radius: 4px;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .form-check-input:checked {
                background-color: #4f46e5;
                border-color: #4f46e5;
            }

            .form-check-input:indeterminate {
                background-color: #4f46e5;
                border-color: #4f46e5;
            }

            .form-check-input:focus {
                box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
            }

            /* ===== Breadcrumb ===== */
            .breadcrumb {
                margin-bottom: 0;
            }

            .breadcrumb-item a {
                color: #64748b;
                text-decoration: none;
                transition: color 0.2s ease;
            }

            .breadcrumb-item a:hover {
                color: #4f46e5;
            }

            .breadcrumb-item.active {
                color: #94a3b8;
            }

            .breadcrumb-item+.breadcrumb-item::before {
                color: #cbd5e1;
            }

            /* ===== Smooth Scroll ===== */
            html {
                scroll-behavior: smooth;
            }

            /* ===== Selection Color ===== */
            ::selection {
                background-color: #4f46e5;
                color: white;
            }

            /* ===== Focus Visible ===== */
            *:focus-visible {
                outline: 2px solid #4f46e5;
                outline-offset: 2px;
            }

            /* ===== Loading Spinner ===== */
            .spinner-border-sm {
                width: 1rem;
                height: 1rem;
                border-width: 0.15em;
            }

            /* ===== List Group Custom ===== */
            .list-group-item {
                border: 1px solid #e2e8f0;
                padding: 12px 16px;
            }

            .list-group-item:first-child {
                border-top-left-radius: 8px;
                border-top-right-radius: 8px;
            }

            .list-group-item:last-child {
                border-bottom-left-radius: 8px;
                border-bottom-right-radius: 8px;
            }
        </style>
    @endpush
@endsection
