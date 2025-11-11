{{-- @php
    use Illuminate\Support\Str;
@endphp

@extends('layouts.admin')

@section('title', 'Quản lý ảnh')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Quản lý ảnh</h1>
            <a href="{{ route('admin.images.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tải lên ảnh mới
            </a>
        </div>

        <!-- Bộ lọc -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.images.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Loại ảnh</label>
                        <select name="type" class="form-select">
                            <option value="">Tất cả</option>
                            <option value="product" {{ request('type') == 'product' ? 'selected' : '' }}>Sản phẩm</option>
                            <option value="avatar" {{ request('type') == 'avatar' ? 'selected' : '' }}>Avatar</option>
                            <option value="banner" {{ request('type') == 'banner' ? 'selected' : '' }}>Banner</option>
                            <option value="blog" {{ request('type') == 'blog' ? 'selected' : '' }}>Blog</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tìm kiếm</label>
                        <input type="text" name="search" class="form-control" placeholder="Tên file, mô tả..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Lọc
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- <!-- Thông báo -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @elseif (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif --}}

<!-- Lưới ảnh -->
{{-- <div class="card shadow-sm">
            <div class="card-body">
                <div class="row g-3">
                    @forelse ($images as $image)
                        @php
                            $path = trim($image->path ?? '');
                            // 🔹 Chuẩn hóa URL ảnh
                            if (empty($path)) {
                                $imageUrl = asset('images/no-image.png');
                            } elseif (Str::startsWith($path, ['http://', 'https://'])) {
                                $imageUrl = $path;
                            } else {
                                $imageUrl = asset('storage/' . ltrim($path, '/'));
                            }
                        @endphp

                        <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                            <div class="card h-100 shadow-sm border-0 hover-shadow position-relative">
                                <img src="{{ $imageUrl }}" class="card-img-top" alt="{{ $image->alt_text ?? 'Image' }}"
                                    onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}';"
                                    style="height: 150px; object-fit: cover; border-top-left-radius: .5rem; border-top-right-radius: .5rem;">
                                <span
                                    class="position-absolute top-0 end-0 badge bg-{{ $image->is_active ? 'success' : 'secondary' }} m-1">
                                    {{ $image->is_active ? 'Active' : 'Inactive' }}
                                </span>

                                <div class="card-body p-2">
                                    <small class="d-block text-truncate mb-1">
                                        <strong>Type:</strong> {{ ucfirst($image->type ?? 'Unknown') }}
                                    </small>
                                    <small class="d-block text-truncate mb-2" title="{{ $image->alt_text }}">
                                        {{ $image->alt_text ?: 'Không có mô tả' }}
                                    </small>

                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.images.edit', $image) }}"
                                            class="btn btn-sm btn-outline-primary flex-fill">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.images.destroy', $image) }}" method="POST"
                                            class="flex-fill"
                                            onsubmit="return confirm('Bạn có chắc chắn muốn xóa ảnh này không?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle"></i> Không có ảnh nào.
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Phân trang -->
                <div class="mt-4">
                    {{ $images->links('components.pagination') }}
                </div>
            </div>
        </div>
    </div>
@endsection --}}


@php
    use Illuminate\Support\Str;

    $typeLabels = [
        'product' => 'Sản phẩm',
        'avatar' => 'Ảnh đại diện',
        'banner' => 'Banner',
        'blog' => 'Blog',
        'thumbnail' => 'Thumbnail',
        'cover' => 'Cover',
    ];
@endphp

@extends('layouts.admin')

@section('title', 'Quản lý ảnh')

@section('content')
    <div class="container-fluid py-4">

        {{-- ===== Header ===== --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0"><i class="fas fa-images me-2 text-primary"></i>Quản lý ảnh</h2>
            <a href="{{ route('admin.images.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-upload me-1"></i> Tải lên ảnh mới
            </a>
        </div>

        {{-- ===== Card thống kê ===== --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="card info-card bg-gradient-primary shadow-sm border-0">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Tổng ảnh</h6>
                            <h3 class="fw-bold mb-0">{{ $stats['total'] ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-images fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card info-card bg-gradient-success shadow-sm border-0">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Ảnh sản phẩm</h6>
                            <h3 class="fw-bold mb-0">{{ $stats['by_type']['product'] ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-box-open fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card info-card bg-gradient-warning shadow-sm border-0">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-dark opacity-75 mb-2">Ảnh avatar</h6>
                            <h3 class="fw-bold mb-0">{{ $stats['by_type']['avatar'] ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-user fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card info-card bg-gradient-secondary shadow-sm border-0">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Banner & Blog</h6>
                            <h3 class="fw-bold mb-0">
                                {{ ($stats['by_type']['banner'] ?? 0) + ($stats['by_type']['blog'] ?? 0) }}
                            </h3>
                        </div>
                        <i class="fas fa-image fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== Bộ lọc ===== --}}
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.images.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold"><i class="fas fa-filter me-1"></i>Loại ảnh</label>
                        <select name="type" class="form-select">
                            <option value="">-- Tất cả --</option>
                            @foreach ($typeLabels as $key => $label)
                                <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold"><i class="fas fa-search me-1"></i>Tìm kiếm</label>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Tên file, mô tả...">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa-solid fa-filter me-2"></i>Lọc
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===== Grid ảnh kiểu TikTok ===== --}}
        <div class="d-flex flex-wrap justify-content-start gap-3">
            @forelse($images as $image)
                @php
                    $path = trim($image->path ?? '');
                    $imageUrl = empty($path)
                        ? asset('images/no-image.png')
                        : (Str::startsWith($path, ['http://', 'https://'])
                            ? $path
                            : asset('storage/' . $path));
                    $typeLabel = $typeLabels[$image->type] ?? ucfirst($image->type ?? 'Unknown');
                    $altText = $image->alt_text ?: 'Không có mô tả';
                @endphp

                <div class="image-card position-relative overflow-hidden shadow-sm hover-scale"
                    style="flex:0 0 19%; max-width:19%; border-radius:12px; background:#fff; cursor:pointer;">
                    <div class="ratio ratio-1x1 rounded-3">
                        <img src="{{ $imageUrl }}" alt="{{ $altText }}" class="card-img-top object-fit-cover"
                            onerror="this.onerror=null;this.src='{{ asset('images/no-image.png') }}';">
                        <div
                            class="overlay d-flex flex-column justify-content-center align-items-center text-white p-2 text-center">
                            <small class="fw-bold">{{ $typeLabel }}</small>
                            <small>{{ $altText }}</small>
                            <span class="badge bg-{{ $image->is_active ? 'success' : 'secondary' }} mt-2">
                                {{ $image->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                    <div class="d-flex gap-1 mt-2 px-2 pb-2">
                        <a href="{{ route('admin.images.edit', $image) }}" class="btn btn-sm btn-outline-primary flex-fill"
                            data-bs-toggle="tooltip" title="Chỉnh sửa"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.images.destroy', $image) }}" method="POST" class="flex-fill"
                            onsubmit="return confirm('Bạn có chắc chắn muốn xóa ảnh này không?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger w-100" data-bs-toggle="tooltip"
                                title="Xóa">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 text-muted">
                    <i class="fas fa-image fa-3x mb-3 d-block"></i>
                    Không có ảnh nào
                </div>
            @endforelse
        </div>

        {{-- Phân trang & thống kê số ảnh --}}
        <div class="card-footer d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted small">
                Hiển thị {{ $images->firstItem() ?? 0 }} - {{ $images->lastItem() ?? 0 }}
                trong tổng số {{ $images->total() }} ảnh
            </div>
            <div>
                {{ $images->links('components.pagination') }}
            </div>
        </div>

    </div>

    @push('styles')
        <style>
            /* Info card hover */
            .info-card {
                border-radius: 12px;
                transition: transform .2s, box-shadow .2s;
            }

            .info-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            }

            /* Grid ảnh TikTok style */
            .hover-scale {
                transition: transform .3s ease;
            }

            .hover-scale:hover {
                transform: scale(1.05);
            }

            .object-fit-cover {
                object-fit: cover;
                width: 100%;
                height: 100%;
                border-radius: 12px;
            }

            .overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.45);
                opacity: 0;
                transition: opacity .3s ease;
                border-radius: 12px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                text-align: center;
                padding: 0.5rem;
            }

            .image-card:hover .overlay {
                opacity: 1;
            }

            @media(max-width:1200px) {
                .image-card {
                    flex: 0 0 23%;
                    max-width: 23%;
                }
            }

            @media(max-width:992px) {
                .image-card {
                    flex: 0 0 31%;
                    max-width: 31%;
                }
            }

            @media(max-width:768px) {
                .image-card {
                    flex: 0 0 48%;
                    max-width: 48%;
                }
            }

            @media(max-width:576px) {
                .image-card {
                    flex: 0 0 100%;
                    max-width: 100%;
                }
            }

            /* Card shadow + viền */
            .image-card {
                border: 1px solid #ddd;
                border-radius: 12px;
                background: #fff;
                transition: all .3s ease;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            }

            .image-card:hover {
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
                border-color: #bbb;
            }

            .card-footer button,
            .card-footer a {
                font-size: 0.85rem;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function(el) {
                    return new bootstrap.Tooltip(el)
                });
            });
        </script>
    @endpush
@endsection
