@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Thông Báo')

@section('content')
    <div class="container-fluid px-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold text-dark mb-2">
                            <i class="fa-solid fa-pen text-primary me-2"></i>
                            Chỉnh Sửa Thông Báo
                        </h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.notifications.index') }}">Thông báo</a>
                                </li>
                                <li class="breadcrumb-item"><a
                                        href="{{ route('admin.notifications.show', $notification->id) }}">Chi tiết</a></li>
                                <li class="breadcrumb-item active">Chỉnh sửa</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.notifications.show', $notification->id) }}"
                            class="btn btn-outline-secondary btn-lg">
                            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.notifications.update', $notification->id) }}" method="POST" id="notificationForm">
            @csrf
            @method('PUT')

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
                                    <select name="type"
                                        class="form-select form-select-lg @error('type') is-invalid @enderror" required>
                                        <option value="">-- Chọn loại --</option>
                                        @foreach ($types as $type)
                                            <option value="{{ $type->value }}"
                                                {{ old('type', $notification->type->value) == $type->value ? 'selected' : '' }}>
                                                {{ ucfirst($type->value) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Read Status (Info only) -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Trạng thái hiện tại</label>
                                    <div class="form-control form-control-lg bg-light" readonly>
                                        @if ($notification->is_read)
                                            <span class="badge bg-success">
                                                <i class="fa-solid fa-check-circle me-1"></i>Đã đọc
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                <i class="fa-solid fa-envelope me-1"></i>Chưa đọc
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Title -->
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold required">Tiêu đề</label>
                                    <input type="text" name="title"
                                        class="form-control form-control-lg @error('title') is-invalid @enderror"
                                        placeholder="Nhập tiêu đề thông báo..."
                                        value="{{ old('title', $notification->title) }}" required maxlength="255">
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Tối đa 255 ký tự</div>
                                </div>

                                <!-- Message -->
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold required">Nội dung</label>
                                    <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="6"
                                        placeholder="Nhập nội dung thông báo..." required maxlength="2000">{{ old('message', $notification->message) }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Tối đa 2000 ký tự. Hỗ trợ biến động: {name}, {email},
                                        {order_number}, v.v.</div>
                                </div>

                                <!-- Variables (Optional) -->
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Biến động (JSON)</label>
                                    <textarea name="variables" class="form-control @error('variables') is-invalid @enderror" rows="4"
                                        placeholder='{"name": "John Doe", "order_number": "ORD-001"}'>{{ old('variables', $notification->variables ? json_encode($notification->variables, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '') }}</textarea>
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
                                    <input type="datetime-local" name="expires_at"
                                        class="form-control form-control-lg @error('expires_at') is-invalid @enderror"
                                        value="{{ old('expires_at', $notification->expires_at ? $notification->expires_at->format('Y-m-d\TH:i') : '') }}">
                                    @error('expires_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Để trống nếu không có thời hạn</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Change Log Alert -->
                    <div class="alert alert-warning">
                        <i class="fa-solid fa-exclamation-triangle me-2"></i>
                        <strong>Lưu ý:</strong> Thay đổi thông báo không ảnh hưởng đến các bản đã gửi. Chỉ cập nhật thông
                        tin hiển thị.
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-user text-primary me-2"></i>Người nhận
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3"
                                    style="width:50px; height:50px;">
                                    <i class="fa-solid fa-user text-primary fs-4"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">
                                        {{ trim(($notification->user->first_name ?? '') . ' ' . ($notification->user->last_name ?? '')) ?: 'N/A' }}
                                    </div>
                                    <div class="small text-muted">{{ $notification->user->email }}</div>
                                </div>
                            </div>
                            <div class="alert alert-info mb-0">
                                <i class="fa-solid fa-info-circle me-2"></i>
                                Không thể thay đổi người nhận sau khi tạo
                            </div>
                        </div>

                        <div class="card-body border-top p-4">
                            <h6 class="fw-semibold mb-3">Thông tin thêm</h6>
                            <div class="mb-2">
                                <small class="text-muted">Ngày tạo:</small>
                                <div class="fw-semibold">{{ $notification->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Cập nhật lần cuối:</small>
                                <div class="fw-semibold">{{ $notification->updated_at->format('d/m/Y H:i') }}</div>
                            </div>
                            @if ($notification->read_at)
                                <div class="mb-2">
                                    <small class="text-muted">Đã đọc lúc:</small>
                                    <div class="fw-semibold text-success">
                                        {{ $notification->read_at->format('d/m/Y H:i') }}</div>
                                </div>
                            @endif
                        </div>

                        <div class="card-footer bg-white border-top py-3">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fa-solid fa-save me-2"></i> Lưu thay đổi
                                </button>
                                <a href="{{ route('admin.notifications.show', $notification->id) }}"
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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
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
            });

            // Character counter for message
            const messageTextarea = document.querySelector('[name="message"]');
            if (messageTextarea) {
                const counter = document.createElement('div');
                counter.className = 'form-text text-end mt-1';
                messageTextarea.parentNode.appendChild(counter);

                const updateCounter = () => {
                    const length = messageTextarea.value.length;
                    const max = 2000;
                    counter.innerHTML =
                        `<span class="${length > max ? 'text-danger' : ''}">${length}</span> / ${max} ký tự`;
                };

                messageTextarea.addEventListener('input', updateCounter);
                updateCounter();
            }

            // Character counter for title
            const titleInput = document.querySelector('[name="title"]');
            if (titleInput) {
                const counter = document.createElement('div');
                counter.className = 'form-text text-end mt-1';
                titleInput.parentNode.appendChild(counter);

                const updateCounter = () => {
                    const length = titleInput.value.length;
                    const max = 255;
                    counter.innerHTML =
                        `<span class="${length > max ? 'text-danger' : ''}">${length}</span> / ${max} ký tự`;
                };

                titleInput.addEventListener('input', updateCounter);
                updateCounter();
            }

            // Auto-format JSON
            const variablesTextarea = document.querySelector('[name="variables"]');
            if (variablesTextarea && variablesTextarea.value.trim()) {
                try {
                    const parsed = JSON.parse(variablesTextarea.value);
                    variablesTextarea.value = JSON.stringify(parsed, null, 2);
                } catch (e) {
                    // Invalid JSON, leave as is
                }
            }
        });
    </script>
@endpush
