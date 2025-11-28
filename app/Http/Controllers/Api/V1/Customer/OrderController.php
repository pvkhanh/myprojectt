<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\OrderResource;
use App\Models\Order;
use App\Models\Payment;
use App\Models\UserAddress;
use App\Models\ShippingAddress;
use App\Repositories\Contracts\CartItemRepositoryInterface;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
                ->with(['orderItems.product', 'payments', 'shippingAddress'])
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
    public function store(Request $request)
    {
        return $this->checkout($request);
    }


    /**
     * Checkout: tạo đơn hàng mới
     */

    public function checkout(Request $request)
    {
        $request->validate([
            'address_id' => 'nullable|exists:user_addresses,id',
            'receiver_name' => 'required_without:address_id|string|max:255',
            'phone' => 'required_without:address_id|string|max:20',
            'address' => 'required_without:address_id|string|max:500',
            'province' => 'required_without:address_id|string|max:255',
            'district' => 'required_without:address_id|string|max:255',
            'ward' => 'required_without:address_id|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            // 'payment_method' => 'required|in:cod,bank,momo,vnpay',
            'payment_method' => ['required', Rule::in(PaymentMethod::values())],
            'note' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $userId = auth('api')->id();

            // // 1️⃣ Lấy địa chỉ hoặc tạo mới
            // if ($request->filled('address_id')) {
            //     $userAddress = UserAddress::where('id', $request->address_id)
            //         ->where('user_id', $userId)
            //         ->first();

            //     if (!$userAddress) {
            //         return response()->json([
            //             'success' => false,
            //             'message' => 'Địa chỉ không tồn tại hoặc không thuộc người dùng'
            //         ], 400);
            //     }
            // } else {
            //     $userAddress = UserAddress::create([
            //         'user_id' => $userId,
            //         'receiver_name' => $request->receiver_name,
            //         'phone' => $request->phone,
            //         'address' => $request->address,
            //         'province' => $request->province,
            //         'district' => $request->district,
            //         'ward' => $request->ward,
            //         'postal_code' => $request->postal_code,
            //         'is_default' => true,
            //     ]);
            // }
            // 1️⃣ Lấy địa chỉ hoặc tạo mới
            $userAddress = null;
            if ($request->address_id) {
                $userAddress = UserAddress::where('id', $request->address_id)
                    ->where('user_id', $userId)
                    ->first();

                // Nếu address_id không tồn tại hoặc không thuộc user → tạo mới
                if (!$userAddress) {
                    $userAddress = UserAddress::create([
                        'user_id' => $userId,
                        'receiver_name' => $request->receiver_name ?? 'Người nhận',
                        'phone' => $request->phone ?? '0000000000',
                        'address' => $request->address ?? 'Chưa cập nhật',
                        'province' => $request->province ?? '',
                        'district' => $request->district ?? '',
                        'ward' => $request->ward ?? '',
                        'postal_code' => $request->postal_code,
                        'is_default' => true,
                    ]);
                }
            } else {
                $userAddress = UserAddress::create([
                    'user_id' => $userId,
                    'receiver_name' => $request->receiver_name,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'province' => $request->province,
                    'district' => $request->district,
                    'ward' => $request->ward,
                    'postal_code' => $request->postal_code,
                    'is_default' => true,
                ]);
            }

            // 2️⃣ Lấy giỏ hàng
            $cartItems = $this->cartRepo->selectedForUser($userId);
            if ($cartItems->isEmpty()) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Giỏ hàng trống'], 400);
            }

            // 3️⃣ Tính toán subtotal, tổng
            $subtotal = 0;
            $orderItems = [];
            foreach ($cartItems as $item) {
                $product = $item->product;
                $variant = $item->variant;
                $price = $variant ? $variant->price : ($product->sale_price ?? $product->price);
                $availableStock = $variant ? $variant->stockItems->sum('quantity') : $product->stock_quantity;

                if ($availableStock < $item->quantity) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Sản phẩm {$product->name} không đủ hàng",
                    ], 400);
                }

                $subtotal += $price * $item->quantity;
                $orderItems[] = [
                    'product_id' => $product->id,
                    'variant_id' => $variant?->id,
                    'quantity' => $item->quantity,
                    'price' => $price,
                ];
            }

            $shippingFee = 30000; // phí ship mặc định
            $totalAmount = $subtotal + $shippingFee;

            // 4️⃣ Tạo Order
            $order = Order::create([
                'user_id' => $userId,
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'status' => OrderStatus::Pending,
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'total_amount' => $totalAmount,
                'note' => $request->note,
            ]);

            // 5️⃣ Tạo ShippingAddress
            $shippingAddress = ShippingAddress::create([
                'order_id' => $order->id,
                'receiver_name' => $userAddress->receiver_name,
                'phone' => $userAddress->phone,
                'address' => $userAddress->address,
                'province' => $userAddress->province,
                'district' => $userAddress->district,
                'ward' => $userAddress->ward,
                'postal_code' => $userAddress->postal_code,
            ]);

            // // 6️⃣ Tạo OrderItems và trừ tồn kho
            // foreach ($orderItems as $item) {
            //     $order->orderItems()->create($item);
            //     if ($item['variant_id']) {
            //         \App\Models\ProductVariant::find($item['variant_id'])
            //             ->stockItems()->decrement('quantity', $item['quantity']);
            //     } else {
            //         \App\Models\Product::find($item['product_id'])
            //             ->decrement('stock_quantity', $item['quantity']);
            //     }
            // }
            // 6️⃣ Tạo OrderItems và trừ tồn kho
            foreach ($orderItems as $item) {
                $order->orderItems()->create($item);

                if ($item['variant_id']) {
                    // Trừ tồn kho của variant
                    $variant = \App\Models\ProductVariant::find($item['variant_id']);
                    $variant->stockItems()->decrement('quantity', $item['quantity']);
                } else {
                    // Trừ tồn kho của sản phẩm chính
                    $product = \App\Models\Product::find($item['product_id']);
                    $product->stockItems()->decrement('quantity', $item['quantity']);
                }
            }


            // 7️⃣ Tạo Payment
            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_method' => PaymentMethod::from($request->payment_method),
                'amount' => $totalAmount,
                'status' => PaymentStatus::Pending,
            ]);

            // 8️⃣ Xóa giỏ hàng
            $this->cartRepo->clearUserCart($userId);

            DB::commit();

            // 9️⃣ Trả về dữ liệu đầy đủ cho frontend
            return response()->json([
                'success' => true,
                'message' => 'Đặt hàng thành công',
                'data' => [
                    'order' => new OrderResource($order->load(['orderItems.product', 'payments', 'shippingAddress'])),
                    'shippingAddress' => $shippingAddress,
                    'payment' => [
                        'id' => $payment->id,
                        'method' => $payment->payment_method->value,
                        'amount' => $payment->amount,
                        'status' => $payment->status->value,
                    ],
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
                ->with(['orderItems.product', 'orderItems.variant', 'payments', 'shippingAddress'])
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

    //  /**
    //  * Hủy đơn hàng
    //  */
    // public function cancel(Request $request, $id)
    // {
    //     $request->validate([
    //         'reason' => 'required|string|max:500'
    //     ]);

    //     try {
    //         DB::beginTransaction();

    //         $userId = auth('api')->id();

    //         $order = Order::where('id', $id)
    //             ->where('user_id', $userId)
    //             ->with(['orderItems', 'payments'])
    //             ->firstOrFail();

    //         // Chỉ được hủy đơn hàng pending hoặc paid
    //         if (!in_array($order->status, [OrderStatus::Pending, OrderStatus::Paid])) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Không thể hủy đơn hàng ở trạng thái này'
    //             ], 400);
    //         }

    //         // Cập nhật trạng thái
    //         $order->update([
    //             'status' => OrderStatus::Cancelled,
    //             'cancelled_at' => now(),
    //             'customer_note' => $request->reason,
    //         ]);

    //         // Hoàn lại tồn kho
    //         foreach ($order->orderItems as $item) {
    //             if ($item->variant_id) {
    //                 $item->variant->stockItems()->increment('quantity', $item->quantity);
    //             } else {
    //                 $item->product->increment('stock_quantity', $item->quantity);
    //             }
    //         }

    //         // Cập nhật payment
    //         if ($payment = $order->payments->first()) {
    //             $payment->update(['status' => PaymentStatus::Failed]);
    //         }

    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Đã hủy đơn hàng',
    //             'data' => new OrderResource($order->fresh())
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Không thể hủy đơn hàng',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    /**
     * Hủy đơn hàng
     */
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $userId = auth('api')->id();

        // Lấy đơn hàng, tránh dùng firstOrFail()
        $order = Order::where('id', $id)
            ->where('user_id', $userId)
            ->with(['orderItems', 'orderItems.product', 'orderItems.variant', 'payments'])
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng không tồn tại hoặc không thuộc người dùng'
            ], 404);
        }

        // Chỉ được hủy đơn hàng pending hoặc paid
        if (!in_array($order->status, [OrderStatus::Pending, OrderStatus::Paid])) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể hủy đơn hàng ở trạng thái này'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Cập nhật trạng thái đơn hàng
            $order->update([
                'status' => OrderStatus::Cancelled,
                'cancelled_at' => now(),
                'customer_note' => $request->reason,
            ]);

            // Hoàn lại tồn kho
            foreach ($order->orderItems as $item) {
                if ($item->variant_id) {
                    // Nếu là variant, tăng quantity trong stockItems
                    $item->variant->stockItems()->increment('quantity', $item->quantity);
                } else {
                    // Nếu là sản phẩm thường, tăng quantity trong stockItems liên quan
                    if ($item->product->stockItems()->exists()) {
                        $item->product->stockItems()->increment('quantity', $item->quantity);
                    } else {
                        // Không có stockItems => không làm gì (hoặc log cảnh báo)
                    }
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
    //     //Mới thêm ngày 27/11/2025 để test pay
    public function pay(Order $order)
    {
        $payment = $order->payments()->first(); // Lấy bản ghi payment đầu tiên

        if ($payment) {
            $payment->update([
                'status' => PaymentStatus::Success->value,
                'paid_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Thanh toán thành công',
        ]);
    }

}