@extends('layouts.admin')

@section('title', 'Chỉnh sửa đánh giá')

@section('content')
    <div class="container-fluid py-4">

        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.reviews.index') }}">Đánh giá</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.reviews.show', $review) }}">Chi tiết
                        #{{ $review->id }}</a></li>
                <li class="breadcrumb-item active">Chỉnh sửa</li>
            </ol>
        </nav>

        <div class="row g-4">
            {{-- Form chỉnh sửa --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-gradient-primary text-white">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-edit me-2"></i>
                            Chỉnh sửa đánh giá #{{ $review->id }}
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('admin.reviews.update', $review) }}" method="POST" id="editReviewForm">
                            @csrf
                            @method('PUT')

                            {{-- Thông tin người đánh giá (readonly) --}}
                            <div class="mb-4 pb-4 border-bottom">
                                <h6 class="text-muted mb-3 fw-semibold">
                                    <i class="fas fa-user me-2"></i>NGƯỜI ĐÁNH GIÁ
                                </h6>
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <img src="{{ $review->user->avatar_url ?? asset('images/default-avatar.png') }}"
                                        class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                    <div>
                                        <div class="fw-bold">{{ $review->user->username }}</div>
                                        <small class="text-muted">{{ $review->user->email }}</small>
                                    </div>
                                </div>
                            </div>

                            {{-- Thông tin sản phẩm (readonly) --}}
                            <div class="mb-4 pb-4 border-bottom">
                                <h6 class="text-muted mb-3 fw-semibold">
                                    <i class="fas fa-box me-2"></i>SẢN PHẨM
                                </h6>
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <img src="{{ $review->product->main_image_url }}" class="rounded me-3"
                                        style="width: 70px; height: 70px; object-fit: cover;">
                                    <div>
                                        <div class="fw-bold">{{ $review->product->name }}</div>
                                        <small class="text-muted">
                                            <i class="fas fa-tag me-1"></i>
                                            {{ number_format($review->product->price, 0, ',', '.') }}đ
                                        </small>
                                    </div>
                                </div>
                            </div>

                            {{-- Đánh giá sao --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-star text-warning me-2"></i>
                                    Số sao đánh giá <span class="text-danger">*</span>
                                </label>
                                <div class="rating-input-container">
                                    <div class="rating-stars-interactive mb-2" id="ratingStars">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star rating-star {{ $i <= old('rating', $review->rating) ? 'active' : '' }}"
                                                data-rating="{{ $i }}"></i>
                                        @endfor
                                    </div>
                                    <input type="hidden" name="rating" id="ratingInput"
                                        value="{{ old('rating', $review->rating) }}">
                                    <div class="rating-text">
                                        <span class="badge bg-warning text-dark fs-6" id="ratingDisplay">
                                            {{ old('rating', $review->rating) }} sao
                                        </span>
                                    </div>
                                </div>
                                @error('rating')
                                    <div class="text-danger small mt-1">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Nội dung đánh giá --}}
                            <div class="mb-4">
                                <label for="comment" class="form-label fw-bold">
                                    <i class="fas fa-comment me-2"></i>
                                    Nội dung đánh giá <span class="text-danger">*</span>
                                </label>
                                <textarea name="comment" id="comment" rows="6" class="form-control @error('comment') is-invalid @enderror"
                                    placeholder="Nhập nội dung đánh giá..." maxlength="1000">{{ old('comment', $review->comment) }}</textarea>
                                <div class="d-flex justify-content-between mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Tối thiểu 10 ký tự, tối đa 1000 ký tự
                                    </small>
                                    <small class="text-muted">
                                        <span id="charCount">{{ strlen(old('comment', $review->comment)) }}</span>/1000
                                    </small>
                                </div>
                                @error('comment')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Trạng thái --}}
                            <div class="mb-4">
                                <label for="status" class="form-label fw-bold">
                                    <i class="fas fa-toggle-on me-2"></i>
                                    Trạng thái <span class="text-danger">*</span>
                                </label>
                                <select name="status" id="status"
                                    class="form-select @error('status') is-invalid @enderror">
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->value }}"
                                            {{ old('status', $review->status->value) == $status->value ? 'selected' : '' }}>
                                            {{ ucfirst($status->value) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    <strong>Approved:</strong> Hiển thị công khai |
                                    <strong>Pending:</strong> Chờ duyệt |
                                    <strong>Rejected:</strong> Từ chối
                                </small>
                            </div>

                            {{-- Ghi chú cho admin --}}
                            <div class="alert alert-info">
                                <i class="fas fa-shield-alt me-2"></i>
                                <strong>Lưu ý:</strong> Việc chỉnh sửa đánh giá sẽ được ghi lại trong lịch sử.
                                Hãy đảm bảo nội dung chỉnh sửa phù hợp với chính sách của hệ thống.
                            </div>

                            {{-- Buttons --}}
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('admin.reviews.show', $review) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Hủy
                                </a>
                                <button type="reset" class="btn btn-outline-warning">
                                    <i class="fas fa-redo me-2"></i>Đặt lại
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Lưu thay đổi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Sidebar: Preview và thông tin --}}
            <div class="col-lg-4">
                {{-- Preview card --}}
                <div class="card border-0 shadow-sm mb-4 sticky-top" style="top: 20px;">
                    <div class="card-header bg-white">
                        <h6 class="mb-0 fw-bold">
                            <i class="fas fa-eye me-2 text-info"></i>
                            Xem trước
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="preview-container p-3 border rounded bg-light">
                            <div class="d-flex align-items-center mb-3">
                                <img src="{{ $review->user->avatar_url ?? asset('images/default-avatar.png') }}"
                                    class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                <div>
                                    <div class="fw-bold small">{{ $review->user->username }}</div>
                                    <div class="rating-stars-preview">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star small {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"
                                                id="previewStar{{ $i }}"></i>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <div class="preview-comment">
                                <p class="mb-0 small" id="previewComment">{{ $review->comment }}</p>
                            </div>
                            <div class="mt-2">
                                <span class="badge preview-status" id="previewStatus">
                                    {{ ucfirst($review->status->value) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Lịch sử card --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h6 class="mb-0 fw-bold">
                            <i class="fas fa-history me-2 text-secondary"></i>
                            Lịch sử
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-icon bg-primary">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <div class="timeline-content">
                                    <small class="text-muted">Ngày tạo</small>
                                    <div class="fw-semibold">{{ $review->created_at->format('d/m/Y H:i') }}</div>
                                    <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-icon bg-success">
                                    <i class="fas fa-edit"></i>
                                </div>
                                <div class="timeline-content">
                                    <small class="text-muted">Lần cập nhật cuối</small>
                                    <div class="fw-semibold">{{ $review->updated_at->format('d/m/Y H:i') }}</div>
                                    <small class="text-muted">{{ $review->updated_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Rating stars interactive
                const stars = document.querySelectorAll('.rating-star');
                const ratingInput = document.getElementById('ratingInput');
                const ratingDisplay = document.getElementById('ratingDisplay');

                stars.forEach(star => {
                    star.addEventListener('click', function() {
                        const rating = this.dataset.rating;
                        ratingInput.value = rating;
                        ratingDisplay.textContent = rating + ' sao';

                        // Update UI
                        stars.forEach((s, index) => {
                            if (index < rating) {
                                s.classList.add('active');
                            } else {
                                s.classList.remove('active');
                            }
                        });

                        // Update preview
                        updatePreviewRating(rating);
                    });

                    star.addEventListener('mouseenter', function() {
                        const rating = this.dataset.rating;
                        stars.forEach((s, index) => {
                            if (index < rating) {
                                s.classList.add('hover');
                            } else {
                                s.classList.remove('hover');
                            }
                        });
                    });
                });

                document.getElementById('ratingStars').addEventListener('mouseleave', function() {
                    stars.forEach(s => s.classList.remove('hover'));
                });

                // Character counter
                const commentTextarea = document.getElementById('comment');
                const charCount = document.getElementById('charCount');

                commentTextarea.addEventListener('input', function() {
                    charCount.textContent = this.value.length;

                    // Update preview
                    document.getElementById('previewComment').textContent = this.value ||
                    'Nội dung đánh giá...';
                });

                // Status preview
                const statusSelect = document.getElementById('status');
                statusSelect.addEventListener('change', function() {
                    updatePreviewStatus(this.value);
                });

                // Functions
                function updatePreviewRating(rating) {
                    for (let i = 1; i <= 5; i++) {
                        const star = document.getElementById('previewStar' + i);
                        if (i <= rating) {
                            star.classList.add('text-warning');
                            star.classList.remove('text-muted');
                        } else {
                            star.classList.remove('text-warning');
                            star.classList.add('text-muted');
                        }
                    }
                }

                function updatePreviewStatus(status) {
                    const previewStatus = document.getElementById('previewStatus');
                    previewStatus.className = 'badge preview-status';
                    previewStatus.textContent = status.charAt(0).toUpperCase() + status.slice(1);

                    switch (status) {
                        case 'approved':
                            previewStatus.classList.add('bg-success');
                            break;
                        case 'pending':
                            previewStatus.classList.add('bg-warning', 'text-dark');
                            break;
                        case 'rejected':
                            previewStatus.classList.add('bg-danger');
                            break;
                    }
                }

                // Form validation
                document.getElementById('editReviewForm').addEventListener('submit', function(e) {
                    const rating = ratingInput.value;
                    const comment = commentTextarea.value.trim();

                    if (!rating || rating < 1 || rating > 5) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: 'Vui lòng chọn số sao đánh giá (1-5)',
                            confirmButtonColor: '#4f46e5'
                        });
                        return false;
                    }

                    if (comment.length < 10) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: 'Nội dung đánh giá phải có ít nhất 10 ký tự',
                            confirmButtonColor: '#4f46e5'
                        });
                        return false;
                    }
                });
            });

            // Toast notifications
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
        </script>
    @endpush

    @push('styles')
        <style>
            .bg-gradient-primary {
                background: linear-gradient(135deg, #4f46e5 0%, #6d28d9 100%);
            }

            .rating-stars-interactive {
                font-size: 2.5rem;
                user-select: none;
            }

            .rating-star {
                cursor: pointer;
                color: #d1d5db;
                transition: all 0.2s ease;
                margin: 0 5px;
            }

            .rating-star:hover,
            .rating-star.hover {
                color: #fbbf24;
                transform: scale(1.2);
            }

            .rating-star.active {
                color: #fbbf24;
            }

            .form-control,
            .form-select {
                border: 2px solid #e2e8f0;
                border-radius: 8px;
                transition: all 0.2s ease;
                font-size: 15px;
            }

            .form-control:focus,
            .form-select:focus {
                border-color: #4f46e5;
                box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
            }

            .card {
                border-radius: 12px;
                border: none;
            }

            .card-header {
                border-radius: 12px 12px 0 0 !important;
                border-bottom: 2px solid #e2e8f0;
            }

            .btn {
                border-radius: 8px;
                font-weight: 600;
                padding: 10px 24px;
                transition: all 0.2s ease;
            }

            .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            }

            .preview-container {
                background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            }

            .preview-comment {
                line-height: 1.6;
                color: #475569;
                white-space: pre-wrap;
            }

            .timeline {
                position: relative;
                padding-left: 30px;
            }

            .timeline-item {
                position: relative;
                padding-bottom: 20px;
            }

            .timeline-item:not(:last-child)::before {
                content: '';
                position: absolute;
                left: -22px;
                top: 30px;
                width: 2px;
                height: calc(100% - 20px);
                background: #e2e8f0;
            }

            .timeline-icon {
                position: absolute;
                left: -30px;
                top: 0;
                width: 32px;
                height: 32px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 0.75rem;
            }

            .timeline-content {
                padding-left: 10px;
            }

            .sticky-top {
                z-index: 1020;
            }

            .alert {
                border-radius: 12px;
                border: none;
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

            .card {
                animation: fadeIn 0.5s ease;
            }
        </style>
    @endpush
@endsection
