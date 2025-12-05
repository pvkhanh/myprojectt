{{-- resources/views/client/profile/index.blade.php --}}
@extends('client.layouts.master')

@section('title', 'Tài khoản của tôi')

@push('styles')
<style>
    .profile-page {
        padding: 60px 0;
        background: #f8fafc;
    }
    
    .profile-sidebar {
        background: white;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        position: sticky;
        top: 100px;
    }
    
    .profile-avatar {
        text-align: center;
        margin-bottom: 25px;
    }
    
    .avatar-wrapper {
        position: relative;
        width: 120px;
        height: 120px;
        margin: 0 auto 15px;
    }
    
    .avatar-img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #e2e8f0;
    }
    
    .avatar-upload {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 40px;
        height: 40px;
        background: var(--primary-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .avatar-upload:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
    }
    
    .avatar-upload input {
        display: none;
    }
    
    .profile-name {
        font-size: 20px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 5px;
    }
    
    .profile-email {
        color: #64748b;
        font-size: 14px;
    }
    
    .profile-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .menu-item {
        margin-bottom: 8px;
    }
    
    .menu-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 15px;
        color: #64748b;
        text-decoration: none;
        border-radius: 10px;
        transition: all 0.3s;
        font-weight: 500;
    }
    
    .menu-link:hover {
        background: #f8fafc;
        color: var(--primary-color);
    }
    
    .menu-link.active {
        background: #eff6ff;
        color: var(--primary-color);
    }
    
    .menu-icon {
        width: 20px;
        text-align: center;
    }
    
    .profile-content {
        background: white;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .content-header {
        padding-bottom: 20px;
        margin-bottom: 30px;
        border-bottom: 2px solid #e2e8f0;
    }
    
    .content-title {
        font-size: 24px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-label {
        display: block;
        font-weight: 600;
        color: #334155;
        margin-bottom: 8px;
    }
    
    .form-label.required::after {
        content: ' *';
        color: #ef4444;
    }
    
    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 15px;
        transition: all 0.3s;
    }
    
    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        outline: none;
    }
    
    .form-control:disabled {
        background: #f8fafc;
        cursor: not-allowed;
    }
    
    .btn-submit {
        padding: 14px 32px;
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .btn-submit:hover {
        background: #1d4ed8;
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(37, 99, 235, 0.3);
    }
    
    .btn-cancel {
        padding: 14px 32px;
        background: #f1f5f9;
        color: #64748b;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s;
        margin-left: 10px;
    }
    
    .btn-cancel:hover {
        background: #e2e8f0;
    }
    
    .address-card {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
        transition: all 0.3s;
    }
    
    .address-card:hover {
        border-color: var(--primary-color);
        background: #f8fafc;
    }
    
    .address-card.default {
        border-color: var(--primary-color);
        background: #eff6ff;
    }
    
    .address-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 12px;
    }
    
    .address-name {
        font-weight: 700;
        color: #1e293b;
        font-size: 16px;
    }
    
    .default-badge {
        background: var(--primary-color);
        color: white;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .address-details {
        color: #64748b;
        line-height: 1.6;
        margin-bottom: 15px;
    }
    
    .address-actions {
        display: flex;
        gap: 10px;
    }
    
    .btn-address {
        padding: 8px 16px;
        border: none;
        border-radius: 8px;
        font-weight: 500;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .btn-edit {
        background: #eff6ff;
        color: #2563eb;
    }
    
    .btn-edit:hover {
        background: #2563eb;
        color: white;
    }
    
    .btn-delete {
        background: #fee2e2;
        color: #ef4444;
    }
    
    .btn-delete:hover {
        background: #ef4444;
        color: white;
    }
    
    .btn-add-address {
        width: 100%;
        padding: 15px;
        background: #f8fafc;
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        color: #64748b;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .btn-add-address:hover {
        border-color: var(--primary-color);
        color: var(--primary-color);
        background: #eff6ff;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 25px;
        border-radius: 12px;
        color: white;
    }
    
    .stat-card:nth-child(2) {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    
    .stat-card:nth-child(3) {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    
    .stat-card:nth-child(4) {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }
    
    .stat-icon {
        font-size: 36px;
        margin-bottom: 12px;
        opacity: 0.9;
    }
    
    .stat-value {
        font-size: 28px;
        font-weight: 800;
        margin-bottom: 5px;
    }
    
    .stat-label {
        font-size: 14px;
        opacity: 0.9;
    }
</style>
@endpush

@section('content')
<div class="profile-page">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active">Tài khoản</li>
            </ol>
        </nav>
        
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="profile-sidebar">
                    <div class="profile-avatar">
                        <div class="avatar-wrapper">
                            <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) }}" 
                                 alt="{{ auth()->user()->name }}" 
                                 class="avatar-img">
                            <label class="avatar-upload">
                                <i class="fas fa-camera"></i>
                                <input type="file" accept="image/*" id="avatarUpload">
                            </label>
                        </div>
                        <div class="profile-name">{{ auth()->user()->name }}</div>
                        <div class="profile-email">{{ auth()->user()->email }}</div>
                    </div>
                    
                    <ul class="profile-menu">
                        <li class="menu-item">
                            <a href="{{ route('client.profile.index') }}" class="menu-link active">
                                <i class="fas fa-user menu-icon"></i>
                                Thông tin cá nhân
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{ route('client.orders.index') }}" class="menu-link">
                                <i class="fas fa-shopping-bag menu-icon"></i>
                                Đơn hàng
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{ route('client.wishlist.index') }}" class="menu-link">
                                <i class="fas fa-heart menu-icon"></i>
                                Yêu thích
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="menu-link" data-tab="addresses">
                                <i class="fas fa-map-marker-alt menu-icon"></i>
                                Địa chỉ
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="menu-link" data-tab="password">
                                <i class="fas fa-lock menu-icon"></i>
                                Đổi mật khẩu
                            </a>
                        </li>
                        <li class="menu-item">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="menu-link" style="width: 100%; text-align: left; background: none; border: none;">
                                    <i class="fas fa-sign-out-alt menu-icon"></i>
                                    Đăng xuất
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-lg-9">
                <!-- Profile Info -->
                <div class="profile-content" id="profileTab">
                    <div class="content-header">
                        <h2 class="content-title">
                            <i class="fas fa-user-circle me-2"></i>
                            Thông tin cá nhân
                        </h2>
                    </div>
                    
                    <!-- Stats -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fas fa-shopping-bag"></i></div>
                            <div class="stat-value">{{ $totalOrders ?? 0 }}</div>
                            <div class="stat-label">Đơn hàng</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fas fa-heart"></i></div>
                            <div class="stat-value">{{ $totalWishlist ?? 0 }}</div>
                            <div class="stat-label">Yêu thích</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fas fa-star"></i></div>
                            <div class="stat-value">{{ $totalReviews ?? 0 }}</div>
                            <div class="stat-label">Đánh giá</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fas fa-coins"></i></div>
                            <div class="stat-value">{{ $totalPoints ?? 0 }}</div>
                            <div class="stat-label">Điểm tích lũy</div>
                        </div>
                    </div>
                    
                    <!-- Form -->
                    <form action="{{ route('client.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label required">Họ và tên</label>
                                    <input type="text" class="form-control" name="name" 
                                           value="{{ auth()->user()->name }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label required">Email</label>
                                    <input type="email" class="form-control" name="email" 
                                           value="{{ auth()->user()->email }}" required disabled>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Số điện thoại</label>
                                    <input type="tel" class="form-control" name="phone" 
                                           value="{{ auth()->user()->phone ?? '' }}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Ngày sinh</label>
                                    <input type="date" class="form-control" name="birthday" 
                                           value="{{ auth()->user()->birthday ?? '' }}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Giới tính</label>
                                    <select class="form-control" name="gender">
                                        <option value="">Chọn giới tính</option>
                                        <option value="male" {{ (auth()->user()->gender ?? '') === 'male' ? 'selected' : '' }}>Nam</option>
                                        <option value="female" {{ (auth()->user()->gender ?? '') === 'female' ? 'selected' : '' }}>Nữ</option>
                                        <option value="other" {{ (auth()->user()->gender ?? '') === 'other' ? 'selected' : '' }}>Khác</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Địa chỉ</label>
                                    <input type="text" class="form-control" name="address" 
                                           value="{{ auth()->user()->address ?? '' }}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn-submit">
                                <i class="fas fa-save me-2"></i>Lưu thay đổi
                            </button>
                            <button type="reset" class="btn-cancel">
                                <i class="fas fa-times me-2"></i>Hủy
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Addresses (Hidden by default) -->
                <div class="profile-content" id="addressesTab" style="display: none;">
                    <div class="content-header">
                        <h2 class="content-title">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            Địa chỉ giao hàng
                        </h2>
                    </div>
                    
                    @forelse($addresses ?? [] as $address)
                    <div class="address-card {{ $address->is_default ? 'default' : '' }}">
                        <div class="address-header">
                            <div class="address-name">
                                {{ $address->name }}
                                <span class="text-muted ms-2">| {{ $address->phone }}</span>
                            </div>
                            @if($address->is_default)
                                <span class="default-badge">Mặc định</span>
                            @endif
                        </div>
                        <div class="address-details">
                            {{ $address->full_address }}
                        </div>
                        <div class="address-actions">
                            <button class="btn-address btn-edit" onclick="editAddress({{ $address->id }})">
                                <i class="fas fa-edit me-1"></i>Sửa
                            </button>
                            @if(!$address->is_default)
                            <button class="btn-address btn-delete" onclick="deleteAddress({{ $address->id }})">
                                <i class="fas fa-trash me-1"></i>Xóa
                            </button>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-muted py-5">Chưa có địa chỉ nào</p>
                    @endforelse
                    
                    <button class="btn-add-address" onclick="showAddAddressModal()">
                        <i class="fas fa-plus me-2"></i>Thêm địa chỉ mới
                    </button>
                </div>
                
                <!-- Change Password (Hidden by default) -->
                <div class="profile-content" id="passwordTab" style="display: none;">
                    <div class="content-header">
                        <h2 class="content-title">
                            <i class="fas fa-lock me-2"></i>
                            Đổi mật khẩu
                        </h2>
                    </div>
                    
                    <form action="{{ route('client.profile.change-password') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label class="form-label required">Mật khẩu hiện tại</label>
                            <input type="password" class="form-control" name="current_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label required">Mật khẩu mới</label>
                            <input type="password" class="form-control" name="new_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label required">Xác nhận mật khẩu mới</label>
                            <input type="password" class="form-control" name="new_password_confirmation" required>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn-submit">
                                <i class="fas fa-key me-2"></i>Đổi mật khẩu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Tab switching
    $('.menu-link[data-tab]').click(function(e) {
        e.preventDefault();
        $('.menu-link').removeClass('active');
        $(this).addClass('active');
        
        const tab = $(this).data('tab');
        $('.profile-content').hide();
        $('#' + tab + 'Tab').show();
    });
    
    // Avatar upload
    $('#avatarUpload').change(function() {
        const file = this.files[0];
        if (file) {
            const formData = new FormData();
            formData.append('avatar', file);
            formData.append('_token', '{{ csrf_token() }}');
            
            $.ajax({
                url: '{{ route("client.profile.upload-avatar") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('.avatar-img').attr('src', response.avatar_url);
                        alert('Cập nhật ảnh đại diện thành công');
                    }
                }
            });
        }
    });
});

function editAddress(id) {
    // Implement edit address modal
    alert('Edit address: ' + id);
}

function deleteAddress(id) {
    if (!confirm('Bạn có chắc muốn xóa địa chỉ này?')) {
        return;
    }
    
    $.ajax({
        url: '/profile/addresses/' + id,
        method: 'DELETE',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            }
        }
    });
}

function showAddAddressModal() {
    // Implement add address modal
    alert('Add new address');
}
</script>
@endpush