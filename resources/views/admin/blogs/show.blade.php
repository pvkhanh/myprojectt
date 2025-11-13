@extends('layouts.admin')

@section('title', 'Chi tiết bài viết')

@section('content')
    <div class="blog-show-container">
        {{-- Header --}}
        <div class="show-header">
            <div class="header-left">
                <a href="{{ route('admin.blogs.index') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Quay lại
                </a>
                <div class="title-group">
                    <h1 class="page-title">Chi tiết bài viết</h1>
                    <div class="breadcrumb">
                        <span>Blog</span>
                        <i class="fas fa-chevron-right"></i>
                        <span class="active">{{ Str::limit($blog->title, 40) }}</span>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ url('/blog/' . $blog->slug) }}" target="_blank" class="btn-view" data-tooltip="Xem công khai">
                    <i class="fas fa-external-link-alt"></i>
                    Xem trên web
                </a>
                <a href="{{ route('admin.blogs.edit', $blog) }}" class="btn-edit">
                    <i class="fas fa-edit"></i>
                    Chỉnh sửa
                </a>
                <button type="button" class="btn-delete" onclick="confirmDelete()">
                    <i class="fas fa-trash-alt"></i>
                    Xóa
                </button>
            </div>
        </div>

        <div class="show-layout">
            {{-- Main Content --}}
            <div class="main-content">
                {{-- Featured Image --}}
                @php
                    $primaryImage = $blog->images->firstWhere('pivot.is_main', true) ?? $blog->images->first();
                @endphp

                @if ($primaryImage)
                    <div class="featured-image">
                        <img src="{{ asset('storage/' . $primaryImage->path) }}" alt="{{ $blog->title }}">
                        <div class="image-overlay">
                            <span class="status-badge {{ $blog->status->value }}">
                                {{ ucfirst($blog->status->value) }}
                            </span>
                        </div>
                    </div>
                @endif

                {{-- Title & Meta --}}
                <div class="content-header">
                    <h1 class="content-title">{{ $blog->title }}</h1>

                    <div class="content-meta">
                        <div class="author-section">
                            <img src="{{ $blog->author->avatar_url ?? asset('images/default-avatar.png') }}"
                                alt="{{ $blog->author->username }}" class="author-avatar">
                            <div class="author-info">
                                <span class="author-name">{{ $blog->author->username }}</span>
                                <span class="post-date">
                                    <i class="fas fa-calendar"></i>
                                    {{ $blog->created_at->format('d/m/Y H:i') }}
                                </span>
                            </div>
                        </div>

                        <div class="engagement-stats">
                            <div class="stat-item">
                                <i class="fas fa-eye"></i>
                                <span>{{ number_format($blog->views_count ?? 0) }}</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-heart"></i>
                                <span>{{ number_format($blog->likes_count ?? 0) }}</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-comment"></i>
                                <span>{{ number_format($blog->comments_count ?? 0) }}</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-share-alt"></i>
                                <span>{{ number_format($blog->shares_count ?? 0) }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Categories --}}
                    @if ($blog->categories->count() > 0)
                        <div class="categories-tags">
                            @foreach ($blog->categories as $category)
                                <span class="category-tag">
                                    <i class="fas fa-tag"></i>
                                    {{ $category->name }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Content --}}
                <div class="blog-content">
                    {!! $blog->content !!}
                </div>

                {{-- SEO Information --}}
                @if ($blog->meta_title || $blog->meta_description)
                    <div class="seo-section">
                        <h3 class="section-title">
                            <i class="fas fa-search"></i>
                            Thông tin SEO
                        </h3>
                        <div class="seo-content">
                            @if ($blog->meta_title)
                                <div class="seo-item">
                                    <label>Meta Title:</label>
                                    <p>{{ $blog->meta_title }}</p>
                                </div>
                            @endif

                            @if ($blog->meta_description)
                                <div class="seo-item">
                                    <label>Meta Description:</label>
                                    <p>{{ $blog->meta_description }}</p>
                                </div>
                            @endif

                            <div class="seo-item">
                                <label>Slug:</label>
                                <p class="slug-display">
                                    <span>{{ url('/blog/' . $blog->slug) }}</span>
                                    <button type="button" class="btn-copy"
                                        onclick="copyToClipboard('{{ url('/blog/' . $blog->slug) }}')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Activity Timeline --}}
                <div class="timeline-section">
                    <h3 class="section-title">
                        <i class="fas fa-history"></i>
                        Lịch sử hoạt động
                    </h3>
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-icon created">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="timeline-content">
                                <h4>Bài viết được tạo</h4>
                                <p>{{ $blog->created_at->format('d/m/Y H:i') }} - {{ $blog->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>

                        @if ($blog->updated_at != $blog->created_at)
                            <div class="timeline-item">
                                <div class="timeline-icon updated">
                                    <i class="fas fa-edit"></i>
                                </div>
                                <div class="timeline-content">
                                    <h4>Cập nhật gần nhất</h4>
                                    <p>{{ $blog->updated_at->format('d/m/Y H:i') }} -
                                        {{ $blog->updated_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endif

                        @if ($blog->published_at)
                            <div class="timeline-item">
                                <div class="timeline-icon published">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="timeline-content">
                                    <h4>Xuất bản</h4>
                                    <p>{{ $blog->published_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="sidebar-content">
                {{-- Quick Stats --}}
                <div class="sidebar-card stats-card">
                    <div class="card-header">
                        <i class="fas fa-chart-bar"></i>
                        <span>Thống kê nhanh</span>
                    </div>
                    <div class="card-body">
                        <div class="quick-stat">
                            <div class="stat-icon views">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div class="stat-details">
                                <span class="stat-value">{{ number_format($blog->views_count ?? 0) }}</span>
                                <span class="stat-label">Lượt xem</span>
                            </div>
                        </div>

                        <div class="quick-stat">
                            <div class="stat-icon likes">
                                <i class="fas fa-heart"></i>
                            </div>
                            <div class="stat-details">
                                <span class="stat-value">{{ number_format($blog->likes_count ?? 0) }}</span>
                                <span class="stat-label">Lượt thích</span>
                            </div>
                        </div>

                        <div class="quick-stat">
                            <div class="stat-icon comments">
                                <i class="fas fa-comment"></i>
                            </div>
                            <div class="stat-details">
                                <span class="stat-value">{{ number_format($blog->comments_count ?? 0) }}</span>
                                <span class="stat-label">Bình luận</span>
                            </div>
                        </div>

                        <div class="quick-stat">
                            <div class="stat-icon shares">
                                <i class="fas fa-share-alt"></i>
                            </div>
                            <div class="stat-details">
                                <span class="stat-value">{{ number_format($blog->shares_count ?? 0) }}</span>
                                <span class="stat-label">Chia sẻ</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status Info --}}
                <div class="sidebar-card">
                    <div class="card-header">
                        <i class="fas fa-info-circle"></i>
                        <span>Thông tin</span>
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-flag"></i>
                                Trạng thái
                            </span>
                            <span class="status-badge-inline {{ $blog->status->value }}">
                                {{ ucfirst($blog->status->value) }}
                            </span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-user"></i>
                                Tác giả
                            </span>
                            <span class="info-value">{{ $blog->author->username }}</span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-calendar-plus"></i>
                                Ngày tạo
                            </span>
                            <span class="info-value">{{ $blog->created_at->format('d/m/Y') }}</span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-clock"></i>
                                Cập nhật
                            </span>
                            <span class="info-value">{{ $blog->updated_at->diffForHumans() }}</span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">
                                <i class="fas fa-folder"></i>
                                Danh mục
                            </span>
                            <span class="info-value">{{ $blog->categories->count() }}</span>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="sidebar-card actions-card">
                    <div class="card-header">
                        <i class="fas fa-bolt"></i>
                        <span>Thao tác nhanh</span>
                    </div>
                    <div class="card-body">
                        <button type="button" class="action-btn duplicate">
                            <i class="fas fa-copy"></i>
                            Nhân bản bài viết
                        </button>

                        <button type="button" class="action-btn share">
                            <i class="fas fa-share-nodes"></i>
                            Chia sẻ
                        </button>

                        <button type="button" class="action-btn export">
                            <i class="fas fa-download"></i>
                            Xuất PDF
                        </button>

                        <button type="button" class="action-btn archive">
                            <i class="fas fa-archive"></i>
                            Lưu trữ
                        </button>
                    </div>
                </div>

                {{-- Share Social --}}
                <div class="sidebar-card social-card">
                    <div class="card-header">
                        <i class="fas fa-share-alt"></i>
                        <span>Chia sẻ mạng xã hội</span>
                    </div>
                    <div class="card-body">
                        <div class="social-buttons">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url('/blog/' . $blog->slug)) }}"
                                target="_blank" class="social-btn facebook" data-tooltip="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(url('/blog/' . $blog->slug)) }}&text={{ urlencode($blog->title) }}"
                                target="_blank" class="social-btn twitter" data-tooltip="Twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url('/blog/' . $blog->slug)) }}"
                                target="_blank" class="social-btn linkedin" data-tooltip="LinkedIn">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <a href="https://pinterest.com/pin/create/button/?url={{ urlencode(url('/blog/' . $blog->slug)) }}"
                                target="_blank" class="social-btn pinterest" data-tooltip="Pinterest">
                                <i class="fab fa-pinterest-p"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Delete Form --}}
        <form action="{{ route('admin.blogs.destroy', $blog) }}" method="POST" class="d-none" id="deleteForm">
            @csrf
            @method('DELETE')
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

        .blog-show-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 2rem;
        }

        /* Header */
        .show-header {
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
            flex: 1;
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
            margin-bottom: 0.5rem;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: #666;
        }

        .breadcrumb i {
            font-size: 0.7rem;
        }

        .breadcrumb .active {
            color: #ff0050;
            font-weight: 600;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
        }

        .btn-view,
        .btn-edit,
        .btn-delete {
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
            position: relative;
        }

        .btn-view {
            background: #e3f2fd;
            color: #1976d2;
        }

        .btn-view:hover {
            background: #1976d2;
            color: white;
        }

        .btn-edit {
            background: #fff3e0;
            color: #f57c00;
        }

        .btn-edit:hover {
            background: #f57c00;
            color: white;
        }

        .btn-delete {
            background: #ffebee;
            color: #d32f2f;
        }

        .btn-delete:hover {
            background: #d32f2f;
            color: white;
        }

        /* Layout */
        .show-layout {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 2rem;
            align-items: start;
        }

        /* Main Content */
        .main-content {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .featured-image {
            position: relative;
            width: 100%;
            height: 400px;
            overflow: hidden;
        }

        .featured-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.3), transparent);
            padding: 1.5rem;
        }

        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
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

        /* Content Header */
        .content-header {
            padding: 2rem;
            border-bottom: 2px solid #f0f0f0;
        }

        .content-title {
            font-size: 2rem;
            font-weight: 700;
            color: #1a1a1a;
            line-height: 1.3;
            margin-bottom: 1.5rem;
        }

        .content-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .author-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .author-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #ff0050;
        }

        .author-info {
            display: flex;
            flex-direction: column;
        }

        .author-name {
            font-weight: 600;
            color: #1a1a1a;
            font-size: 1.1rem;
        }

        .post-date {
            color: #666;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .engagement-stats {
            display: flex;
            gap: 1.5rem;
        }

        .engagement-stats .stat-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: #f8f9fa;
            border-radius: 20px;
            font-weight: 600;
            color: #666;
        }

        .engagement-stats .stat-item i {
            color: #ff0050;
        }

        .categories-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .category-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, #ff0050 0%, #ff4d94 100%);
            color: white;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        /* Blog Content */
        .blog-content {
            padding: 2rem;
            font-size: 1.1rem;
            line-height: 1.8;
            color: #333;
        }

        .blog-content h2,
        .blog-content h3 {
            margin: 2rem 0 1rem;
            color: #1a1a1a;
        }

        .blog-content p {
            margin-bottom: 1.5rem;
        }

        .blog-content img {
            max-width: 100%;
            height: auto;
            border-radius: 12px;
            margin: 1.5rem 0;
        }

        .blog-content ul,
        .blog-content ol {
            margin: 1rem 0 1.5rem 2rem;
        }

        .blog-content li {
            margin-bottom: 0.75rem;
        }

        .blog-content blockquote {
            border-left: 4px solid #ff0050;
            padding-left: 1.5rem;
            margin: 1.5rem 0;
            font-style: italic;
            color: #666;
        }

        /* SEO Section */
        .seo-section,
        .timeline-section {
            padding: 2rem;
            border-top: 2px solid #f0f0f0;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.3rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 1.5rem;
        }

        .section-title i {
            color: #ff0050;
        }

        .seo-content {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .seo-item label {
            display: block;
            font-weight: 600;
            color: #666;
            margin-bottom: 0.5rem;
        }

        .seo-item p {
            padding: 0.75rem 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            color: #333;
        }

        .slug-display {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .slug-display span {
            flex: 1;
            color: #ff0050;
            font-weight: 600;
        }

        .btn-copy {
            padding: 0.5rem 1rem;
            background: #ff0050;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-copy:hover {
            background: #d90045;
        }

        /* Timeline */
        .timeline {
            position: relative;
            padding-left: 3rem;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 1rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, #ff0050, #ff4d94);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
            display: flex;
            gap: 1.5rem;
        }

        .timeline-icon {
            position: absolute;
            left: -2.5rem;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.9rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .timeline-icon.created {
            background: linear-gradient(135deg, #00d787, #00b871);
        }

        .timeline-icon.updated {
            background: linear-gradient(135deg, #ffa500, #ff8c00);
        }

        .timeline-icon.published {
            background: linear-gradient(135deg, #1976d2, #1565c0);
        }

        .timeline-content h4 {
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 0.25rem;
        }

        .timeline-content p {
            color: #666;
            font-size: 0.9rem;
        }

        /* Sidebar */
        .sidebar-content {
            position: sticky;
            top: 2rem;
        }

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
        }

        .card-header span {
            flex: 1;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Stats Card */
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

        .quick-stat {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            margin-bottom: 1rem;
            backdrop-filter: blur(10px);
        }

        .quick-stat:last-child {
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

        .stat-icon.shares {
            background: rgba(255, 193, 7, 0.3);
        }

        .stat-details {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 0.85rem;
            opacity: 0.9;
        }

        /* Info Rows */
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            color: #666;
        }

        .info-value,
        .status-badge-inline {
            font-weight: 600;
            color: #1a1a1a;
        }

        .status-badge-inline.published {
            background: rgba(0, 215, 135, 0.2);
            color: #00d787;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
        }

        .status-badge-inline.draft {
            background: rgba(255, 165, 0, 0.2);
            color: #ffa500;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
        }

        .status-badge-inline.archived {
            background: rgba(128, 128, 128, 0.2);
            color: #808080;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
        }

        /* Action Buttons */
        .actions-card .action-btn {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            border: none;
            background: #f0f0f0;
            font-weight: 600;
            color: #1a1a1a;
            cursor: pointer;
            margin-bottom: 0.75rem;
            transition: all 0.3s ease;
        }

        .actions-card .action-btn:last-child {
            margin-bottom: 0;
        }

        .actions-card .action-btn:hover {
            background: #ff0050;
            color: white;
        }

        /* Social Share */
        .social-card .social-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .social-card .social-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .social-btn.facebook {
            background: #3b5998;
        }

        .social-btn.twitter {
            background: #1da1f2;
        }

        .social-btn.linkedin {
            background: #0077b5;
        }

        .social-btn.pinterest {
            background: #bd081c;
        }

        .social-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .show-layout {
                grid-template-columns: 1fr;
            }

            .sidebar-content {
                position: relative;
                top: 0;
            }
        }

        @media (max-width: 768px) {
            .show-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .header-actions {
                width: 100%;
                justify-content: flex-start;
                gap: 0.5rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        function confirmDelete() {
            if (confirm('Bạn có chắc muốn xóa bài viết này không?')) {
                document.getElementById('deleteForm').submit();
            }
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Đã sao chép: ' + text);
            }).catch(err => {
                console.error('Lỗi khi sao chép: ', err);
            });
        }
    </script>
@endpush
