@extends('layouts.admin')

@section('title', 'Chỉnh sửa bài viết')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-lg-8">
                <form action="{{ route('admin.blogs.update', $blog) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-gradient-warning text-white">
                            <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Chỉnh sửa bài viết</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold required">Tiêu đề</label>
                                <input type="text" name="title"
                                    class="form-control @error('title') is-invalid @enderror"
                                    value="{{ old('title', $blog->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Slug</label>
                                <input type="text" name="slug"
                                    class="form-control @error('slug') is-invalid @enderror"
                                    value="{{ old('slug', $blog->slug) }}">
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold required">Nội dung</label>
                                <textarea name="content" id="editor" class="form-control" rows="15" required>{{ old('content', $blog->content) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mb-4">
                        <button type="submit" class="btn btn-warning btn-lg">
                            <i class="fas fa-save me-2"></i>Cập nhật
                        </button>
                        <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                    </div>
                </form>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-bold">Trạng thái</h6>
                    </div>
                    <div class="card-body">
                        <select name="status" class="form-select" required>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}"
                                    {{ $blog->status->value == $status->value ? 'selected' : '' }}>
                                    {{ ucfirst($status->value) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-bold">Danh mục</h6>
                    </div>
                    <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                        @foreach ($categories as $category)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="categories[]"
                                    value="{{ $category->id }}" id="cat{{ $category->id }}"
                                    {{ $blog->categories->contains($category->id) ? 'checked' : '' }}>
                                <label class="form-check-label" for="cat{{ $category->id }}">
                                    {{ $category->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-bold">Ảnh đại diện</h6>
                    </div>
                    <div class="card-body">
                        @php
                            $primaryImage = $blog->images->firstWhere('pivot.is_main', true);
                        @endphp
                        @if ($primaryImage)
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $primaryImage->path) }}" class="img-fluid rounded"
                                    style="max-height: 200px;">
                            </div>
                        @endif
                        <input type="file" name="primary_image" class="form-control" accept="image/*" id="imageInput">
                        <div id="imagePreview" class="text-center d-none mt-3">
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
            ClassicEditor.create(document.querySelector('#editor'));

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
