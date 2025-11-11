@extends('layouts.admin')

@section('title', 'Chỉnh sửa danh mục')

@section('content')
    <div class="container-fluid py-4">

        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Danh mục</a></li>
                <li class="breadcrumb-item active">{{ $category->name }}</li>
            </ol>
        </nav>

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">
                    <i class="fas fa-pen-to-square me-2 text-warning"></i>Chỉnh sửa: {{ $category->name }}
                </h2>
                <p class="text-muted mb-0">Cập nhật thông tin danh mục</p>
            </div>
            <div>
                <a href="{{ route('admin.categories.show', $category) }}" class="btn btn-outline-info me-2">
                    <i class="fas fa-eye me-2"></i>Xem
                </a>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                {{-- Form Card --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-gradient-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thông tin cơ bản</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.categories.update', $category) }}" method="POST" id="categoryForm">
                            @csrf
                            @method('PUT')

                            {{-- Tên danh mục --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold required">
                                    <i class="fas fa-tag me-1 text-warning"></i>Tên danh mục
                                </label>
                                <input type="text" name="name" id="name"
                                    class="form-control form-control-lg @error('name') is-invalid @enderror"
                                    value="{{ old('name', $category->name) }}" required autofocus>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Slug --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold required">
                                    <i class="fas fa-link me-1 text-warning"></i>Đường dẫn (Slug)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-globe"></i>
                                    </span>
                                    <input type="text" name="slug" id="slug"
                                        class="form-control @error('slug') is-invalid @enderror"
                                        value="{{ old('slug', $category->slug) }}" required>
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Mô tả --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-align-left me-1 text-warning"></i>Mô tả
                                </label>
                                <textarea name="description" id="description" rows="4"
                                    class="form-control @error('description') is-invalid @enderror">{{ old('description', $category->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <span id="charCount">{{ strlen($category->description ?? '') }}</span>/1000 ký tự
                                </small>
                            </div>

                            {{-- Danh mục cha --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-sitemap me-1 text-warning"></i>Danh mục cha
                                </label>
                                <select name="parent_id" id="parent_id"
                                    class="form-select @error('parent_id') is-invalid @enderror">
                                    <option value="">-- Không có (Danh mục gốc) --</option>
                                    @foreach ($parentCategories as $parent)
                                        @if ($parent->id != $category->id)
                                            <option value="{{ $parent->id }}"
                                                {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                                                {{ $parent->name }}
                                            </option>
                                            @if ($parent->children->count() > 0)
                                                @foreach ($parent->children as $child)
                                                    @if ($child->id != $category->id)
                                                        <option value="{{ $child->id }}"
                                                            {{ old('parent_id', $category->parent_id) == $child->id ? 'selected' : '' }}>
                                                            &nbsp;&nbsp;&nbsp;└─ {{ $child->name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        @endif
                                    @endforeach
                                </select>
                                @error('parent_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if ($category->children->count() > 0)
                                    <div class="alert alert-info mt-2 mb-0">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Danh mục này có <strong>{{ $category->children->count() }}</strong> danh mục con
                                    </div>
                                @endif
                            </div>

                            {{-- Vị trí --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-sort-numeric-down me-1 text-warning"></i>Vị trí sắp xếp
                                </label>
                                <input type="number" name="position"
                                    class="form-control @error('position') is-invalid @enderror"
                                    value="{{ old('position', $category->position) }}" min="0">
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Action Buttons --}}
                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                                    <i class="fas fa-trash me-2"></i>Xóa danh mục
                                </button>
                                <div>
                                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary me-2">
                                        <i class="fas fa-times me-2"></i>Hủy bỏ
                                    </a>
                                    <button type="submit" class="btn btn-warning text-dark">
                                        <i class="fas fa-save me-2"></i>Cập nhật
                                    </button>
                                </div>
                            </div>
                        </form>

                        {{-- Delete Form --}}
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-none"
                            id="deleteForm">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>

                {{-- Products in Category --}}
                @if ($category->products->count() > 0)
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="fas fa-box-open me-2 text-success"></i>
                                Sản phẩm trong danh mục ({{ $category->products->count() }})
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Ảnh</th>
                                            <th>Tên sản phẩm</th>
                                            <th>Giá</th>
                                            <th>Trạng thái</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($category->products->take(5) as $product)
                                            <tr>
                                                <td>
                                                    @php
                                                        $img = $product->images->first();
                                                    @endphp
                                                    @if ($img)
                                                        <img src="{{ asset('storage/' . $img->path) }}" class="rounded"
                                                            style="width:40px;height:40px;object-fit:cover;">
                                                    @endif
                                                </td>
                                                <td>{{ $product->name }}</td>
                                                <td>{{ number_format($product->price, 0, ',', '.') }}đ</td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $product->status->value == 'active' ? 'success' : 'secondary' }}">
                                                        {{ $product->status->label() }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.products.edit', $product) }}"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if ($category->products->count() > 5)
                                <div class="text-center mt-3">
                                    <a href="{{ route('admin.products.index', ['category_id' => $category->id]) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        Xem tất cả {{ $category->products->count() }} sản phẩm
                                        <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                {{-- Info Card --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-info-circle text-warning me-2"></i>Thông tin</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-3 pb-3 border-bottom">
                                <small class="text-muted d-block mb-1">ID</small>
                                <strong>#{{ $category->id }}</strong>
                            </li>
                            <li class="mb-3 pb-3 border-bottom">
                                <small class="text-muted d-block mb-1">Cấp độ</small>
                                <span class="badge bg-info">Cấp {{ $category->level }}</span>
                            </li>
                            <li class="mb-3 pb-3 border-bottom">
                                <small class="text-muted d-block mb-1">Số sản phẩm</small>
                                <strong class="text-success">{{ $category->products->count() }}</strong>
                            </li>
                            <li class="mb-3 pb-3 border-bottom">
                                <small class="text-muted d-block mb-1">Số danh mục con</small>
                                <strong class="text-primary">{{ $category->children->count() }}</strong>
                            </li>
                            <li class="mb-3 pb-3 border-bottom">
                                <small class="text-muted d-block mb-1">Ngày tạo</small>
                                <strong>{{ $category->created_at->format('d/m/Y H:i') }}</strong>
                            </li>
                            <li>
                                <small class="text-muted d-block mb-1">Cập nhật lần cuối</small>
                                <strong>{{ $category->updated_at->format('d/m/Y H:i') }}</strong>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Children Categories --}}
                @if ($category->children->count() > 0)
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-sitemap text-primary me-2"></i>Danh mục con
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                @foreach ($category->children as $child)
                                    <li class="mb-2 pb-2 border-bottom">
                                        <a href="{{ route('admin.categories.edit', $child) }}"
                                            class="text-decoration-none">
                                            <i class="fas fa-folder me-2 text-warning"></i>
                                            {{ $child->name }}
                                        </a>
                                        <small class="text-muted d-block ms-4">
                                            {{ $child->products->count() }} sản phẩm
                                        </small>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Character counter
            const descInput = document.getElementById('description');
            const charCount = document.getElementById('charCount');

            descInput.addEventListener('input', function() {
                charCount.textContent = this.value.length;
            });

            // Auto update slug
            document.getElementById('name').addEventListener('input', function() {
                const slug = generateSlug(this.value);
                document.getElementById('slug').value = slug;
            });

            function generateSlug(text) {
                return text
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/đ/g, 'd')
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }

            // Delete confirmation
            function confirmDelete() {
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
                        document.getElementById('deleteForm').submit();
                    }
                });
            }

            // Success message
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
        </script>
    @endpush

    @push('styles')
        <style>
            .required::after {
                content: ' *';
                color: #ef4444;
            }

            .form-control:focus,
            .form-select:focus {
                border-color: #f59e0b;
                box-shadow: 0 0 0 0.2rem rgba(245, 158, 11, 0.25);
            }

            .bg-gradient-warning {
                background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
            }

            .card {
                animation: fadeIn 0.5s ease;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>
    @endpush
@endsection
