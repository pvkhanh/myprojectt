<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }

        .content {
            background: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
        }

        .order-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border: 2px solid #10b981;
        }

        .success-icon {
            font-size: 48px;
            color: #10b981;
        }

        .order-number {
            font-size: 24px;
            font-weight: bold;
            color: #1f2937;
            margin: 10px 0;
        }

        .item {
            padding: 15px;
            border-bottom: 1px solid #e5e7eb;
        }

        .item:last-child {
            border-bottom: none;
        }

        .total {
            background: #f0fdf4;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .button {
            display: inline-block;
            background: #3b82f6;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="success-icon">✓</div>
        <h1>Thanh toán thành công!</h1>
        <p>Cảm ơn bạn đã mua hàng</p>
    </div>

    <div class="content">
        <div class="order-box">
            <p style="color: #6b7280; margin: 0;">Mã đơn hàng</p>
            <div class="order-number">{{ $order->order_number }}</div>
            <p style="color: #6b7280; margin: 0;">{{ $order->created_at->format('d/m/Y H:i') }}</p>
        </div>

        <h2>Thông tin đơn hàng</h2>

        <div style="background: white; padding: 20px; border-radius: 8px;">
            @foreach ($order->orderItems as $item)
                <div class="item">
                    <strong>{{ $item->product->name }}</strong>
                    @if ($item->variant)
                        <br><span style="color: #6b7280; font-size: 14px;">{{ $item->variant->name }}</span>
                    @endif
                    <div style="display: flex; justify-content: space-between; margin-top: 8px;">
                        <span style="color: #6b7280;">Số lượng: {{ $item->quantity }}</span>
                        <strong>{{ number_format($item->price * $item->quantity) }}đ</strong>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="total">
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <span>Tạm tính:</span>
                <strong>{{ number_format($order->total_amount - $order->shipping_fee) }}đ</strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <span>Phí vận chuyển:</span>
                <strong>{{ number_format($order->shipping_fee) }}đ</strong>
            </div>
            <div
                style="display: flex; justify-content: space-between; font-size: 18px; padding-top: 10px; border-top: 2px solid #10b981;">
                <strong>Tổng cộng:</strong>
                <strong style="color: #10b981;">{{ number_format($order->total_amount) }}đ</strong>
            </div>
        </div>

        <h3>Thông tin giao hàng</h3>
        <div style="background: white; padding: 20px; border-radius: 8px;">
            <p><strong>Người nhận:</strong> {{ $order->shippingAddress->receiver_name }}</p>
            <p><strong>Số điện thoại:</strong> {{ $order->shippingAddress->phone }}</p>
            <p><strong>Địa chỉ:</strong> {{ $order->shippingAddress->address }}, {{ $order->shippingAddress->ward }},
                {{ $order->shippingAddress->district }}, {{ $order->shippingAddress->province }}</p>
        </div>

        <div style="text-align: center;">
            <a href="{{ route('orders.show', $order->id) }}" class="button">Xem chi tiết đơn hàng</a>
        </div>
    </div>

    <div class="footer">
        <p>Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi.</p>
        <p>&copy; {{ date('Y') }} E-Commerce Store. All rights reserved.</p>
    </div>
</body>

</html>
