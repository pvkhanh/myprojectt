<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\OrderResource;
use App\Models\Order;
use App\Models\Payment;
use App\Models\UserAddress;
use App\Repositories\Contracts\CartItemRepositoryInterface;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    protected CartItemRepositoryInterface $cartRepo;

    public function __construct(CartItemRepositoryInterface $cartRepo)
    {
        $this->cartRepo = $cartRepo;
    }

    /**
     * Danh sách đơn hàng của user
     */
    public function index(Request $request)
    {
        try {
            $userId = auth('api')->id();
            $perPage = $request->get('per_page', 15);
            $status = $request->get('status');

            $query = Order::where('user_id', $userId)
                ->with(['orderItems.product', 'payments'])
                ->latest();

            if ($status) {
                $query->where('status', $status);
            }

            $orders = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => OrderResource::collection($orders),
                'meta' => [
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải danh sách đơn hàng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tạo đơn hàng mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:user_addresses,id',
            'payment_method' => 'required|in:cod,bank,momo,vnpay',
            'note' => 'nullable|string|max:500',
            'coupon_code' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $userId = auth('api')->id();

            // Kiểm tra địa chỉ
            $address = UserAddress::where('id', $request->address_id)
                ->where('user_id', $userId)
                ->firstOrFail();

            // Lấy giỏ hàng
            $cartItems = $this->cartRepo->selectedForUser($userId);

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Giỏ hàng trống'
                ], 400);
            }

            // Tính toán
            $subtotal = 0;
            $orderItems = [];

            foreach ($cartItems as $item) {
                $product = $item->product;
                $variant = $item->variant;

                // Kiểm tra tồn kho
                if ($variant) {
                    $availableStock = $variant->stockItems->sum('quantity');
                    $price = $variant->price;
                } else {
                    $availableStock = $product->stock_quantity;
                    $price = $product->sale_price ?? $product->price;
                }

                if ($availableStock < $item->quantity) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Sản phẩm {$product->name} không đủ hàng",
                        'product' => $product->name,
                        'available' => $availableStock,
                        'requested' => $item->quantity
                    ], 400);
                }

                $itemTotal = $price * $item->quantity;
                $subtotal += $itemTotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'variant_id' => $variant?->id,
                    'quantity' => $item->quantity,
                    'price' => $price,
                ];
            }

            // Phí ship (có thể tính theo logic riêng)
            $shippingFee = 30000; // 30k mặc định

            // Giảm giá (nếu có coupon)
            $discountAmount = 0;
            // TODO: Logic áp dụng coupon

            $totalAmount = $subtotal + $shippingFee - $discountAmount;

            // Tạo order
            $order = Order::create([
                'user_id' => $userId,
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'status' => OrderStatus::Pending,
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'shipping_name' => $address->receiver_name,
                'shipping_phone' => $address->phone,
                'shipping_address' => $address->address,
                'shipping_ward' => $address->ward,
                'shipping_district' => $address->district,
                'shipping_city' => $address->city,
                'note' => $request->note,
            ]);

            // Tạo order items
            foreach ($orderItems as $item) {
                $order->orderItems()->create($item);

                // Trừ tồn kho
                if ($item['variant_id']) {
                    $variant = \App\Models\ProductVariant::find($item['variant_id']);
                    $variant->stockItems()->decrement('quantity', $item['quantity']);
                } else {
                    $product = \App\Models\Product::find($item['product_id']);
                    $product->decrement('stock_quantity', $item['quantity']);
                }
            }

            // Tạo payment
            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_method' => PaymentMethod::from($request->payment_method),
                'amount' => $totalAmount,
                'status' => PaymentStatus::Pending,
            ]);

            // Xóa giỏ hàng
            $this->cartRepo->clearUserCart($userId);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đặt hàng thành công',
                'data' => [
                    'order' => new OrderResource($order->load(['orderItems.product', 'payments'])),
                    'payment' => [
                        'id' => $payment->id,
                        'method' => $payment->payment_method->value,
                        'amount' => $payment->amount,
                        'status' => $payment->status->value,
                    ]
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Đặt hàng thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Chi tiết đơn hàng
     */
    public function show($id)
    {
        try {
            $userId = auth('api')->id();
            
            $order = Order::where('id', $id)
                ->where('user_id', $userId)
                ->with(['orderItems.product', 'orderItems.variant', 'payments'])
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => new OrderResource($order)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Hủy đơn hàng
     */
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $userId = auth('api')->id();
            
            $order = Order::where('id', $id)
                ->where('user_id', $userId)
                ->with(['orderItems', 'payments'])
                ->firstOrFail();

            // Chỉ được hủy đơn hàng pending hoặc paid
            if (!in_array($order->status, [OrderStatus::Pending, OrderStatus::Paid])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể hủy đơn hàng ở trạng thái này'
                ], 400);
            }

            // Cập nhật trạng thái
            $order->update([
                'status' => OrderStatus::Cancelled,
                'cancelled_at' => now(),
                'customer_note' => $request->reason,
            ]);

            // Hoàn lại tồn kho
            foreach ($order->orderItems as $item) {
                if ($item->variant_id) {
                    $item->variant->stockItems()->increment('quantity', $item->quantity);
                } else {
                    $item->product->increment('stock_quantity', $item->quantity);
                }
            }

            // Cập nhật payment
            if ($payment = $order->payments->first()) {
                $payment->update(['status' => PaymentStatus::Failed]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã hủy đơn hàng',
                'data' => new OrderResource($order->fresh())
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Không thể hủy đơn hàng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xác nhận đã nhận hàng
     */
    public function confirmReceived($id)
    {
        try {
            DB::beginTransaction();

            $userId = auth('api')->id();
            
            $order = Order::where('id', $id)
                ->where('user_id', $userId)
                ->firstOrFail();

            if ($order->status !== OrderStatus::Shipped) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đơn hàng chưa được giao'
                ], 400);
            }

            $order->update([
                'status' => OrderStatus::Completed,
                'completed_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã xác nhận nhận hàng',
                'data' => new OrderResource($order->fresh())
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Không thể xác nhận',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Theo dõi đơn hàng
     */
    public function track($id)
    {
        try {
            $userId = auth('api')->id();
            
            $order = Order::where('id', $id)
                ->where('user_id', $userId)
                ->firstOrFail();

            $timeline = [
                [
                    'status' => 'pending',
                    'label' => 'Chờ xác nhận',
                    'time' => $order->created_at,
                    'completed' => true
                ],
                [
                    'status' => 'paid',
                    'label' => 'Đã thanh toán',
                    'time' => $order->paid_at,
                    'completed' => $order->paid_at !== null
                ],
                [
                    'status' => 'shipped',
                    'label' => 'Đang giao hàng',
                    'time' => $order->shipped_at,
                    'completed' => $order->shipped_at !== null
                ],
                [
                    'status' => 'completed',
                    'label' => 'Đã hoàn thành',
                    'time' => $order->completed_at,
                    'completed' => $order->completed_at !== null
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'order_number' => $order->order_number,
                    'current_status' => $order->status->value,
                    'timeline' => $timeline,
                    'estimated_delivery' => $order->shipped_at ? 
                        $order->shipped_at->addDays(3)->format('d/m/Y') : null
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể theo dõi đơn hàng',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}