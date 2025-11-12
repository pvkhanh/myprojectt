@extends('layouts.admin')

@section('title', 'Chỉnh sửa người dùng')

@section('content')
    <div class="container-fluid px-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold text-dark mb-2">
                            <i class="fa-solid fa-user-pen text-warning me-2"></i>
                            Chỉnh sửa người dùng
                        </h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Người dùng</a></li>
                                <li class="breadcrumb-item active">Chỉnh sửa</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-outline-info btn-lg">
                            <i class="fa-solid fa-eye me-2"></i> Xem chi tiết
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data" id="editUserForm">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <!-- Left Column: Avatar & Settings -->
                <div class="col-lg-4">
                    <!-- Avatar Upload -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-image text-primary me-2"></i>Ảnh đại diện
                            </h5>
                        </div>
                        <div class="card-body text-center py-4">
                            <!-- Avatar Preview -->
                            <div class="position-relative d-inline-block mb-3">
                                @if($user->avatar)
                                    <img id="avatarPreview" 
                                         src="{{ $user->avatar_url }}"
                                         class="rounded-circle border border-4 border-primary shadow-lg"
                                         style="width: 200px; height: 200px; object-fit: cover;">
                                @else
                                    <div id="avatarPreview" 
                                         class="rounded-circle bg-gradient-primary text-white d-flex align-items-center justify-content-center shadow-lg mx-auto"
                                         style="width: 200px; height: 200px; font-size: 4rem; font-weight: bold;">
                                        {{ $user->initials }}
                                    </div>
                                @endif
                                
                                <!-- Upload Button Overlay -->
                                <label for="avatar" 
                                       class="position-absolute bottom-0 end-0 btn btn-warning rounded-circle shadow-lg"
                                       style="width: 50px; height: 50px; cursor: pointer;"
                                       title="Thay đổi ảnh">
                                    <i class="fa-solid fa-camera fs-5"></i>
                                </label>
                                
                                <!-- Remove Button -->
                                @if($user->avatar)
                                <button type="button" 
                                        id="removeAvatarBtn"
                                        class="position-absolute top-0 start-0 btn btn-danger rounded-circle shadow-lg"
                                        style="width: 40px; height: 40px;"
                                        title="Xóa ảnh"
                                        onclick="removeAvatarPreview()">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                                @endif
                            </div>

                            <!-- Hidden File Input -->
                            <input type="file" 
                                   name="avatar" 
                                   id="avatar"
                                   class="d-none" 
                                   accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                   onchange="previewAvatar(event)">

                            <!-- Current Avatar Info -->
                            @if($user->avatar)
                            <div class="alert alert-light small mb-2">
                                <i class="fa-solid fa-circle-info me-1"></i>
                                <strong>Avatar hiện tại:</strong> Đã tải lên
                            </div>
                            @endif

                            <!-- Info Text -->
                            <div class="alert alert-info small mb-0">
                                <i class="fa-solid fa-info-circle me-2"></i>
                                <strong>Định dạng:</strong> JPG, PNG, GIF, WEBP<br>
                                <strong>Kích thước tối đa:</strong> 2MB
                            </div>

                            @error('avatar')
                                <div class="text-danger small mt-2">
                                    <i class="fa-solid fa-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
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
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                                        Admin - Quản trị viên
                                    </option>
                                    <option value="buyer" {{ old('role', $user->role) == 'buyer' ? 'selected' : '' }}>
                                        Buyer - Người mua
                                    </option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
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
                                    <option value="1" {{ old('is_active', $user->is_active) == 1 ? 'selected' : '' }}>
                                        ✅ Hoạt động
                                    </option>
                                    <option value="0" {{ old('is_active', $user->is_active) == 0 ? 'selected' : '' }}>
                                        ❌ Vô hiệu hóa
                                    </option>
                                </select>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Form Details -->
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
                                <!-- Username -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fa-solid fa-user text-primary me-1"></i>
                                        Tên đăng nhập <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           name="username"
                                           class="form-control form-control-lg @error('username') is-invalid @enderror"
                                           value="{{ old('username', $user->username) }}" 
                                           required>
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fa-solid fa-envelope text-info me-1"></i>
                                        Email <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="email" 
                                               name="email"
                                               class="form-control form-control-lg @error('email') is-invalid @enderror"
                                               value="{{ old('email', $user->email) }}" 
                                               required>
                                        @if($user->email_verified_at)
                                            <span class="input-group-text bg-success text-white">
                                                <i class="fa-solid fa-circle-check"></i>
                                            </span>
                                        @endif
                                    </div>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Password -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fa-solid fa-lock text-warning me-1"></i>
                                        Mật khẩu mới
                                    </label>
                                    <div class="input-group">
                                        <input type="password" 
                                               name="password" 
                                               id="password"
                                               class="form-control form-control-lg @error('password') is-invalid @enderror"
                                               placeholder="Để trống nếu không đổi">
                                        <button class="btn btn-outline-secondary" 
                                                type="button" 
                                                onclick="togglePassword('password')">
                                            <i class="fa-solid fa-eye" id="password-icon"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">Để trống nếu không muốn thay đổi mật khẩu</small>
                                    @error('password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Password Confirmation -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fa-solid fa-lock-open text-warning me-1"></i>
                                        Xác nhận mật khẩu mới
                                    </label>
                                    <div class="input-group">
                                        <input type="password" 
                                               name="password_confirmation" 
                                               id="password_confirmation"
                                               class="form-control form-control-lg"
                                               placeholder="Nhập lại mật khẩu mới">
                                        <button class="btn btn-outline-secondary" 
                                                type="button" 
                                                onclick="togglePassword('password_confirmation')">
                                            <i class="fa-solid fa-eye" id="password_confirmation-icon"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-id-card text-primary me-2"></i>Thông tin cá nhân
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <!-- First Name -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Họ <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           name="first_name"
                                           class="form-control form-control-lg @error('first_name') is-invalid @enderror"
                                           value="{{ old('first_name', $user->first_name) }}" 
                                           required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Last Name -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Tên <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           name="last_name"
                                           class="form-control form-control-lg @error('last_name') is-invalid @enderror"
                                           value="{{ old('last_name', $user->last_name) }}" 
                                           required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Phone -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fa-solid fa-phone text-success me-1"></i>
                                        Số điện thoại
                                    </label>
                                    <input type="text" 
                                           name="phone"
                                           class="form-control form-control-lg @error('phone') is-invalid @enderror"
                                           value="{{ old('phone', $user->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Gender -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fa-solid fa-venus-mars text-info me-1"></i>
                                        Giới tính
                                    </label>
                                    <select name="gender"
                                        class="form-select form-select-lg @error('gender') is-invalid @enderror">
                                        <option value="">-- Chọn giới tính --</option>
                                        <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Nam</option>
                                        <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Nữ</option>
                                        <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Khác</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Birthday -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fa-solid fa-cake-candles text-warning me-1"></i>
                                        Ngày sinh
                                    </label>
                                    <input type="date" 
                                           name="birthday"
                                           class="form-control form-control-lg @error('birthday') is-invalid @enderror"
                                           value="{{ old('birthday', optional($user->birthday)->format('Y-m-d')) }}" 
                                           max="{{ date('Y-m-d') }}">
                                    @error('birthday')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Bio -->
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        <i class="fa-solid fa-comment text-primary me-1"></i>
                                        Giới thiệu bản thân
                                    </label>
                                    <textarea name="bio" 
                                              rows="4" 
                                              class="form-control form-control-lg @error('bio') is-invalid @enderror">{{ old('bio', $user->bio) }}</textarea>
                                    @error('bio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
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
                                <button type="submit" class="btn btn-warning btn-lg px-4">
                                    <i class="fa-solid fa-pen me-2"></i>Cập nhật
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>

    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        #avatarPreview {
            transition: all 0.3s ease;
        }

        #avatarPreview:hover {
            transform: scale(1.05);
        }
    </style>

    <script>
        let originalAvatarSrc = "{{ $user->avatar_url }}";
        let hasAvatar = {{ $user->avatar ? 'true' : 'false' }};

        // Preview Avatar
        function previewAvatar(event) {
            const file = event.target.files[0];
            
            if (file) {
                // Validate file size (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    toastr.error('Ảnh vượt quá 2MB. Vui lòng chọn ảnh khác!');
                    event.target.value = '';
                    return;
                }

                // Validate file type
                const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    toastr.error('Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP)!');
                    event.target.value = '';
                    return;
                }

                // Show preview
                const reader = new FileReader();
                reader.onload = e => {
                    const preview = document.getElementById('avatarPreview');
                    
                    // If it was a div (initials), replace with img
                    if (preview.tagName === 'DIV') {
                        const img = document.createElement('img');
                        img.id = 'avatarPreview';
                        img.className = 'rounded-circle border border-4 border-primary shadow-lg';
                        img.style.cssText = 'width: 200px; height: 200px; object-fit: cover;';
                        img.src = e.target.result;
                        preview.replaceWith(img);
                    } else {
                        preview.src = e.target.result;
                    }
                    
                    document.getElementById('removeAvatarBtn').classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            }
        }

        // Remove Avatar Preview
        function removeAvatarPreview() {
            const preview = document.getElementById('avatarPreview');
            
            if (hasAvatar) {
                // If user had avatar, show original
                if (preview.tagName === 'DIV') {
                    const img = document.createElement('img');
                    img.id = 'avatarPreview';
                    img.className = 'rounded-circle border border-4 border-primary shadow-lg';
                    img.style.cssText = 'width: 200px; height: 200px; object-fit: cover;';
                    img.src = originalAvatarSrc;
                    preview.replaceWith(img);
                } else {
                    preview.src = originalAvatarSrc;
                }
            } else {
                // If no avatar, show initials div
                const div = document.createElement('div');
                div.id = 'avatarPreview';
                div.className = 'rounded-circle bg-gradient-primary text-white d-flex align-items-center justify-content-center shadow-lg mx-auto';
                div.style.cssText = 'width: 200px; height: 200px; font-size: 4rem; font-weight: bold;';
                div.textContent = "{{ $user->initials }}";
                preview.replaceWith(div);
            }
            
            document.getElementById('avatar').value = '';
            document.getElementById('removeAvatarBtn').classList.add('d-none');
        }

        // Toggle Password Visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '-icon');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Form Validation
        document.getElementById('editUserForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirmation').value;

            if (password && password !== passwordConfirm) {
                e.preventDefault();
                toastr.error('Mật khẩu xác nhận không khớp!');
                return false;
            }

            if (password && password.length < 8) {
                e.preventDefault();
                toastr.error('Mật khẩu phải có ít nhất 8 ký tự!');
                return false;
            }
        });

        
    </script>
@endsection