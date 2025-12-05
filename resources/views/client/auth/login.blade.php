@extends('client.layouts.master')

@section('title', 'Đăng nhập')

@section('content')

    <div class="row justify-content-center mt-5">
        <div class="col-md-5">

            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <h3 class="fw-bold mb-4 text-center">Đăng nhập</h3>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Email:</label>
                            <input type="email" name="email" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mật khẩu:</label>
                            <input type="password" name="password" class="form-control">
                        </div>

                        <button class="btn btn-primary w-100 mt-2">Đăng nhập</button>

                    </form>

                    <p class="mt-3 text-center">
                        Chưa có tài khoản?
                        <a href="{{ route('register') }}">Đăng ký</a>
                    </p>

                </div>
            </div>

        </div>
    </div>

@endsection
