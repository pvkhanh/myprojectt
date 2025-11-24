{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.app')

@section('title', 'Đăng nhập')

@section('content')
    <div class="min-vh-100 d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5">
                    <div class="card shadow-lg border-0">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <a href="{{ route('home') }}" class="text-decoration-none">
                                    <i class="bi bi-bag-heart-fill text-primary" style="font-size: 3rem;"></i>
                                </a>
                                <h3 class="mt-3 fw-bold">Đăng nhập</h3>
                                <p class="text-muted">Chào mừng bạn quay trở lại!</p>
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    @foreach ($errors->all() as $error)
                                        {{ $error }}<br>
                                    @endforeach
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-envelope me-1"></i>Email
                                    </label>
                                    <input type="email" name="email"
                                        class="form-control form-control-lg @error('email') is-invalid @enderror"
                                        value="{{ old('email') }}" placeholder="your@email.com" required autofocus>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-lock me-1"></i>Mật khẩu
                                    </label>
                                    <div class="input-group">
                                        <input type="password" name="password" id="password"
                                            class="form-control form-control-lg @error('password') is-invalid @enderror"
                                            placeholder="••••••••" required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                            <i class="bi bi-eye" id="toggleIcon"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                        <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                                    </div>
                                    <a href="{{ route('password.request') }}" class="text-decoration-none small">Quên mật
                                        khẩu?</a>
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Đăng nhập
                                </button>

                                <div class="position-relative my-4">
                                    <hr>
                                    <span
                                        class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small">
                                        hoặc đăng nhập với
                                    </span>
                                </div>

                                <div class="d-flex gap-2">
                                    <a href="{{ route('social.login', 'google') }}"
                                        class="btn btn-outline-danger flex-fill">
                                        <i class="bi bi-google me-2"></i>Google
                                    </a>
                                    <a href="{{ route('social.login', 'facebook') }}"
                                        class="btn btn-outline-primary flex-fill">
                                        <i class="bi bi-facebook me-2"></i>Facebook
                                    </a>
                                </div>
                            </form>

                            <div class="text-center mt-4">
                                <span class="text-muted">Chưa có tài khoản?</span>
                                <a href="{{ route('register') }}" class="text-decoration-none fw-semibold ms-1">Đăng ký
                                    ngay</a>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('home') }}" class="text-white text-decoration-none">
                            <i class="bi bi-arrow-left me-1"></i>Quay về trang chủ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }
    </script>
@endpush
