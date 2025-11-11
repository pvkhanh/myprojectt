@extends('layouts.admin')

@section('title', 'Chi tiết danh mục')

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
                    <i class="fas fa-folder-open me-2 text-warning"></i>{{ $category->name }}
                </h2>
                @if ($category->description)
                    <p class="text-muted mb-0">{{ $category->description }}</p>
                @endif
            </div>
            <div>
                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-warning text-dark me-2">
                    <i class="fas fa-edit me-2"></i>Chỉnh sửa
                </a>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                {{-- Statistics Cards --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm bg-gradient-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-white-50 mb-1">Tổng sản phẩm</h6>
                                        <h3 class="fw-bold mb-0">{{ $category->products_count }}</h3>
                                    </div>
                                    <i class="fas fa-box-open fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-white-50 mb-1">Danh mục con</h6>
                                        <h3 class="fw-bold mb-0">{{ $category->children_count }}</h3>
                                    </div>
                                    <i class="fas fa-sitemap fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm bg-gradient-warning text-dark">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="opacity-75 mb-1">Cấp độ</h6>
                                        <h3 class="fw-bold mb-0">{{ $category->level }}</h3>
                                    </div>
                                    <i class="fas fa-layer-group fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Category Hierarchy --}}
                @if ($category->parent || $category->children->count() > 0)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="fas fa-sitemap me-2 text-primary"></i>Cấu trúc danh mục
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="hierarchy-tree">
                                @if ($category->parent)
                                    <div class="parent-category mb-3 p-3 bg-light rounded">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-level-up-alt text-muted me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Danh mục cha</small>
                                                <a href="{{ route('admin.categories.show', $category->parent) }}"
                                                    class="fw-bold text-decoration-none">
                                                    {{ $category->parent->name }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="current-category p-3 bg-warning bg-opacity-10 rounded border border-warning mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-folder text-warning me-2 fs-4"></i>
                                        <div>
                                            <small class="text-muted d-block">Danh mục hiện tại</small>
                                            <strong class="fs-5">{{ $category->name }}</strong>
                                        </div>
                                    </div>
                                </div>

                                @if ($category->children->count() > 0)
                                    <div class="children-categories">
                                        <small class="text-muted d-block mb-2">
                                            <i class="fas fa-level-down-alt me-1"></i>Danh mục con
                                        </small>
                                        <div class="row g-3">
                                            @foreach ($category->children as $child)
                                                <div class="col-md-6">
                                                    <div class="card border">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between align-items-start">
                                                                <div>
                                                                    <h6 class="mb-1">
                                                                        <a href="{{ route('admin.categories.show', $child) }}"
                                                                            class="text-decoration-none">
                                                                            {{ $child->name }}
                                                                        </a>
                                                                    </h6>
                                                                    <small class="text-muted">
                                                                        <i class="fas fa-box me-1"></i>
                                                                        {{ $child->products->count() }} sản phẩm
                                                                    </small>
                                                                </div>
                                                                <a href="{{ route('admin.categories.edit', $child) }}"
                                                                    class="btn btn-sm btn-outline-primary">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Products List --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-box-open me-2 text-success"></i>Sản phẩm trong danh mục
                        </h5>
                        @if ($products->total() > 0)
                            <a href="{{ route('admin.products.create') }}?category_id={{ $category->id }}"
                                class="btn btn-sm btn-success">
                                <i class="fas fa-plus me-1"></i>Thêm sản phẩm
                            </a>
                        @endif
                    </div>
                    <div class="card-body">
                        @if ($products->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="80">Ảnh</th>
                                            <th>Tên sản phẩm</th>
                                            <th>Giá</th>
                                            <th>Tồn kho</th>
                                            <th>Trạng thái</th>
                                            <th class="text-center" width="120">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($products as $product)
                                            <tr>
                                                <td>
                                                    @php
                                                        $img = $product->images->firstWhere('pivot.is_main', true) ?? $product->images->first();
                                                    @endphp
                                                    @if ($img)
                                                        <img src="{{ asset('storage/' . $img->path) }}" class="rounded shadow-sm"
                                                            style="width:60px;height:60px;object-fit:cover;">
                                                    @else
                                                        <div class="bg-light d-flex align-items-center justify-content-center rounded"
                                                            style="width:60px;height:60px;">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.products.show', $product) }}"
                                                        class="text-decoration-none fw-semibold">
                                                        {{ Str::limit($product->name, 40) }}
                                                    </a>
                                                    @if ($product->sku)
                                                        <small class="d-block text-muted">SKU: {{ $product->sku }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($product->sale_price && $product->sale_price < $product->price)
                                                        <div>
                                                            <span class="text-danger fw-bold">
                                                                {{ number_format($product->sale_price, 0, ',', '.') }}đ
                                                            </span>
                                                            <small class="d-block text-muted text-decoration-line-through">
                                                                {{ number_format($product->price, 0, ',', '.') }}đ
                                                            </small>
                                                        </div>
                                                    @else
                                                        <span class="fw-bold">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $stock = $product->stockItems->sum('quantity');
                                                    @endphp
                                                    @if ($stock > 50)
                                                        <span class="badge bg-success">{{ $stock }}</span>
                                                    @elseif($stock > 10)
                                                        <span class="badge bg-warning">{{ $stock }}</span>
                                                    @elseif($stock > 0)
                                                        <span class="badge bg-danger">{{ $stock }}</span>
                                                    @else
                                                        <span class="badge bg-dark">Hết hàng</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge-status {{ $product->status->value }}">
                                                        {{ $product->status->label() }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.products.show', $product) }}"
                                                            class="btn btn-sm btn-outline-info" title="Xem">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.products.edit', $product) }}"
                                                            class="btn btn-sm btn-outline-warning" title="Sửa">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $products->links('components.pagination') }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-3">Chưa có sản phẩm nào trong danh mục này</p>
                                <a href="{{ route('admin.products.create') }}?category_id={{ $category->id }}"
                                    class="btn btn-success">
                                    <i class="fas fa-plus me-2"></i>Thêm sản phẩm đầu tiên
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                {{-- Info Card --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-info-circle text-warning me-2"></i>Thông tin chi tiết</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-3 pb-3 border-bottom">
                                <small class="text-muted d-block mb-1"><i class="fas fa-hashtag me-1"></i>ID</small>
                                <strong>#{{ $category->id }}</strong>
                            </li>
                            <li class="mb-3 pb-3 border-bottom">
                                <small class="text-muted d-block mb-1"><i class="fas fa-link me-1"></i>Slug</small>
                                <code class="bg-light px-2 py-1 rounded">{{ $category->slug }}</code>
                            </li>
                            <li class="mb-3 pb-3 border-bottom">
                                <small class="text-muted d-block mb-1"><i class="fas fa-layer-group me-1"></i>Cấp độ</small>
                                <span class="badge bg-info">Cấp {{ $category->level }}</span>
                            </li>
                            <li class="mb-3 pb-3 border-bottom">
                                <small class="text-muted d-block mb-1"><i class="fas fa-sort-numeric-down me-1"></i>Vị trí</small>
                                <strong>{{ $category->position }}</strong>
                            </li>
                            <li class="mb-3 pb-3 border-bottom">
                                <small class="text-muted d-block mb-1"><i class="fas fa-calendar-plus me-1"></i>Ngày tạo</small>
                                <strong>{{ $category->created_at->format('d/m/Y H:i') }}</strong>
                                <br>
                                <small class="text-muted">{{ $category->created_at->diffForHumans() }}</small>
                            </li>
                            <li>
                                <small class="text-muted d-block mb-1"><i class="fas fa-calendar-check me-1"></i>Cập nhật cuối</small>
                                <strong>{{ $category->updated_at->format('d/m/Y H:i') }}</strong>
                                <br>
                                <small class="text-muted">{{ $category->updated_at->diffForHumans() }}</small>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-bolt text-warning me-2"></i>Thao tác nhanh</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-warning text-dark">
                                <i class="fas fa-edit me-2"></i>Chỉnh sửa danh mục
                            </a>
                            <a href="{{ route('admin.products.create') }}?category_id={{ $category->id }}"
                                class="btn btn-success">
                                <i class="fas fa-plus me-2"></i>Thêm sản phẩm
                            </a>
                            @if ($category->products_count > 0)
                                <a href="{{ route('admin.products.index', ['category_id' => $category->id]) }}"
                                    class="btn btn-info">
                                    <i class="fas fa-list me-2"></i>Xem tất cả sản phẩm
                                </a>
                            @endif
                            <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                                <i class="fas fa-trash me-2"></i>Xóa danh mục
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Delete Form --}}
    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-none" id="deleteForm">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
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
        </script>
    @endpush

    @push('styles')
        <style>
            .bg-gradient-success {
                background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);
            }

            .bg-gradient-primary {
                background: linear-gradient(135deg, #4f46e5 0%, #6d28d9 100%);
            }

            .bg-gradient-warning {
                background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
            }

            .hierarchy-tree {
                position: relative;
            }

            .badge-status {
                padding: 0.5rem 1rem;
                border-radius: 999px;
                font-weight: 700;
                font-size: 0.85rem;
            }

            .badge-status.active {
                background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);
                color: white;
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
