@extends('client.layouts.master')

@section('title', 'Quên mật khẩu')

@section('content')

    <div class="row justify-content-center py-5">
        <div class="col-md-5">

            <div class="card shadow border-0">
                <div class="card-body p-4">

                    <h3 class="fw-bold text-center mb-3">Quên mật khẩu?</h3>

                    <p class="text-muted text-center mb-4">
                        Nhập email của bạn để nhận liên kết đặt lại mật khẩu.
                    </p>

                    {{-- Thông báo gửi mail thành công --}}
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Địa chỉ Email</label>
                            <input type="email" name="email" id="email"
                                class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                                placeholder="name@example.com" required autofocus>

                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mt-2">
                            <i class="bi bi-envelope-paper"></i> Gửi liên kết đặt lại mật khẩu
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <a href="{{ route('login') }}" class="text-decoration-none">
                            <i class="bi bi-arrow-left"></i> Quay lại đăng nhập
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>

@endsection
