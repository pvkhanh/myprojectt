@extends('layouts.admin')

@section('title', 'Quản lý Blog')

@section('content')
    <div class="blog-manager">
        {{-- Header avec statistiques --}}
        <div class="blog-header">
            <div class="header-top">
                <div class="title-section">
                    <h1 class="page-title">
                        <i class="fas fa-blog"></i>
                        Quản lý Blog
                    </h1>
                    <p class="subtitle">Tạo và quản lý nội dung của bạn</p>
                </div>
                <div class="header-actions">
                    <button type="button" class="btn-bulk" id="bulkActionsBtn" style="display:none;">
                        <i class="fas fa-tasks"></i>
                        <span>Thao tác hàng loạt</span>
                    </button>
                    <a href="{{ route('admin.blogs.create') }}" class="btn-create">
                        <i class="fas fa-plus"></i>
                        <span>Tạo bài viết</span>
                    </a>
                </div>
            </div>

            {{-- Statistics Cards --}}
            <div class="stats-grid">
                <div class="stat-card primary">
                    <div class="stat-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-label">Tổng bài viết</span>
                        <span class="stat-value">{{ number_format($totalBlogs) }}</span>
                    </div>
                    <div class="stat-trend up">
                        <i class="fas fa-arrow-up"></i>
                        <span>12%</span>
                    </div>
                </div>

                <div class="stat-card success">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-label">Đã xuất bản</span>
                        <span class="stat-value">{{ number_format($publishedBlogs) }}</span>
                    </div>
                    <div class="stat-trend up">
                        <i class="fas fa-arrow-up"></i>
                        <span>8%</span>
                    </div>
                </div>

                <div class="stat-card warning">
                    <div class="stat-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-label">Bản nháp</span>
                        <span class="stat-value">{{ number_format($draftBlogs) }}</span>
                    </div>
                    <div class="stat-trend down">
                        <i class="fas fa-arrow-down"></i>
                        <span>3%</span>
                    </div>
                </div>

                <div class="stat-card info">
                    <div class="stat-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-label">Lượt xem</span>
                        <span class="stat-value">{{ number_format($totalViews ?? 0) }}</span>
                    </div>
                    <div class="stat-trend up">
                        <i class="fas fa-arrow-up"></i>
                        <span>24%</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters Section --}}
        <div class="filters-section">
            <form method="GET" class="filters-form">
                <div class="filter-group">
                    <div class="filter-item search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" name="keyword" value="{{ request('keyword') }}"
                            placeholder="Tìm kiếm bài viết..." class="search-input">
                    </div>

                    <div class="filter-item">
                        <select name="category_id" class="filter-select">
                            <option value="">Tất cả danh mục</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-item">
                        <select name="status" class="filter-select">
                            <option value="">Tất cả trạng thái</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}"
                                    {{ request('status') == $status->value ? 'selected' : '' }}>
                                    {{ ucfirst($status->value) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-item">
                        <select name="author_id" class="filter-select">
                            <option value="">Tất cả tác giả</option>
                            @foreach ($authors as $author)
                                <option value="{{ $author->id }}"
                                    {{ request('author_id') == $author->id ? 'selected' : '' }}>
                                    {{ $author->username }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-item">
                        <select name="sort_by" class="filter-select">
                            <option value="latest" {{ request('sort_by') == 'latest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="oldest" {{ request('sort_by') == 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                            <option value="title" {{ request('sort_by') == 'title' ? 'selected' : '' }}>Tên A-Z</option>
                            <option value="views" {{ request('sort_by') == 'views' ? 'selected' : '' }}>Lượt xem</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-filter">
                        <i class="fas fa-filter"></i>
                        Lọc
                    </button>
                </div>
            </form>
        </div>

        {{-- Blog Grid (Style TikTok) --}}
        <div class="blogs-container">
            <div class="table-header">
                <div class="select-all">
                    <input type="checkbox" id="selectAll" class="checkbox-modern">
                    <label for="selectAll">Chọn tất cả</label>
                    <span class="selected-count" id="selectedCount">(0 mục)</span>
                </div>
                <div class="view-toggle">
                    <button class="view-btn active" data-view="grid">
                        <i class="fas fa-th"></i>
                    </button>
                    <button class="view-btn" data-view="list">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>

            <div class="blogs-grid" id="blogsGrid">
                @forelse($blogs as $blog)
                    <div class="blog-card" data-blog-id="{{ $blog->id }}">
                        <div class="card-checkbox">
                            <input type="checkbox" class="checkbox-modern blog-checkbox" value="{{ $blog->id }}">
                        </div>

                        {{-- Image principale --}}
                        <div class="card-image">
                            @php
                                $primaryImage =
                                    $blog->images->firstWhere('pivot.is_main', true) ?? $blog->images->first();
                                $imgPath = $primaryImage ? $primaryImage->path : 'images/default-blog.png';
                            @endphp
                            <img src="{{ asset('storage/' . $imgPath) }}" alt="{{ $blog->title }}" loading="lazy">

                            <div class="image-overlay">
                                <div class="overlay-stats">
                                    <span class="stat-item">
                                        <i class="fas fa-eye"></i>
                                        {{ number_format($blog->views_count ?? 0) }}
                                    </span>
                                    <span class="stat-item">
                                        <i class="fas fa-heart"></i>
                                        {{ number_format($blog->likes_count ?? 0) }}
                                    </span>
                                </div>
                            </div>

                            <span class="status-badge {{ $blog->status->value }}">
                                {{ ucfirst($blog->status->value) }}
                            </span>
                        </div>

                        {{-- Content --}}
                        <div class="card-content">
                            <h3 class="card-title">
                                <a href="{{ route('admin.blogs.show', $blog) }}">
                                    {{ Str::limit($blog->title, 60) }}
                                </a>
                            </h3>

                            <div class="card-meta">
                                {{-- Author avec avatar --}}
                                <div class="author-info">
                                    <img src="{{ $blog->author->avatar_url ?? asset('images/default-avatar.png') }}"
                                        alt="{{ $blog->author->username }}" class="author-avatar">
                                    <span class="author-name">{{ $blog->author->username }}</span>
                                </div>

                                <span class="post-date">
                                    <i class="fas fa-clock"></i>
                                    {{ $blog->created_at->diffForHumans() }}
                                </span>
                            </div>

                            {{-- Categories --}}
                            <div class="card-tags">
                                @foreach ($blog->categories->take(3) as $cat)
                                    <span class="tag">{{ $cat->name }}</span>
                                @endforeach
                                @if ($blog->categories->count() > 3)
                                    <span class="tag more">+{{ $blog->categories->count() - 3 }}</span>
                                @endif
                            </div>

                            {{-- Actions --}}
                            <div class="card-actions">
                                <a href="{{ route('admin.blogs.show', $blog) }}" class="action-btn view"
                                    data-tooltip="Xem">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.blogs.edit', $blog) }}" class="action-btn edit"
                                    data-tooltip="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="action-btn delete"
                                    onclick="confirmDelete({{ $blog->id }})" data-tooltip="Xóa">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>

                            <form action="{{ route('admin.blogs.destroy', $blog) }}" method="POST" class="d-none"
                                id="deleteForm{{ $blog->id }}">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <h3>Chưa có bài viết nào</h3>
                        <p>Bắt đầu tạo bài viết đầu tiên của bạn</p>
                        <a href="{{ route('admin.blogs.create') }}" class="btn-create">
                            <i class="fas fa-plus"></i>
                            Tạo bài viết
                        </a>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if ($blogs->hasPages())
                <div class="pagination-wrapper">
                    <div class="pagination-info">
                        Hiển thị {{ $blogs->firstItem() ?? 0 }} - {{ $blogs->lastItem() ?? 0 }}
                        trong tổng số {{ $blogs->total() }} bài viết
                    </div>
                    {{ $blogs->links('components.pagination') }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Bulk Actions --}}
    <div class="modal fade" id="bulkActionsModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modern-modal">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-tasks"></i>
                        Thao tác hàng loạt
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="bulkActionForm">
                        @csrf
                        <input type="hidden" name="ids" id="bulkIds">

                        <div class="form-group">
                            <label class="form-label">Chọn hành động</label>
                            <select class="form-control modern-select" id="bulkAction" required>
                                <option value="">-- Chọn --</option>
                                <option value="update_status">Cập nhật trạng thái</option>
                                <option value="delete">Xóa bài viết</option>
                            </select>
                        </div>

                        <div class="form-group d-none" id="statusSelectDiv">
                            <label class="form-label">Trạng thái mới</label>
                            <select class="form-control modern-select" name="status" id="newStatus">
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->value }}">{{ ucfirst($status->value) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="alert alert-warning d-none" id="deleteWarning">
                            <i class="fas fa-exclamation-triangle"></i>
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

    @push('styles')
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            .blog-manager {
                padding: 2rem;
                background: #f8f9fa;
                min-height: 100vh;
            }

            /* Header */
            .blog-header {
                margin-bottom: 2rem;
            }

            .header-top {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 2rem;
            }

            .title-section {
                flex: 1;
            }

            .page-title {
                font-size: 2rem;
                font-weight: 700;
                color: #1a1a1a;
                display: flex;
                align-items: center;
                gap: 0.75rem;
                margin-bottom: 0.5rem;
            }

            .page-title i {
                color: #ff0050;
            }

            .subtitle {
                color: #666;
                font-size: 0.95rem;
            }

            .header-actions {
                display: flex;
                gap: 1rem;
            }

            .btn-create,
            .btn-bulk {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.75rem 1.5rem;
                border-radius: 12px;
                font-weight: 600;
                border: none;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .btn-create {
                background: linear-gradient(135deg, #ff0050 0%, #ff4d94 100%);
                color: white;
                text-decoration: none;
            }

            .btn-create:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 20px rgba(255, 0, 80, 0.3);
            }

            .btn-bulk {
                background: white;
                color: #333;
                border: 2px solid #e0e0e0;
            }

            .btn-bulk:hover {
                border-color: #ff0050;
                color: #ff0050;
            }

            /* Statistics Grid */
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 1.5rem;
            }

            .stat-card {
                background: white;
                border-radius: 16px;
                padding: 1.5rem;
                display: flex;
                align-items: center;
                gap: 1rem;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }

            .stat-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 4px;
                height: 100%;
                background: currentColor;
            }

            .stat-card.primary {
                color: #ff0050;
            }

            .stat-card.success {
                color: #00d787;
            }

            .stat-card.warning {
                color: #ffa500;
            }

            .stat-card.info {
                color: #00bcd4;
            }

            .stat-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            }

            .stat-icon {
                width: 60px;
                height: 60px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.5rem;
                background: currentColor;
                color: white;
                opacity: 0.9;
            }

            .stat-content {
                flex: 1;
                display: flex;
                flex-direction: column;
            }

            .stat-label {
                font-size: 0.85rem;
                color: #666;
                margin-bottom: 0.25rem;
            }

            .stat-value {
                font-size: 1.75rem;
                font-weight: 700;
                color: #1a1a1a;
            }

            .stat-trend {
                display: flex;
                align-items: center;
                gap: 0.25rem;
                font-size: 0.85rem;
                font-weight: 600;
                padding: 0.25rem 0.5rem;
                border-radius: 6px;
            }

            .stat-trend.up {
                color: #00d787;
                background: rgba(0, 215, 135, 0.1);
            }

            .stat-trend.down {
                color: #ff4444;
                background: rgba(255, 68, 68, 0.1);
            }

            /* Filters */
            .filters-section {
                background: white;
                border-radius: 16px;
                padding: 1.5rem;
                margin-bottom: 2rem;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }

            .filters-form {
                display: flex;
            }

            .filter-group {
                display: flex;
                gap: 1rem;
                flex-wrap: wrap;
                width: 100%;
            }

            .filter-item {
                position: relative;
            }

            .search-box {
                flex: 2;
                min-width: 300px;
                position: relative;
            }

            .search-box i {
                position: absolute;
                left: 1rem;
                top: 50%;
                transform: translateY(-50%);
                color: #999;
            }

            .search-input {
                width: 100%;
                padding: 0.75rem 1rem 0.75rem 2.75rem;
                border: 2px solid #e0e0e0;
                border-radius: 12px;
                font-size: 0.95rem;
                transition: all 0.3s ease;
            }

            .search-input:focus {
                outline: none;
                border-color: #ff0050;
            }

            .filter-select {
                padding: 0.75rem 1rem;
                border: 2px solid #e0e0e0;
                border-radius: 12px;
                font-size: 0.95rem;
                background: white;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .filter-select:focus {
                outline: none;
                border-color: #ff0050;
            }

            .btn-filter {
                padding: 0.75rem 1.5rem;
                background: #ff0050;
                color: white;
                border: none;
                border-radius: 12px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .btn-filter:hover {
                background: #d90045;
                transform: translateY(-2px);
            }

            /* Blogs Container */
            .blogs-container {
                background: white;
                border-radius: 16px;
                padding: 1.5rem;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }

            .table-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 1.5rem;
                padding-bottom: 1rem;
                border-bottom: 2px solid #f0f0f0;
            }

            .select-all {
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }

            .checkbox-modern {
                width: 20px;
                height: 20px;
                cursor: pointer;
                accent-color: #ff0050;
            }

            .selected-count {
                color: #666;
                font-size: 0.9rem;
            }

            .view-toggle {
                display: flex;
                gap: 0.5rem;
            }

            .view-btn {
                width: 40px;
                height: 40px;
                border: 2px solid #e0e0e0;
                background: white;
                border-radius: 8px;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
            }

            .view-btn.active,
            .view-btn:hover {
                border-color: #ff0050;
                color: #ff0050;
            }

            /* Blog Grid */
            .blogs-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
                gap: 1.5rem;
            }

            .blog-card {
                background: white;
                border-radius: 16px;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                transition: all 0.3s ease;
                position: relative;
                border: 2px solid transparent;
            }

            .blog-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
            }

            .card-checkbox {
                position: absolute;
                top: 1rem;
                left: 1rem;
                z-index: 10;
            }

            .card-image {
                position: relative;
                width: 100%;
                height: 240px;
                overflow: hidden;
                background: #f0f0f0;
            }

            .card-image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.5s ease;
            }

            .blog-card:hover .card-image img {
                transform: scale(1.1);
            }

            .image-overlay {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                background: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);
                padding: 1rem;
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .blog-card:hover .image-overlay {
                opacity: 1;
            }

            .overlay-stats {
                display: flex;
                gap: 1rem;
            }

            .stat-item {
                color: white;
                font-size: 0.9rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .status-badge {
                position: absolute;
                top: 1rem;
                right: 1rem;
                padding: 0.5rem 1rem;
                border-radius: 20px;
                font-size: 0.8rem;
                font-weight: 600;
                text-transform: uppercase;
                backdrop-filter: blur(10px);
            }

            .status-badge.published {
                background: rgba(0, 215, 135, 0.9);
                color: white;
            }

            .status-badge.draft {
                background: rgba(255, 165, 0, 0.9);
                color: white;
            }

            .status-badge.archived {
                background: rgba(128, 128, 128, 0.9);
                color: white;
            }

            .card-content {
                padding: 1.25rem;
            }

            .card-title {
                margin-bottom: 1rem;
            }

            .card-title a {
                color: #1a1a1a;
                text-decoration: none;
                font-size: 1.1rem;
                font-weight: 600;
                line-height: 1.4;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            .card-title a:hover {
                color: #ff0050;
            }

            .card-meta {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 1rem;
                padding-bottom: 1rem;
                border-bottom: 1px solid #f0f0f0;
            }

            .author-info {
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .author-avatar {
                width: 32px;
                height: 32px;
                border-radius: 50%;
                object-fit: cover;
                border: 2px solid #ff0050;
            }

            .author-name {
                font-weight: 600;
                color: #333;
                font-size: 0.9rem;
            }

            .post-date {
                color: #666;
                font-size: 0.85rem;
                display: flex;
                align-items: center;
                gap: 0.25rem;
            }

            .card-tags {
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
                margin-bottom: 1rem;
            }

            .tag {
                padding: 0.35rem 0.75rem;
                background: #f0f0f0;
                border-radius: 16px;
                font-size: 0.8rem;
                color: #666;
                font-weight: 500;
            }

            .tag.more {
                background: #ff0050;
                color: white;
            }

            .card-actions {
                display: flex;
                gap: 0.5rem;
            }

            .action-btn {
                flex: 1;
                padding: 0.65rem;
                border-radius: 8px;
                border: none;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.95rem;
                transition: all 0.3s ease;
                text-decoration: none;
                position: relative;
            }

            .action-btn::before {
                content: attr(data-tooltip);
                position: absolute;
                bottom: 100%;
                left: 50%;
                transform: translateX(-50%);
                background: #1a1a1a;
                color: white;
                padding: 0.5rem 0.75rem;
                border-radius: 6px;
                font-size: 0.75rem;
                white-space: nowrap;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.3s ease;
                margin-bottom: 0.5rem;
            }

            .action-btn:hover::before {
                opacity: 1;
            }

            .action-btn.view {
                background: #e3f2fd;
                color: #1976d2;
            }

            .action-btn.view:hover {
                background: #1976d2;
                color: white;
            }

            .action-btn.edit {
                background: #fff3e0;
                color: #f57c00;
            }

            .action-btn.edit:hover {
                background: #f57c00;
                color: white;
            }

            .action-btn.delete {
                background: #ffebee;
                color: #d32f2f;
            }

            .action-btn.delete:hover {
                background: #d32f2f;
                color: white;
            }

            /* Empty State */
            .empty-state {
                text-align: center;
                padding: 4rem 2rem;
            }

            .empty-icon {
                font-size: 4rem;
                color: #ddd;
                margin-bottom: 1rem;
            }

            .empty-state h3 {
                color: #666;
                margin-bottom: 0.5rem;
            }

            .empty-state p {
                color: #999;
                margin-bottom: 2rem;
            }

            /* Pagination */
            .pagination-wrapper {
                margin-top: 2rem;
                padding-top: 2rem;
                border-top: 2px solid #f0f0f0;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .pagination-info {
                color: #666;
                font-size: 0.9rem;
            }

            /* Modal */
            .modern-modal {
                border-radius: 16px;
                border: none;
            }

            .modern-modal .modal-header {
                border-bottom: 2px solid #f0f0f0;
                padding: 1.5rem;
            }

            .modern-modal .modal-title {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                color: #1a1a1a;
                font-weight: 600;
            }

            .modern-modal .modal-body {
                padding: 2rem;
            }

            .form-group {
                margin-bottom: 1.5rem;
            }

            .form-label {
                display: block;
                margin-bottom: 0.5rem;
                font-weight: 600;
                color: #333;
            }

            .form-control {
                width: 100%;
                padding: 0.75rem;
                border: 2px solid #e0e0e0;
                border-radius: 8px;
                font-size: 0.95rem;
            }

            .form-control:focus {
                outline: none;
                border-color: #ff0050;
            }

            .modern-select {
                cursor: pointer;
            }

            /* Responsive */
            @media (max-width: 768px) {
                .blog-manager {
                    padding: 1rem;
                }

                .header-top {
                    flex-direction: column;
                    gap: 1rem;
                    align-items: flex-start;
                }

                .stats-grid {
                    grid-template-columns: 1fr;
                }

                .filter-group {
                    flex-direction: column;
                }

                .search-box {
                    min-width: 100%;
                }

                .blogs-grid {
                    grid-template-columns: 1fr;
                }

                .pagination-wrapper {
                    flex-direction: column;
                    gap: 1rem;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Checkbox handling
                const selectAllCheckbox = document.getElementById('selectAll');
                const blogCheckboxes = document.querySelectorAll('.blog-checkbox');
                const selectedCountSpan = document.getElementById('selectedCount');
                const bulkActionsBtn = document.getElementById('bulkActionsBtn');

                function updateSelectedCount() {
                    const checked = document.querySelectorAll('.blog-checkbox:checked').length;
                    const total = blogCheckboxes.length;
                    selectedCountSpan.textContent = `(${checked}/${total} mục)`;
                    bulkActionsBtn.style.display = checked > 0 ? 'inline-flex' : 'none';

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

                selectAllCheckbox?.addEventListener('change', function() {
                    blogCheckboxes.forEach(cb => {
                        cb.checked = this.checked;
                        const card = cb.closest('.blog-card');
                        if (card) {
                            card.style.borderColor = this.checked ? '#ff0050' : 'transparent';
                        }
                    });
                    updateSelectedCount();
                });

                blogCheckboxes.forEach(cb => {
                    cb.addEventListener('change', function() {
                        updateSelectedCount();
                        const card = this.closest('.blog-card');
                        if (card) {
                            card.style.borderColor = this.checked ? '#ff0050' : 'transparent';
                        }
                    });
                });

                // Bulk actions modal
                bulkActionsBtn?.addEventListener('click', function() {
                    const checkedIds = Array.from(document.querySelectorAll('.blog-checkbox:checked'))
                        .map(cb => cb.value);
                    document.getElementById('bulkIds').value = checkedIds.join(',');
                    document.getElementById('deleteCount').textContent = checkedIds.length;
                    const modal = new bootstrap.Modal(document.getElementById('bulkActionsModal'));
                    modal.show();
                });

                // Bulk action type change
                document.getElementById('bulkAction')?.addEventListener('change', function() {
                    const statusDiv = document.getElementById('statusSelectDiv');
                    const deleteWarning = document.getElementById('deleteWarning');

                    statusDiv.classList.add('d-none');
                    deleteWarning.classList.add('d-none');

                    if (this.value === 'update_status') {
                        statusDiv.classList.remove('d-none');
                    } else if (this.value === 'delete') {
                        deleteWarning.classList.remove('d-none');
                    }
                });

                // Execute bulk action
                document.getElementById('executeBulkAction')?.addEventListener('click', function() {
                    const action = document.getElementById('bulkAction').value;
                    const ids = document.getElementById('bulkIds').value.split(',');

                    if (!action) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: 'Vui lòng chọn hành động'
                        });
                        return;
                    }

                    const btn = this;
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';

                    let url = action === 'update_status' ?
                        '{{ route('admin.blogs.bulk-update-status') }}' :
                        '{{ route('admin.blogs.bulk-delete') }}';

                    let data = {
                        ids,
                        _token: '{{ csrf_token() }}'
                    };

                    if (action === 'update_status') {
                        data.status = document.getElementById('newStatus').value;
                    }

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
                                    timer: 2000,
                                    showConfirmButton: false
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
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: 'Có lỗi xảy ra, vui lòng thử lại'
                            });
                            btn.disabled = false;
                            btn.innerHTML = 'Thực hiện';
                        });
                });

                // View toggle
                const viewBtns = document.querySelectorAll('.view-btn');
                const blogsGrid = document.getElementById('blogsGrid');

                viewBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        viewBtns.forEach(b => b.classList.remove('active'));
                        this.classList.add('active');

                        const view = this.dataset.view;
                        if (view === 'list') {
                            blogsGrid.style.gridTemplateColumns = '1fr';
                        } else {
                            blogsGrid.style.gridTemplateColumns =
                                'repeat(auto-fill, minmax(320px, 1fr))';
                        }
                    });
                });

                updateSelectedCount();
            });

            // Delete confirmation
            function confirmDelete(id) {
                Swal.fire({
                    title: "Xác nhận xóa?",
                    text: "Bài viết này sẽ bị xóa vĩnh viễn!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d32f2f",
                    cancelButtonColor: "#666",
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy',
                    reverseButtons: true
                }).then(result => {
                    if (result.isConfirmed) {
                        document.getElementById('deleteForm' + id).submit();
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

@endsection
