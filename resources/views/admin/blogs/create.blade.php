@extends('layouts.admin')

@section('title', 'Thêm bài viết mới')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-lg-8">
                <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-gradient-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Thông tin bài viết</h5>
                        </div>
                        <div class="card-body">
                            {{-- Title --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold required">Tiêu đề</label>
                                <input type="text" name="title"
                                    class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}"
                                    required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Slug --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Slug (URL thân thiện)</label>
                                <input type="text" name="slug"
                                    class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}"
                                    placeholder="Tự động tạo nếu bỏ trống">
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Content --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold required">Nội dung</label>
                                <textarea name="content" id="editor" class="form-control @error('content') is-invalid @enderror" rows="15"
                                    required>{{ old('content') }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- SEO Section --}}
                            <div class="border-top pt-4">
                                <h6 class="fw-bold mb-3"><i class="fas fa-search me-2 text-primary"></i>Tối ưu SEO</h6>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Meta Title</label>
                                    <input type="text" name="meta_title" class="form-control"
                                        value="{{ old('meta_title') }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Meta Description</label>
                                    <textarea name="meta_description" class="form-control" rows="3">{{ old('meta_description') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="d-flex gap-2 mb-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>Lưu bài viết
                        </button>
                        <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>Hủy
                        </a>
                    </div>
                </form>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                {{-- Status --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-flag me-2"></i>Trạng thái</h6>
                    </div>
                    <div class="card-body">
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}"
                                    {{ old('status') == $status->value ? 'selected' : '' }}>
                                    {{ ucfirst($status->value) }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Categories --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-folder me-2"></i>Danh mục</h6>
                    </div>
                    <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                        @foreach ($categories as $category)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="categories[]"
                                    value="{{ $category->id }}" id="cat{{ $category->id }}"
                                    {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="cat{{ $category->id }}">
                                    {{ $category->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Featured Image --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-image me-2"></i>Ảnh đại diện</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="file" name="primary_image"
                                class="form-control @error('primary_image') is-invalid @enderror" accept="image/*"
                                id="imageInput">
                            @error('primary_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div id="imagePreview" class="text-center d-none">
                            <img src="" class="img-fluid rounded" style="max-height: 200px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
        <script>
            // CKEditor
            ClassicEditor.create(document.querySelector('#editor'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'blockQuote',
                    'insertTable', '|', 'undo', 'redo'
                ]
            });

            // Auto slug generation
            document.querySelector('input[name="title"]').addEventListener('input', function(e) {
                const slugInput = document.querySelector('input[name="slug"]');
                if (!slugInput.value) {
                    const slug = e.target.value
                        .toLowerCase()
                        .replace(/[^\w\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-');
                    slugInput.value = slug;
                }
            });

            // Image preview
            document.getElementById('imageInput').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById('imagePreview');
                        preview.querySelector('img').src = e.target.result;
                        preview.classList.remove('d-none');
                    };
                    reader.readAsDataURL(file);
                }
            });
        </script>
    @endpush

@endsection
