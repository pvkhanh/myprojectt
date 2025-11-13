@extends('layouts.admin')

@section('title', 'Tạo Thông Báo Mới')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-dark mb-2">
                        <i class="fa-solid fa-plus-circle text-primary me-2"></i>
                        Tạo Thông Báo Mới
                    </h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.notifications.index') }}">Thông báo</a></li>
                            <li class="breadcrumb-item active">Tạo mới</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary btn-lg">
                    <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.notifications.store') }}" method="POST" id="notificationForm">
        @csrf
        <div class="row g-4">
            <!-- Main Form -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-info-circle text-primary me-2"></i>Thông tin thông báo
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <!-- Type -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold required">Loại thông báo</label>
                                <select name="type" class="form-select form-select-lg @error('type') is-invalid @enderror" required>
                                    <option value="">-- Chọn loại --</option>
                                    @foreach($types as $type)
                                    <option value="{{ $type->value }}" {{ old('type') == $type->value ? 'selected' : '' }}>
                                        {{ ucfirst($type->value) }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Title -->
                            <div class="col-md-12">
                                <label class="form-label fw-semibold required">Tiêu đề</label>
                                <input type="text" name="title" class="form-control form-control-lg @error('title') is-invalid @enderror"
                                    placeholder="Nhập tiêu đề thông báo..." value="{{ old('title') }}" required maxlength="255">
                                @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Tối đa 255 ký tự</div>
                            </div>

                            <!-- Message -->
                            <div class="col-md-12">
                                <label class="form-label fw-semibold required">Nội dung</label>
                                <textarea name="message" class="form-control @error('message') is-invalid @enderror"
                                    rows="6" placeholder="Nhập nội dung thông báo..." required maxlength="2000">{{ old('message') }}</textarea>
                                @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Tối đa 2000 ký tự. Hỗ trợ biến động: {name}, {email}, {order_number}, v.v.</div>
                            </div>

                            <!-- Variables (Optional) -->
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Biến động (JSON)</label>
                                <textarea name="variables" class="form-control @error('variables') is-invalid @enderror"
                                    rows="4" placeholder='{"name": "John Doe", "order_number": "ORD-001"}'>{{ old('variables') }}</textarea>
                                @error('variables')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="fa-solid fa-info-circle text-info me-1"></i>
                                    Định dạng JSON. Ví dụ: {"key": "value"}
                                </div>
                            </div>

                            <!-- Expires At -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Hết hạn vào</label>
                                <input type="datetime-local" name="expires_at" class="form-control form-control-lg @error('expires_at') is-invalid @enderror"
                                    value="{{ old('expires_at') }}" min="{{ now()->format('Y-m-d\TH:i') }}">
                                @error('expires_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Để trống nếu không có thời hạn</div>
                            </div>

                            <!-- Send Immediately -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold d-block">Gửi ngay</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="send_immediately" id="sendImmediately"
                                        value="1" {{ old('send_immediately') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sendImmediately">
                                        Gửi thông báo real-time ngay lập tức
                                    </label>
                                </div>
                                <div class="form-text mt-2">
                                    <i class="fa-solid fa-bolt text-warning me-1"></i>
                                    Bật để gửi thông báo real-time đến người dùng
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recipients -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-users text-primary me-2"></i>Người nhận
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <label class="form-label fw-semibold required">Chọn người nhận</label>
                        <select name="user_ids[]" class="form-select @error('user_ids') is-invalid @enderror" 
                            id="userSelect" multiple required>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ in_array($user->id, old('user_ids', [])) ? 'selected' : '' }}>
                                {{ $user->email }} - {{ trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: 'N/A' }}
                            </option>
                            @endforeach
                        </select>
                        @error('user_ids')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="form-text mt-2">
                            <i class="fa-solid fa-info-circle me-1"></i>
                            Có thể chọn nhiều người nhận
                        </div>

                        <div class="alert alert-info mt-3">
                            <i class="fa-solid fa-lightbulb me-2"></i>
                            <strong>Mẹo:</strong> Sử dụng Ctrl/Cmd + Click để chọn nhiều người
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top py-3">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fa-solid fa-paper-plane me-2"></i> Tạo Thông Báo
                            </button>
                            <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary btn-lg">
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Initialize Select2
    $('#userSelect').select2({
        theme: 'bootstrap-5',
        placeholder: 'Chọn người nhận...',
        allowClear: true,
        width: '100%'
    });

    // Form validation
    const form = document.getElementById('notificationForm');
    form.addEventListener('submit', function(e) {
        const variables = document.querySelector('[name="variables"]').value.trim();
        
        if (variables) {
            try {
                JSON.parse(variables);
            } catch (error) {
                e.preventDefault();
                alert('Biến động JSON không hợp lệ!\n\nVui lòng kiểm tra lại định dạng.');
                return false;
            }
        }

        const selectedUsers = $('#userSelect').val();
        if (!selectedUsers || selectedUsers.length === 0) {
            e.preventDefault();
            alert('Vui lòng chọn ít nhất 1 người nhận!');
            return false;
        }
    });

    // Character counter
    const messageTextarea = document.querySelector('[name="message"]');
    if (messageTextarea) {
        const counter = document.createElement('div');
        counter.className = 'form-text text-end mt-1';
        messageTextarea.parentNode.appendChild(counter);
        
        const updateCounter = () => {
            const length = messageTextarea.value.length;
            const max = 2000;
            counter.innerHTML = `<span class="${length > max ? 'text-danger' : ''}">${length}</span> / ${max} ký tự`;
        };
        
        messageTextarea.addEventListener('input', updateCounter);
        updateCounter();
    }
});
</script>
@endpush