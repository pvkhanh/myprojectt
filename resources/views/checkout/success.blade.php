{{-- @extends('layouts.app')

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
@endsection --}}


@extends('layouts.app')

@section('title', 'Thanh toán thành công')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-green-50 to-emerald-100 flex items-center justify-center p-4">
        <div class="max-w-2xl w-full bg-white rounded-2xl shadow-2xl overflow-hidden">
            <!-- Success Icon -->
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-8 text-center">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-white mb-2">Thanh toán thành công!</h2>
                <p class="text-green-100">Đơn hàng của bạn đã được xác nhận</p>
            </div>

            <!-- Order Details -->
            <div class="p-8">
                <!-- Order Number -->
                <div class="bg-green-50 rounded-lg p-6 mb-6 text-center">
                    <p class="text-sm text-gray-600 mb-2">Mã đơn hàng</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $order->order_number }}</p>
                    <p class="text-sm text-gray-500 mt-2">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>

                <!-- Payment Amount -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-gray-600">Tổng tiền hàng</span>
                        <span class="font-medium">{{ number_format($order->total_amount - $order->shipping_fee) }}đ</span>
                    </div>
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-gray-600">Phí vận chuyển</span>
                        <span class="font-medium">{{ number_format($order->shipping_fee) }}đ</span>
                    </div>
                    <div class="border-t pt-3 flex justify-between items-center">
                        <span class="text-lg font-bold text-gray-800">Tổng thanh toán</span>
                        <span class="text-2xl font-bold text-green-600">{{ number_format($order->total_amount) }}đ</span>
                    </div>
                </div>

                <!-- Shipping Info -->
                <div class="border-t border-b py-6 mb-6">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Thông tin giao hàng
                    </h3>
                    <div class="space-y-2 text-sm">
                        <p><span class="font-medium text-gray-700">Người nhận:</span>
                            {{ $order->shippingAddress->receiver_name }}</p>
                        <p><span class="font-medium text-gray-700">Số điện thoại:</span>
                            {{ $order->shippingAddress->phone }}</p>
                        <p><span class="font-medium text-gray-700">Địa chỉ:</span> {{ $order->shippingAddress->address }},
                            {{ $order->shippingAddress->ward }}, {{ $order->shippingAddress->district }},
                            {{ $order->shippingAddress->province }}</p>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="mb-6">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        Sản phẩm đã mua
                    </h3>
                    <div class="space-y-3">
                        @foreach ($order->orderItems as $item)
                            <div class="flex gap-4 p-3 bg-gray-50 rounded-lg">
                                <img src="{{ $item->product->image_url ?? 'https://via.placeholder.com/80' }}"
                                    alt="{{ $item->product->name }}" class="w-16 h-16 object-cover rounded-lg">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-800">{{ $item->product->name }}</h4>
                                    @if ($item->variant)
                                        <p class="text-sm text-gray-500">{{ $item->variant->name }}</p>
                                    @endif
                                    <div class="flex items-center justify-between mt-2">
                                        <span class="text-sm text-gray-600">Số lượng: {{ $item->quantity }}</span>
                                        <span
                                            class="font-medium text-gray-800">{{ number_format($item->price * $item->quantity) }}đ</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="grid md:grid-cols-2 gap-4">
                    <a href="{{ route('orders.show', $order->id) }}"
                        class="bg-blue-500 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-600 transition-colors text-center">
                        Xem chi tiết đơn hàng
                    </a>
                    <a href="{{ route('home') }}"
                        class="bg-gray-100 text-gray-700 py-3 px-6 rounded-lg font-semibold hover:bg-gray-200 transition-colors text-center">
                        Tiếp tục mua sắm
                    </a>
                </div>

                <!-- Additional Info -->
                <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                    <p class="text-sm text-blue-900">
                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                        <strong>Lưu ý:</strong> Chúng tôi đã gửi email xác nhận đơn hàng đến địa chỉ <span
                            class="font-medium">{{ auth()->user()->email }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
