<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\CheckoutService;
use App\Enums\PaymentMethod;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    protected CheckoutService $checkoutService;

    public function __construct(CheckoutService $checkoutService)
    {
        $this->middleware('auth');
        $this->checkoutService = $checkoutService;
    }

    /**
     * Hiển thị trang checkout
     */
    public function index()
    {
        $user = auth()->user();

        // Validate checkout
        $validation = $this->checkoutService->validateCheckout($user);

        if (!$validation['valid']) {
            return redirect()->route('client.cart')
                ->with('error', $validation['message']);
        }

        // Lấy cart items
        $cartItems = $user->cartItems()->with(['product', 'variant'])->get();

        // Tính tổng
        $subtotal = $cartItems->sum(fn($item) => $item->quantity * $item->product->price);
        $shippingFee = 30000; // Default
        $total = $subtotal + $shippingFee;

        // Lấy địa chỉ mặc định
        $defaultAddress = $user->addresses()->where('is_default', true)->first();

        // Payment methods
        $paymentMethods = PaymentMethod::cases();

        return view('client.checkout.index', compact(
            'cartItems',
            'subtotal',
            'shippingFee',
            'total',
            'defaultAddress',
            'paymentMethods'
        ));
    }

    /**
     * Xử lý đặt hàng
     */
    public function process(Request $request)
    {
        $validated = $request->validate([
            'receiver_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'ward' => 'required|string',
            'district' => 'required|string',
            'province' => 'required|string',
            'payment_method' => 'required|in:' . implode(',', array_column(PaymentMethod::cases(), 'value')),
            'note' => 'nullable|string|max:500',
        ]);

        $result = $this->checkoutService->createOrder($validated, auth()->user());

        if (!$result['success']) {
            return back()->withInput()->with('error', $result['message']);
        }

        $order = $result['order'];
        $paymentMethod = PaymentMethod::from($validated['payment_method']);

        // Nếu thanh toán Stripe (Card), redirect đến payment page
        if ($paymentMethod === PaymentMethod::Card) {
            return redirect()->route('client.checkout.payment', $order->id);
        }

        // Nếu COD hoặc Bank Transfer, redirect đến success
        return redirect()->route('client.checkout.success', $order->id);
    }

    /**
     * Trang thanh toán Stripe
     */
    public function payment($orderId)
    {
        $order = auth()->user()->orders()
            ->with(['orderItems.product', 'shippingAddress', 'payments'])
            ->findOrFail($orderId);

        $payment = $order->payments()->latest()->first();

        if (!$payment || $payment->payment_method !== PaymentMethod::Card) {
            return redirect()->route('client.orders.show', $order->id);
        }

        $stripePublicKey = config('services.stripe.key');
        $clientSecret = $payment->gateway_response
            ? json_decode($payment->gateway_response, true)['client_secret'] ?? null
            : null;

        return view('client.checkout.payment', compact(
            'order',
            'payment',
            'stripePublicKey',
            'clientSecret'
        ));
    }

    /**
     * Xác nhận thanh toán thành công
     */
    public function paymentSuccess(Request $request, $orderId)
    {
        $order = auth()->user()->orders()->findOrFail($orderId);

        // Stripe sẽ gọi webhook để cập nhật, nhưng có thể verify lại ở đây
        // TODO: Verify payment intent status with Stripe

        return redirect()->route('client.checkout.success', $order->id);
    }

    /**
     * Trang thành công
     */
    public function success($orderId)
    {
        $order = auth()->user()->orders()
            ->with(['orderItems.product', 'shippingAddress', 'payments'])
            ->findOrFail($orderId);

        return view('client.checkout.success', compact('order'));
    }

    /**
     * Tính phí vận chuyển AJAX
     */
    public function calculateShipping(Request $request)
    {
        $validated = $request->validate([
            'province' => 'required|string',
            'district' => 'required|string',
            'ward' => 'required|string',
        ]);

        $shippingFee = $this->checkoutService->calculateShipping($validated);

        return response()->json([
            'success' => true,
            'shipping_fee' => $shippingFee,
            'formatted_fee' => number_format($shippingFee) . '₫',
        ]);
    }
}
