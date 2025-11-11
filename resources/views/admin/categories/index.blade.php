@extends('layouts.admin')

@section('title', 'Quản lý danh mục')

@section('content')
    <div class="container-fluid py-4">

        {{-- ====== Header & Breadcrumb ====== --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0"><i class="fas fa-folder-tree me-2 text-warning"></i>Quản lý danh mục</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb small text-muted mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Danh mục</li>
                    </ol>
                </nav>
            </div>
            <div>
                <button type="button" class="btn btn-secondary me-2" id="bulkActionsBtn" style="display:none;">
                    <i class="fas fa-tasks me-1"></i>Thao tác hàng loạt
                </button>
                <a href="{{ route('admin.categories.create') }}" class="btn btn-warning text-dark shadow-sm">
                    <i class="fas fa-plus me-2"></i>Thêm danh mục
                </a>
            </div>
        </div>

        {{-- ====== Thẻ thống kê ====== --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="card info-card bg-gradient-warning text-dark shadow-sm border-0">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-dark opacity-75 mb-2">Tổng danh mục</h6>
                            <h3 class="fw-bold mb-0">{{ number_format($totalCategories) }}</h3>
                        </div>
                        <i class="fas fa-folder fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card info-card bg-gradient-primary text-white shadow-sm border-0">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Danh mục gốc</h6>
                            <h3 class="fw-bold mb-0">{{ number_format($rootCategories) }}</h3>
                        </div>
                        <i class="fas fa-layer-group fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card info-card bg-gradient-success text-white shadow-sm border-0">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Có sản phẩm</h6>
                            <h3 class="fw-bold mb-0">{{ number_format($withProducts) }}</h3>
                        </div>
                        <i class="fas fa-box-open fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card info-card bg-gradient-secondary text-white shadow-sm border-0">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Danh mục trống</h6>
                            <h3 class="fw-bold mb-0">{{ number_format($emptyCategories) }}</h3>
                        </div>
                        <i class="fas fa-inbox fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====== Bộ lọc ====== --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-search me-1"></i>Tìm kiếm
                        </label>
                        <input type="text" name="search" value="{{ $keyword }}" class="form-control"
                            placeholder="Tên, slug, mô tả...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-filter me-1"></i>Danh mục cha
                        </label>
                        <select name="parent_id" class="form-select">
                            <option value="">-- Tất cả --</option>
                            <option value="root" {{ $parentId === 'root' ? 'selected' : '' }}>Danh mục gốc</option>
                            @foreach ($parentCategories as $parent)
                                <option value="{{ $parent->id }}" {{ $parentId == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-sort me-1"></i>Sắp xếp
                        </label>
                        <select name="sort_by" class="form-select">
                            <option value="position" {{ $sortBy == 'position' ? 'selected' : '' }}>Vị trí</option>
                            <option value="name" {{ $sortBy == 'name' ? 'selected' : '' }}>Tên A-Z</option>
                            <option value="latest" {{ $sortBy == 'latest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="products_count" {{ $sortBy == 'products_count' ? 'selected' : '' }}>Số sản phẩm
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-warning text-dark w-100">
                            <i class="fa-solid fa-filter me-2"></i>Lọc
                        </button>
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
                    <i class="fas fa-info-circle me-1"></i>Kéo thả để sắp xếp vị trí
                </div>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0 table-hover" id="categoriesTable">
                    <thead class="table-light">
                        <tr>
                            <th width="30"></th>
                            <th width="40"><i class="fas fa-grip-vertical"></i></th>
                            <th>Tên danh mục</th>
                            <th>Slug</th>
                            <th>Cấp độ</th>
                            <th>Danh mục cha</th>
                            <th>Số sản phẩm</th>
                            <th>Số danh mục con</th>
                            <th>Vị trí</th>
                            <th class="text-center" width="150">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="sortableCategories">
                        @forelse($categories as $category)
                            <tr data-id="{{ $category->id }}">
                                <td>
                                    <input type="checkbox" class="form-check-input category-checkbox"
                                        value="{{ $category->id }}">
                                </td>
                                <td class="drag-handle" style="cursor: move;">
                                    <i class="fas fa-grip-vertical text-muted"></i>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="category-icon me-2">
                                            <i class="fas fa-folder text-warning"></i>
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.categories.show', $category) }}"
                                                class="text-decoration-none text-dark fw-semibold">
                                                {{ $category->name }}
                                            </a>
                                            @if ($category->description)
                                                <small class="d-block text-muted">
                                                    {{ Str::limit($category->description, 50) }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">{{ $category->slug }}</code>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        Cấp {{ $category->level }}
                                    </span>
                                </td>
                                <td>
                                    @if ($category->parent)
                                        <a href="{{ route('admin.categories.show', $category->parent) }}"
                                            class="text-decoration-none">
                                            <i class="fas fa-level-up-alt me-1"></i>{{ $category->parent->name }}
                                        </a>
                                    @else
                                        <span class="text-muted"><i class="fas fa-home me-1"></i>Gốc</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($category->products_count > 0)
                                        <a href="{{ route('admin.products.index', ['category_id' => $category->id]) }}"
                                            class="badge bg-success text-decoration-none">
                                            <i class="fas fa-box me-1"></i>{{ $category->products_count }}
                                        </a>
                                    @else
                                        <span class="badge bg-light text-dark">0</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($category->children_count > 0)
                                        <span class="badge bg-primary">
                                            <i class="fas fa-sitemap me-1"></i>{{ $category->children_count }}
                                        </span>
                                    @else
                                        <span class="badge bg-light text-dark">0</span>
                                    @endif
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm position-input"
                                        style="width: 70px;" value="{{ $category->position }}"
                                        data-id="{{ $category->id }}">
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.categories.show', $category) }}"
                                            class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Xem">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.categories.edit', $category) }}"
                                            class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip"
                                            title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="confirmDelete({{ $category->id }})" data-bs-toggle="tooltip"
                                            title="Xóa">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                        class="d-none" id="deleteForm{{ $category->id }}">
                                        @csrf @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-5">
                                    <i class="fas fa-folder-open fa-3x mb-3 d-block"></i>
                                    <p class="mb-0">Không có danh mục nào</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Hiển thị {{ $categories->firstItem() ?? 0 }} - {{ $categories->lastItem() ?? 0 }}
                    trong tổng số {{ $categories->total() }} danh mục
                </div>
                {{ $categories->links('components.pagination') }}
            </div>
        </div>

    </div>

    {{-- Modal bulk actions --}}
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
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Bạn có chắc muốn xóa <strong><span id="deleteCount"></span></strong> danh mục đã chọn?
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger" id="executeBulkDelete">
                        <i class="fas fa-trash me-1"></i>Xóa
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Checkbox handling
            const selectAllCheckbox = document.getElementById('selectAll');
            const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
            const selectedCountSpan = document.getElementById('selectedCount');
            const bulkActionsBtn = document.getElementById('bulkActionsBtn');

            function updateSelectedCount() {
                const checked = document.querySelectorAll('.category-checkbox:checked').length;
                selectedCountSpan.textContent = `(${checked} mục được chọn)`;
                bulkActionsBtn.style.display = checked > 0 ? 'inline-block' : 'none';
            }

            selectAllCheckbox.addEventListener('change', function() {
                categoryCheckboxes.forEach(cb => cb.checked = this.checked);
                updateSelectedCount();
            });

            categoryCheckboxes.forEach(cb => {
                cb.addEventListener('change', updateSelectedCount);
            });

            // Bulk delete
            bulkActionsBtn.addEventListener('click', function() {
                const checkedIds = Array.from(document.querySelectorAll('.category-checkbox:checked'))
                    .map(cb => cb.value);
                document.getElementById('bulkIds').value = checkedIds.join(',');
                document.getElementById('deleteCount').textContent = checkedIds.length;
                new bootstrap.Modal(document.getElementById('bulkActionsModal')).show();
            });

            document.getElementById('executeBulkDelete').addEventListener('click', function() {
                const ids = document.getElementById('bulkIds').value.split(',');
                const btn = this;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';

                fetch('{{ route('admin.categories.bulk-delete') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            ids: ids
                        })
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
                            Swal.fire('Lỗi', data.message, 'error');
                            btn.disabled = false;
                            btn.innerHTML = '<i class="fas fa-trash me-1"></i>Xóa';
                        }
                    });
            });

            // Sortable
            new Sortable(document.getElementById('sortableCategories'), {
                handle: '.drag-handle',
                animation: 150,
                onEnd: function() {
                    const positions = {};
                    document.querySelectorAll('#sortableCategories tr').forEach((row, index) => {
                        const id = row.dataset.id;
                        if (id) positions[id] = index + 1;
                    });

                    fetch('{{ route('admin.categories.update-position') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            positions
                        })
                    });
                }
            });

            // Position input
            document.querySelectorAll('.position-input').forEach(input => {
                input.addEventListener('change', function() {
                    const id = this.dataset.id;
                    const position = this.value;

                    fetch('{{ route('admin.categories.update-position') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            positions: {
                                [id]: position
                            }
                        })
                    });
                });
            });

            // Delete confirmation
            function confirmDelete(id) {
                Swal.fire({
                    title: "Xác nhận xóa?",
                    text: "Danh mục này sẽ bị xóa vĩnh viễn!",
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

            // Toast notifications
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

            // Tooltips
            document.addEventListener('DOMContentLoaded', function() {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function(el) {
                    return new bootstrap.Tooltip(el);
                });
            });
        </script>
    @endpush

    @push('styles')
        <style>
            /* Giống style của products */
            body,
            table {
                font-family: 'Inter', 'Roboto', sans-serif;
                font-size: 15px;
            }

            .info-card {
                transition: transform 0.2s ease, box-shadow 0.2s ease;
                border-radius: 12px;
            }

            .info-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            }

            .bg-gradient-warning {
                background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
            }

            .bg-gradient-primary {
                background: linear-gradient(135deg, #4f46e5 0%, #6d28d9 100%);
            }

            .bg-gradient-success {
                background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);
            }

            .bg-gradient-secondary {
                background: linear-gradient(135deg, #64748b 0%, #94a3b8 100%);
            }

            thead {
                background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
                font-weight: 600;
            }

            tbody tr {
                transition: all 0.2s ease;
            }

            tbody tr:hover {
                background-color: #fef3c7;
            }

            .card {
                border-radius: 12px;
                animation: fadeIn 0.5s ease;
            }

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

            .drag-handle {
                opacity: 0.3;
                transition: opacity 0.2s;
            }

            tr:hover .drag-handle {
                opacity: 1;
            }

            .sortable-ghost {
                opacity: 0.4;
                background: #fef3c7;
            }
        </style>
    @endpush
@endsection
