@extends('client.layouts.master')

@section('title', 'Chi tiết đơn hàng')

@section('content')

    <h3 class="fw-bold mb-3">Chi tiết đơn hàng</h3>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            <h5 class="fw-bold">Mã đơn hàng: {{ $order->order_number }}</h5>
            <p>Ngày tạo: {{ $order->created_at->format('d/m/Y H:i') }}</p>
            <p>Trạng thái: <span class="badge bg-info">{{ $order->status }}</span></p>

        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-bold">Sản phẩm</div>
        <div class="card-body">

            @foreach ($order->items as $item)
                <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                    <span>{{ $item->product->title }} (x{{ $item->quantity }})</span>
                    <span class="fw-bold text-danger">{{ number_format($item->total) }}₫</span>
                </div>
            @endforeach

            <hr>

            <div class="d-flex justify-content-between h5">
                <span>Tổng cộng</span>
                <span class="text-danger">{{ number_format($order->total_amount) }}₫</span>
            </div>

        </div>
    </div>

@endsection
