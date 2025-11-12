@extends('layouts.admin')

@section('title', 'Thêm banner mới')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="card border-0 shadow-lg mb-4">
                    <div class="card-header bg-gradient-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-images me-2"></i>Thông tin banner</h5>
                    </div>
                    <div class="card-body p-4">

                        {{-- Title --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold required">Tiêu đề banner</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                        </div>

                        {{-- URL --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">URL liên kết</label>
                            <input type="url" name="url" class="form-control" value="{{ old('url') }}">
                        </div>

                        {{-- Image Upload --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Ảnh banner</label>
                            <input type="file" name="image_file" class="form-control">
                            <small class="text-muted">Hoặc chọn ảnh từ thư viện</small>
                        </div>

                        {{-- Type / Position / Status --}}
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Loại banner</label>
                                <select name="type" class="form-select">
                                    <option value="">-- Chọn loại --</option>
                                    <option value="hero" {{ old('type') == 'hero' ? 'selected' : '' }}>Hero</option>
                                    <option value="sidebar" {{ old('type') == 'sidebar' ? 'selected' : '' }}>Sidebar</option>
                                    <option value="popup" {{ old('type') == 'popup' ? 'selected' : '' }}>Popup</option>
                                    <option value="footer" {{ old('type') == 'footer' ? 'selected' : '' }}>Footer</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Vị trí hiển thị</label>
                                <input type="number" name="position" class="form-control" value="{{ old('position', 0) }}" min="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold d-block">Trạng thái</label>
                                <div class="form-check form-switch mt-2">
                                    <input type="checkbox" name="is_active" class="form-check-input" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label">Kích hoạt</label>
                                </div>
                            </div>
                        </div>

                        {{-- Schedule --}}
                        <div class="border-top mt-4 pt-4">
                            <h6 class="fw-bold mb-3"><i class="fas fa-calendar-alt me-2 text-primary"></i>Lên lịch hiển thị</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label>Bắt đầu từ</label>
                                    <input type="datetime-local" name="start_at" class="form-control" value="{{ old('start_at') }}">
                                </div>
                                <div class="col-md-6">
                                    <label>Kết thúc lúc</label>
                                    <input type="datetime-local" name="end_at" class="form-control" value="{{ old('end_at') }}">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Action --}}
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Quay lại</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Lưu banner</button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
