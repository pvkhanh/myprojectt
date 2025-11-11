<?php

// namespace App\Services;

// use App\Repositories\Contracts\OrderRepositoryInterface;
// use App\Repositories\Contracts\OrderItemRepositoryInterface;
// use App\Repositories\Contracts\ShippingAddressRepositoryInterface;
// use App\Models\Order;
// use App\Models\Payment;
// use App\Enums\OrderStatus;
// use App\Enums\PaymentStatus;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\Mail;
// use App\Mail\OrderStatusUpdated;
// use App\Mail\PaymentConfirmed;
// use Illuminate\Support\Str;

// class OrderService
// {
//     public function __construct(
//         protected OrderRepositoryInterface $orderRepo,
//         protected OrderItemRepositoryInterface $orderItemRepo,
//         protected ShippingAddressRepositoryInterface $shippingAddressRepo
//     ) {}

//     /** Lấy danh sách đơn hàng + thống kê */
//     public function getOrdersWithStats(Request $request)
//     {
//         $query = Order::with(['user', 'payments', 'orderItems'])->latest();

//         if ($search = $request->input('search')) {
//             $query->where(function ($q) use ($search) {
//                 $q->where('order_number', 'like', "%{$search}%")
//                   ->orWhereHas('user', function ($u) use ($search) {
//                       $u->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%")
//                         ->orWhere('email', 'like', "%{$search}%");
//                   });
//             });
//         }

//         if ($status = $request->status) $query->where('status', $status);
//         if ($from = $request->from) $query->whereDate('created_at', '>=', $from);
//         if ($to = $request->to) $query->whereDate('created_at', '<=', $to);
//         if ($min = $request->min_amount) $query->where('total_amount', '>=', $min);
//         if ($max = $request->max_amount) $query->where('total_amount', '<=', $max);

//         $orders = $query->paginate(15)->withQueryString();

//         $statsQuery = Order::query();
//         if ($from) $statsQuery->whereDate('created_at', '>=', $from);
//         if ($to) $statsQuery->whereDate('created_at', '<=', $to);

//         $stats = [
//             'total' => (clone $statsQuery)->count(),
//             'pending' => (clone $statsQuery)->where('status', OrderStatus::Pending)->count(),
//             'paid' => (clone $statsQuery)->where('status', OrderStatus::Paid)->count(),
//             'shipped' => (clone $statsQuery)->where('status', OrderStatus::Shipped)->count(),
//             'completed' => (clone $statsQuery)->where('status', OrderStatus::Completed)->count(),
//             'cancelled' => (clone $statsQuery)->where('status', OrderStatus::Cancelled)->count(),
//             'total_revenue' => (clone $statsQuery)->where('status', OrderStatus::Completed)->sum('total_amount'),
//         ];

//         return [$orders, $stats];
//     }

//     /** Lấy chi tiết đơn hàng */
//     public function getOrderDetail($id)
//     {
//         $order = $this->orderRepo->find($id, ['user', 'orderItems.product', 'orderItems.variant', 'shippingAddress', 'payments']);
//         $payment = $order?->payments?->first();
//         $actions = $order ? $this->getAvailableActions($order, $payment) : [];

//         return compact('order', 'payment', 'actions');
//     }

//     /** Dữ liệu cho form edit */
//     public function getEditData($id)
//     {
//         $order = $this->orderRepo->find($id, ['user', 'orderItems.product', 'orderItems.variant', 'shippingAddress', 'payments']);
//         $statuses = OrderStatus::cases();
//         return compact('order', 'statuses');
//     }

//     /** Cập nhật đơn hàng */
//     public function updateOrder(Request $request, $id)
//     {
//         $validated = $request->validate([
//             'status' => 'required|in:' . implode(',', OrderStatus::values()),
//             'admin_note' => 'nullable|string|max:500',
//         ]);

//         try {
//             DB::beginTransaction();

//             $order = $this->orderRepo->find($id, ['orderItems', 'user']);
//             if (!$order) throw new \Exception('Không tìm thấy đơn hàng');

//             $oldStatus = $order->status;
//             $newStatus = OrderStatus::from($validated['status']);

//             if (!$this->canTransitionTo($oldStatus, $newStatus)) {
//                 throw new \Exception("Không thể chuyển từ {$oldStatus->label()} sang {$newStatus->label()}");
//             }

//             $updateData = [
//                 'status' => $newStatus,
//                 'admin_note' => $validated['admin_note'] ?? $order->admin_note,
//             ];

//             if ($newStatus === OrderStatus::Paid && $oldStatus !== OrderStatus::Paid) $updateData['paid_at'] = now();
//             if ($newStatus === OrderStatus::Shipped && $oldStatus !== OrderStatus::Shipped) $updateData['shipped_at'] = now();
//             if ($newStatus === OrderStatus::Completed && $oldStatus !== OrderStatus::Completed) $updateData['completed_at'] = now();

//             $this->orderRepo->update($id, $updateData);

//             if ($oldStatus !== $newStatus && $order->user?->email) {
//                 Mail::to($order->user->email)->send(new OrderStatusUpdated($order, $newStatus));
//             }

//             DB::commit();
//             return redirect()->route('admin.orders.show', $id)->with('success', 'Cập nhật đơn hàng thành công');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             Log::error('Update Order Error: ' . $e->getMessage());
//             return back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
//         }
//     }

//     /** Xác nhận thanh toán */
//     public function confirmPayment(Request $request, int $id)
//     {
//         $order = $this->orderRepo->find($id, ['payments', 'orderItems']);
//         if (!$order) return redirect()->route('admin.orders.index')->with('error', 'Không tìm thấy đơn hàng');

//         $payment = $order->payments()->latest('created_at')->first();
//         if (!$payment) return redirect()->route('admin.orders.show', $id)->with('error', 'Không tìm thấy thông tin thanh toán');

//         DB::beginTransaction();
//         try {
//             $verifier = auth()->user() ?? \App\Models\User::find(1);
//             $payment->markAsVerified($verifier, $request->input('note'));

//             $order->update([
//                 'status' => OrderStatus::Paid,
//                 'paid_at' => now(),
//             ]);

//             DB::commit();
//             return redirect()->route('admin.orders.show', $id)->with('success', 'Đã xác nhận thanh toán');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             Log::error('Confirm payment error: ' . $e->getMessage(), [
//                 'order_id' => $id,
//                 'payment_id' => $payment->id,
//                 'user_id' => auth()->id() ?? null
//             ]);
//             return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
//         }
//     }

//     /** Từ chối thanh toán */
//     public function rejectPayment(Request $request, $id)
//     {
//         $request->validate(['reason' => 'required|string|max:500']);

//         try {
//             DB::transaction(function () use ($id, $request) {
//                 $order = Order::with(['payments', 'orderItems'])->findOrFail($id);
//                 $payment = $order->payments()->latest()->first();
//                 if (!$payment) throw new \Exception('Không tìm thấy thông tin thanh toán');

//                 $payment->update([
//                     'status' => PaymentStatus::Failed,
//                     'verification_note' => $request->reason,
//                 ]);

//                 $order->update([
//                     'status' => OrderStatus::Cancelled,
//                     'cancelled_at' => now(),
//                     'admin_note' => 'Thanh toán bị từ chối: ' . $request->reason,
//                 ]);

//                 foreach ($order->orderItems as $item) {
//                     if ($item->variant_id) $item->variant->stockItems()->increment('quantity', $item->quantity);
//                     else $item->product->stockItems()->increment('quantity', $item->quantity);
//                 }
//             });

//             return back()->with('success', 'Đã từ chối thanh toán và hủy đơn hàng');
//         } catch (\Exception $e) {
//             return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
//         }
//     }

//     /** Cập nhật trạng thái nhanh */
//     public function updateStatus(Request $request, $id)
//     {
//         $validated = $request->validate(['status' => 'required|in:' . implode(',', OrderStatus::values())]);
//         try {
//             DB::beginTransaction();

//             $order = $this->orderRepo->find($id, ['user', 'payments']);
//             if (!$order) throw new \Exception('Không tìm thấy đơn hàng');

//             $oldStatus = $order->status;
//             $newStatus = OrderStatus::from($validated['status']);
//             if (!$this->canTransitionTo($oldStatus, $newStatus)) throw new \Exception("Không thể chuyển từ {$oldStatus->label()} sang {$newStatus->label()}");

//             $updateData = ['status' => $newStatus];
//             if ($newStatus === OrderStatus::Paid) $updateData['paid_at'] = now();
//             if ($newStatus === OrderStatus::Shipped) $updateData['shipped_at'] = now();
//             if ($newStatus === OrderStatus::Completed) $updateData['completed_at'] = now();

//             $this->orderRepo->update($id, $updateData);

//             if ($order->user?->email) Mail::to($order->user->email)->send(new OrderStatusUpdated($order, $newStatus));

//             DB::commit();
//             return back()->with('success', 'Cập nhật trạng thái thành công');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
//         }
//     }

//     /** Hủy đơn hàng */
//     public function cancel(Request $request, $id)
//     {
//         $request->validate(['reason' => 'required|string|max:500']);
//         try {
//             DB::beginTransaction();

//             $order = Order::with(['orderItems', 'payments'])->findOrFail($id);
//             if (!in_array($order->status, [OrderStatus::Pending, OrderStatus::Paid])) throw new \Exception('Không thể hủy đơn hàng');

//             if ($payment = $order->payments->first()) $payment->update(['status' => PaymentStatus::Failed]);

//             $order->update([
//                 'status' => OrderStatus::Cancelled,
//                 'cancelled_at' => now(),
//                 'admin_note' => $request->reason,
//             ]);

//             foreach ($order->orderItems as $item) {
//                 if ($item->variant_id) $item->variant->stockItems()->increment('quantity', $item->quantity);
//                 else $item->product->stockItems()->increment('quantity', $item->quantity);
//             }

//             DB::commit();
//             return back()->with('success', 'Đã hủy đơn hàng');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
//         }
//     }

//     /** Chi tiết khách hàng */
//     public function getCustomerDetails($orderId)
//     {
//         $order = Order::with([
//             'user.addresses',
//             'user.orders' => fn($q) => $q->latest()->limit(10),
//             'shippingAddress'
//         ])->findOrFail($orderId);

//         $customer = $order->user;
//         if (!$customer) return back()->with('error', 'Không tìm thấy thông tin khách hàng');

//         $customerStats = [
//             'total_orders' => $customer->orders()->count(),
//             'completed_orders' => $customer->orders()->where('status', OrderStatus::Completed)->count(),
//             'cancelled_orders' => $customer->orders()->where('status', OrderStatus::Cancelled)->count(),
//             'total_spent' => $customer->orders()->where('status', OrderStatus::Completed)->sum('total_amount'),
//             'average_order_value' => $customer->orders()->where('status', OrderStatus::Completed)->avg('total_amount'),
//             'first_order' => $customer->orders()->oldest()->first(),
//             'last_order' => $customer->orders()->latest()->first(),
//         ];

//         return view('admin.orders.customer-details', compact('order', 'customer', 'customerStats'));
//     }

//     /** Danh sách thanh toán đang chờ */
//     public function pendingPayments()
//     {
//         $pendingPayments = Payment::with(['order.user', 'order.orderItems'])
//             ->pendingVerification()
//             ->latest()
//             ->paginate(20);

//         $stats = [
//             'total' => Payment::pendingVerification()->count(),
//             'cod' => Payment::pendingVerification()->where('payment_method', 'cod')->count(),
//             'bank_transfer' => Payment::pendingVerification()->where('payment_method', 'bank')->count(),
//         ];

//         return view('admin.orders.pending-payments', compact('pendingPayments', 'stats'));
//     }

//     /** Xóa mềm */
//     public function softDelete($id)
//     {
//         try {
//             $order = $this->orderRepo->find($id);
//             if (!$order) return back()->with('error', 'Không tìm thấy đơn hàng');
//             if (!in_array($order->status, [OrderStatus::Cancelled, OrderStatus::Completed])) {
//                 return back()->with('error', 'Chỉ xóa đơn hàng đã hủy hoặc hoàn thành');
//             }
//             $this->orderRepo->delete($id);
//             return redirect()->route('admin.orders.index')->with('success', 'Đã chuyển đơn hàng vào thùng rác');
//         } catch (\Exception $e) {
//             return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
//         }
//     }

//     /** Danh sách đã xóa */
//     public function getTrashed()
//     {
//         $orders = Order::onlyTrashed()->with(['user', 'payments'])->latest('deleted_at')->paginate(15);

//         $stats = [
//             'total' => Order::onlyTrashed()->count(),
//             'pending' => Order::onlyTrashed()->where('status', OrderStatus::Pending)->count(),
//             'paid' => Order::onlyTrashed()->where('status', OrderStatus::Paid)->count(),
//             'shipped' => Order::onlyTrashed()->where('status', OrderStatus::Shipped)->count(),
//             'completed' => Order::onlyTrashed()->where('status', OrderStatus::Completed)->count(),
//             'cancelled' => Order::onlyTrashed()->where('status', OrderStatus::Cancelled)->count(),
//         ];

//         return view('admin.orders.trashed', compact('orders', 'stats'));
//     }

//     /** Khôi phục */
//     public function restore($id)
//     {
//         $order = Order::onlyTrashed()->findOrFail($id);
//         $order->restore();
//         return redirect()->route('admin.orders.trashed')->with('success', 'Khôi phục đơn hàng thành công');
//     }

//     /** Xóa vĩnh viễn */
//     public function forceDelete($id)
//     {
//         try {
//             DB::transaction(function () use ($id) {
//                 $order = Order::onlyTrashed()->findOrFail($id);
//                 $order->orderItems()->forceDelete();
//                 $order->shippingAddress()->forceDelete();
//                 $order->payments()->forceDelete();
//                 $order->forceDelete();
//             });
//             return back()->with('success', 'Đã xóa vĩnh viễn đơn hàng');
//         } catch (\Exception $e) {
//             return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
//         }
//     }

//     /** In hóa đơn */
//     public function invoice($id)
//     {
//         $order = $this->orderRepo->find($id, ['user', 'orderItems.product', 'orderItems.variant', 'shippingAddress', 'payments']);
//         return view('admin.orders.invoice', compact('order'));
//     }

//     /** Export CSV */
//     public function export(Request $request)
//     {
//         try {
//             $query = Order::with(['user', 'payments', 'orderItems', 'shippingAddress']);
//             if ($status = $request->status) $query->where('status', $status);
//             if ($from = $request->from) $query->whereDate('created_at', '>=', $from);
//             if ($to = $request->to) $query->whereDate('created_at', '<=', $to);

//             $orders = $query->get();
//             $filename = 'orders_' . now()->format('Ymd_His') . '.csv';
//             $handle = fopen('php://temp', 'r+');

//             // BOM UTF-8
//             fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
//             fputcsv($handle, ['=== DANH SÁCH ĐƠN HÀNG ===']);
//             fputcsv($handle, ['Ngày xuất: ' . now()->format('d/m/Y H:i')]);
//             fputcsv($handle, []);
//             fputcsv($handle, [
//                 'Mã đơn','Khách hàng','Email','Điện thoại','Ngày đặt',
//                 'Tổng tiền','Phí ship','Trạng thái đơn','Phương thức TT',
//                 'Trạng thái TT','Ngày giao'
//             ]);

//             foreach ($orders as $order) {
//                 $payment = $order->payments->first();
//                 fputcsv($handle, [
//                     $order->order_number,
//                     $order->user ? ($order->user->first_name . ' ' . $order->user->last_name) : 'N/A',
//                     $order->user->email ?? 'N/A',
//                     $order->shippingAddress->phone ?? 'N/A',
//                     $order->created_at->format('d/m/Y H:i'),
//                     $order->total_amount,
//                     $order->shipping_fee,
//                     $order->status->label(),
//                     $payment?->payment_method?->label() ?? 'N/A',
//                     $payment?->status?->label() ?? 'N/A',
//                     $order->shipped_at?->format('d/m/Y') ?? 'N/A'
//                 ]);
//             }

//             rewind($handle);
//             $content = stream_get_contents($handle);
//             fclose($handle);

//             return response($content)
//                 ->header('Content-Type', 'text/csv')
//                 ->header('Content-Disposition', "attachment; filename={$filename}");
//         } catch (\Exception $e) {
//             return back()->with('error', 'Có lỗi xảy ra khi xuất file: ' . $e->getMessage());
//         }
//     }

//     /** Hỗ trợ */
//     private function getAvailableActions($order, $payment)
//     {
//         $a = [
//             'canConfirmPayment'=>false,
//             'canRejectPayment'=>false,
//             'canMarkAsPaid'=>false,
//             'canMarkAsShipped'=>false,
//             'canMarkAsCompleted'=>false,
//             'canCancel'=>false
//         ];

//         $status = $order->status->value;
//         $paymentStatus = $payment?->status->value;

//         if ($payment && $payment->canBeVerified() && $status === 'pending') {
//             $a['canConfirmPayment'] = $a['canRejectPayment'] = true;
//         }

//         if ($status === 'pending' && $paymentStatus === 'success' && !$payment->needsVerification()) $a['canMarkAsPaid'] = true;
//         if ($status === 'paid') $a['canMarkAsShipped'] = true;
//         if ($status === 'shipped') $a['canMarkAsCompleted'] = true;
//         if (in_array($status, ['pending','paid'])) $a['canCancel'] = true;

//         return $a;
//     }

//     private function canTransitionTo(OrderStatus $from, OrderStatus $to): bool
//     {
//         $map = [
//             OrderStatus::Pending->value => [OrderStatus::Paid->value, OrderStatus::Cancelled->value],
//             OrderStatus::Paid->value => [OrderStatus::Shipped->value, OrderStatus::Cancelled->value],
//             OrderStatus::Shipped->value => [OrderStatus::Completed->value],
//             OrderStatus::Completed->value => [],
//             OrderStatus::Cancelled->value => [],
//         ];
//         return in_array($to->value, $map[$from->value] ?? []);
//     }
// }





namespace App\Services;

use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\OrderItemRepositoryInterface;
use App\Repositories\Contracts\ShippingAddressRepositoryInterface;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Mail\OrderStatusUpdated;

class OrderService
{
    protected OrderRepositoryInterface $orderRepo;
    protected OrderItemRepositoryInterface $orderItemRepo;
    protected ShippingAddressRepositoryInterface $shippingAddressRepo;

    public function __construct(
        OrderRepositoryInterface $orderRepo,
        OrderItemRepositoryInterface $orderItemRepo,
        ShippingAddressRepositoryInterface $shippingAddressRepo
    ) {
        $this->orderRepo = $orderRepo;
        $this->orderItemRepo = $orderItemRepo;
        $this->shippingAddressRepo = $shippingAddressRepo;
    }

    /** Lấy danh sách đơn + thống kê */
    public function getOrdersWithStats(Request $request)
    {
        try {
            $query = Order::with(['user', 'payments', 'orderItems'])->latest();

            if ($search = $request->input('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($u) use ($search) {
                            $u->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            }

            if ($status = $request->status) $query->where('status', $status);
            if ($from = $request->from) $query->whereDate('created_at', '>=', $from);
            if ($to = $request->to) $query->whereDate('created_at', '<=', $to);
            if ($minAmount = $request->min_amount) $query->where('total_amount', '>=', $minAmount);
            if ($maxAmount = $request->max_amount) $query->where('total_amount', '<=', $maxAmount);

            $orders = $query->paginate(15)->withQueryString();

            $statsQuery = Order::query();
            if ($from) $statsQuery->whereDate('created_at', '>=', $from);
            if ($to) $statsQuery->whereDate('created_at', '<=', $to);

            $stats = [
                'total' => (clone $statsQuery)->count(),
                'pending' => (clone $statsQuery)->where('status', OrderStatus::Pending)->count(),
                'paid' => (clone $statsQuery)->where('status', OrderStatus::Paid)->count(),
                'shipped' => (clone $statsQuery)->where('status', OrderStatus::Shipped)->count(),
                'completed' => (clone $statsQuery)->where('status', OrderStatus::Completed)->count(),
                'cancelled' => (clone $statsQuery)->where('status', OrderStatus::Cancelled)->count(),
                'total_revenue' => (clone $statsQuery)->where('status', OrderStatus::Completed)->sum('total_amount'),
            ];

            return [$orders, $stats];
        } catch (\Exception $e) {
            Log::error('Get Orders With Stats Error: '.$e->getMessage());
            throw $e;
        }
    }

    /** Chi tiết đơn hàng */
    public function getOrderDetail($id)
    {
        try {
            $order = $this->orderRepo->find($id, ['user', 'orderItems.product', 'orderItems.variant', 'shippingAddress', 'payments']);
            $payment = $order?->payments->first();
            $actions = $this->getAvailableActions($order, $payment);

            return [
                'order' => $order,
                'payment' => $payment,
                'actions' => $actions,
            ];
        } catch (\Exception $e) {
            Log::error('Get Order Detail Error: '.$e->getMessage());
            throw $e;
        }
    }

    /** Form edit data */
    public function getEditData($id)
    {
        try {
            $order = $this->orderRepo->find($id, ['user', 'orderItems.product', 'orderItems.variant', 'shippingAddress', 'payments']);
            $statuses = OrderStatus::cases();
            return ['order' => $order, 'statuses' => $statuses];
        } catch (\Exception $e) {
            Log::error('Get Edit Data Error: '.$e->getMessage());
            throw $e;
        }
    }

    /** Cập nhật đơn hàng */
    public function updateOrder(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $order = $this->orderRepo->find($id, ['orderItems', 'user']);
            if (!$order) throw new \Exception('Không tìm thấy đơn hàng');

            $oldStatus = $order->status;
            $newStatus = OrderStatus::from($request->status);

            if (!$this->canTransitionTo($oldStatus, $newStatus)) {
                throw new \Exception('Không thể chuyển từ '.$oldStatus->label().' sang '.$newStatus->label());
            }

            $updateData = [
                'status' => $newStatus,
                'admin_note' => $request->admin_note ?? $order->admin_note,
            ];

            if ($newStatus === OrderStatus::Paid && $oldStatus !== OrderStatus::Paid) $updateData['paid_at'] = now();
            elseif ($newStatus === OrderStatus::Shipped && $oldStatus !== OrderStatus::Shipped) $updateData['shipped_at'] = now();
            elseif ($newStatus === OrderStatus::Completed && $oldStatus !== OrderStatus::Completed) $updateData['completed_at'] = now();

            $this->orderRepo->update($id, $updateData);

            if ($oldStatus !== $newStatus && $order->user?->email) {
                try {
                    Mail::to($order->user->email)->send(new OrderStatusUpdated($order, $newStatus));
                } catch (\Exception $e) {
                    Log::error('Send email error: '.$e->getMessage());
                }
            }

            DB::commit();
            return redirect()->route('admin.orders.show', $id)->with('success', 'Cập nhật đơn hàng thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Order Error: '.$e->getMessage());
            return back()->withInput()->with('error', 'Có lỗi xảy ra: '.$e->getMessage());
        }
    }

    /** Confirm payment */
    public function confirmPayment(Request $request, int $id)
    {
        try {
            DB::beginTransaction();

            $order = $this->orderRepo->find($id, ['payments', 'orderItems']);
            if (!$order) throw new \Exception('Không tìm thấy đơn hàng');

            $payment = $order->payments()->latest()->first();
            if (!$payment) throw new \Exception('Không tìm thấy thanh toán');

            $verifier = auth()->user() ?? User::find(1);
            $payment->markAsVerified($verifier, $request->note ?? null);

            $order->update(['status' => OrderStatus::Paid, 'paid_at' => now()]);

            DB::commit();
            return redirect()->route('admin.orders.show', $id)->with('success', 'Đã xác nhận thanh toán');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Confirm Payment Error: '.$e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra: '.$e->getMessage());
        }
    }

    /** Reject payment */
    public function rejectPayment(Request $request, $id)
    {
        try {
            $request->validate(['reason' => 'required|string|max:500']);
            DB::transaction(function () use ($id, $request) {
                $order = Order::with(['payments', 'orderItems'])->findOrFail($id);
                $payment = $order->payments()->latest()->first();
                if (!$payment) throw new \Exception('Không tìm thấy thanh toán');

                $payment->update(['status' => PaymentStatus::Failed, 'verification_note' => $request->reason]);
                $order->update([
                    'status' => OrderStatus::Cancelled,
                    'cancelled_at' => now(),
                    'admin_note' => 'Thanh toán bị từ chối: ' . $request->reason,
                ]);

                foreach ($order->orderItems as $item) {
                    if ($item->variant_id) $item->variant->stockItems()->increment('quantity', $item->quantity);
                    else $item->product->stockItems()->increment('quantity', $item->quantity);
                }
            });
            return back()->with('success', 'Đã từ chối thanh toán và hủy đơn hàng');
        } catch (\Exception $e) {
            Log::error('Reject Payment Error: '.$e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra: '.$e->getMessage());
        }
    }

    /** Update status quick */
    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate(['status' => 'required|in:' . implode(',', OrderStatus::values())]);
            DB::beginTransaction();

            $order = $this->orderRepo->find($id, ['user', 'payments']);
            if (!$order) throw new \Exception('Không tìm thấy đơn hàng');

            $oldStatus = $order->status;
            $newStatus = OrderStatus::from($request->status);

            if (!$this->canTransitionTo($oldStatus, $newStatus)) {
                throw new \Exception('Không thể chuyển từ '.$oldStatus->label().' sang '.$newStatus->label());
            }

            $updateData = ['status' => $newStatus];
            if ($newStatus === OrderStatus::Paid) $updateData['paid_at'] = now();
            elseif ($newStatus === OrderStatus::Shipped) $updateData['shipped_at'] = now();
            elseif ($newStatus === OrderStatus::Completed) $updateData['completed_at'] = now();

            $this->orderRepo->update($id, $updateData);

            if ($order->user?->email) {
                try {
                    Mail::to($order->user->email)->send(new OrderStatusUpdated($order, $newStatus));
                } catch (\Exception $e) {
                    Log::error('Send email error: '.$e->getMessage());
                }
            }

            DB::commit();
            return back()->with('success', 'Cập nhật trạng thái thành công và gửi email');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Status Error: '.$e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra: '.$e->getMessage());
        }
    }

    /** Cancel order */
    public function cancel(Request $request, $id)
    {
        try {
            $request->validate(['reason' => 'required|string|max:500']);
            DB::beginTransaction();

            $order = Order::with(['orderItems', 'payments'])->findOrFail($id);
            if (!in_array($order->status, [OrderStatus::Pending, OrderStatus::Paid])) {
                throw new \Exception('Không thể hủy đơn hàng ở trạng thái này');
            }

            if ($payment = $order->payments->first()) {
                $payment->update(['status' => PaymentStatus::Failed]);
            }

            $order->update([
                'status' => OrderStatus::Cancelled,
                'cancelled_at' => now(),
                'admin_note' => $request->reason,
            ]);

            foreach ($order->orderItems as $item) {
                if ($item->variant_id) $item->variant->stockItems()->increment('quantity', $item->quantity);
                else $item->product->stockItems()->increment('quantity', $item->quantity);
            }

            DB::commit();
            return back()->with('success', 'Đã hủy đơn hàng');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Cancel Order Error: '.$e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra: '.$e->getMessage());
        }
    }

    /** Trashed orders */
    public function trashed()
    {
        try {
            $orders = Order::onlyTrashed()->with(['user', 'payments'])->latest('deleted_at')->paginate(15);

            $stats = [
                'total' => Order::onlyTrashed()->count(),
                'pending' => Order::onlyTrashed()->where('status', OrderStatus::Pending)->count(),
                'paid' => Order::onlyTrashed()->where('status', OrderStatus::Paid)->count(),
                'shipped' => Order::onlyTrashed()->where('status', OrderStatus::Shipped)->count(),
                'completed' => Order::onlyTrashed()->where('status', OrderStatus::Completed)->count(),
                'cancelled' => Order::onlyTrashed()->where('status', OrderStatus::Cancelled)->count(),
            ];

            return [$orders, $stats];
        } catch (\Exception $e) {
            Log::error('Trashed Orders Error: '.$e->getMessage());
            throw $e;
        }
    }

    /** Restore order */
    public function restore($id)
    {
        try {
            $order = Order::onlyTrashed()->findOrFail($id);
            $order->restore();

            return redirect()->route('admin.orders.trashed')->with('success', 'Khôi phục đơn hàng thành công');
        } catch (\Exception $e) {
            Log::error('Restore Order Error: '.$e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra: '.$e->getMessage());
        }
    }

    /** Force delete order */
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
            Log::error('Force Delete Order Error: '.$e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra: '.$e->getMessage());
        }
    }

    /** Invoice view */
    public function invoice($id)
    {
        try {
            $order = $this->orderRepo->find($id, ['user', 'orderItems.product', 'orderItems.variant', 'shippingAddress', 'payments']);
            return $order;
        } catch (\Exception $e) {
            Log::error('Invoice Error: '.$e->getMessage());
            throw $e;
        }
    }

    /** Export CSV */
    public function export(Request $request)
    {
        try {
            $query = Order::with(['user', 'payments', 'orderItems', 'shippingAddress']);

            if ($status = $request->status) $query->where('status', $status);
            if ($from = $request->from) $query->whereDate('created_at', '>=', $from);
            if ($to = $request->to) $query->whereDate('created_at', '<=', $to);

            $orders = $query->get();
            $filename = 'orders_' . now()->format('Ymd_His') . '.csv';
            $handle = fopen('php://temp', 'r+');

            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, ['=== DANH SÁCH ĐƠN HÀNG ===']);
            fputcsv($handle, ['Ngày xuất: ' . now()->format('d/m/Y H:i')]);
            fputcsv($handle, []);
            fputcsv($handle, [
                'Mã đơn', 'Khách hàng', 'Email', 'Điện thoại', 'Ngày đặt',
                'Tổng tiền', 'Phí ship', 'Trạng thái đơn', 'Phương thức TT', 'Trạng thái TT', 'Ngày giao'
            ]);

            foreach ($orders as $order) {
                $payment = $order->payments->first();
                fputcsv($handle, [
                    $order->order_number,
                    $order->user ? ($order->user->first_name.' '.$order->user->last_name) : 'N/A',
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
            Log::error('Export CSV Error: '.$e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra: '.$e->getMessage());
        }
    }

    /** Các phương thức phụ trợ */
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

        $orderStatus = $order?->status->value;
        $paymentStatus = $payment?->status->value ?? null;

        if ($payment && $payment->canBeVerified() && $orderStatus === 'pending') {
            $actions['canConfirmPayment'] = true;
            $actions['canRejectPayment'] = true;
        }

        if ($orderStatus === 'pending' && $paymentStatus === 'success' && !$payment?->needsVerification()) {
            $actions['canMarkAsPaid'] = true;
        }

        if ($orderStatus === 'paid') $actions['canMarkAsShipped'] = true;
        if ($orderStatus === 'shipped') $actions['canMarkAsCompleted'] = true;
        if (in_array($orderStatus, ['pending', 'paid'])) $actions['canCancel'] = true;

        return $actions;
    }

    private function canTransitionTo(OrderStatus $from, OrderStatus $to): bool
    {
        $allowed = [
            OrderStatus::Pending->value => [OrderStatus::Paid->value, OrderStatus::Cancelled->value],
            OrderStatus::Paid->value => [OrderStatus::Shipped->value, OrderStatus::Cancelled->value],
            OrderStatus::Shipped->value => [OrderStatus::Completed->value],
            OrderStatus::Completed->value => [],
            OrderStatus::Cancelled->value => [],
        ];

        return in_array($to->value, $allowed[$from->value] ?? []);
    }
}