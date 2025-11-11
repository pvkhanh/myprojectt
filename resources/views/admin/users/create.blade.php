@extends('layouts.admin')

@section('title', 'Tạo mới người dùng')

@section('content')
    <div class="container-fluid px-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold text-dark mb-2">
                            <i class="fa-solid fa-user-plus text-success me-2"></i>
                            Tạo mới người dùng
                        </h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Người dùng</a></li>
                                <li class="breadcrumb-item active">Tạo mới</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf

            <div class="row g-4">
                <!-- Left Column -->
                <div class="col-lg-4">
                    <!-- Avatar -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-image text-primary me-2"></i>Ảnh đại diện
                            </h5>
                        </div>
                        <div class="card-body text-center">
                            <div class="position-relative d-inline-block mb-3">
                                <img id="avatarPreview" src="{{ asset('images/default-avatar.png') }}"
                                    class="rounded-circle border border-4 border-primary shadow-sm"
                                    style="width: 180px; height: 180px; object-fit: cover;">
                                <label for="avatar"
                                    class="position-absolute bottom-0 end-0 btn btn-warning rounded-circle shadow"
                                    style="width: 45px; height: 45px; cursor: pointer;">
                                    <i class="fa-solid fa-camera"></i>
                                </label>
                            </div>
                            <input type="file" name="avatar" id="avatar"
                                class="form-control @error('avatar') is-invalid @enderror d-none" accept="image/*"
                                onchange="previewAvatar(event)">
                            <p class="text-muted small mb-0">
                                <i class="fa-solid fa-info-circle me-1"></i>
                                Định dạng: JPG, PNG (Max: 2MB)
                            </p>
                            @error('avatar')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Role & Status -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-cog text-primary me-2"></i>Cài đặt tài khoản
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Role -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fa-solid fa-user-shield text-danger me-1"></i>
                                    Vai trò <span class="text-danger">*</span>
                                </label>
                                <select name="role"
                                    class="form-select form-select-lg @error('role') is-invalid @enderror" required>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin - Quản trị
                                        viên</option>
                                    <option value="buyer" {{ old('role') == 'buyer' ? 'selected' : '' }}>Buyer - Người mua
                                    </option>
                                </select>
                                @error('role')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="mb-0">
                                <label class="form-label fw-semibold">
                                    <i class="fa-solid fa-toggle-on text-success me-1"></i>
                                    Trạng thái <span class="text-danger">*</span>
                                </label>
                                <select name="is_active"
                                    class="form-select form-select-lg @error('is_active') is-invalid @enderror" required>
                                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>✅ Hoạt động
                                    </option>
                                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>❌ Vô hiệu hóa
                                    </option>
                                </select>
                                @error('is_active')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-lg-8">
                    <!-- Account Information -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-key text-primary me-2"></i>Thông tin đăng nhập
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i
                                            class="fa-solid fa-user text-primary me-1"></i>Tên đăng nhập <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="username"
                                        class="form-control form-control-lg @error('username') is-invalid @enderror"
                                        value="{{ old('username') }}" required>
                                    @error('username')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i
                                            class="fa-solid fa-envelope text-info me-1"></i>Email <span
                                            class="text-danger">*</span></label>
                                    <input type="email" name="email"
                                        class="form-control form-control-lg @error('email') is-invalid @enderror"
                                        value="{{ old('email') }}" required>
                                    @error('email')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i
                                            class="fa-solid fa-lock text-warning me-1"></i>Mật khẩu <span
                                            class="text-danger">*</span></label>
                                    <input type="password" name="password"
                                        class="form-control form-control-lg @error('password') is-invalid @enderror"
                                        required>
                                    @error('password')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i
                                            class="fa-solid fa-lock-open text-warning me-1"></i>Xác nhận mật khẩu <span
                                            class="text-danger">*</span></label>
                                    <input type="password" name="password_confirmation"
                                        class="form-control form-control-lg" required>
                                </div>

                                <input type="hidden" name="remember_token" value="{{ Str::random(60) }}">
                            </div>
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold"><i class="fa-solid fa-id-card text-primary me-2"></i>Thông tin cá
                                nhân</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i
                                            class="fa-solid fa-user text-primary me-1"></i>Họ <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="first_name"
                                        class="form-control form-control-lg @error('first_name') is-invalid @enderror"
                                        value="{{ old('first_name') }}" required>
                                    @error('first_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i
                                            class="fa-solid fa-user text-primary me-1"></i>Tên <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="last_name"
                                        class="form-control form-control-lg @error('last_name') is-invalid @enderror"
                                        value="{{ old('last_name') }}" required>
                                    @error('last_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i
                                            class="fa-solid fa-phone text-success me-1"></i>Số điện thoại</label>
                                    <input type="text" name="phone"
                                        class="form-control form-control-lg @error('phone') is-invalid @enderror"
                                        value="{{ old('phone') }}">
                                    @error('phone')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i
                                            class="fa-solid fa-venus-mars text-info me-1"></i>Giới tính</label>
                                    <select name="gender"
                                        class="form-select form-select-lg @error('gender') is-invalid @enderror">
                                        <option value="">-- Chọn giới tính --</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Nam</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Khác</option>
                                    </select>
                                    @error('gender')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i
                                            class="fa-solid fa-cake-candles text-warning me-1"></i>Ngày sinh</label>
                                    <input type="date" name="birthday"
                                        class="form-control form-control-lg @error('birthday') is-invalid @enderror"
                                        value="{{ old('birthday') }}" max="{{ date('Y-m-d') }}">
                                    @error('birthday')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold"><i
                                            class="fa-solid fa-comment text-primary me-1"></i>Giới thiệu bản thân</label>
                                    <textarea name="bio" rows="4" class="form-control form-control-lg @error('bio') is-invalid @enderror">{{ old('bio') }}</textarea>
                                    @error('bio')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email Options -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold"><i class="fa-solid fa-envelope text-primary me-2"></i>Tùy Chọn
                                Email</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="send_welcome_email"
                                    name="send_welcome_email" value="1" checked>
                                <label class="form-check-label" for="send_welcome_email">
                                    <strong>Gửi email chào mừng</strong>
                                    <div class="small text-muted">
                                        Tự động gửi email chào mừng đến người dùng sau khi tạo tài khoản thành công
                                    </div>
                                </label>
                            </div>

                            <div class="alert alert-info mt-3 mb-0">
                                <i class="fa-solid fa-info-circle me-2"></i>
                                <strong>Lưu ý:</strong> Email sẽ được gửi từ <code>{{ config('mail.from.address') }}</code>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                                    <i class="fa-solid fa-times me-2"></i>Hủy bỏ
                                </a>
                                <button type="submit" class="btn btn-success btn-lg px-4">
                                    <i class="fa-solid fa-plus me-2"></i>Tạo mới
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>

    <script>
        function previewAvatar(event) {
            const [file] = event.target.files;
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById('avatarPreview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }

        document.getElementById('avatar').addEventListener('change', function() {
            const file = this.files[0];
            if (file && file.size > 2 * 1024 * 1024) {
                toastr.error('Ảnh vượt quá 2MB. Vui lòng chọn ảnh khác!');
                this.value = '';
                document.getElementById('avatarPreview').src = "{{ asset('images/default-avatar.png') }}";
            }
        });
    </script>
@endsection
