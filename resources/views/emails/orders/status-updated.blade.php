@component('mail::message')
# Thông báo cập nhật đơn hàng

Xin chào **{{ $order->user->first_name }}**,

Trạng thái đơn hàng **#{{ $order->order_number }}** của bạn đã được cập nhật thành:

> **{{ ucfirst($order->status->value) }}**

@component('mail::button', ['url' => route('orders.show', $order->id)])
Xem chi tiết đơn hàng
@endcomponent

Cảm ơn bạn đã mua sắm cùng chúng tôi!  
{{ config('app.name') }}
@endcomponent
