@extends('layouts.admin')

@section('title', 'Thêm sản phẩm mới')

@section('content')
    <div class="container-fluid py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0"><i class="fas fa-plus-circle me-2 text-primary"></i>Thêm sản phẩm mới</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb small text-muted mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Sản phẩm</a></li>
                        <li class="breadcrumb-item active">Thêm mới</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
        </div>

        {{-- Form --}}
        <form action="{{ route('admin.products.store') }}" method="POST" id="productForm">
            @csrf
            <div class="row">
                {{-- LEFT --}}
                <div class="col-lg-8">

                    {{-- Thông tin sản phẩm --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thông tin cơ bản</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tên sản phẩm <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" id="productName" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Slug (URL thân thiện)</label>
                                <input type="text" name="slug" id="productSlug" class="form-control"
                                    placeholder="Tự động tạo nếu để trống">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Mô tả sản phẩm</label>
                                <textarea name="description" rows="5" class="form-control" placeholder="Nhập mô tả chi tiết..."></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Hình ảnh --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-images me-2"></i>Hình ảnh sản phẩm</h5>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                data-bs-target="#imageModal">
                                <i class="fas fa-plus me-1"></i>Chọn ảnh
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="selectedImagesContainer" class="row g-3 text-center text-muted py-5">
                                <i class="fas fa-image fa-3x mb-3 d-block opacity-25"></i>
                                <p>Chưa có ảnh nào được chọn</p>
                            </div>
                            <input type="hidden" name="image_ids" id="imageIdsInput">
                            <input type="hidden" name="primary_image_id" id="primaryImageId">
                        </div>
                    </div>
                </div>

                {{-- RIGHT --}}
                <div class="col-lg-4">

                    {{-- Giá & Trạng thái --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-dollar-sign me-2"></i>Giá & Trạng thái</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Giá sản phẩm <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="price" class="form-control" min="0" required>
                                    <span class="input-group-text">đ</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Trạng thái</label>
                                <select name="status" class="form-select">
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Danh mục --}}
                    {{-- <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-folder me-2"></i>Danh mục</h5>
                        </div>
                        <div class="card-body category-checkboxes" style="max-height: 300px; overflow-y:auto;">
                            @foreach ($categories as $category)
                                <div class="form-check mb-2">
                                    <input class="form-check-input category-toggle" type="checkbox"
                                        id="cat{{ $category->id }}" value="{{ $category->id }}"
                                        data-category-id="{{ $category->id }}" name="category_ids[]">
                                    <label class="form-check-label fw-semibold"
                                        for="cat{{ $category->id }}">{{ $category->name }}</label>

                                    {{-- Danh mục con --}}
                    {{-- @if ($category->children->count())
                                        <div class="ms-4 mt-2 category-children" id="children-{{ $category->id }}"
                                            style="display: none;">
                                            @foreach ($category->children as $child)
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="cat{{ $child->id }}" value="{{ $child->id }}"
                                                        name="category_ids[]">
                                                    <label class="form-check-label"
                                                        for="cat{{ $child->id }}">{{ $child->name }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div> --}}
                    {{-- Danh mục --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-folder me-2"></i>Danh mục</h5>
                        </div>
                        <div class="card-body category-checkboxes" style="max-height: 300px; overflow-y:auto;">
                            @foreach ($categories as $category)
                                @php
                                    // Kiểm tra xem category này có được pre-select không
                                    $isChecked = in_array(
                                        $category->id,
                                        old('category_ids', $selectedCategoryId ? [$selectedCategoryId] : []),
                                    );
                                @endphp

                                <div class="form-check mb-2">
                                    <input class="form-check-input category-toggle" type="checkbox"
                                        id="cat{{ $category->id }}" value="{{ $category->id }}"
                                        data-category-id="{{ $category->id }}" name="category_ids[]"
                                        {{ $isChecked ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold"
                                        for="cat{{ $category->id }}">{{ $category->name }}</label>

                                    {{-- Danh mục con --}}
                                    @if ($category->children->count())
                                        <div class="ms-4 mt-2 category-children" id="children-{{ $category->id }}"
                                            style="display: {{ $isChecked ? 'block' : 'none' }};">
                                            @foreach ($category->children as $child)
                                                @php
                                                    $isChildChecked = in_array(
                                                        $child->id,
                                                        old(
                                                            'category_ids',
                                                            $selectedCategoryId ? [$selectedCategoryId] : [],
                                                        ),
                                                    );
                                                @endphp
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="cat{{ $child->id }}" value="{{ $child->id }}"
                                                        name="category_ids[]" {{ $isChildChecked ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="cat{{ $child->id }}">{{ $child->name }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Action --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-save me-2"></i>Lưu sản phẩm
                            </button>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary w-100">
                                <i class="fas fa-times me-2"></i>Hủy bỏ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Modal chọn ảnh --}}
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="imageModalLabel"><i class="fas fa-images me-2"></i>Chọn ảnh sản phẩm</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="imageLibrary" class="row g-3 text-center text-muted py-5">
                        <i class="fas fa-spinner fa-spin fa-2x mb-3 d-block"></i>
                        <p>Đang tải thư viện ảnh...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="confirmImageSelection" class="btn btn-success"><i
                            class="fas fa-check me-2"></i>Xác nhận</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                            class="fas fa-times me-2"></i>Đóng</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            // ===== 1️⃣ SLUG TỰ ĐỘNG =====
            const nameInput = document.getElementById('productName');
            const slugInput = document.getElementById('productSlug');
            if (nameInput && slugInput) {
                nameInput.addEventListener('input', e => {
                    let slug = e.target.value.toLowerCase()
                        .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                        .replace(/đ/g, 'd')
                        .replace(/[^a-z0-9\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-')
                        .replace(/^-+|-+$/g, '');
                    slugInput.value = slug;
                });
            }

            // ===== 2️⃣ QUẢN LÝ DANH MỤC CHA - CON =====
            const categoryContainer = document.querySelector('.category-checkboxes');
            if (categoryContainer) {
                const selectedIds = JSON.parse(categoryContainer.dataset.selected || '[]');

                // Tick danh mục đã chọn khi edit
                selectedIds.forEach(id => {
                    const checkbox = document.getElementById(`cat${id}`);
                    if (!checkbox) return;
                    checkbox.checked = true;

                    // Nếu là con
                    const parentContainer = checkbox.closest('.category-children');
                    if (parentContainer) {
                        parentContainer.style.display = 'block';
                        const parentId = parentContainer.id.replace('children-', '');
                        const parentCheckbox = document.querySelector(
                            `.category-toggle[data-category-id="${parentId}"]`);
                        if (parentCheckbox) parentCheckbox.checked = true;
                    }

                    // Nếu là cha
                    const childrenContainer = document.getElementById(`children-${id}`);
                    if (childrenContainer) childrenContainer.style.display = 'block';
                });

                // Check/uncheck cha => mở/ẩn con
                document.querySelectorAll('.category-toggle').forEach(toggle => {
                    toggle.addEventListener('change', function() {
                        const categoryId = this.dataset.categoryId;
                        const childrenContainer = document.getElementById(`children-${categoryId}`);
                        if (!childrenContainer) return;
                        if (this.checked) {
                            childrenContainer.style.display = 'block';
                        } else {
                            childrenContainer.style.display = 'none';
                            childrenContainer.querySelectorAll('input[type="checkbox"]').forEach(
                                child => child.checked = false);
                        }
                    });
                });

                // Check/uncheck con => tự động check cha
                document.querySelectorAll('.category-children input[type="checkbox"]').forEach(child => {
                    child.addEventListener('change', function() {
                        const parentContainer = this.closest('.category-children');
                        if (!parentContainer) return;
                        const parentId = parentContainer.id.replace('children-', '');
                        const parentCheckbox = document.querySelector(
                            `.category-toggle[data-category-id="${parentId}"]`);
                        if (!parentCheckbox) return;

                        const anyChildChecked = parentContainer.querySelectorAll(
                            'input[type="checkbox"]:checked').length > 0;
                        parentCheckbox.checked = anyChildChecked;
                        parentContainer.style.display = anyChildChecked ? 'block' : 'none';
                    });
                });
            }

            // ===== 3️⃣ QUẢN LÝ ẢNH SẢN PHẨM =====
            const imageLibrary = document.getElementById('imageLibrary');
            const selectedImagesContainer = document.getElementById('selectedImagesContainer');
            const imageIdsInput = document.getElementById('imageIdsInput');
            const primaryImageIdInput = document.getElementById('primaryImageId');
            let selectedImages = [];

            async function loadImages() {
                const res = await fetch('{{ route('admin.images.api.list') }}');
                const data = await res.json();
                imageLibrary.innerHTML = '';
                data.data.forEach(img => {
                    const div = document.createElement('div');
                    div.className = 'col-6 col-md-4 col-lg-3';
                    div.innerHTML =
                        `<div class="select-image" data-id="${img.id}" data-url="${img.url}"><img src="${img.url}" alt=""></div>`;
                    imageLibrary.appendChild(div);
                });
            }
            loadImages();

            imageLibrary.addEventListener('click', e => {
                const card = e.target.closest('.select-image');
                if (!card) return;
                const id = card.dataset.id;
                card.classList.toggle('selected');
                selectedImages.includes(id) ? selectedImages = selectedImages.filter(x => x !== id) :
                    selectedImages.push(id);
            });

            document.getElementById('confirmImageSelection').addEventListener('click', () => {
                if (!primaryImageIdInput.value && selectedImages.length) primaryImageIdInput.value =
                    selectedImages[0];
                imageIdsInput.value = selectedImages.join(',');
                renderSelectedImages();
                bootstrap.Modal.getInstance(document.getElementById('imageModal')).hide();
            });

            function renderSelectedImages() {
                selectedImagesContainer.innerHTML = '';
                if (!selectedImages.length) {
                    selectedImagesContainer.innerHTML =
                        `<div class="text-center text-muted py-5 w-100"><i class="fas fa-image fa-3x mb-3 d-block opacity-25"></i><p>Chưa có ảnh nào được chọn</p></div>`;
                    return;
                }
                const primaryId = primaryImageIdInput.value || selectedImages[0];
                primaryImageIdInput.value = primaryId;
                selectedImages.forEach(id => {
                    const img = document.querySelector(`.select-image[data-id="${id}"] img`);
                    if (!img) return;
                    const isPrimary = id === primaryId;
                    const card = document.createElement('div');
                    card.className = 'col-6 col-md-4 col-lg-3';
                    card.innerHTML = `
                <div class="selected-image-card shadow-sm border-0 position-relative">
                    <img src="${img.src}" alt="Ảnh sản phẩm">
                    ${isPrimary ? `<span class="badge-primary-image">Ảnh chính</span>` : ''}
                    <button type="button" class="btn-remove" data-id="${id}"><i class="fas fa-times"></i></button>
                    ${!isPrimary ? `<button type="button" class="btn-primary-flag" data-id="${id}">Đặt ảnh chính</button>` : ''}
                </div>`;
                    selectedImagesContainer.appendChild(card);
                });
                imageIdsInput.value = selectedImages.join(',');
            }

            selectedImagesContainer.addEventListener('click', e => {
                const btnRemove = e.target.closest('.btn-remove');
                const btnPrimary = e.target.closest('.btn-primary-flag');
                if (btnRemove) {
                    const id = btnRemove.dataset.id;
                    selectedImages = selectedImages.filter(x => x !== id);
                    if (primaryImageIdInput.value === id) primaryImageIdInput.value = selectedImages[0] ||
                        '';
                    renderSelectedImages();
                }
                if (btnPrimary) {
                    primaryImageIdInput.value = btnPrimary.dataset.id;
                    renderSelectedImages();
                }
            });

            // ===== 4️⃣ VALIDATION FORM =====
            const form = document.getElementById('productForm');
            const priceInput = form.querySelector('input[name="price"]');

            form.addEventListener('submit', e => {
                const name = nameInput.value.trim();
                const price = parseFloat(priceInput.value);

                if (!name) {
                    e.preventDefault();
                    nameInput.focus();
                    Swal.fire({
                        icon: 'error',
                        title: 'Thiếu thông tin',
                        text: 'Vui lòng nhập tên sản phẩm!',
                        confirmButtonColor: '#4f46e5'
                    });
                    return false;
                }
                if (isNaN(price) || price < 0) {
                    e.preventDefault();
                    priceInput.focus();
                    Swal.fire({
                        icon: 'error',
                        title: 'Giá không hợp lệ',
                        text: 'Vui lòng nhập giá hợp lệ (≥ 0)!',
                        confirmButtonColor: '#4f46e5'
                    });
                    return false;
                }
                if (!selectedImages.length) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Chưa chọn ảnh',
                        text: 'Vui lòng chọn ít nhất một ảnh cho sản phẩm!',
                        confirmButtonColor: '#4f46e5'
                    });
                    return false;
                }

                Swal.fire({
                    title: 'Đang lưu sản phẩm...',
                    text: 'Vui lòng đợi trong giây lát',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
            });

            // ===== 5️⃣ FLASH MESSAGES =====
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                    toast: true,
                    position: 'top-end'
                });
            @endif
            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    html: `{!! implode('<br>', $errors->all()) !!}`,
                    confirmButtonColor: '#4f46e5'
                });
            @endif

        });
    </script>
@endpush
@push('styles')
    <style>
        /* --- ẢNH TRONG MODAL --- */
        .select-image {
            position: relative;
            cursor: pointer;
            border-radius: 10px;
            overflow: hidden;
            border: 2px solid transparent;
            transition: 0.2s;
            aspect-ratio: 1 / 1;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .select-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
            display: block;
        }

        .select-image.selected {
            border-color: #198754;
            box-shadow: 0 0 0 3px rgba(25, 135, 84, .3);
        }

        .select-image.selected::after {
            content: '✓';
            position: absolute;
            top: 8px;
            right: 8px;
            background: #198754;
            color: #fff;
            font-weight: bold;
            border-radius: 50%;
            width: 26px;
            height: 26px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        /* --- ẢNH ĐÃ CHỌN TRONG FORM --- */
        .selected-image-card {
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            aspect-ratio: 1 / 1;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .selected-image-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
        }

        .btn-remove,
        .btn-primary-flag {
            position: absolute;
            border: none;
            background: rgba(0, 0, 0, 0.6);
            color: white;
            cursor: pointer;
            transition: 0.2s;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-remove:hover,
        .btn-primary-flag:hover {
            background: rgba(0, 0, 0, 0.8);
        }

        .btn-remove {
            top: 6px;
            right: 6px;
            border-radius: 50%;
            width: 28px;
            height: 28px;
        }

        .btn-primary-flag {
            bottom: 6px;
            left: 6px;
            border-radius: 8px;
            width: auto;
            height: auto;
            padding: 2px 6px;
            font-size: 12px;
        }

        .badge-primary-image {
            position: absolute;
            top: 6px;
            left: 6px;
            background: #198754;
            color: #fff;
            padding: 3px 6px;
            border-radius: 6px;
            font-size: 12px;
        }

        /* --- DANH MỤC CON --- */
        .category-children {
            transition: all 0.3s ease;
            overflow: hidden;
        }

        /* --- GRID CHO MODAL VÀ FORM --- */
        #imageLibrary .col-6,
        #selectedImagesContainer .col-6 {
            padding: 8px;
        }

        #imageLibrary {
            display: flex;
            flex-wrap: wrap;
        }
    </style>
@endpush
