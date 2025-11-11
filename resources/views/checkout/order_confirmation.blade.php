@component('mail::message')
# Xác nhận đơn hàng #{{ $order->order_number }}

Xin chào **{{ $order->user->first_name }}**,

Chúng tôi đã nhận được đơn hàng của bạn.

@component('mail::button', ['url' => route('orders.show', $order->id)])
Xem chi tiết đơn hàng
@endcomponent

Cảm ơn bạn đã tin tưởng {{ config('app.name') }}!

Trân trọng,  
**Đội ngũ {{ config('app.name') }}**
@endcomponent
