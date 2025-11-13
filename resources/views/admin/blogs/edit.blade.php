@extends('layouts.admin')

@section('title', 'Chỉnh sửa bài viết')

@section('content')
    <div class="blog-edit-container">
        <form action="{{ route('admin.blogs.update', $blog) }}" method="POST" enctype="multipart/form-data" id="blogForm">
            @csrf
            @method('PUT')

            {{-- Header --}}
            <div class="edit-header">
                <div class="header-left">
                    <a href="{{ route('admin.blogs.index') }}" class="btn-back">
                        <i class="fas fa-arrow-left"></i>
                        Quay lại
                    </a>
                    <div class="title-group">
                        <h1 class="page-title">
                            <i class="fas fa-edit"></i>
                            Chỉnh sửa bài viết
                        </h1>
                        <p class="subtitle">Cập nhật nội dung của bạn</p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('admin.blogs.show', $blog) }}" class="btn-preview">
                        <i class="fas fa-eye"></i>
                        Xem trước
                    </a>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i>
                        Lưu thay đổi
                    </button>
                </div>
            </div>

            <div class="edit-layout">
                {{-- Main Content --}}
                <div class="main-content">
                    {{-- Title Section --}}
                    <div class="editor-card">
                        <div class="card-header">
                            <i class="fas fa-heading"></i>
                            <span>Tiêu đề bài viết</span>
                        </div>
                        <div class="card-body">
                            <input type="text" name="title" class="input-title @error('title') is-invalid @enderror"
                                value="{{ old('title', $blog->title) }}" placeholder="Nhập tiêu đề hấp dẫn..."
                                maxlength="255" required>
                            @error('title')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                            <div class="char-count">
                                <span id="titleCount">{{ strlen($blog->title) }}</span>/255 ký tự
                            </div>
                        </div>
                    </div>

                    {{-- Slug Section --}}
                    <div class="editor-card">
                        <div class="card-header">
                            <i class="fas fa-link"></i>
                            <span>URL thân thiện (Slug)</span>
                        </div>
                        <div class="card-body">
                            <div class="slug-preview">
                                <span class="domain">{{ url('/blog') }}/</span>
                                <input type="text" name="slug" class="input-slug @error('slug') is-invalid @enderror"
                                    value="{{ old('slug', $blog->slug) }}" placeholder="url-tu-dong-tao">
                            </div>
                            @error('slug')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Content Editor --}}
                    <div class="editor-card">
                        <div class="card-header">
                            <i class="fas fa-edit"></i>
                            <span>Nội dung</span>
                            <span class="badge-required">Bắt buộc</span>
                        </div>
                        <div class="card-body">
                            <textarea name="content" id="editor" class="@error('content') is-invalid @enderror" required>{{ old('content', $blog->content) }}</textarea>
                            @error('content')
                                <div class="error-message">{{ $message }}</div>
                            @enderror>
                        </div>
                    </div>

                    {{-- SEO Section --}}
                    <div class="editor-card">
                        <div class="card-header">
                            <i class="fas fa-search"></i>
                            <span>Tối ưu SEO</span>
                            <button type="button" class="btn-collapse" data-target="seoSection">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </div>
                        <div class="card-body collapsible" id="seoSection">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-tag"></i>
                                    Meta Title
                                </label>
                                <input type="text" name="meta_title" class="form-input"
                                    value="{{ old('meta_title', $blog->meta_title) }}"
                                    placeholder="Tiêu đề SEO (60-70 ký tự)" maxlength="255">
                                <div class="char-count">
                                    <span id="metaTitleCount">{{ strlen($blog->meta_title ?? '') }}</span>/70 ký tự tối ưu
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-align-left"></i>
                                    Meta Description
                                </label>
                                <textarea name="meta_description" class="form-textarea" rows="3"
                                    placeholder="Mô tả ngắn gọn cho công cụ tìm kiếm (150-160 ký tự)" maxlength="500">{{ old('meta_description', $blog->meta_description) }}</textarea>
                                <div class="char-count">
                                    <span id="metaDescCount">{{ strlen($blog->meta_description ?? '') }}</span>/160 ký tự
                                    tối ưu
                                </div>
                            </div>

                            <div class="seo-preview">
                                <div class="preview-label">
                                    <i class="fas fa-google"></i>
                                    Preview trên Google
                                </div>
                                <div class="google-preview">
                                    <div class="preview-url">{{ url('/blog/' . $blog->slug) }}</div>
                                    <div class="preview-title" id="previewTitle">{{ $blog->meta_title ?? $blog->title }}
                                    </div>
                                    <div class="preview-desc" id="previewDesc">
                                        {{ $blog->meta_description ?? 'Mô tả sẽ hiển thị ở đây...' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="sidebar-content">
                    {{-- Publish Settings --}}
                    <div class="sidebar-card sticky">
                        <div class="card-header">
                            <i class="fas fa-cog"></i>
                            <span>Cài đặt xuất bản</span>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-flag"></i>
                                    Trạng thái
                                </label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->value }}"
                                            {{ old('status', $blog->status->value) == $status->value ? 'selected' : '' }}>
                                            {{ ucfirst($status->value) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="publish-info">
                                <div class="info-item">
                                    <i class="fas fa-user"></i>
                                    <span>Tác giả: <strong>{{ $blog->author->username }}</strong></span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>Tạo: <strong>{{ $blog->created_at->format('d/m/Y') }}</strong></span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-clock"></i>
                                    <span>Cập nhật: <strong>{{ $blog->updated_at->diffForHumans() }}</strong></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Categories --}}
                    <div class="sidebar-card">
                        <div class="card-header">
                            <i class="fas fa-folder"></i>
                            <span>Danh mục</span>
                        </div>
                        <div class="card-body">
                            <div class="categories-list">
                                @forelse($categories as $category)
                                    <label class="category-item">
                                        <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                            {{ in_array($category->id, old('categories', $blog->categories->pluck('id')->toArray())) ? 'checked' : '' }}>
                                        <span class="category-name">{{ $category->name }}</span>
                                        <span class="post-count">{{ $category->blogs_count ?? 0 }}</span>
                                    </label>
                                @empty
                                    <p class="text-muted">Chưa có danh mục</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- Featured Image --}}
                    <div class="sidebar-card">
                        <div class="card-header">
                            <i class="fas fa-image"></i>
                            <span>Ảnh đại diện</span>
                        </div>
                        <div class="card-body">
                            <div class="image-upload-area" id="imageUploadArea">
                                <input type="file" name="primary_image"
                                    class="file-input @error('primary_image') is-invalid @enderror" id="imageInput"
                                    accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">

                                @php
                                    $primaryImage =
                                        $blog->images->firstWhere('pivot.is_main', true) ?? $blog->images->first();
                                @endphp

                                <div class="upload-placeholder" id="uploadPlaceholder"
                                    style="{{ $primaryImage ? 'display: none;' : '' }}">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p class="upload-text">Kéo thả ảnh hoặc click để chọn</p>
                                    <p class="upload-hint">JPG, PNG, GIF, WEBP (Max 5MB)</p>
                                    <button type="button" class="btn-browse">Chọn ảnh</button>
                                </div>

                                <div class="image-preview" id="imagePreview"
                                    style="{{ $primaryImage ? 'display: block;' : 'display: none;' }}">
                                    <img src="{{ $primaryImage ? asset('storage/' . $primaryImage->path) : '' }}"
                                        alt="Preview" id="previewImage">
                                    <div class="preview-overlay">
                                        <button type="button" class="btn-remove" id="removeImage">
                                            <i class="fas fa-trash-alt"></i>
                                            Xóa ảnh
                                        </button>
                                        <button type="button" class="btn-change">
                                            <i class="fas fa-sync-alt"></i>
                                            Đổi ảnh
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @error('primary_image')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Statistics --}}
                    <div class="sidebar-card stats-card">
                        <div class="card-header">
                            <i class="fas fa-chart-line"></i>
                            <span>Thống kê</span>
                        </div>
                        <div class="card-body">
                            <div class="stat-item">
                                <div class="stat-icon views">
                                    <i class="fas fa-eye"></i>
                                </div>
                                <div class="stat-info">
                                    <span class="stat-label">Lượt xem</span>
                                    <span class="stat-value">{{ number_format($blog->views_count ?? 0) }}</span>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-icon likes">
                                    <i class="fas fa-heart"></i>
                                </div>
                                <div class="stat-info">
                                    <span class="stat-label">Lượt thích</span>
                                    <span class="stat-value">{{ number_format($blog->likes_count ?? 0) }}</span>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-icon comments">
                                    <i class="fas fa-comment"></i>
                                </div>
                                <div class="stat-info">
                                    <span class="stat-label">Bình luận</span>
                                    <span class="stat-value">{{ number_format($blog->comments_count ?? 0) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .blog-edit-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 2rem;
        }

        /* Header */
        .edit-header {
            background: white;
            padding: 1.5rem 2rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .btn-back {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #666;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: #ff0050;
            color: white;
            transform: translateX(-4px);
        }

        .title-group {
            flex: 1;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1a1a1a;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.25rem;
        }

        .page-title i {
            color: #ff0050;
        }

        .subtitle {
            color: #666;
            font-size: 0.9rem;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
        }

        .btn-preview,
        .btn-submit {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-preview {
            background: #f0f0f0;
            color: #666;
        }

        .btn-preview:hover {
            background: #e0e0e0;
        }

        .btn-submit {
            background: linear-gradient(135deg, #ff0050 0%, #ff4d94 100%);
            color: white;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 0, 80, 0.3);
        }

        /* Layout */
        .edit-layout {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 2rem;
            align-items: start;
        }

        /* Editor Cards */
        .editor-card,
        .sidebar-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 600;
            color: #1a1a1a;
        }

        .card-header i {
            color: #ff0050;
            font-size: 1.1rem;
        }

        .card-header span {
            flex: 1;
        }

        .badge-required {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            background: #ffebee;
            color: #d32f2f;
        }

        .btn-collapse {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #f0f0f0;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .btn-collapse:hover {
            background: #e0e0e0;
        }

        .btn-collapse i {
            color: #666;
            transition: transform 0.3s ease;
        }

        .btn-collapse.collapsed i {
            transform: rotate(-90deg);
        }

        .card-body {
            padding: 1.5rem;
        }

        .collapsible {
            max-height: 1000px;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
        }

        .collapsible.collapsed {
            max-height: 0;
            padding: 0 1.5rem;
        }

        /* Form Elements */
        .input-title {
            width: 100%;
            padding: 1rem;
            font-size: 1.5rem;
            font-weight: 600;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .input-title:focus {
            outline: none;
            border-color: #ff0050;
            box-shadow: 0 0 0 4px rgba(255, 0, 80, 0.1);
        }

        .slug-preview {
            display: flex;
            align-items: center;
            background: #f8f9fa;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            gap: 0.5rem;
        }

        .domain {
            color: #999;
            font-weight: 500;
        }

        .input-slug {
            flex: 1;
            border: none;
            background: transparent;
            font-weight: 600;
            color: #ff0050;
        }

        .input-slug:focus {
            outline: none;
        }

        .char-count {
            margin-top: 0.5rem;
            text-align: right;
            font-size: 0.85rem;
            color: #999;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.75rem;
        }

        .form-label i {
            color: #ff0050;
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: #ff0050;
            box-shadow: 0 0 0 4px rgba(255, 0, 80, 0.1);
        }

        .form-textarea {
            resize: vertical;
            font-family: inherit;
        }

        /* SEO Preview */
        .seo-preview {
            margin-top: 1.5rem;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 12px;
        }

        .preview-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
        }

        .preview-label i {
            color: #4285f4;
        }

        .google-preview {
            background: white;
            padding: 1rem;
            border-radius: 8px;
        }

        .preview-url {
            color: #006621;
            font-size: 0.85rem;
            margin-bottom: 0.25rem;
        }

        .preview-title {
            color: #1a0dab;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .preview-desc {
            color: #545454;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        /* Sidebar */
        .sidebar-content {
            position: sticky;
            top: 2rem;
        }

        .publish-info {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid #f0f0f0;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
            font-size: 0.9rem;
            color: #666;
        }

        .info-item i {
            color: #ff0050;
            width: 20px;
        }

        /* Categories */
        .categories-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .category-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-bottom: 0.5rem;
        }

        .category-item:hover {
            background: #f8f9fa;
        }

        .category-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #ff0050;
        }

        .category-name {
            flex: 1;
            font-weight: 500;
            color: #333;
        }

        .post-count {
            padding: 0.25rem 0.5rem;
            background: #f0f0f0;
            border-radius: 12px;
            font-size: 0.8rem;
            color: #666;
        }

        /* Image Upload */
        .image-upload-area {
            position: relative;
        }

        .file-input {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
            z-index: 10;
        }

        .upload-placeholder {
            text-align: center;
            padding: 3rem 1rem;
            border: 3px dashed #e0e0e0;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .upload-placeholder:hover {
            border-color: #ff0050;
            background: rgba(255, 0, 80, 0.02);
        }

        .upload-placeholder i {
            font-size: 3rem;
            color: #ddd;
            margin-bottom: 1rem;
        }

        .upload-text {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .upload-hint {
            font-size: 0.85rem;
            color: #999;
            margin-bottom: 1rem;
        }

        .btn-browse {
            padding: 0.75rem 1.5rem;
            background: #ff0050;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-browse:hover {
            background: #d90045;
        }

        .image-preview {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
        }

        .image-preview img {
            width: 100%;
            height: auto;
            display: block;
        }

        .preview-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1rem;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
            display: flex;
            gap: 0.5rem;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .image-preview:hover .preview-overlay {
            opacity: 1;
        }

        .btn-remove,
        .btn-change {
            flex: 1;
            padding: 0.5rem;
            border: none;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-remove {
            background: #d32f2f;
            color: white;
        }

        .btn-remove:hover {
            background: #b71c1c;
        }

        .btn-change {
            background: white;
            color: #333;
        }

        .btn-change:hover {
            background: #f0f0f0;
        }

        /* Statistics Card */
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .stats-card .card-header {
            border-bottom-color: rgba(255, 255, 255, 0.2);
        }

        .stats-card .card-header i,
        .stats-card .card-header span {
            color: white;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            margin-bottom: 0.75rem;
            backdrop-filter: blur(10px);
        }

        .stat-item:last-child {
            margin-bottom: 0;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .stat-icon.views {
            background: rgba(33, 150, 243, 0.3);
        }

        .stat-icon.likes {
            background: rgba(244, 67, 54, 0.3);
        }

        .stat-icon.comments {
            background: rgba(76, 175, 80, 0.3);
        }

        .stat-info {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .stat-label {
            font-size: 0.85rem;
            opacity: 0.9;
            margin-bottom: 0.25rem;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
        }

        /* Error Messages */
        .error-message {
            margin-top: 0.5rem;
            color: #d32f2f;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .is-invalid {
            border-color: #d32f2f !important;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .edit-layout {
                grid-template-columns: 1fr 320px;
                gap: 1.5rem;
            }
        }

        @media (max-width: 992px) {
            .edit-layout {
                grid-template-columns: 1fr;
            }

            .sidebar-content {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .blog-edit-container {
                padding: 1rem;
            }

            .edit-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .header-left {
                width: 100%;
            }

            .header-actions {
                width: 100%;
                justify-content: flex-end;
            }

            .input-title {
                font-size: 1.25rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // CKEditor initialization
            ClassicEditor.create(document.querySelector('#editor'), {
                toolbar: [
                    'heading', '|',
                    'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|',
                    'blockQuote', 'insertTable', '|',
                    'undo', 'redo'
                ],
                heading: {
                    options: [{
                            model: 'paragraph',
                            title: 'Paragraph',
                            class: 'ck-heading_paragraph'
                        },
                        {
                            model: 'heading2',
                            view: 'h2',
                            title: 'Heading 2',
                            class: 'ck-heading_heading2'
                        },
                        {
                            model: 'heading3',
                            view: 'h3',
                            title: 'Heading 3',
                            class: 'ck-heading_heading3'
                        }
                    ]
                }
            }).catch(error => {
                console.error(error);
            });

            // Title character count
            const titleInput = document.querySelector('input[name="title"]');
            const titleCount = document.getElementById('titleCount');

            titleInput?.addEventListener('input', function() {
                titleCount.textContent = this.value.length;
                const previewTitle = document.getElementById('previewTitle');
                if (previewTitle) {
                    previewTitle.textContent = this.value || 'Tiêu đề bài viết của bạn';
                }
            });

            // Meta Title & Description character count + preview
            const metaTitleInput = document.querySelector('input[name="meta_title"]');
            const metaTitleCount = document.getElementById('metaTitleCount');
            const metaDescInput = document.querySelector('textarea[name="meta_description"]');
            const metaDescCount = document.getElementById('metaDescCount');
            const previewDesc = document.getElementById('previewDesc');

            metaTitleInput?.addEventListener('input', function() {
                metaTitleCount.textContent = this.value.length;
                const previewTitle = document.getElementById('previewTitle');
                if (previewTitle) previewTitle.textContent = this.value || titleInput.value ||
                    'Tiêu đề bài viết của bạn';
            });

            metaDescInput?.addEventListener('input', function() {
                metaDescCount.textContent = this.value.length;
                if (previewDesc) previewDesc.textContent = this.value || 'Mô tả sẽ hiển thị ở đây...';
            });

            // Collapsible SEO Section
            const collapseBtns = document.querySelectorAll('.btn-collapse');
            collapseBtns.forEach(btn => {
                const targetId = btn.dataset.target;
                const targetEl = document.getElementById(targetId);
                btn.addEventListener('click', () => {
                    targetEl.classList.toggle('collapsed');
                    btn.classList.toggle('collapsed');
                });
            });

            // Image preview
            const imageInput = document.getElementById('imageInput');
            const imagePreviewContainer = document.getElementById('imagePreview');
            const previewImage = document.getElementById('previewImage');
            const uploadPlaceholder = document.getElementById('uploadPlaceholder');
            const removeBtn = document.getElementById('removeImage');

            imageInput?.addEventListener('change', function(e) {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        previewImage.src = event.target.result;
                        imagePreviewContainer.style.display = 'block';
                        uploadPlaceholder.style.display = 'none';
                    };
                    reader.readAsDataURL(file);
                }
            });

            removeBtn?.addEventListener('click', function() {
                imageInput.value = '';
                previewImage.src = '';
                imagePreviewContainer.style.display = 'none';
                uploadPlaceholder.style.display = 'block';
            });

            // Browse button click triggers file input
            document.querySelector('.btn-browse')?.addEventListener('click', () => {
                imageInput.click();
            });
        });
    </script>
@endpush
