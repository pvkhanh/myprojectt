create.blade.php
@extends('layouts.admin')

@section('title', isset($mail) ? 'Chỉnh Sửa Mail' : 'Tạo Mail Mới')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--multiple {
            min-height: 45px;
            border-color: #dee2e6;
        }

        .note-editor.note-frame {
            border-color: #dee2e6;
        }

        .segment-card {
            cursor: pointer;
            transition: all 0.3s;
        }

        .segment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .segment-card.active {
            border: 2px solid #0d6efd !important;
            background: #e7f1ff;
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
                            <i class="fa-solid fa-{{ isset($mail) ? 'pen' : 'plus' }} text-primary me-2"></i>
                            {{ isset($mail) ? 'Chỉnh Sửa Mail' : 'Tạo Mail Mới' }}
                        </h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.mails.index') }}">Mail</a></li>
                                <li class="breadcrumb-item active">{{ isset($mail) ? 'Chỉnh sửa' : 'Tạo mới' }}</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="{{ route('admin.mails.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST"
            action="{{ isset($mail) ? route('admin.mails.update', $mail->id) : route('admin.mails.store') }}">
            @csrf
            @if (isset($mail))
                @method('PUT')
            @endif

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
                                        id="subject" name="subject"
                                        value="{{ old('subject', $mail->subject ?? ($template->subject ?? '')) }}"
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
                                        <option value="">Chọn loại mail</option>
                                        @foreach ($types as $type)
                                            <option value="{{ $type->value }}"
                                                {{ old('type', $mail->type->value ?? ($template->type->value ?? '')) == $type->value ? 'selected' : '' }}>
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
                                        value="{{ old('sender_email', $mail->sender_email ?? config('mail.from.address')) }}"
                                        placeholder="admin@example.com">
                                    @error('sender_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12">
                                    <label for="template_key" class="form-label fw-semibold">
                                        Template Key (không bắt buộc)
                                    </label>
                                    <input type="text"
                                        class="form-control form-control-lg @error('template_key') is-invalid @enderror"
                                        id="template_key" name="template_key"
                                        value="{{ old('template_key', $mail->template_key ?? ($template->template_key ?? '')) }}"
                                        placeholder="welcome-email, reset-password...">
                                    @error('template_key')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="fa-solid fa-info-circle me-1"></i>
                                        Sử dụng để nhóm các mail cùng mẫu
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Editor -->
                    <div class="card border-0 shadow-sm mb-4">
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
                                required>{{ old('content', $mail->content ?? ($template->content ?? '')) }}</textarea>
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

                    @if (!isset($mail))
                        <!-- Recipients Selection -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="mb-0 fw-semibold">
                                    <i class="fa-solid fa-users text-primary me-2"></i>Chọn Người Nhận
                                    <span class="text-danger">*</span>
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <label class="form-label fw-semibold">Danh sách người nhận</label>
                                <select class="form-control @error('recipients') is-invalid @enderror" id="recipients"
                                    name="recipients[]" multiple="multiple" required>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ in_array($user->id, old('recipients', $selectedUsers ?? [])) ? 'selected' : '' }}>
                                            {{ trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) }}
                                            ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('recipients')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="fa-solid fa-info-circle me-1"></i>
                                    Chọn một hoặc nhiều người nhận
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Quick Segments (Only for Create) -->
                    @if (!isset($mail))
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="mb-0 fw-semibold">
                                    <i class="fa-solid fa-users-between-lines text-primary me-2"></i>Phân nhóm nhanh
                                </h5>
                            </div>
                            <div class="card-body p-3">
                                <div class="segment-card card border mb-2 p-3" data-segment="all_users">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fa-solid fa-users text-primary fs-4 me-2"></i>
                                            <strong>Tất cả</strong>
                                        </div>
                                        <span class="badge bg-primary">{{ $users->count() }}</span>
                                    </div>
                                </div>
                                <div class="form-text small">
                                    <i class="fa-solid fa-info-circle me-1"></i>
                                    Click vào nhóm để chọn nhanh người nhận
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Advanced Options -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-cog text-primary me-2"></i>Tùy chọn nâng cao
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label for="variables" class="form-label fw-semibold">
                                    Variables (JSON)
                                </label>
                                <textarea class="form-control @error('variables') is-invalid @enderror" id="variables" name="variables"
                                    rows="4" placeholder='{"promo_code": "SAVE20", "expire_date": "31/12/2024"}'>{{ old('variables', isset($mail) && $mail->variables ? json_encode($mail->variables) : '') }}</textarea>
                                @error('variables')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text small">
                                    Nhập dữ liệu JSON để tùy chỉnh nội dung mail
                                </div>
                            </div>

                            @if (!isset($mail))
                                <div class="mb-0">
                                    <label for="schedule_at" class="form-label fw-semibold">
                                        Lên lịch gửi
                                    </label>
                                    <input type="datetime-local"
                                        class="form-control @error('schedule_at') is-invalid @enderror" id="schedule_at"
                                        name="schedule_at" value="{{ old('schedule_at') }}">
                                    @error('schedule_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text small">
                                        Để trống để gửi ngay sau khi tạo
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fa-solid fa-{{ isset($mail) ? 'save' : 'plus' }} me-2"></i>
                                    {{ isset($mail) ? 'Cập nhật' : 'Tạo Mail' }}
                                </button>
                                <button type="button" class="btn btn-outline-info btn-lg" id="previewBtn">
                                    <i class="fa-solid fa-eye me-2"></i> Xem trước
                                </button>
                                <a href="{{ route('admin.mails.index') }}" class="btn btn-outline-secondary btn-lg">
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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

            // Select2 for Recipients
            $('#recipients').select2({
                placeholder: 'Chọn người nhận...',
                allowClear: true,
                width: '100%'
            });

            // Segment Quick Selection
            $('.segment-card').click(function() {
                $('.segment-card').removeClass('active');
                $(this).addClass('active');

                // Select all users in recipients dropdown
                $('#recipients option').prop('selected', true);
                $('#recipients').trigger('change');
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
