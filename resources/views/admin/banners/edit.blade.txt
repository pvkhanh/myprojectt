@extends('layouts.admin')

@section('title', 'Chỉnh sửa banner')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="card border-0 shadow-lg mb-4">
                        <div class="card-header bg-gradient-warning text-white">
                            <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Chỉnh sửa banner</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <label class="form-label fw-semibold required">Tiêu đề banner</label>
                                <input type="text" name="title"
                                    class="form-control form-control-lg @error('title') is-invalid @enderror"
                                    value="{{ old('title', $banner->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">URL liên kết</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-link"></i></span>
                                    <input type="url" name="url" class="form-control"
                                        value="{{ old('url', $banner->url) }}">
                                </div>
                            </div>

                            {{-- Current Image --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Ảnh hiện tại</label>
                                <div class="border rounded p-3 bg-light text-center">
                                    <img src="{{ asset('storage/' . $banner->image_path) }}"
                                        class="img-fluid rounded shadow" style="max-height: 250px;">
                                </div>
                            </div>

                            {{-- New Image Upload --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Thay đổi ảnh (Tùy chọn)</label>
                                <div class="border-dashed border-2 rounded p-4 text-center bg-light" id="dropZone">
                                    <input type="file" name="image" class="d-none" accept="image/*" id="imageInput">
                                    <div id="dropZoneContent">
                                        <i class="fas fa-cloud-upload-alt fa-3x text-warning mb-3"></i>
                                        <p class="mb-2 fw-semibold">Kéo thả ảnh mới vào đây hoặc <a href="#"
                                                id="browseBtn" class="text-warning">chọn file</a></p>
                                        <small class="text-muted">PNG, JPG hoặc GIF (tối đa 2MB)</small>
                                    </div>
                                    <div id="imagePreview" class="d-none">
                                        <img src="" class="img-fluid rounded shadow" style="max-height: 300px;">
                                        <div class="mt-3">
                                            <button type="button" class="btn btn-sm btn-outline-danger" id="removeImage">
                                                <i class="fas fa-times me-1"></i>Xóa ảnh mới
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Loại banner</label>
                                    <select name="type" class="form-select">
                                        <option value="">-- Chọn loại --</option>
                                        <option value="hero" {{ old('type', $banner->type) == 'hero' ? 'selected' : '' }}>
                                            Hero</option>
                                        <option value="sidebar"
                                            {{ old('type', $banner->type) == 'sidebar' ? 'selected' : '' }}>Sidebar</option>
                                        <option value="popup"
                                            {{ old('type', $banner->type) == 'popup' ? 'selected' : '' }}>Popup</option>
                                        <option value="footer"
                                            {{ old('type', $banner->type) == 'footer' ? 'selected' : '' }}>Footer</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Vị trí hiển thị</label>
                                    <input type="number" name="position" class="form-control"
                                        value="{{ old('position', $banner->position) }}" min="0">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold d-block">Trạng thái</label>
                                    <div class="form-check form-switch form-switch-lg mt-2">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="isActive"
                                            {{ old('is_active', $banner->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="isActive">Kích hoạt</label>
                                    </div>
                                </div>
                            </div>

                            <div class="border-top mt-4 pt-4">
                                <h6 class="fw-bold mb-3"><i class="fas fa-calendar-alt me-2 text-warning"></i>Lên lịch hiển
                                    thị</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Bắt đầu từ</label>
                                        <input type="datetime-local" name="start_at" class="form-control"
                                            value="{{ old('start_at', $banner->start_at?->format('Y-m-d\TH:i')) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Kết thúc lúc</label>
                                        <input type="datetime-local" name="end_at" class="form-control"
                                            value="{{ old('end_at', $banner->end_at?->format('Y-m-d\TH:i')) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-between mb-4">
                        <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                        <button type="submit" class="btn btn-warning btn-lg">
                            <i class="fas fa-save me-2"></i>Cập nhật banner
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Same image upload logic as create
            const dropZone = document.getElementById('dropZone');
            const imageInput = document.getElementById('imageInput');
            const browseBtn = document.getElementById('browseBtn');
            const dropZoneContent = document.getElementById('dropZoneContent');
            const imagePreview = document.getElementById('imagePreview');
            const removeImageBtn = document.getElementById('removeImage');

            browseBtn.addEventListener('click', (e) => {
                e.preventDefault();
                imageInput.click();
            });

            imageInput.addEventListener('change', handleImageSelect);

            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.classList.add('border-warning', 'bg-warning-subtle');
            });

            dropZone.addEventListener('dragleave', () => {
                dropZone.classList.remove('border-warning', 'bg-warning-subtle');
            });

            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.classList.remove('border-warning', 'bg-warning-subtle');

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    imageInput.files = files;
                    handleImageSelect();
                }
            });

            function handleImageSelect() {
                const file = imageInput.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        imagePreview.querySelector('img').src = e.target.result;
                        dropZoneContent.classList.add('d-none');
                        imagePreview.classList.remove('d-none');
                    };
                    reader.readAsDataURL(file);
                }
            }

            removeImageBtn.addEventListener('click', () => {
                imageInput.value = '';
                dropZoneContent.classList.remove('d-none');
                imagePreview.classList.add('d-none');
            });
        </script>
    @endpush

    @push('styles')
        <style>
            .border-dashed {
                border-style: dashed !important;
            }

            .form-switch-lg .form-check-input {
                width: 3rem;
                height: 1.5rem;
            }

            #dropZone {
                transition: all 0.3s ease;
                cursor: pointer;
            }
        </style>
    @endpush

@endsection
