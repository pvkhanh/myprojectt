<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\OrderItemRepositoryInterface;
use App\Repositories\Contracts\ShippingAddressRepositoryInterface;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Payment;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderStatusUpdated;
use App\Mail\PaymentConfirmed;

class OrderController extends Controller
{
    protected $orderRepo;
    protected $orderItemRepo;
    protected $shippingAddressRepo;

    public function __construct(
        OrderRepositoryInterface $orderRepo,
        OrderItemRepositoryInterface $orderItemRepo,
        ShippingAddressRepositoryInterface $shippingAddressRepo
    ) {
        $this->orderRepo = $orderRepo;
        $this->orderItemRepo = $orderItemRepo;
        $this->shippingAddressRepo = $shippingAddressRepo;
    }
    /**
     * Danh sách đơn hàng
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'payments', 'orderItems'])->latest();

        // Tìm kiếm
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Lọc trạng thái
        if ($status = $request->status) {
            $query->where('status', $status);
        }

        // Lọc ngày
        if ($from = $request->from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->to) {
            $query->whereDate('created_at', '<=', $to);
        }

        // Lọc khoảng tiền
        if ($minAmount = $request->min_amount) {
            $query->where('total_amount', '>=', $minAmount);
        }
        if ($maxAmount = $request->max_amount) {
            $query->where('total_amount', '<=', $maxAmount);
        }

        $orders = $query->paginate(15)->withQueryString();

        // Thống kê
        $statsQuery = Order::query();
        if ($from)
            $statsQuery->whereDate('created_at', '>=', $from);
        if ($to)
            $statsQuery->whereDate('created_at', '<=', $to);

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'pending' => (clone $statsQuery)->where('status', OrderStatus::Pending)->count(),
            'paid' => (clone $statsQuery)->where('status', OrderStatus::Paid)->count(),
            'shipped' => (clone $statsQuery)->where('status', OrderStatus::Shipped)->count(),
            'completed' => (clone $statsQuery)->where('status', OrderStatus::Completed)->count(),
            'cancelled' => (clone $statsQuery)->where('status', OrderStatus::Cancelled)->count(),
            'total_revenue' => (clone $statsQuery)->where('status', OrderStatus::Completed)->sum('total_amount'),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }


    /**
     * Chi tiết đơn hàng với logic hiển thị nút
     */
    public function show($id)
    {
        $order = $this->orderRepo->find($id, ['user', 'orderItems.product', 'orderItems.variant', 'shippingAddress', 'payments']);

        if (!$order) {
            return redirect()->route('admin.orders.index')->with('error', 'Không tìm thấy đơn hàng');
        }

        $payment = $order->payments->first();

        // Xác định các actions có thể thực hiện
        $actions = $this->getAvailableActions($order, $payment);

        return view('admin.orders.show', compact('order', 'payment', 'actions'));
    }
    /**
     * Form chỉnh sửa
     */
    public function edit($id)
    {
        $order = $this->orderRepo->find($id, ['user', 'orderItems.product', 'orderItems.variant', 'shippingAddress', 'payments']);

        if (!$order) {
            return redirect()->route('admin.orders.index')->with('error', 'Không tìm thấy đơn hàng');
        }

        $statuses = OrderStatus::cases();

        return view('admin.orders.edit', compact('order', 'statuses'));
    }

    /**
     * Cập nhật đơn hàng
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', OrderStatus::values()),
            'admin_note' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $order = $this->orderRepo->find($id, ['orderItems', 'user']);

            if (!$order) {
                throw new \Exception('Không tìm thấy đơn hàng');
            }

            $oldStatus = $order->status;
            $newStatus = OrderStatus::from($validated['status']);

            // Validate trạng thái chuyển đổi
            if (!$this->canTransitionTo($oldStatus, $newStatus)) {
                throw new \Exception('Không thể chuyển từ trạng thái ' . $oldStatus->label() . ' sang ' . $newStatus->label());
            }

            $updateData = [
                'status' => $newStatus,
                'admin_note' => $validated['admin_note'] ?? $order->admin_note,
            ];

            // Cập nhật timestamp dựa trên trạng thái
            if ($newStatus === OrderStatus::Paid && $oldStatus !== OrderStatus::Paid) {
                $updateData['paid_at'] = now();
            } elseif ($newStatus === OrderStatus::Shipped && $oldStatus !== OrderStatus::Shipped) {
                $updateData['shipped_at'] = now();
            } elseif ($newStatus === OrderStatus::Completed && $oldStatus !== OrderStatus::Completed) {
                $updateData['completed_at'] = now();
            }

            $this->orderRepo->update($id, $updateData);

            // Gửi email nếu trạng thái thay đổi
            if ($oldStatus !== $newStatus && $order->user && $order->user->email) {
                try {
                    Mail::to($order->user->email)->send(new OrderStatusUpdated($order, $newStatus));
                } catch (\Exception $e) {
                    Log::error('Send email error: ' . $e->getMessage());
                }
            }

            DB::commit();
            return redirect()->route('admin.orders.show', $id)->with('success', 'Cập nhật đơn hàng thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Order Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    private function getAvailableActions($order, $payment)
    {
        $actions = [
            'canConfirmPayment' => false,
            'canRejectPayment' => false,
            'canMarkAsPaid' => false,
            'canMarkAsShipped' => false,
            'canMarkAsCompleted' => false,
            'canCancel' => false,
        ];

        $orderStatus = $order->status->value;
        $paymentStatus = $payment ? $payment->status->value : null;

        // Logic xác nhận/từ chối thanh toán (COD hoặc Bank Transfer)
        if ($payment && $payment->canBeVerified() && $orderStatus === 'pending') {
            $actions['canConfirmPayment'] = true;
            $actions['canRejectPayment'] = true;
        }

        // Đánh dấu đã thanh toán (khi payment success nhưng order vẫn pending)
        if ($orderStatus === 'pending' && $paymentStatus === 'success' && !$payment->needsVerification()) {
            $actions['canMarkAsPaid'] = true;
        }

        // Đánh dấu đang giao (khi đã thanh toán)
        if ($orderStatus === 'paid') {
            $actions['canMarkAsShipped'] = true;
        }

        // Đánh dấu hoàn thành (khi đang giao)
        if ($orderStatus === 'shipped') {
            $actions['canMarkAsCompleted'] = true;
        }

        // Có thể hủy đơn (pending hoặc paid)
        if (in_array($orderStatus, ['pending', 'paid'])) {
            $actions['canCancel'] = true;
        }

        return $actions;
    }

    public function confirmPayment(Request $request, int $id)
    {
        // ⚠️ Bỏ kiểm tra đăng nhập để test
        // if (!auth()->check()) { ... }

        $order = $this->orderRepo->find($id, ['payments', 'orderItems']);

        if (!$order) {
            return redirect()->route('admin.orders.index')
                ->with('error', 'Không tìm thấy đơn hàng');
        }

        // Lấy payment gần nhất
        $payment = $order->payments()->latest('created_at')->first();

        if (!$payment) {
            return redirect()->route('admin.orders.show', $id)
                ->with('error', 'Không tìm thấy thông tin thanh toán');
        }

        // ⚠️ Bỏ điều kiện canBeVerified để test
        // if (!$payment->canBeVerified()) { ... }

        DB::beginTransaction();
        try {
            // Giả định user xác nhận là admin ID = 1 (hoặc có thể để null)
            $verifier = auth()->user() ?? \App\Models\User::find(1);

            // Xác nhận thanh toán
            $payment->markAsVerified($verifier, $request->input('note'));

            // Cập nhật trạng thái đơn hàng
            $order->update([
                'status' => OrderStatus::Paid,
                'paid_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('admin.orders.show', $id)
                ->with('success', '✅ Đã xác nhận thanh toán (bỏ qua kiểm tra để test)');

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Confirm payment error: ' . $e->getMessage(), [
                'order_id' => $id,
                'payment_id' => $payment->id,
                'user_id' => auth()->id() ?? null
            ]);

            return redirect()->back()
                ->with('error', '❌ Có lỗi xảy ra: ' . $e->getMessage());
        }
    }





    /**
     * Từ chối thanh toán
     */
    public function rejectPayment(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            DB::transaction(function () use ($id, $request) {
                $order = Order::with(['payments', 'orderItems'])->findOrFail($id);
                $payment = $order->payments()->latest()->first();

                if (!$payment) {
                    throw new \Exception('Không tìm thấy thông tin thanh toán');
                }

                $payment->update([
                    'status' => PaymentStatus::Failed,
                    'verification_note' => $request->reason,
                ]);

                $order->update([
                    'status' => OrderStatus::Cancelled,
                    'cancelled_at' => now(),
                    'admin_note' => 'Thanh toán bị từ chối: ' . $request->reason,
                ]);

                // Hoàn lại stock
                foreach ($order->orderItems as $item) {
                    if ($item->variant_id) {
                        $item->variant->stockItems()->increment('quantity', $item->quantity);
                    } else {
                        $item->product->stockItems()->increment('quantity', $item->quantity);
                    }
                }
            });

            return back()->with('success', 'Đã từ chối thanh toán và hủy đơn hàng.');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Cập nhật trạng thái nhanh
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', OrderStatus::values()),
        ]);

        try {
            DB::beginTransaction();

            $order = $this->orderRepo->find($id, ['user', 'payments']);
            if (!$order) {
                throw new \Exception('Không tìm thấy đơn hàng');
            }

            $oldStatus = $order->status;
            $newStatus = OrderStatus::from($validated['status']);

            // Validate status transition
            if (!$this->canTransitionTo($oldStatus, $newStatus)) {
                throw new \Exception('Không thể chuyển từ trạng thái ' . $oldStatus->label() . ' sang ' . $newStatus->label());
            }

            $updateData = ['status' => $newStatus];

            // Update timestamp
            if ($newStatus === OrderStatus::Paid) {
                $updateData['paid_at'] = now();
            } elseif ($newStatus === OrderStatus::Shipped) {
                $updateData['shipped_at'] = now();
            } elseif ($newStatus === OrderStatus::Completed) {
                $updateData['completed_at'] = now();
            }

            $this->orderRepo->update($id, $updateData);

            // Gửi email
            if ($order->user && $order->user->email) {
                try {
                    Mail::to($order->user->email)->send(new OrderStatusUpdated($order, $newStatus));
                } catch (\Exception $e) {
                    Log::error('Send email error: ' . $e->getMessage());
                }
            }

            DB::commit();
            return back()->with('success', 'Cập nhật trạng thái thành công và đã gửi email thông báo!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Check if can transition between statuses
     */
    private function canTransitionTo(OrderStatus $from, OrderStatus $to): bool
    {
        $allowedTransitions = [
            OrderStatus::Pending->value => [OrderStatus::Paid->value, OrderStatus::Cancelled->value],
            OrderStatus::Paid->value => [OrderStatus::Shipped->value, OrderStatus::Cancelled->value],
            OrderStatus::Shipped->value => [OrderStatus::Completed->value],
            OrderStatus::Completed->value => [],
            OrderStatus::Cancelled->value => [],
        ];

        return in_array($to->value, $allowedTransitions[$from->value] ?? []);
    }

    /**
     * Hủy đơn hàng
     */
    public function cancel(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:500']);

        try {
            DB::beginTransaction();

            $order = Order::with(['orderItems', 'payments'])->findOrFail($id);

            if (!in_array($order->status, [OrderStatus::Pending, OrderStatus::Paid])) {
                throw new \Exception('Không thể hủy đơn hàng ở trạng thái này!');
            }

            // Cập nhật payment nếu có
            if ($payment = $order->payments->first()) {
                $payment->update(['status' => PaymentStatus::Failed]);
            }

            $order->update([
                'status' => OrderStatus::Cancelled,
                'cancelled_at' => now(),
                'admin_note' => $request->reason,
            ]);

            // Trả lại stock
            foreach ($order->orderItems as $item) {
                if ($item->variant_id) {
                    $item->variant->stockItems()->increment('quantity', $item->quantity);
                } else {
                    $item->product->stockItems()->increment('quantity', $item->quantity);
                }
            }

            DB::commit();
            return back()->with('success', 'Đã hủy đơn hàng thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xem chi tiết khách hàng từ đơn hàng
     */
    public function customerDetails($orderId)
    {
        $order = Order::with([
            'user.addresses',
            'user.orders' => function ($query) {
                $query->latest()->limit(10);
            },
            'shippingAddress'
        ])->findOrFail($orderId);

        $customer = $order->user;

        if (!$customer) {
            return back()->with('error', 'Không tìm thấy thông tin khách hàng');
        }

        // Thống kê khách hàng
        $customerStats = [
            'total_orders' => $customer->orders()->count(),
            'completed_orders' => $customer->orders()->where('status', OrderStatus::Completed)->count(),
            'cancelled_orders' => $customer->orders()->where('status', OrderStatus::Cancelled)->count(),
            'total_spent' => $customer->orders()->where('status', OrderStatus::Completed)->sum('total_amount'),
            'average_order_value' => $customer->orders()->where('status', OrderStatus::Completed)->avg('total_amount'),
            'first_order' => $customer->orders()->oldest()->first(),
            'last_order' => $customer->orders()->latest()->first(),
        ];

        return view('admin.orders.customer-details', compact('order', 'customer', 'customerStats'));
    }

    /**
     * Danh sách thanh toán cần xác nhận
     */
    public function pendingPayments()
    {
        $pendingPayments = Payment::with(['order.user', 'order.orderItems'])
            ->pendingVerification()
            ->latest()
            ->paginate(20);

        $stats = [
            'total' => Payment::pendingVerification()->count(),
            'cod' => Payment::pendingVerification()->where('payment_method', 'cod')->count(),
            'bank_transfer' => Payment::pendingVerification()->where('payment_method', 'bank')->count(),
        ];

        return view('admin.orders.pending-payments', compact('pendingPayments', 'stats'));
    }

    /**
     * Xóa mềm
     */
    public function destroy($id)
    {
        try {
            $order = $this->orderRepo->find($id);

            if (!$order) {
                return back()->with('error', 'Không tìm thấy đơn hàng');
            }

            if (!in_array($order->status, [OrderStatus::Cancelled, OrderStatus::Completed])) {
                return back()->with('error', 'Chỉ có thể xóa đơn hàng đã hủy hoặc hoàn thành');
            }

            $this->orderRepo->delete($id);
            return redirect()->route('admin.orders.index')->with('success', 'Đã chuyển đơn hàng vào thùng rác');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Đơn hàng đã xóa
     */
    public function trashed()
    {
        $orders = Order::onlyTrashed()
            ->with(['user', 'payments'])
            ->latest('deleted_at')
            ->paginate(15);

        $stats = [
            'total' => Order::onlyTrashed()->count(),
            'pending' => Order::onlyTrashed()->where('status', OrderStatus::Pending)->count(),
            'paid' => Order::onlyTrashed()->where('status', OrderStatus::Paid)->count(),
            'shipped' => Order::onlyTrashed()->where('status', OrderStatus::Shipped)->count(),
            'completed' => Order::onlyTrashed()->where('status', OrderStatus::Completed)->count(),
            'cancelled' => Order::onlyTrashed()->where('status', OrderStatus::Cancelled)->count(),
        ];

        return view('admin.orders.trashed', compact('orders', 'stats'));
    }

    /**
     * Khôi phục
     */
    public function restore($id)
    {
        $order = Order::onlyTrashed()->findOrFail($id);
        $order->restore();

        return redirect()->route('admin.orders.trashed')->with('success', 'Khôi phục đơn hàng thành công');
    }

    /**
     * Xóa vĩnh viễn
     */
    public function forceDelete($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $order = Order::onlyTrashed()->findOrFail($id);
                $order->orderItems()->forceDelete();
                $order->shippingAddress()->forceDelete();
                $order->payments()->forceDelete();
                $order->forceDelete();
            });

            return back()->with('success', 'Đã xóa vĩnh viễn đơn hàng');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * In hóa đơn
     */
    public function invoice($id)
    {
        $order = $this->orderRepo->find($id, ['user', 'orderItems.product', 'orderItems.variant', 'shippingAddress', 'payments']);
        return view('admin.orders.invoice', compact('order'));
    }

    /**
     * Export Excel/CSV
     */
    public function export(Request $request)
    {
        try {
            $query = Order::with(['user', 'payments', 'orderItems', 'shippingAddress']);

            if ($status = $request->status)
                $query->where('status', $status);
            if ($from = $request->from)
                $query->whereDate('created_at', '>=', $from);
            if ($to = $request->to)
                $query->whereDate('created_at', '<=', $to);

            $orders = $query->get();
            $filename = 'orders_' . now()->format('Ymd_His') . '.csv';
            $handle = fopen('php://temp', 'r+');

            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, ['=== DANH SÁCH ĐƠN HÀNG ===']);
            fputcsv($handle, ['Ngày xuất: ' . now()->format('d/m/Y H:i')]);
            fputcsv($handle, []);

            fputcsv($handle, [
                'Mã đơn',
                'Khách hàng',
                'Email',
                'Điện thoại',
                'Ngày đặt',
                'Tổng tiền',
                'Phí ship',
                'Trạng thái đơn',
                'Phương thức TT',
                'Trạng thái TT',
                'Ngày giao'
            ]);

            foreach ($orders as $order) {
                $payment = $order->payments->first();
                fputcsv($handle, [
                    $order->order_number,
                    $order->user ? ($order->user->first_name . ' ' . $order->user->last_name) : 'N/A',
                    $order->user->email ?? 'N/A',
                    $order->shippingAddress->phone ?? 'N/A',
                    $order->created_at->format('d/m/Y H:i'),
                    $order->total_amount,
                    $order->shipping_fee,
                    $order->status->label(),
                    $payment?->payment_method?->label() ?? 'N/A',
                    $payment?->status?->label() ?? 'N/A',
                    $order->shipped_at ? $order->shipped_at->format('d/m/Y') : 'Chưa giao',
                ]);
            }

            rewind($handle);
            $csv = stream_get_contents($handle);
            fclose($handle);

            return response($csv, 200, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    private function generateOrderNumber(): string
    {
        return 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));
    }
}








// namespace App\Http\Controllers\Admin;

// use App\Http\Controllers\Controller;
// use App\Http\Requests\OrderRequest;
// use App\Services\OrderService;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Log;

// class OrderController extends Controller
// {
//     protected OrderService $orderService;

//     public function __construct(OrderService $orderService)
//     {
//         $this->orderService = $orderService;
//     }

//     /** Danh sách đơn hàng */
//     public function index(Request $request)
//     {
//         try {
//             [$orders, $stats] = $this->orderService->getOrdersWithStats($request);
//             return view('admin.orders.index', compact('orders', 'stats'));
//         } catch (\Exception $e) {
//             Log::error('Order Index Error: '.$e->getMessage());
//             return back()->with('error', 'Có lỗi xảy ra khi lấy danh sách đơn hàng');
//         }
//     }

//     /** Chi tiết đơn hàng */
//     public function show($id)
//     {
//         try {
//             $data = $this->orderService->getOrderDetail($id);
//             if (!$data['order']) {
//                 return redirect()->route('admin.orders.index')->with('error', 'Không tìm thấy đơn hàng');
//             }
//             return view('admin.orders.show', $data);
//         } catch (\Exception $e) {
//             Log::error('Order Show Error: '.$e->getMessage());
//             return back()->with('error', 'Có lỗi xảy ra khi lấy chi tiết đơn hàng');
//         }
//     }

//     /** Form chỉnh sửa */
//     public function edit($id)
//     {
//         try {
//             $data = $this->orderService->getEditData($id);
//             if (!$data['order']) {
//                 return redirect()->route('admin.orders.index')->with('error', 'Không tìm thấy đơn hàng');
//             }
//             return view('admin.orders.edit', $data);
//         } catch (\Exception $e) {
//             Log::error('Order Edit Error: '.$e->getMessage());
//             return back()->with('error', 'Có lỗi xảy ra khi lấy dữ liệu chỉnh sửa đơn hàng');
//         }
//     }

//     /** Cập nhật đơn hàng */
//     public function update(OrderRequest $request, $id)
//     {
//         try {
//             return $this->orderService->updateOrder($request, $id);
//         } catch (\Exception $e) {
//             Log::error('Order Update Error: '.$e->getMessage());
//             return back()->withInput()->with('error', 'Có lỗi xảy ra khi cập nhật đơn hàng');
//         }
//     }

//     /** Xác nhận thanh toán */
//     public function confirmPayment(Request $request, $id)
//     {
//         try {
//             return $this->orderService->confirmPayment($request, $id);
//         } catch (\Exception $e) {
//             Log::error('Confirm Payment Error: '.$e->getMessage());
//             return back()->with('error', 'Có lỗi xảy ra khi xác nhận thanh toán');
//         }
//     }

//     /** Từ chối thanh toán */
//     public function rejectPayment(Request $request, $id)
//     {
//         try {
//             return $this->orderService->rejectPayment($request, $id);
//         } catch (\Exception $e) {
//             Log::error('Reject Payment Error: '.$e->getMessage());
//             return back()->with('error', 'Có lỗi xảy ra khi từ chối thanh toán');
//         }
//     }

//     /** Cập nhật trạng thái nhanh */
//     public function updateStatus(Request $request, $id)
//     {
//         try {
//             return $this->orderService->updateStatus($request, $id);
//         } catch (\Exception $e) {
//             Log::error('Update Status Error: '.$e->getMessage());
//             return back()->with('error', 'Có lỗi xảy ra khi cập nhật trạng thái đơn hàng');
//         }
//     }

//     /** Hủy đơn hàng */
//     public function cancel(Request $request, $id)
//     {
//         try {
//             return $this->orderService->cancel($request, $id);
//         } catch (\Exception $e) {
//             Log::error('Cancel Order Error: '.$e->getMessage());
//             return back()->with('error', 'Có lỗi xảy ra khi hủy đơn hàng');
//         }
//     }

//     /** Chi tiết khách hàng từ đơn hàng */
//     public function customerDetails($orderId)
//     {
//         try {
//             return $this->orderService->getCustomerDetails($orderId);
//         } catch (\Exception $e) {
//             Log::error('Customer Details Error: '.$e->getMessage());
//             return back()->with('error', 'Có lỗi xảy ra khi lấy chi tiết khách hàng');
//         }
//     }

//     /** Danh sách thanh toán chờ xác nhận */
//     public function pendingPayments()
//     {
//         try {
//             return $this->orderService->pendingPayments();
//         } catch (\Exception $e) {
//             Log::error('Pending Payments Error: '.$e->getMessage());
//             return back()->with('error', 'Có lỗi xảy ra khi lấy danh sách thanh toán chờ xác nhận');
//         }
//     }

//     /** Xóa mềm */
//     public function destroy($id)
//     {
//         try {
//             return $this->orderService->softDelete($id);
//         } catch (\Exception $e) {
//             Log::error('Destroy Order Error: '.$e->getMessage());
//             return back()->with('error', 'Có lỗi xảy ra khi xóa đơn hàng');
//         }
//     }

//     /** Danh sách đơn đã xóa */
//     public function trashed()
//     {
//         try {
//             return $this->orderService->getTrashed();
//         } catch (\Exception $e) {
//             Log::error('Trashed Orders Error: '.$e->getMessage());
//             return back()->with('error', 'Có lỗi xảy ra khi lấy danh sách đơn hàng đã xóa');
//         }
//     }

//     /** Khôi phục đơn hàng */
//     public function restore($id)
//     {
//         try {
//             return $this->orderService->restore($id);
//         } catch (\Exception $e) {
//             Log::error('Restore Order Error: '.$e->getMessage());
//             return back()->with('error', 'Có lỗi xảy ra khi khôi phục đơn hàng');
//         }
//     }

//     /** Xóa vĩnh viễn */
//     public function forceDelete($id)
//     {
//         try {
//             return $this->orderService->forceDelete($id);
//         } catch (\Exception $e) {
//             Log::error('Force Delete Order Error: '.$e->getMessage());
//             return back()->with('error', 'Có lỗi xảy ra khi xóa vĩnh viễn đơn hàng');
//         }
//     }

//     /** In hóa đơn */
//     public function invoice($id)
//     {
//         try {
//             return $this->orderService->invoice($id);
//         } catch (\Exception $e) {
//             Log::error('Invoice Error: '.$e->getMessage());
//             return back()->with('error', 'Có lỗi xảy ra khi in hóa đơn');
//         }
//     }

//     /** Export CSV/Excel */
//     public function export(Request $request)
//     {
//         try {
//             return $this->orderService->export($request);
//         } catch (\Exception $e) {
//             Log::error('Export Orders Error: '.$e->getMessage());
//             return back()->with('error', 'Có lỗi xảy ra khi xuất file đơn hàng');
//         }
//     }
// }