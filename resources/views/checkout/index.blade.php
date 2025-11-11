@extends('layouts.app')

@section('title', 'Thanh toán')

@section('content')
<div class="container py-4">
    <h3 class="mb-3">Thông tin giao hàng</h3>
    <form method="POST" action="{{ route('checkout.store') }}">
        @csrf

        <div class="row">
            <div class="col-md-8">
                <div class="mb-3">
                    <label>Địa chỉ</label>
                    <input type="text" name="address" class="form-control" required>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label>Phường/Xã</label>
                        <input type="text" name="ward" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>Quận/Huyện</label>
                        <input type="text" name="district" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>Tỉnh/Thành phố</label>
                        <input type="text" name="province" class="form-control" required>
                    </div>
                </div>
                <div class="mt-3">
                    <label>Số điện thoại</label>
                    <input type="text" name="phone" class="form-control" required>
                </div>

                <div class="mt-3">
                    <label>Phương thức thanh toán</label>
                    <select name="payment_method" class="form-select" required>
                        <option value="cod">Thanh toán khi nhận hàng (COD)</option>
                        <option value="bank">Chuyển khoản ngân hàng</option>
                    </select>
                </div>
            </div>

            <div class="col-md-4">
                <h5>Tóm tắt đơn hàng</h5>
                <ul class="list-group mb-3">
                    @foreach ($cart as $item)
                        <li class="list-group-item d-flex justify-content-between">
                            <div>{{ $item['name'] }} x{{ $item['quantity'] }}</div>
                            <strong>{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}đ</strong>
                        </li>
                    @endforeach
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Phí giao hàng</span>
                        <span>{{shipping_fee}}</span>
                        {{-- <strong>30.000đ</strong> --}}
                    </li>
                </ul>
                <button class="btn btn-primary w-100">Đặt hàng</button>
            </div>
        </div>
    </form>
</div>
@endsection
