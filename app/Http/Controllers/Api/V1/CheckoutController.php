<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    protected CheckoutService $checkoutService;

    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    /**
     * Hiển thị trang checkout
     */
    public function index()
    {
        $user = auth()->user();
        $cartItems = $user->cartItems()->with(['product', 'variant'])->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống');
        }

        return view('checkout.index', compact('cartItems'));
    }

    /**
     * Validate checkout
     */
    public function validate(Request $request)
    {
        $user = auth()->user();
        $validation = $this->checkoutService->validateCheckout($user);

        return response()->json($validation);
    }

    /**
     * Calculate shipping
     */
    public function calculateShipping(Request $request)
    {
        $shippingFee = $this->checkoutService->calculateShipping($request->all());

        return response()->json([
            'success' => true,
            'shipping_fee' => $shippingFee
        ]);
    }

    /**
     * Process checkout
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver_name' => 'required|string|max:255',
            'phone' => 'required|regex:/^[0-9]{10}$/',
            'address' => 'required|string|max:500',
            'ward' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'payment_method' => 'required|in:card,bank,cod,wallet',
            'note' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();
        $result = $this->checkoutService->createOrder($request->all(), $user);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'order' => $result['order'],
                'payment' => [
                    'id' => $result['payment']->id,
                    'status' => $result['payment']->status->value,
                    'payment_method' => $result['payment']->payment_method->value,
                    'gateway_response' => json_decode($result['payment']->gateway_response, true)
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 400);
    }

    /**
     * Success page
     */
    public function success($orderId)
    {
        $order = auth()->user()->orders()->with(['orderItems.product', 'shippingAddress', 'payments'])->findOrFail($orderId);

        return view('checkout.success', compact('order'));
    }
}
