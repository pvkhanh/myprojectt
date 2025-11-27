<?php

// namespace App\Http\Controllers\Admin;

// use App\Http\Controllers\Controller;
// use App\Repositories\Contracts\OrderRepositoryInterface;
// use App\Repositories\Contracts\OrderItemRepositoryInterface;
// use App\Repositories\Contracts\ShippingAddressRepositoryInterface;
// use App\Models\Order;
// use App\Models\Product;
// use App\Models\User;
// use App\Models\Payment;
// use App\Enums\OrderStatus;
// use App\Enums\PaymentStatus;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Str;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Mail;
// use App\Mail\OrderStatusUpdated;
// use App\Mail\PaymentConfirmed;

// class OrderController extends Controller
// {
//     protected $orderRepo;
//     protected $orderItemRepo;
//     protected $shippingAddressRepo;

//     public function __construct(
//         OrderRepositoryInterface $orderRepo,
//         OrderItemRepositoryInterface $orderItemRepo,
//         ShippingAddressRepositoryInterface $shippingAddressRepo
//     ) {
//         $this->orderRepo = $orderRepo;
//         $this->orderItemRepo = $orderItemRepo;
//         $this->shippingAddressRepo = $shippingAddressRepo;
//     }

//     /**
//      * Danh sách đơn hàng
//      */
//     public function index(Request $request)
//     {
//         $query = Order::with(['user', 'payments', 'orderItems'])->latest();

//         // Tìm kiếm
//         if ($search = $request->input('search')) {
//             $query->where(function ($q) use ($search) {
//                 $q->where('order_number', 'like', "%{$search}%")
//                     ->orWhereHas('user', function ($u) use ($search) {
//                         $u->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%")
//                             ->orWhere('email', 'like', "%{$search}%");
//                     });
//             });
//         }

//         // Lọc trạng thái
//         if ($status = $request->status) {
//             $query->where('status', $status);
//         }

//         // Lọc ngày
//         if ($from = $request->from) {
//             $query->whereDate('created_at', '>=', $from);
//         }
//         if ($to = $request->to) {
//             $query->whereDate('created_at', '<=', $to);
//         }

//         // Lọc khoảng tiền
//         if ($minAmount = $request->min_amount) {
//             $query->where('total_amount', '>=', $minAmount);
//         }
//         if ($maxAmount = $request->max_amount) {
//             $query->where('total_amount', '<=', $maxAmount);
//         }

//         $orders = $query->paginate(15)->withQueryString();

//         // Thống kê
//         $statsQuery = Order::query();
//         if ($from)
//             $statsQuery->whereDate('created_at', '>=', $from);
//         if ($to)
//             $statsQuery->whereDate('created_at', '<=', $to);

//         $stats = [
//             'total' => (clone $statsQuery)->count(),
//             'pending' => (clone $statsQuery)->where('status', OrderStatus::Pending)->count(),
//             'paid' => (clone $statsQuery)->where('status', OrderStatus::Paid)->count(),
//             'shipped' => (clone $statsQuery)->where('status', OrderStatus::Shipped)->count(),
//             'completed' => (clone $statsQuery)->where('status', OrderStatus::Completed)->count(),
//             'cancelled' => (clone $statsQuery)->where('status', OrderStatus::Cancelled)->count(),
//             'total_revenue' => (clone $statsQuery)->where('status', OrderStatus::Completed)->sum('total_amount'),
//         ];

//         return view('admin.orders.index', compact('orders', 'stats'));
//     }

//     /**
//      * Chi tiết đơn hàng với logic hiển thị nút
//      */
//     public function show($id)
//     {
//         $order = $this->orderRepo->find($id, ['user', 'orderItems.product', 'orderItems.variant', 'shippingAddress', 'payments']);

//         if (!$order) {
//             return redirect()->route('admin.orders.index')->with('error', 'Không tìm thấy đơn hàng');
//         }

//         $payment = $order->payments->first();

//         // Xác định các actions có thể thực hiện
//         $actions = $this->getAvailableActions($order, $payment);

//         return view('admin.orders.show', compact('order', 'payment', 'actions'));
//     }

//     /**
//      * Xác định actions khả dụng dựa trên trạng thái
//      */
//     private function getAvailableActions($order, $payment)
//     {
//         $actions = [
//             'canConfirmPayment' => false,
//             'canRejectPayment' => false,
//             'canMarkAsPaid' => false,
//             'canMarkAsShipped' => false,
//             'canMarkAsCompleted' => false,
//             'canCancel' => false,
//         ];

//         $orderStatus = $order->status->value;
//         $paymentStatus = $payment ? $payment->status->value : null;
//         $paymentMethod = $payment ? $payment->payment_method->value : null;

//         // Logic cho COD - cần xác nhận thủ công khi giao hàng thành công
//         if ($paymentMethod === 'cod') {
//             if ($orderStatus === 'pending' && $paymentStatus === 'pending') {
//                 // COD đang chờ xử lý - có thể xác nhận đơn để chuyển sang đang giao
//                 $actions['canMarkAsPaid'] = true;
//                 $actions['canCancel'] = true;
//             } elseif ($orderStatus === 'paid' || $orderStatus === 'shipped') {
//                 // Đang giao hàng - chỉ có thể đánh dấu hoàn thành hoặc hủy
//                 $actions['canMarkAsShipped'] = ($orderStatus === 'paid');
//                 $actions['canMarkAsCompleted'] = ($orderStatus === 'shipped');
//                 $actions['canConfirmPayment'] = ($orderStatus === 'shipped' && $paymentStatus === 'pending');
//             }
//         }

//         // Logic cho Stripe - thanh toán online tự động
//         elseif (in_array($paymentMethod, ['card', 'stripe'])) {
//             if ($paymentStatus === 'success' || $paymentStatus === 'paid') {
//                 // Stripe đã thanh toán thành công
//                 if ($orderStatus === 'pending') {
//                     $actions['canMarkAsPaid'] = true;
//                 } elseif ($orderStatus === 'paid') {
//                     $actions['canMarkAsShipped'] = true;
//                 } elseif ($orderStatus === 'shipped') {
//                     $actions['canMarkAsCompleted'] = true;
//                 }
//             } elseif ($paymentStatus === 'pending' || $paymentStatus === 'processing') {
//                 // Stripe đang xử lý - có thể hủy
//                 $actions['canCancel'] = true;
//             }
//         }

//         // Logic cho Bank Transfer - cần xác nhận thủ công
//         elseif ($paymentMethod === 'bank') {
//             if ($paymentStatus === 'pending' && $orderStatus === 'pending') {
//                 // Chờ xác nhận chuyển khoản
//                 $actions['canConfirmPayment'] = true;
//                 $actions['canRejectPayment'] = true;
//             } elseif ($paymentStatus === 'success' || $paymentStatus === 'paid') {
//                 // Đã xác nhận chuyển khoản
//                 if ($orderStatus === 'pending') {
//                     $actions['canMarkAsPaid'] = true;
//                 } elseif ($orderStatus === 'paid') {
//                     $actions['canMarkAsShipped'] = true;
//                 } elseif ($orderStatus === 'shipped') {
//                     $actions['canMarkAsCompleted'] = true;
//                 }
//             }
//         }

//         // Có thể hủy đơn khi ở trạng thái pending hoặc paid (chưa giao)
//         if (in_array($orderStatus, ['pending', 'paid'])) {
//             $actions['canCancel'] = true;
//         }

//         return $actions;
//     }

//     /**
//      * Xác nhận thanh toán (COD hoặc Bank Transfer)
//      */
//     public function confirmPayment(Request $request, int $id)
//     {
//         $order = $this->orderRepo->find($id, ['payments', 'orderItems', 'user']);

//         if (!$order) {
//             return redirect()->route('admin.orders.index')
//                 ->with('error', 'Không tìm thấy đơn hàng');
//         }

//         $payment = $order->payments()->latest('created_at')->first();

//         if (!$payment) {
//             return redirect()->route('admin.orders.show', $id)
//                 ->with('error', 'Không tìm thấy thông tin thanh toán');
//         }

//         DB::beginTransaction();
//         try {
//             $verifier = auth()->user();
//             $paymentMethod = $payment->payment_method->value;

//             // Cập nhật payment
//             $payment->update([
//                 'status' => PaymentStatus::Success,
//                 'is_verified' => true,
//                 'verified_by' => $verifier->id,
//                 'verified_at' => now(),
//                 'verification_note' => $request->input('note'),
//                 'transaction_id' => $request->input('transaction_id'),
//             ]);

//             // Cập nhật order status
//             $newOrderStatus = OrderStatus::Paid;

//             // Nếu là COD và đang ở trạng thái shipped, đánh dấu completed
//             if ($paymentMethod === 'cod' && $order->status->value === 'shipped') {
//                 $newOrderStatus = OrderStatus::Completed;
//             }

//             $order->update([
//                 'status' => $newOrderStatus,
//                 'paid_at' => now(),
//             ]);

//             // Gửi email xác nhận
//             if ($order->user && $order->user->email) {
//                 try {
//                     Mail::to($order->user->email)->send(new PaymentConfirmed($order));
//                 } catch (\Exception $e) {
//                     Log::error('Send payment confirmation email error: ' . $e->getMessage());
//                 }
//             }

//             DB::commit();

//             return redirect()->route('admin.orders.show', $id)
//                 ->with('success', '✅ Đã xác nhận thanh toán thành công!');

//         } catch (\Exception $e) {
//             DB::rollBack();

//             Log::error('Confirm payment error: ' . $e->getMessage(), [
//                 'order_id' => $id,
//                 'payment_id' => $payment->id,
//                 'user_id' => auth()->id()
//             ]);

//             return redirect()->back()
//                 ->with('error', '❌ Có lỗi xảy ra: ' . $e->getMessage());
//         }
//     }

//     /**
//      * Từ chối thanh toán
//      */
//     public function rejectPayment(Request $request, $id)
//     {
//         $request->validate([
//             'reason' => 'required|string|max:500',
//         ]);

//         try {
//             DB::transaction(function () use ($id, $request) {
//                 $order = Order::with(['payments', 'orderItems'])->findOrFail($id);
//                 $payment = $order->payments()->latest()->first();

//                 if (!$payment) {
//                     throw new \Exception('Không tìm thấy thông tin thanh toán');
//                 }

//                 $payment->update([
//                     'status' => PaymentStatus::Failed,
//                     'verification_note' => $request->reason,
//                 ]);

//                 $order->update([
//                     'status' => OrderStatus::Cancelled,
//                     'cancelled_at' => now(),
//                     'admin_note' => 'Thanh toán bị từ chối: ' . $request->reason,
//                 ]);

//                 // Hoàn lại stock
//                 foreach ($order->orderItems as $item) {
//                     if ($item->variant_id) {
//                         $item->variant->stockItems()->increment('quantity', $item->quantity);
//                     } else {
//                         $item->product->stockItems()->increment('quantity', $item->quantity);
//                     }
//                 }
//             });

//             return back()->with('success', 'Đã từ chối thanh toán và hủy đơn hàng.');
//         } catch (\Exception $e) {
//             return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
//         }
//     }

//     /**
//      * Cập nhật trạng thái nhanh
//      */
//     public function updateStatus(Request $request, $id)
//     {
//         $validated = $request->validate([
//             'status' => 'required|in:' . implode(',', OrderStatus::values()),
//         ]);

//         try {
//             DB::beginTransaction();

//             $order = $this->orderRepo->find($id, ['user', 'payments']);
//             if (!$order) {
//                 throw new \Exception('Không tìm thấy đơn hàng');
//             }

//             $oldStatus = $order->status;
//             $newStatus = OrderStatus::from($validated['status']);

//             // Validate status transition
//             if (!$this->canTransitionTo($oldStatus, $newStatus)) {
//                 throw new \Exception('Không thể chuyển từ trạng thái ' . $oldStatus->label() . ' sang ' . $newStatus->label());
//             }

//             $updateData = ['status' => $newStatus];

//             // Update timestamp
//             if ($newStatus === OrderStatus::Paid && !$order->paid_at) {
//                 $updateData['paid_at'] = now();
//             } elseif ($newStatus === OrderStatus::Shipped && !$order->shipped_at) {
//                 $updateData['shipped_at'] = now();
//             } elseif ($newStatus === OrderStatus::Completed && !$order->completed_at) {
//                 $updateData['completed_at'] = now();
//             }

//             $this->orderRepo->update($id, $updateData);

//             // Gửi email
//             if ($order->user && $order->user->email) {
//                 try {
//                     Mail::to($order->user->email)->send(new OrderStatusUpdated($order, $newStatus));
//                 } catch (\Exception $e) {
//                     Log::error('Send email error: ' . $e->getMessage());
//                 }
//             }

//             DB::commit();
//             return back()->with('success', 'Cập nhật trạng thái thành công và đã gửi email thông báo!');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
//         }
//     }

//     /**
//      * Check if can transition between statuses
//      */
//     private function canTransitionTo(OrderStatus $from, OrderStatus $to): bool
//     {
//         $allowedTransitions = [
//             OrderStatus::Pending->value => [OrderStatus::Paid->value, OrderStatus::Cancelled->value],
//             OrderStatus::Paid->value => [OrderStatus::Shipped->value, OrderStatus::Cancelled->value],
//             OrderStatus::Shipped->value => [OrderStatus::Completed->value],
//             OrderStatus::Completed->value => [],
//             OrderStatus::Cancelled->value => [],
//         ];

//         return in_array($to->value, $allowedTransitions[$from->value] ?? []);
//     }

//     /**
//      * Hủy đơn hàng
//      */
//     public function cancel(Request $request, $id)
//     {
//         $request->validate(['reason' => 'required|string|max:500']);

//         try {
//             DB::beginTransaction();

//             $order = Order::with(['orderItems', 'payments'])->findOrFail($id);

//             if (!in_array($order->status, [OrderStatus::Pending, OrderStatus::Paid])) {
//                 throw new \Exception('Không thể hủy đơn hàng ở trạng thái này!');
//             }

//             // Cập nhật payment nếu có
//             if ($payment = $order->payments->first()) {
//                 $payment->update(['status' => PaymentStatus::Failed]);
//             }

//             $order->update([
//                 'status' => OrderStatus::Cancelled,
//                 'cancelled_at' => now(),
//                 'admin_note' => $request->reason,
//             ]);

//             // Trả lại stock
//             foreach ($order->orderItems as $item) {
//                 if ($item->variant_id) {
//                     $item->variant->stockItems()->increment('quantity', $item->quantity);
//                 } else {
//                     $item->product->stockItems()->increment('quantity', $item->quantity);
//                 }
//             }

//             DB::commit();
//             return back()->with('success', 'Đã hủy đơn hàng thành công.');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
//         }
//     }
//     /**
//      * Form chỉnh sửa
//      */
//     public function edit($id)
//     {
//         $order = $this->orderRepo->find($id, ['user', 'orderItems.product', 'orderItems.variant', 'shippingAddress', 'payments']);

//         if (!$order) {
//             return redirect()->route('admin.orders.index')->with('error', 'Không tìm thấy đơn hàng');
//         }

//         $statuses = OrderStatus::cases();

//         return view('admin.orders.edit', compact('order', 'statuses'));
//     }

//     /**
//      * Cập nhật đơn hàng
//      */
//     public function update(Request $request, $id)
//     {
//         $validated = $request->validate([
//             'status' => 'required|in:' . implode(',', OrderStatus::values()),
//             'admin_note' => 'nullable|string|max:500',
//         ]);

//         try {
//             DB::beginTransaction();

//             $order = $this->orderRepo->find($id, ['orderItems', 'user']);

//             if (!$order) {
//                 throw new \Exception('Không tìm thấy đơn hàng');
//             }

//             $oldStatus = $order->status;
//             $newStatus = OrderStatus::from($validated['status']);

//             // Validate trạng thái chuyển đổi
//             if (!$this->canTransitionTo($oldStatus, $newStatus)) {
//                 throw new \Exception('Không thể chuyển từ trạng thái ' . $oldStatus->label() . ' sang ' . $newStatus->label());
//             }

//             $updateData = [
//                 'status' => $newStatus,
//                 'admin_note' => $validated['admin_note'] ?? $order->admin_note,
//             ];

//             // Cập nhật timestamp dựa trên trạng thái
//             if ($newStatus === OrderStatus::Paid && $oldStatus !== OrderStatus::Paid) {
//                 $updateData['paid_at'] = now();
//             } elseif ($newStatus === OrderStatus::Shipped && $oldStatus !== OrderStatus::Shipped) {
//                 $updateData['shipped_at'] = now();
//             } elseif ($newStatus === OrderStatus::Completed && $oldStatus !== OrderStatus::Completed) {
//                 $updateData['completed_at'] = now();
//             }

//             $this->orderRepo->update($id, $updateData);

//             // Gửi email nếu trạng thái thay đổi
//             if ($oldStatus !== $newStatus && $order->user && $order->user->email) {
//                 try {
//                     Mail::to($order->user->email)->send(new OrderStatusUpdated($order, $newStatus));
//                 } catch (\Exception $e) {
//                     Log::error('Send email error: ' . $e->getMessage());
//                 }
//             }

//             DB::commit();
//             return redirect()->route('admin.orders.show', $id)->with('success', 'Cập nhật đơn hàng thành công');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             Log::error('Update Order Error: ' . $e->getMessage());
//             return back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
//         }
//     }
//     /**
//      * Xem chi tiết khách hàng từ đơn hàng
//      */
//     public function customerDetails($orderId)
//     {
//         $order = Order::with([
//             'user.addresses',
//             'user.orders' => function ($query) {
//                 $query->latest()->limit(10);
//             },
//             'shippingAddress'
//         ])->findOrFail($orderId);

//         $customer = $order->user;

//         if (!$customer) {
//             return back()->with('error', 'Không tìm thấy thông tin khách hàng');
//         }

//         // Thống kê khách hàng
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

//     /**
//      * Danh sách thanh toán cần xác nhận
//      */
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

//     /**
//      * Xóa mềm
//      */
//     public function destroy($id)
//     {
//         try {
//             $order = $this->orderRepo->find($id);

//             if (!$order) {
//                 return back()->with('error', 'Không tìm thấy đơn hàng');
//             }

//             if (!in_array($order->status, [OrderStatus::Cancelled, OrderStatus::Completed])) {
//                 return back()->with('error', 'Chỉ có thể xóa đơn hàng đã hủy hoặc hoàn thành');
//             }

//             $this->orderRepo->delete($id);
//             return redirect()->route('admin.orders.index')->with('success', 'Đã chuyển đơn hàng vào thùng rác');
//         } catch (\Exception $e) {
//             return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
//         }
//     }

//     /**
//      * Đơn hàng đã xóa
//      */
//     public function trashed()
//     {
//         $orders = Order::onlyTrashed()
//             ->with(['user', 'payments'])
//             ->latest('deleted_at')
//             ->paginate(15);

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

//     /**
//      * Khôi phục
//      */
//     public function restore($id)
//     {
//         $order = Order::onlyTrashed()->findOrFail($id);
//         $order->restore();

//         return redirect()->route('admin.orders.trashed')->with('success', 'Khôi phục đơn hàng thành công');
//     }

//     /**
//      * Xóa vĩnh viễn
//      */
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

//     /**
//      * In hóa đơn
//      */
//     public function invoice($id)
//     {
//         $order = $this->orderRepo->find($id, ['user', 'orderItems.product', 'orderItems.variant', 'shippingAddress', 'payments']);
//         return view('admin.orders.invoice', compact('order'));
//     }

//     /**
//      * Export Excel/CSV
//      */
//     public function export(Request $request)
//     {
//         try {
//             $query = Order::with(['user', 'payments', 'orderItems', 'shippingAddress']);

//             if ($status = $request->status)
//                 $query->where('status', $status);
//             if ($from = $request->from)
//                 $query->whereDate('created_at', '>=', $from);
//             if ($to = $request->to)
//                 $query->whereDate('created_at', '<=', $to);

//             $orders = $query->get();
//             $filename = 'orders_' . now()->format('Ymd_His') . '.csv';
//             $handle = fopen('php://temp', 'r+');

//             fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

//             fputcsv($handle, ['=== DANH SÁCH ĐƠN HÀNG ===']);
//             fputcsv($handle, ['Ngày xuất: ' . now()->format('d/m/Y H:i')]);
//             fputcsv($handle, []);

//             fputcsv($handle, [
//                 'Mã đơn',
//                 'Khách hàng',
//                 'Email',
//                 'Điện thoại',
//                 'Ngày đặt',
//                 'Tổng tiền',
//                 'Phí ship',
//                 'Trạng thái đơn',
//                 'Phương thức TT',
//                 'Trạng thái TT',
//                 'Ngày giao'
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
//                     $order->shipped_at ? $order->shipped_at->format('d/m/Y') : 'Chưa giao',
//                 ]);
//             }

//             rewind($handle);
//             $csv = stream_get_contents($handle);
//             fclose($handle);

//             return response($csv, 200, [
//                 'Content-Type' => 'text/csv; charset=UTF-8',
//                 'Content-Disposition' => 'attachment; filename="' . $filename . '"',
//             ]);
//         } catch (\Exception $e) {
//             return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
//         }
//     }

//     private function generateOrderNumber(): string
//     {
//         return 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));
//     }
//     // ... (các method khác giữ nguyên: edit, update, customerDetails, pendingPayments, destroy, trashed, restore, forceDelete, invoice, export)
// }





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
use App\Jobs\SendOrderMailJob; // ✅ Sử dụng Job có sẵn

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
    // public function show($id)
    // {
    //     $order = $this->orderRepo->find($id, ['user', 'orderItems.product', 'orderItems.variant', 'shippingAddress', 'payments']);

    //     if (!$order) {
    //         return redirect()->route('admin.orders.index')->with('error', 'Không tìm thấy đơn hàng');
    //     }

    //     $payment = $order->payments->first();

    //     // Xác định các actions có thể thực hiện
    //     $actions = $this->getAvailableActions($order, $payment);

    //     return view('admin.orders.show', compact('order', 'payment', 'actions'));
    // }
    /**
     * Chi tiết đơn hàng với timeline và actions động
     */
    public function show($id)
    {
        $order = $this->orderRepo->find($id, [
            'user',
            'orderItems.product',
            'orderItems.variant',
            'shippingAddress',
            'payments'
        ]);

        if (!$order) {
            return redirect()->route('admin.orders.index')
                ->with('error', 'Không tìm thấy đơn hàng');
        }

        $payment = $order->payments->first();

        // Xác định actions và timeline
        $actions = $this->getAvailableActions($order, $payment);
        $timelineSteps = $this->getTimelineSteps($order, $payment);

        return view('admin.orders.show', compact('order', 'payment', 'actions', 'timelineSteps'));
    }
    /**
     * Timeline steps theo từng phương thức thanh toán
     */
    private function getTimelineSteps($order, $payment)
    {
        $paymentMethod = $payment ? $payment->payment_method->value : null;
        $orderStatus = $order->status->value;
        $paymentStatus = $payment ? $payment->status->value : null;

        // STRIPE - Thanh toán trước
        if (in_array($paymentMethod, ['card', 'stripe'])) {
            return [
                [
                    'key' => 'payment_pending',
                    'icon' => 'credit-card',
                    'label' => 'Chờ thanh toán',
                    'time' => $order->created_at,
                    'status' => ($paymentStatus === 'pending' || $paymentStatus === 'processing') ? 'active' : 'completed',
                    'description' => 'Khách hàng thanh toán qua Stripe'
                ],
                [
                    'key' => 'payment_confirmed',
                    'icon' => 'check-circle',
                    'label' => 'Đã thanh toán',
                    'time' => $payment?->paid_at,
                    'status' => ($paymentStatus === 'success' || $paymentStatus === 'paid') ? 'completed' : 'pending',
                    'description' => 'Thanh toán thành công'
                ],
                [
                    'key' => 'preparing',
                    'icon' => 'box',
                    'label' => 'Chuẩn bị hàng',
                    'time' => $order->paid_at,
                    'status' => $orderStatus === 'paid' ? 'active' : ($order->paid_at ? 'completed' : 'pending'),
                    'description' => 'Đang đóng gói sản phẩm'
                ],
                [
                    'key' => 'shipping',
                    'icon' => 'truck',
                    'label' => 'Đang giao hàng',
                    'time' => $order->shipped_at,
                    'status' => $orderStatus === 'shipped' ? 'active' : ($order->shipped_at ? 'completed' : 'pending'),
                    'description' => 'Đơn vị vận chuyển đang giao'
                ],
                [
                    'key' => 'completed',
                    'icon' => 'star',
                    'label' => 'Hoàn thành',
                    'time' => $order->completed_at,
                    'status' => $orderStatus === 'completed' ? 'completed' : 'pending',
                    'description' => 'Giao hàng thành công'
                ]
            ];
        }

        // COD - Thanh toán khi nhận hàng
        if ($paymentMethod === 'cod') {
            return [
                [
                    'key' => 'order_placed',
                    'icon' => 'shopping-cart',
                    'label' => 'Đặt hàng',
                    'time' => $order->created_at,
                    'status' => 'completed',
                    'description' => 'Đơn hàng đã được tạo'
                ],
                [
                    'key' => 'confirmed',
                    'icon' => 'check-circle',
                    'label' => 'Xác nhận đơn',
                    'time' => $order->paid_at ?: ($orderStatus !== 'pending' ? $order->updated_at : null),
                    'status' => $orderStatus === 'pending' ? 'active' : 'completed',
                    'description' => 'Shop xác nhận và chuẩn bị hàng'
                ],
                [
                    'key' => 'shipping',
                    'icon' => 'truck',
                    'label' => 'Đang giao hàng',
                    'time' => $order->shipped_at,
                    'status' => $orderStatus === 'shipped' ? 'active' : ($order->shipped_at ? 'completed' : 'pending'),
                    'description' => 'Shipper đang giao hàng'
                ],
                [
                    'key' => 'payment_on_delivery',
                    'icon' => 'money-bill-wave',
                    'label' => 'Thanh toán COD',
                    'time' => $payment?->paid_at,
                    'status' => $orderStatus === 'shipped' && $paymentStatus === 'pending' ? 'active' : ($paymentStatus === 'success' ? 'completed' : 'pending'),
                    'description' => 'Khách thanh toán khi nhận hàng'
                ],
                [
                    'key' => 'completed',
                    'icon' => 'star',
                    'label' => 'Hoàn thành',
                    'time' => $order->completed_at,
                    'status' => $orderStatus === 'completed' ? 'completed' : 'pending',
                    'description' => 'Đơn hàng hoàn tất'
                ]
            ];
        }

        // BANK TRANSFER
        if ($paymentMethod === 'bank') {
            return [
                [
                    'key' => 'order_placed',
                    'icon' => 'shopping-cart',
                    'label' => 'Đặt hàng',
                    'time' => $order->created_at,
                    'status' => 'completed',
                    'description' => 'Chờ chuyển khoản'
                ],
                [
                    'key' => 'waiting_payment',
                    'icon' => 'university',
                    'label' => 'Chờ chuyển khoản',
                    'time' => null,
                    'status' => $paymentStatus === 'pending' && $orderStatus === 'pending' ? 'active' : 'completed',
                    'description' => 'Khách hàng chuyển khoản'
                ],
                [
                    'key' => 'payment_verified',
                    'icon' => 'check-circle',
                    'label' => 'Xác nhận thanh toán',
                    'time' => $payment?->verified_at,
                    'status' => $payment?->is_verified ? 'completed' : 'pending',
                    'description' => 'Shop xác nhận đã nhận tiền'
                ],
                [
                    'key' => 'preparing',
                    'icon' => 'box',
                    'label' => 'Chuẩn bị hàng',
                    'time' => $order->paid_at,
                    'status' => $orderStatus === 'paid' ? 'active' : ($order->paid_at ? 'completed' : 'pending'),
                    'description' => 'Đang đóng gói'
                ],
                [
                    'key' => 'shipping',
                    'icon' => 'truck',
                    'label' => 'Đang giao',
                    'time' => $order->shipped_at,
                    'status' => $orderStatus === 'shipped' ? 'active' : ($order->shipped_at ? 'completed' : 'pending'),
                    'description' => 'Đang vận chuyển'
                ],
                [
                    'key' => 'completed',
                    'icon' => 'star',
                    'label' => 'Hoàn thành',
                    'time' => $order->completed_at,
                    'status' => $orderStatus === 'completed' ? 'completed' : 'pending',
                    'description' => 'Giao thành công'
                ]
            ];
        }

        // Default timeline
        return [
            [
                'key' => 'pending',
                'icon' => 'clock',
                'label' => 'Chờ xử lý',
                'time' => $order->created_at,
                'status' => $orderStatus === 'pending' ? 'active' : 'completed',
                'description' => ''
            ],
            [
                'key' => 'paid',
                'icon' => 'credit-card',
                'label' => 'Đã xác nhận',
                'time' => $order->paid_at,
                'status' => $orderStatus === 'paid' ? 'active' : ($order->paid_at ? 'completed' : 'pending'),
                'description' => ''
            ],
            [
                'key' => 'shipped',
                'icon' => 'truck',
                'label' => 'Đang giao',
                'time' => $order->shipped_at,
                'status' => $orderStatus === 'shipped' ? 'active' : ($order->shipped_at ? 'completed' : 'pending'),
                'description' => ''
            ],
            [
                'key' => 'completed',
                'icon' => 'check-circle',
                'label' => 'Hoàn thành',
                'time' => $order->completed_at,
                'status' => $orderStatus === 'completed' ? 'completed' : 'pending',
                'description' => ''
            ]
        ];
    }

    /**
     * Xác định actions khả dụng dựa trên trạng thái
     */
    // private function getAvailableActions($order, $payment)
    // {
    //     $actions = [
    //         'canConfirmPayment' => false,
    //         'canRejectPayment' => false,
    //         'canMarkAsPaid' => false,
    //         'canMarkAsShipped' => false,
    //         'canMarkAsCompleted' => false,
    //         'canCancel' => false,
    //     ];

    //     $orderStatus = $order->status->value;
    //     $paymentStatus = $payment ? $payment->status->value : null;
    //     $paymentMethod = $payment ? $payment->payment_method->value : null;

    //     // Logic cho COD - cần xác nhận thủ công khi giao hàng thành công
    //     if ($paymentMethod === 'cod') {
    //         if ($orderStatus === 'pending' && $paymentStatus === 'pending') {
    //             // COD đang chờ xử lý - có thể xác nhận đơn để chuyển sang đang giao
    //             $actions['canMarkAsPaid'] = true;
    //             $actions['canCancel'] = true;
    //         } elseif ($orderStatus === 'paid' || $orderStatus === 'shipped') {
    //             // Đang giao hàng - chỉ có thể đánh dấu hoàn thành hoặc hủy
    //             $actions['canMarkAsShipped'] = ($orderStatus === 'paid');
    //             $actions['canMarkAsCompleted'] = ($orderStatus === 'shipped');
    //             $actions['canConfirmPayment'] = ($orderStatus === 'shipped' && $paymentStatus === 'pending');
    //         }
    //     }

    //     // Logic cho Stripe - thanh toán online tự động
    //     elseif (in_array($paymentMethod, ['card', 'stripe'])) {
    //         if ($paymentStatus === 'success' || $paymentStatus === 'paid') {
    //             // Stripe đã thanh toán thành công
    //             if ($orderStatus === 'pending') {
    //                 $actions['canMarkAsPaid'] = true;
    //             } elseif ($orderStatus === 'paid') {
    //                 $actions['canMarkAsShipped'] = true;
    //             } elseif ($orderStatus === 'shipped') {
    //                 $actions['canMarkAsCompleted'] = true;
    //             }
    //         } elseif ($paymentStatus === 'pending' || $paymentStatus === 'processing') {
    //             // Stripe đang xử lý - có thể hủy
    //             $actions['canCancel'] = true;
    //         }
    //     }

    //     // Logic cho Bank Transfer - cần xác nhận thủ công
    //     elseif ($paymentMethod === 'bank') {
    //         if ($paymentStatus === 'pending' && $orderStatus === 'pending') {
    //             // Chờ xác nhận chuyển khoản
    //             $actions['canConfirmPayment'] = true;
    //             $actions['canRejectPayment'] = true;
    //         } elseif ($paymentStatus === 'success' || $paymentStatus === 'paid') {
    //             // Đã xác nhận chuyển khoản
    //             if ($orderStatus === 'pending') {
    //                 $actions['canMarkAsPaid'] = true;
    //             } elseif ($orderStatus === 'paid') {
    //                 $actions['canMarkAsShipped'] = true;
    //             } elseif ($orderStatus === 'shipped') {
    //                 $actions['canMarkAsCompleted'] = true;
    //             }
    //         }
    //     }

    //     // Có thể hủy đơn khi ở trạng thái pending hoặc paid (chưa giao)
    //     if (in_array($orderStatus, ['pending', 'paid'])) {
    //         $actions['canCancel'] = true;
    //     }

    //     return $actions;
    // }

    /**
     * Actions theo flow TikTok Shop - TƯƠNG THÍCH VỚI ENUM HIỆN TẠI
     */
    private function getAvailableActions($order, $payment)
    {
        $actions = [
            'canConfirmOrder' => false,
            'canConfirmPayment' => false,
            'canRejectPayment' => false,
            'canMarkAsShipped' => false,
            'canMarkAsCompleted' => false,
            'canCancel' => false,
            'showShippingCode' => false,
        ];

        if (!$payment) {
            return $actions;
        }

        $orderStatus = $order->status->value;
        $paymentStatus = $payment->status->value;
        $paymentMethod = $payment->payment_method->value;

        // STRIPE FLOW
        if (in_array($paymentMethod, ['card', 'stripe'])) {
            if ($paymentStatus === 'success' || $paymentStatus === 'paid') {
                if ($orderStatus === 'pending') {
                    $actions['canConfirmOrder'] = true;
                }
                if ($orderStatus === 'paid') {
                    $actions['canMarkAsShipped'] = true;
                    $actions['canCancel'] = true;
                }
                if ($orderStatus === 'shipped') {
                    $actions['canMarkAsCompleted'] = true;
                    $actions['showShippingCode'] = true;
                }
            } else {
                $actions['canCancel'] = true;
            }
        }

        // COD FLOW
        elseif ($paymentMethod === 'cod') {
            if ($orderStatus === 'pending') {
                $actions['canConfirmOrder'] = true;
                $actions['canCancel'] = true;
            }
            if ($orderStatus === 'paid') {
                $actions['canMarkAsShipped'] = true;
                $actions['canCancel'] = true;
            }
            if ($orderStatus === 'shipped') {
                $actions['canConfirmPayment'] = true;
                $actions['showShippingCode'] = true;
            }
        }

        // BANK TRANSFER FLOW
        elseif ($paymentMethod === 'bank') {
            if ($orderStatus === 'pending' && $paymentStatus === 'pending') {
                $actions['canConfirmPayment'] = true;
                $actions['canRejectPayment'] = true;
            }
            if ($paymentStatus === 'success' && $orderStatus === 'pending') {
                $actions['canConfirmOrder'] = true;
            }
            if ($orderStatus === 'paid') {
                $actions['canMarkAsShipped'] = true;
                $actions['canCancel'] = true;
            }
            if ($orderStatus === 'shipped') {
                $actions['canMarkAsCompleted'] = true;
                $actions['showShippingCode'] = true;
            }
        }

        // Không thể hủy khi đã hoàn thành hoặc đã hủy
        if (in_array($orderStatus, ['completed', 'cancelled'])) {
            $actions = array_fill_keys(array_keys($actions), false);
        }

        return $actions;
    }



    // /**
    //  * Xác nhận thanh toán (COD hoặc Bank Transfer)
    //  */
    // public function confirmPayment(Request $request, int $id)
    // {
    //     $order = $this->orderRepo->find($id, ['payments', 'orderItems', 'user']);

    //     if (!$order) {
    //         return redirect()->route('admin.orders.index')
    //             ->with('error', 'Không tìm thấy đơn hàng');
    //     }

    //     $payment = $order->payments()->latest('created_at')->first();

    //     if (!$payment) {
    //         return redirect()->route('admin.orders.show', $id)
    //             ->with('error', 'Không tìm thấy thông tin thanh toán');
    //     }

    //     DB::beginTransaction();
    //     try {
    //         $verifier = auth()->user();
    //         $paymentMethod = $payment->payment_method->value;

    //         // Cập nhật payment
    //         $payment->update([
    //             'status' => PaymentStatus::Success,
    //             'is_verified' => true,
    //             'verified_by' => $verifier->id,
    //             'verified_at' => now(),
    //             'verification_note' => $request->input('note'),
    //             'transaction_id' => $request->input('transaction_id'),
    //         ]);

    //         // Cập nhật order status
    //         $newOrderStatus = OrderStatus::Paid;

    //         // Nếu là COD và đang ở trạng thái shipped, đánh dấu completed
    //         if ($paymentMethod === 'cod' && $order->status->value === 'shipped') {
    //             $newOrderStatus = OrderStatus::Completed;
    //         }

    //         $order->update([
    //             'status' => $newOrderStatus,
    //             'paid_at' => now(),
    //         ]);

    //         // ✅ Gửi email xác nhận thanh toán qua Job
    //         if ($order->user && $order->user->email) {
    //             try {
    //                 SendOrderMailJob::dispatch($order, 'order-paid')
    //                     ->delay(now()->addSeconds(2));
    //             } catch (\Exception $e) {
    //                 Log::error('Send payment confirmation email error: ' . $e->getMessage());
    //             }
    //         }

    //         DB::commit();

    //         return redirect()->route('admin.orders.show', $id)
    //             ->with('success', '✅ Đã xác nhận thanh toán thành công!');

    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         Log::error('Confirm payment error: ' . $e->getMessage(), [
    //             'order_id' => $id,
    //             'payment_id' => $payment->id,
    //             'user_id' => auth()->id()
    //         ]);

    //         return redirect()->back()
    //             ->with('error', '❌ Có lỗi xảy ra: ' . $e->getMessage());
    //     }
    // }

    /**
     * Xác nhận đơn hàng - Chuyển từ pending -> paid
     */
    public function confirmOrder(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $order = $this->orderRepo->find($id, ['user', 'payments', 'orderItems']);
            if (!$order) {
                throw new \Exception('Không tìm thấy đơn hàng');
            }

            if ($order->status->value !== 'pending') {
                throw new \Exception('Chỉ có thể xác nhận đơn hàng ở trạng thái chờ xử lý');
            }

            // Cập nhật order sang trạng thái Paid (đã xác nhận, chuẩn bị hàng)
            $order->update([
                'status' => OrderStatus::Paid,
                'paid_at' => now(),
            ]);

            // Gửi email thông báo
            if ($order->user && $order->user->email) {
                try {
                    SendOrderMailJob::dispatch($order, 'order-confirmed')
                        ->delay(now()->addSeconds(2));
                } catch (\Exception $e) {
                    Log::error('Send order confirmation email error: ' . $e->getMessage());
                }
            }

            DB::commit();
            return back()->with('success', '✅ Đã xác nhận đơn hàng! Bắt đầu chuẩn bị hàng.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Confirm order error: ' . $e->getMessage());
            return back()->with('error', '❌ Có lỗi: ' . $e->getMessage());
        }
    }
    /**
     * Xác nhận thanh toán (COD hoặc Bank Transfer)
     */
    public function confirmPayment(Request $request, int $id)
    {
        $order = $this->orderRepo->find($id, ['payments', 'orderItems', 'user']);

        if (!$order) {
            return redirect()->route('admin.orders.index')
                ->with('error', 'Không tìm thấy đơn hàng');
        }

        $payment = $order->payments()->latest('created_at')->first();

        if (!$payment) {
            return redirect()->route('admin.orders.show', $id)
                ->with('error', 'Không tìm thấy thông tin thanh toán');
        }

        DB::beginTransaction();
        try {
            $verifier = auth()->user();
            $paymentMethod = $payment->payment_method->value;

            // Cập nhật payment
            $payment->update([
                'status' => PaymentStatus::Success,
                'is_verified' => true,
                'verified_by' => $verifier->id,
                'verified_at' => now(),
                'verification_note' => $request->input('note'),
                'transaction_id' => $request->input('transaction_id'),
            ]);

            // Cập nhật order status
            $newOrderStatus = OrderStatus::Paid;

            // Nếu là COD và đang ở trạng thái shipped, đánh dấu completed
            if ($paymentMethod === 'cod' && $order->status->value === 'shipped') {
                $newOrderStatus = OrderStatus::Completed;
            }

            $order->update([
                'status' => $newOrderStatus,
                'paid_at' => now(),
            ]);

            // Gửi email xác nhận thanh toán qua Job
            if ($order->user && $order->user->email) {
                try {
                    SendOrderMailJob::dispatch($order, 'order-paid')
                        ->delay(now()->addSeconds(2));
                } catch (\Exception $e) {
                    Log::error('Send payment confirmation email error: ' . $e->getMessage());
                }
            }

            DB::commit();

            return redirect()->route('admin.orders.show', $id)
                ->with('success', '✅ Đã xác nhận thanh toán thành công!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Confirm payment error: ' . $e->getMessage(), [
                'order_id' => $id,
                'payment_id' => $payment->id,
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('error', '❌ Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    /**
     * Từ chối thanh toán
     */
    // public function rejectPayment(Request $request, $id)
    // {
    //     $request->validate([
    //         'reason' => 'required|string|max:500',
    //     ]);

    //     try {
    //         DB::transaction(function () use ($id, $request) {
    //             $order = Order::with(['payments', 'orderItems', 'user'])->findOrFail($id);
    //             $payment = $order->payments()->latest()->first();

    //             if (!$payment) {
    //                 throw new \Exception('Không tìm thấy thông tin thanh toán');
    //             }

    //             $payment->update([
    //                 'status' => PaymentStatus::Failed,
    //                 'verification_note' => $request->reason,
    //             ]);

    //             $order->update([
    //                 'status' => OrderStatus::Cancelled,
    //                 'cancelled_at' => now(),
    //                 'admin_note' => 'Thanh toán bị từ chối: ' . $request->reason,
    //             ]);

    //             // Hoàn lại stock
    //             foreach ($order->orderItems as $item) {
    //                 if ($item->variant_id) {
    //                     $item->variant->stockItems()->increment('quantity', $item->quantity);
    //                 } else {
    //                     $item->product->stockItems()->increment('quantity', $item->quantity);
    //                 }
    //             }

    //             // ✅ Gửi email đơn hàng bị hủy
    //             if ($order->user && $order->user->email) {
    //                 try {
    //                     SendOrderMailJob::dispatch($order, 'order-cancelled')
    //                         ->delay(now()->addSeconds(2));
    //                 } catch (\Exception $e) {
    //                     Log::error('Send cancellation email error: ' . $e->getMessage());
    //                 }
    //             }
    //         });

    //         return back()->with('success', 'Đã từ chối thanh toán và hủy đơn hàng.');
    //     } catch (\Exception $e) {
    //         return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
    //     }
    // }
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
                $order = Order::with(['payments', 'orderItems', 'user'])->findOrFail($id);
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

                // Gửi email đơn hàng bị hủy
                if ($order->user && $order->user->email) {
                    try {
                        SendOrderMailJob::dispatch($order, 'order-cancelled')
                            ->delay(now()->addSeconds(2));
                    } catch (\Exception $e) {
                        Log::error('Send cancellation email error: ' . $e->getMessage());
                    }
                }
            });

            return back()->with('success', 'Đã từ chối thanh toán và hủy đơn hàng.');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    // /**
    //  * Cập nhật trạng thái nhanh
    //  */
    // public function updateStatus(Request $request, $id)
    // {
    //     $validated = $request->validate([
    //         'status' => 'required|in:' . implode(',', OrderStatus::values()),
    //     ]);

    //     try {
    //         DB::beginTransaction();

    //         $order = $this->orderRepo->find($id, ['user', 'payments']);
    //         if (!$order) {
    //             throw new \Exception('Không tìm thấy đơn hàng');
    //         }

    //         $oldStatus = $order->status;
    //         $newStatus = OrderStatus::from($validated['status']);

    //         // Validate status transition
    //         if (!$this->canTransitionTo($oldStatus, $newStatus)) {
    //             throw new \Exception('Không thể chuyển từ trạng thái ' . $oldStatus->label() . ' sang ' . $newStatus->label());
    //         }

    //         $updateData = ['status' => $newStatus];

    //         // Update timestamp
    //         if ($newStatus === OrderStatus::Paid && !$order->paid_at) {
    //             $updateData['paid_at'] = now();
    //         } elseif ($newStatus === OrderStatus::Shipped && !$order->shipped_at) {
    //             $updateData['shipped_at'] = now();
    //         } elseif ($newStatus === OrderStatus::Completed && !$order->completed_at) {
    //             $updateData['completed_at'] = now();
    //         }

    //         $this->orderRepo->update($id, $updateData);

    //         // ✅ Email sẽ được gửi tự động qua OrderObserver
    //         // Không cần gọi mail ở đây nữa

    //         DB::commit();
    //         return back()->with('success', 'Cập nhật trạng thái thành công và đã gửi email thông báo!');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
    //     }
    // }
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

            $updateData = ['status' => $newStatus];

            // Update timestamp
            if ($newStatus === OrderStatus::Paid && !$order->paid_at) {
                $updateData['paid_at'] = now();
            } elseif ($newStatus === OrderStatus::Shipped && !$order->shipped_at) {
                $updateData['shipped_at'] = now();
            } elseif ($newStatus === OrderStatus::Completed && !$order->completed_at) {
                $updateData['completed_at'] = now();
            }

            $this->orderRepo->update($id, $updateData);

            DB::commit();
            return back()->with('success', 'Cập nhật trạng thái thành công!');
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

    // /**
    //  * Hủy đơn hàng
    //  */
    // public function cancel(Request $request, $id)
    // {
    //     $request->validate(['reason' => 'required|string|max:500']);

    //     try {
    //         DB::beginTransaction();

    //         $order = Order::with(['orderItems', 'payments', 'user'])->findOrFail($id);

    //         if (!in_array($order->status, [OrderStatus::Pending, OrderStatus::Paid])) {
    //             throw new \Exception('Không thể hủy đơn hàng ở trạng thái này!');
    //         }

    //         // Cập nhật payment nếu có
    //         if ($payment = $order->payments->first()) {
    //             $payment->update(['status' => PaymentStatus::Failed]);
    //         }

    //         $order->update([
    //             'status' => OrderStatus::Cancelled,
    //             'cancelled_at' => now(),
    //             'admin_note' => $request->reason,
    //         ]);

    //         // Trả lại stock
    //         foreach ($order->orderItems as $item) {
    //             if ($item->variant_id) {
    //                 $item->variant->stockItems()->increment('quantity', $item->quantity);
    //             } else {
    //                 $item->product->stockItems()->increment('quantity', $item->quantity);
    //             }
    //         }

    //         // ✅ Email sẽ được gửi tự động qua OrderObserver

    //         DB::commit();
    //         return back()->with('success', 'Đã hủy đơn hàng thành công.');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
    //     }
    // }
    /**
     * Hủy đơn hàng
     */
    public function cancel(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:500']);

        try {
            DB::beginTransaction();

            $order = Order::with(['orderItems', 'payments', 'user'])->findOrFail($id);

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

            // ✅ Email sẽ được gửi tự động qua OrderObserver

            DB::commit();
            return redirect()->route('admin.orders.show', $id)->with('success', 'Cập nhật đơn hàng thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Order Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    // ... (các method khác giữ nguyên)

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
