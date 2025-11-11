@extends('layouts.admin')

@section('title', 'Thêm danh mục')

@section('content')
    <div class="container-fluid py-4">

        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Danh mục</a></li>
                <li class="breadcrumb-item active">Thêm mới</li>
            </ol>
        </nav>

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">
                    <i class="fas fa-folder-plus me-2 text-warning"></i>Thêm danh mục mới
                </h2>
                <p class="text-muted mb-0">Tạo danh mục sản phẩm mới cho hệ thống</p>
            </div>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                {{-- Form Card --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-gradient-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thông tin cơ bản</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.categories.store') }}" method="POST" id="categoryForm">
                            @csrf

                            {{-- Tên danh mục --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold required">
                                    <i class="fas fa-tag me-1 text-warning"></i>Tên danh mục
                                </label>
                                <input type="text" name="name" id="name"
                                    class="form-control form-control-lg @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}" placeholder="VD: Điện thoại, Laptop..." required autofocus>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Tên hiển thị của danh mục trên website</small>
                            </div>

                            {{-- Slug --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-link me-1 text-warning"></i>Đường dẫn (Slug)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-globe"></i>
                                    </span>
                                    <input type="text" name="slug" id="slug"
                                        class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}"
                                        placeholder="tu-dong-tao-neu-de-trong">
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">
                                    Để trống để tự động tạo từ tên danh mục
                                </small>
                            </div>

                            {{-- Mô tả --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-align-left me-1 text-warning"></i>Mô tả
                                </label>
                                <textarea name="description" id="description" rows="4"
                                    class="form-control @error('description') is-invalid @enderror" placeholder="Mô tả ngắn về danh mục này...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <span id="charCount">0</span>/1000 ký tự
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
                                        <option value="{{ $parent->id }}"
                                            {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->name }}
                                        </option>
                                        @if ($parent->children->count() > 0)
                                            @foreach ($parent->children as $child)
                                                <option value="{{ $child->id }}"
                                                    {{ old('parent_id') == $child->id ? 'selected' : '' }}>
                                                    &nbsp;&nbsp;&nbsp;└─ {{ $child->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </select>
                                @error('parent_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Chọn danh mục cha nếu đây là danh mục con
                                </small>
                            </div>

                            {{-- Vị trí --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-sort-numeric-down me-1 text-warning"></i>Vị trí sắp xếp
                                </label>
                                <input type="number" name="position" id="position"
                                    class="form-control @error('position') is-invalid @enderror"
                                    value="{{ old('position', 0) }}" min="0" placeholder="0">
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Số thứ tự hiển thị (càng nhỏ càng ưu tiên)
                                </small>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Hủy bỏ
                                </a>
                                <div>
                                    <button type="reset" class="btn btn-outline-warning me-2">
                                        <i class="fas fa-redo me-2"></i>Làm mới
                                    </button>
                                    <button type="submit" class="btn btn-warning text-dark">
                                        <i class="fas fa-save me-2"></i>Lưu danh mục
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                {{-- Tips Card --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-lightbulb text-warning me-2"></i>Hướng dẫn</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Lưu ý:</strong>
                        </div>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Tên danh mục nên ngắn gọn, dễ hiểu
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Slug sẽ được tự động tạo từ tên
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Có thể tạo danh mục con tối đa 3 cấp
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Vị trí giúp sắp xếp thứ tự hiển thị
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Preview Card --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-eye text-warning me-2"></i>Preview</h6>
                    </div>
                    <div class="card-body">
                        <div class="preview-box p-3 bg-light rounded">
                            <div class="mb-2">
                                <small class="text-muted">Tên:</small>
                                <div id="previewName" class="fw-bold">---</div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Slug:</small>
                                <div id="previewSlug" class="text-primary">---</div>
                            </div>
                            <div>
                                <small class="text-muted">Danh mục cha:</small>
                                <div id="previewParent">Không có</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            // Auto generate slug
            const nameInput = document.getElementById('name');
            const slugInput = document.getElementById('slug');
            const previewName = document.getElementById('previewName');
            const previewSlug = document.getElementById('previewSlug');

            nameInput.addEventListener('input', function() {
                const name = this.value;
                previewName.textContent = name || '---';

                if (!slugInput.dataset.manual) {
                    const slug = generateSlug(name);
                    slugInput.value = slug;
                    previewSlug.textContent = slug || '---';
                }
            });

            slugInput.addEventListener('input', function() {
                this.dataset.manual = 'true';
                previewSlug.textContent = this.value || '---';
            });

            // Preview parent
            document.getElementById('parent_id').addEventListener('change', function() {
                const selectedText = this.options[this.selectedIndex].text;
                document.getElementById('previewParent').textContent = selectedText || 'Không có';
            });

            // Character counter
            const descInput = document.getElementById('description');
            const charCount = document.getElementById('charCount');

            descInput.addEventListener('input', function() {
                charCount.textContent = this.value.length;
            });

            // Generate slug function
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

            // Form validation
            document.getElementById('categoryForm').addEventListener('submit', function(e) {
                const name = nameInput.value.trim();
                if (name.length < 2) {
                    e.preventDefault();
                    alert('Tên danh mục phải có ít nhất 2 ký tự');
                    nameInput.focus();
                }
            });
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

            .preview-box {
                border: 2px dashed #e5e7eb;
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
