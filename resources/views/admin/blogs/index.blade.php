@extends('layouts.admin')

@section('title', 'Quản lý Blog')

@section('content')
    <div class="container-fluid py-4">

        {{-- ====== Breadcrumb & Header ====== --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0"><i class="fas fa-blog me-2 text-primary"></i>Quản lý Blog</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb small text-muted mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Blog</li>
                    </ol>
                </nav>
            </div>
            <div>
                <button type="button" class="btn btn-secondary me-2" id="bulkActionsBtn" style="display:none;">
                    <i class="fas fa-tasks me-1"></i>Thao tác hàng loạt
                </button>
                <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus me-2"></i>Thêm bài viết
                </a>
            </div>
        </div>

        {{-- ====== Thẻ thống kê ====== --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="card info-card bg-gradient-primary text-white shadow-sm border-0">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Tổng bài viết</h6>
                            <h3 class="fw-bold mb-0">{{ number_format($totalBlogs) }}</h3>
                        </div>
                        <i class="fas fa-file-alt fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card info-card bg-gradient-success text-white shadow-sm border-0">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Đã xuất bản</h6>
                            <h3 class="fw-bold mb-0">{{ number_format($publishedBlogs) }}</h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card info-card bg-gradient-warning text-white shadow-sm border-0">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Bản nháp</h6>
                            <h3 class="fw-bold mb-0">{{ number_format($draftBlogs) }}</h3>
                        </div>
                        <i class="fas fa-edit fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card info-card bg-gradient-secondary text-white shadow-sm border-0">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Tổng lượt xem</h6>
                            <h3 class="fw-bold mb-0">{{ number_format($totalViews ?? 0) }}</h3>
                        </div>
                        <i class="fas fa-eye fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====== Bộ lọc nâng cao ====== --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tìm kiếm</label>
                        <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control"
                            placeholder="Tiêu đề, slug, nội dung...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Danh mục</label>
                        <select name="category_id" class="form-select">
                            <option value="">-- Tất cả --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="">-- Tất cả --</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}"
                                    {{ request('status') == $status->value ? 'selected' : '' }}>
                                    {{ ucfirst($status->value) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Tác giả</label>
                        <select name="author_id" class="form-select">
                            <option value="">-- Tất cả --</option>
                            @foreach ($authors as $author)
                                <option value="{{ $author->id }}"
                                    {{ request('author_id') == $author->id ? 'selected' : '' }}>
                                    {{ $author->username }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Sắp xếp</label>
                        <select name="sort_by" class="form-select">
                            <option value="latest" {{ request('sort_by') == 'latest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="oldest" {{ request('sort_by') == 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                            <option value="title" {{ request('sort_by') == 'title' ? 'selected' : '' }}>Tên A-Z</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-primary w-100"><i class="fas fa-search"></i></button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ====== Bảng danh sách blog ====== --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <input type="checkbox" id="selectAll" class="form-check-input me-2">
                    <label for="selectAll" class="fw-semibold">Chọn tất cả</label>
                    <span class="text-muted ms-2" id="selectedCount">(0 mục được chọn)</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0 table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="30"></th>
                            <th width="80">Ảnh</th>
                            <th>Tiêu đề</th>
                            <th>Danh mục</th>
                            <th>Tác giả</th>
                            <th>Trạng thái</th>
                            <th>Lượt xem</th>
                            <th>Ngày cập nhật</th>
                            <th class="text-center" width="150">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($blogs as $blog)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input blog-checkbox"
                                        value="{{ $blog->id }}">
                                </td>
                                <td>
                                    @php
                                        $primaryImage =
                                            $blog->images->firstWhere('pivot.is_main', true) ?? $blog->images->first();
                                        $imgPath = $primaryImage->path ?? 'images/default-blog.png';
                                    @endphp
                                    <img src="{{ asset('storage/' . $imgPath) }}" class="rounded shadow-sm"
                                        style="width:70px; height:70px; object-fit:cover;">
                                </td>
                                <td>
                                    <a href="{{ route('admin.blogs.show', $blog) }}"
                                        class="text-decoration-none text-dark fw-semibold">
                                        {{ Str::limit($blog->title, 50) }}
                                    </a>
                                    <small class="d-block text-muted">{{ $blog->slug }}</small>
                                </td>
                                <td>
                                    @foreach ($blog->categories->take(2) as $cat)
                                        <span class="badge bg-light text-dark">{{ $cat->name }}</span>
                                    @endforeach
                                    @if ($blog->categories->count() > 2)
                                        <span
                                            class="badge bg-light text-dark">+{{ $blog->categories->count() - 2 }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $blog->author->avatar_url ?? asset('images/default-avatar.png') }}"
                                            class="rounded-circle me-2" width="32" height="32">
                                        <span class="fw-semibold">{{ $blog->author->username }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-status {{ $blog->status->value }}">
                                        {{ ucfirst($blog->status->value) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ number_format($blog->views_count ?? 0) }}</span>
                                </td>
                                <td>
                                    <small>{{ $blog->updated_at->format('d/m/Y H:i') }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.blogs.show', $blog) }}"
                                            class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Xem">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.blogs.edit', $blog) }}"
                                            class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip"
                                            title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="confirmDelete({{ $blog->id }})" data-bs-toggle="tooltip"
                                            title="Xóa">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                    <form action="{{ route('admin.blogs.destroy', $blog) }}" method="POST"
                                        class="d-none" id="deleteForm{{ $blog->id }}">
                                        @csrf @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    <p class="mb-0">Không có bài viết nào</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Hiển thị {{ $blogs->firstItem() ?? 0 }} - {{ $blogs->lastItem() ?? 0 }} trong tổng số
                    {{ $blogs->total() }} bài viết
                </div>
                {{ $blogs->links('components.pagination') }}
            </div>
        </div>

    </div>

    {{-- Modal thao tác hàng loạt --}}
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
                                <option value="update_status">Cập nhật trạng thái</option>
                                <option value="delete">Xóa bài viết</option>
                            </select>
                        </div>
                        <div class="mb-3 d-none" id="statusSelectDiv">
                            <label class="form-label fw-semibold">Trạng thái mới</label>
                            <select class="form-select" name="status" id="newStatus">
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->value }}">{{ ucfirst($status->value) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="alert alert-warning d-none" id="deleteWarning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Bạn có chắc muốn xóa <strong><span id="deleteCount"></span></strong> bài viết đã chọn?
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
        <script>
            // Xử lý checkbox (tương tự products)
            const selectAllCheckbox = document.getElementById('selectAll');
            const blogCheckboxes = document.querySelectorAll('.blog-checkbox');
            const selectedCountSpan = document.getElementById('selectedCount');
            const bulkActionsBtn = document.getElementById('bulkActionsBtn');

            function updateSelectedCount() {
                const checked = document.querySelectorAll('.blog-checkbox:checked').length;
                const total = blogCheckboxes.length;
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
                blogCheckboxes.forEach(cb => {
                    cb.checked = this.checked;
                    cb.closest('tr').classList.toggle('table-active', this.checked);
                });
                updateSelectedCount();
            });

            blogCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    updateSelectedCount();
                    this.closest('tr').classList.toggle('table-active', this.checked);
                });
            });

            bulkActionsBtn.addEventListener('click', function() {
                const checkedIds = Array.from(document.querySelectorAll('.blog-checkbox:checked')).map(cb => cb.value);
                document.getElementById('bulkIds').value = checkedIds.join(',');
                document.getElementById('deleteCount').textContent = checkedIds.length;
                const modal = new bootstrap.Modal(document.getElementById('bulkActionsModal'));
                modal.show();
            });

            document.getElementById('bulkAction').addEventListener('change', function() {
                const statusDiv = document.getElementById('statusSelectDiv');
                const deleteWarning = document.getElementById('deleteWarning');
                statusDiv.classList.add('d-none');
                deleteWarning.classList.add('d-none');
                if (this.value === 'update_status') statusDiv.classList.remove('d-none');
                else if (this.value === 'delete') deleteWarning.classList.remove('d-none');
            });

            document.getElementById('executeBulkAction').addEventListener('click', function() {
                const action = document.getElementById('bulkAction').value;
                const ids = document.getElementById('bulkIds').value.split(',');
                if (!action) return Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Vui lòng chọn hành động'
                });

                const btn = this;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';

                let url = action === 'update_status' ? '{{ route('admin.blogs.bulk-update-status') }}' :
                    '{{ route('admin.blogs.bulk-delete') }}';
                let data = {
                    ids,
                    _token: '{{ csrf_token() }}'
                };
                if (action === 'update_status') data.status = document.getElementById('newStatus').value;

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: data.message,
                                timer: 2000
                            }).then(() => location.reload());
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: data.message
                            });
                            btn.disabled = false;
                            btn.innerHTML = 'Thực hiện';
                        }
                    });
            });

            function confirmDelete(id) {
                Swal.fire({
                    title: "Xác nhận xóa?",
                    text: "Bài viết này sẽ bị xóa vĩnh viễn!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#ef4444",
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy'
                }).then(result => {
                    if (result.isConfirmed) document.getElementById('deleteForm' + id).submit();
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

            document.addEventListener('DOMContentLoaded', updateSelectedCount);
        </script>
    @endpush

    @push('styles')
        <style>
            /* Copie les mêmes styles que products */
            body,
            table {
                font-family: 'Inter', 'Roboto', sans-serif;
                font-size: 15px;
                color: #1e293b;
            }

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

            .bg-gradient-secondary {
                background: linear-gradient(135deg, #64748b 0%, #94a3b8 100%);
            }

            table {
                border-collapse: separate;
                border-spacing: 0;
            }

            thead {
                background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
                font-weight: 600;
            }

            tbody tr:hover {
                background-color: #f8fafc;
                transform: translateX(2px);
            }

            tbody tr.table-active {
                background-color: #eff6ff;
                border-left: 3px solid #4f46e5;
            }

            .badge-status {
                padding: 0.5rem 1rem;
                border-radius: 999px;
                font-weight: 700;
                font-size: 0.85rem;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            }

            .badge-status.published {
                background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);
                color: white;
            }

            .badge-status.draft {
                background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);
                color: white;
            }

            .badge-status.archived {
                background: linear-gradient(135deg, #64748b 0%, #94a3b8 100%);
                color: white;
            }

            .card {
                border: none;
                border-radius: 12px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
            }
        </style>
    @endpush

@endsection
