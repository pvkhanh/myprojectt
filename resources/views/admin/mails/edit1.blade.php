@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Mail')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <style>
        .note-editor.note-frame {
            border-color: #dee2e6;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold text-dark mb-2">
                            <i class="fa-solid fa-pen text-warning me-2"></i>
                            Chỉnh Sửa Mail
                        </h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.mails.index') }}">Mail</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.mails.show', $mail->id) }}">Chi
                                        tiết</a></li>
                                <li class="breadcrumb-item active">Chỉnh sửa</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.mails.show', $mail->id) }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
                        </a>
                        <a href="{{ route('admin.mails.preview', $mail->id) }}" target="_blank"
                            class="btn btn-outline-info btn-lg">
                            <i class="fa-solid fa-eye me-2"></i> Xem Preview
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.mails.update', $mail->id) }}">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <!-- Main Form -->
                <div class="col-lg-8">
                    <!-- Basic Information -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-info-circle text-primary me-2"></i>Thông tin cơ bản
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="subject" class="form-label fw-semibold">
                                        Tiêu đề Mail <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control form-control-lg @error('subject') is-invalid @enderror"
                                        id="subject" name="subject" value="{{ old('subject', $mail->subject) }}"
                                        placeholder="Nhập tiêu đề mail..." required>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="type" class="form-label fw-semibold">
                                        Loại Mail <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select form-select-lg @error('type') is-invalid @enderror"
                                        id="type" name="type" required>
                                        @foreach ($types as $type)
                                            <option value="{{ $type->value }}"
                                                {{ old('type', $mail->type->value) == $type->value ? 'selected' : '' }}>
                                                {{ ucfirst($type->value) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="sender_email" class="form-label fw-semibold">
                                        Email Người Gửi
                                    </label>
                                    <input type="email"
                                        class="form-control form-control-lg @error('sender_email') is-invalid @enderror"
                                        id="sender_email" name="sender_email"
                                        value="{{ old('sender_email', $mail->sender_email) }}"
                                        placeholder="admin@example.com">
                                    @error('sender_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12">
                                    <label for="template_key" class="form-label fw-semibold">
                                        Template Key
                                    </label>
                                    <input type="text"
                                        class="form-control form-control-lg @error('template_key') is-invalid @enderror"
                                        id="template_key" name="template_key"
                                        value="{{ old('template_key', $mail->template_key) }}"
                                        placeholder="welcome-email, reset-password...">
                                    @error('template_key')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Editor -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-file-lines text-primary me-2"></i>Nội dung Mail
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <label class="form-label fw-semibold">
                                Nội dung <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="15"
                                required>{{ old('content', $mail->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="alert alert-info mt-3 mb-0">
                                <strong><i class="fa-solid fa-lightbulb me-2"></i>Biến động có thể sử dụng:</strong>
                                <ul class="mb-0 mt-2">
                                    <li><code>{{ '{{' }}username{{ ' ?>' }}'}}</code> - Tên đầy đủ người
                                        nhận
                                    </li>
                                    <li><code>{{ '{{' }}email{{ ' ?>' }}'}}</code> - Email người nhận
                                    </li>
                                    <li><code>{{ '{{' }}first_name{{ ' ?>' }}'}}</code> - Tên</li>
                                    <li><code>{{ '{{' }}last_name{{ ' ?>' }}'}}</code> - Họ</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Mail Stats -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-chart-simple text-primary me-2"></i>Thống Kê
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                <div>
                                    <div class="small text-muted">Tổng người nhận</div>
                                    <h4 class="fw-bold mb-0">{{ $mail->recipients->count() }}</h4>
                                </div>
                                <div class="fs-2 text-primary opacity-50">
                                    <i class="fa-solid fa-users"></i>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-success">Đã gửi:</span>
                                <strong>{{ $mail->recipients->where('status', 'sent')->count() }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-warning">Chờ gửi:</span>
                                <strong>{{ $mail->recipients->where('status', 'pending')->count() }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-danger">Thất bại:</span>
                                <strong>{{ $mail->recipients->where('status', 'failed')->count() }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Advanced Options -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-cog text-primary me-2"></i>Tùy chọn nâng cao
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <label for="variables" class="form-label fw-semibold">
                                Variables (JSON)
                            </label>
                            <textarea class="form-control @error('variables') is-invalid @enderror" id="variables" name="variables"
                                rows="5" placeholder='{"promo_code": "SAVE20"}'>{{ old('variables', $mail->variables ? json_encode($mail->variables) : '') }}</textarea>
                            @error('variables')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text small">
                                Nhập dữ liệu JSON để tùy chỉnh
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-warning btn-lg text-white">
                                    <i class="fa-solid fa-save me-2"></i> Cập Nhật Mail
                                </button>
                                <button type="button" class="btn btn-outline-info btn-lg" id="previewBtn">
                                    <i class="fa-solid fa-eye me-2"></i> Xem trước
                                </button>
                                <a href="{{ route('admin.mails.show', $mail->id) }}"
                                    class="btn btn-outline-secondary btn-lg">
                                    <i class="fa-solid fa-times me-2"></i> Hủy
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script>
        $(document).ready(function() {
            // Summernote Editor
            $('#content').summernote({
                height: 400,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });

            // Preview Button
            $('#previewBtn').click(function() {
                const content = $('#content').summernote('code');
                const subject = $('#subject').val();

                const previewWindow = window.open('', '_blank');
                previewWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>${subject}</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; }
                    h1 { border-bottom: 2px solid #ccc; padding-bottom: 10px; }
                </style>
            </head>
            <body>
                <h1>${subject}</h1>
                ${content}
            </body>
            </html>
        `);
            });
        });
    </script>
@endpush
