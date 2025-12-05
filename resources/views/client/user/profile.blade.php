@extends('client.layouts.master')

@section('title', 'Hồ sơ người dùng')

@section('content')

    <h3 class="fw-bold mb-3">Hồ sơ người dùng</h3>

    <form method="POST" action="{{ route('client.profile.update') }}">
        @csrf

        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Họ tên</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Địa chỉ</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address', $user->address) }}">
                </div>

                <button class="btn btn-primary">Cập nhật</button>

            </div>
        </div>

    </form>

@endsection
