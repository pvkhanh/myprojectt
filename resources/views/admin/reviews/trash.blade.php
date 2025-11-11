@extends('layouts.admin')

@section('title', 'Thùng rác - Đánh giá')

@section('content')
    <div class="container-fluid py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">
                    <i class="fas fa-trash-alt me-2 text-danger"></i>
                    Thùng rác - Đánh giá
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb small text-muted mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.reviews.index') }}">Đánh giá</a></li>
                        <li class="breadcrumb-item active">Thùng rác</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i>Quay lại
                </a>
            </div>
        </div>

        {{-- Alert thông tin --}}
        <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
            <i class="fas fa-info-circle fa-2x me-3"></i>
            <div>
                <h6 class="mb-1 fw-bold">Lưu ý về thùng rác</h6>
                <p class="mb-0">Các đánh giá trong thùng rác sẽ được tự động xóa vĩnh viễn sau 30 ngày.
                    Bạn có thể khôi phục hoặc xóa vĩnh viễn chúng bất cứ lúc nào.</p>
            </div>
        </div>

        {{-- Bộ lọc tìm kiếm --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-10">
                        <label class="form-label fw-semibold">Tìm kiếm</label>
                        <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control"
                            placeholder="Nội dung đánh giá, sản phẩm...">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Tìm kiếm
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Bảng danh sách --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <span class="fw-semibold">Danh sách đánh giá đã xóa</span>
                    <span class="badge bg-danger ms-2">{{ $trashedCount }} mục</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0 table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="80">User</th>
                            <th>Sản phẩm</th>
                            <th width="120">Đánh giá</th>
                            <th>Nội dung</th>
                            <th width="120">Ngày xóa</th>
                            <th class="text-center" width="180">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trashedReviews as $review)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $review->user->avatar_url ?? asset('images/default-avatar.png') }}"
                                            class="rounded-circle me-2"
                                            style="width: 40px; height: 40px; object-fit: cover;">
                                        <div>
                                            <div class="fw-semibold small">{{ $review->user->username }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $review->product->main_image_url }}" class="rounded me-2"
                                            style="width: 50px; height: 50px; object-fit: cover;">
                                        <div>
                                            <div class="fw-semibold">{{ Str::limit($review->product->name, 40) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="rating-stars">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i
                                                class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                        @endfor
                                    </div>
                                    <small class="text-muted d-block">{{ $review->rating }}/5</small>
                                </td>
                                <td>
                                    <div class="review-comment">
                                        {{ Str::limit($review->comment, 80) }}
                                    </div>
                                </td>
                                <td>
                                    <small>{{ $review->deleted_at->format('d/m/Y') }}</small>
                                    <small class="d-block text-muted">{{ $review->deleted_at->format('H:i') }}</small>
                                    <small class="badge bg-danger">{{ $review->deleted_at->diffForHumans() }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <form action="{{ route('admin.reviews.restore', $review) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success"
                                                data-bs-toggle="tooltip" title="Khôi phục">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </form>

                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="confirmForceDelete({{ $review->id }})" data-bs-toggle="tooltip"
                                            title="Xóa vĩnh viễn">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <form action="{{ route('admin.reviews.force-delete', $review) }}" method="POST"
                                        class="d-none" id="forceDeleteForm{{ $review->id }}">
                                        @csrf @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block text-success"></i>
                                    <p class="mb-0 fw-semibold">Thùng rác trống</p>
                                    <small>Tuyệt vời! Không có đánh giá nào trong thùng rác.</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Hiển thị {{ $trashedReviews->firstItem() ?? 0 }} - {{ $trashedReviews->lastItem() ?? 0 }}
                    trong tổng số {{ $trashedReviews->total() }} đánh giá
                </div>
                {{ $trashedReviews->links('components.pagination') }}
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function confirmForceDelete(id) {
                Swal.fire({
                    title: "XÓA VĨNH VIỄN?",
                    html: `
            <div class="text-center py-3">
                <i class="fas fa-exclamation-triangle fa-4x text-danger mb-3"></i>
                <p class="mb-2"><strong>Hành động này không thể hoàn tác!</strong></p>
                <p class="text-muted">Đánh giá sẽ bị xóa vĩnh viễn khỏi hệ thống.</p>
            </div>
        `,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#dc3545",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: '<i class="fas fa-trash me-2"></i>Xóa vĩnh viễn',
                    cancelButtonText: '<i class="fas fa-times me-2"></i>Hủy',
                    reverseButtons: true,
                    customClass: {
                        popup: 'border-danger border-3'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('forceDeleteForm' + id).submit();
                    }
                });
            }

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

            @if (session('error'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: '{{ session('error') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            // Tooltip
            document.addEventListener('DOMContentLoaded', function() {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            });
        </script>
    @endpush

    @push('styles')
        <style>
            .rating-stars {
                font-size: 1rem;
            }

            .review-comment {
                line-height: 1.5;
                color: #475569;
            }

            table {
                border-collapse: separate;
                border-spacing: 0;
            }

            thead {
                background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.03em;
                color: #475569;
                font-size: 0.85rem;
            }

            thead th {
                padding: 14px 16px;
                border-bottom: 2px solid #e2e8f0;
            }

            tbody td {
                padding: 14px 16px;
                border-bottom: 1px solid #f1f5f9;
                vertical-align: middle;
            }

            tbody tr {
                transition: all 0.2s ease;
                background-color: rgba(239, 68, 68, 0.05);
            }

            tbody tr:hover {
                background-color: rgba(239, 68, 68, 0.1);
                transform: translateX(2px);
            }

            .card {
                border-radius: 12px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
            }

            .card-header {
                background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
                border-bottom: 2px solid #e2e8f0;
                border-radius: 12px 12px 0 0 !important;
            }

            .btn {
                border-radius: 8px;
                font-weight: 600;
                transition: all 0.2s ease;
            }

            .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            }

            .alert {
                border-radius: 12px;
                border: none;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            }
        </style>
    @endpush
@endsection
