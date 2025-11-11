<?php

use Illuminate\Support\Facades\Route;
use App\Models\Order;
use App\Helpers\MailHelper;

/**
 * ⚠️ TESTING ROUTES - XÓA TRƯỚC KHI PRODUCTION
 * Thêm vào routes/web.php khi cần test
 */

Route::middleware(['auth'])->prefix('test-mail')->group(function () {
    
    // Test gửi 1 mail cụ thể
    Route::get('/send/{orderId}/{template}', function($orderId, $template) {
        $order = Order::with(['user', 'shippingAddress', 'orderItems.product'])
            ->findOrFail($orderId);
        
        MailHelper::sendOrderMail($order, $template);
        
        return response()->json([
            'success' => true,
            'message' => "Mail '{$template}' đã được gửi đến {$order->user->email}",
            'order_number' => $order->order_number,
        ]);
    });

    // Test gửi tất cả các loại mail
    Route::get('/send-all/{orderId}', function($orderId) {
        $order = Order::with(['user', 'shippingAddress', 'orderItems.product'])
            ->findOrFail($orderId);
        
        $templates = [
            'order-confirmation',
            'order-paid',
            'order-processing',
            'order-shipped',
            'order-delivered',
            'order-completed',
        ];
        
        foreach ($templates as $template) {
            MailHelper::sendOrderMail($order, $template);
            sleep(1); // Delay 1s giữa các mail
        }
        
        return response()->json([
            'success' => true,
            'message' => "Đã gửi tất cả mail test đến {$order->user->email}",
            'templates_sent' => count($templates),
        ]);
    });

    // Test preview mail HTML
    Route::get('/preview/{orderId}/{template}', function($orderId, $template) {
        $order = Order::with(['user', 'shippingAddress', 'orderItems.product'])
            ->findOrFail($orderId);
        
        $mailTemplate = \App\Models\Mail::where('template_key', $template)->firstOrFail();
        $shippingAddress = $order->shippingAddress;
        
        // Chuẩn bị variables
        $variables = [
            '{{customer_name}}' => $order->user->first_name . ' ' . $order->user->last_name,
            '{{order_number}}' => $order->order_number,
            '{{order_date}}' => $order->created_at->format('d/m/Y H:i'),
            '{{payment_method}}' => $order->payment_method_label ?? 'COD',
            '{{total_amount}}' => number_format($order->total_amount, 0, ',', '.') . 'đ',
            '{{shop_name}}' => config('app.name'),
            // ... thêm các biến khác
        ];
        
        $content = str_replace(array_keys($variables), array_values($variables), $mailTemplate->content);
        
        return view('emails.preview', compact('content', 'mailTemplate'));
    });

    // Xem danh sách templates có sẵn
    Route::get('/templates', function() {
        $templates = \App\Models\Mail::whereNotNull('template_key')
            ->select('template_key', 'subject', 'type')
            ->get();
        
        return response()->json([
            'templates' => $templates,
            'count' => $templates->count(),
        ]);
    });
});