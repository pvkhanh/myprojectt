@extends('layouts.app')

@section('title', 'Đặt hàng thành công')

@section('content')
<div class="container py-5 text-center">
    <h2 class="text-success">🎉 Đặt hàng thành công!</h2>
    <p>Cảm ơn bạn đã mua sắm tại {{ config('app.name') }}.</p>
    <p>Mã đơn hàng của bạn: <strong>#{{ $order->order_number }}</strong></p>

    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-primary mt-3">
        Xem chi tiết đơn hàng
    </a>
</div>
@endsection
