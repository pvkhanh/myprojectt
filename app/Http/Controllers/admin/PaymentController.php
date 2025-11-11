<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Enums\PaymentStatus;
use App\Enums\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;
use App\Models\Order;
class PaymentController extends Controller
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private OrderRepositoryInterface $orderRepository
    ) {
    }

    /**
     * Hiển thị danh sách payments
     */
    public function index(Request $request)
    {
        $query = $this->paymentRepository->newQuery()
            ->with(['order.user', 'verifier'])
            // ->with(['order.user', 'verifier']);
            ->orderByDesc('updated_at'); // ✅ Đưa bản ghi vừa cập nhật lên đầu


        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by verification status
        if ($request->filled('verification')) {
            if ($request->verification === 'pending') {
                $query->where('requires_manual_verification', true)
                    ->where('is_verified', false)
                    ->where('status', PaymentStatus::Pending);
            } elseif ($request->verification === 'verified') {
                $query->where('is_verified', true);
            } elseif ($request->verification === 'auto') {
                $query->where('requires_manual_verification', false);
            }
        }

        // Filter by date range
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        // Search by transaction_id or order_number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                    ->orWhereHas('order', function ($q) use ($search) {
                        $q->where('order_number', 'like', "%{$search}%");
                    });
            });
        }

        // ✅ Ưu tiên highlight (nếu có truyền ID từ redirect)
        if ($request->filled('highlight')) {
            $query->orderByRaw("id = ? DESC", [$request->highlight]);
        }
        $payments = $query->latest()->paginate(20);

        // Statistics
        $stats = [
            'total' => $this->paymentRepository->count(),
            'pending' => $this->paymentRepository->newQuery()->where('status', PaymentStatus::Pending)->count(),
            'success' => $this->paymentRepository->newQuery()->where('status', PaymentStatus::Success)->count(),
            'failed' => $this->paymentRepository->newQuery()->where('status', PaymentStatus::Failed)->count(),
            'pending_verification' => $this->paymentRepository->newQuery()
                ->where('requires_manual_verification', true)
                ->where('is_verified', false)
                ->where('status', PaymentStatus::Pending)
                ->count(),
        ];

        return view('admin.payments.index', compact('payments', 'stats'));
    }

    /**
     * Hiển thị chi tiết payment
     */
    public function show(int $id)
    {
        $payment = $this->paymentRepository->find($id, [
            'order.user',
            'order.orderItems.product',
            'order.shippingAddress',
            'verifier'
        ]);

        if (!$payment) {
            return redirect()->route('admin.payments.index')
                ->with('error', 'Không tìm thấy giao dịch');
        }

        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Form xác nhận thanh toán thủ công
     */
    public function verifyForm(int $id)
    {
        $payment = $this->paymentRepository->find($id, ['order.user']);

        if (!$payment) {
            return redirect()->route('admin.payments.index')
                ->with('error', 'Không tìm thấy giao dịch');
        }

        if (!$payment->canBeVerified()) {
            return redirect()->route('admin.payments.show', $id)
                ->with('error', 'Giao dịch này không thể xác nhận');
        }

        return view('admin.payments.verify', compact('payment'));
    }


    public function verify(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        // Nếu không có 'action' trong request thì mặc định là 'approve'
        $action = $request->input('action', 'approve');

        // Bỏ validate strict, chuyển sang if-check để tránh lỗi validation khi action bị null
        if (!in_array($action, ['approve', 'reject'])) {
            return back()->with('error', 'Hành động không hợp lệ!');
        }

        DB::beginTransaction();
        try {
            $order = $payment->order;

            if ($action === 'approve') {
                // ✅ Xác nhận thanh toán thành công
                $payment->update([
                    'status' => \App\Enums\PaymentStatus::Success,
                    'verified_at' => now(),
                    'verified_by' => 1,
                    'verification_note' => $request->verification_note,
                    'is_verified' => true,
                ]);

                if ($order) {
                    $order->update([
                        'status' => \App\Enums\OrderStatus::Paid,
                        'paid_at' => now(),
                    ]);
                }

                $message = '✅ Thanh toán đã được xác nhận!';
            } else {
                // ❌ Từ chối thanh toán
                $payment->update([
                    'status' => \App\Enums\PaymentStatus::Failed,
                    'verified_at' => now(),
                    'verified_by' => 1,
                    'verification_note' => $request->verification_note,
                    'is_verified' => false,
                ]);

                if ($order) {
                    $order->update([
                        'status' => \App\Enums\OrderStatus::Cancelled,
                    ]);
                }

                $message = '🚫 Giao dịch đã bị từ chối!';
            }

            DB::commit();

            return redirect()
                ->route('admin.payments.index', ['highlight' => $payment->id])
                ->with('success', '✅ Giao dịch #' . $payment->transaction_id . ' đã được xác nhận!');


        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }


    /**
     * Cập nhật trạng thái payment
     */
    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', PaymentStatus::values()),
            'note' => 'nullable|string|max:500'
        ]);

        $payment = $this->paymentRepository->find($id, ['order']);

        if (!$payment) {
            return redirect()->route('admin.payments.index')
                ->with('error', 'Không tìm thấy giao dịch');
        }

        DB::beginTransaction();
        try {
            $this->paymentRepository->update($id, [
                'status' => $request->status,
                'verification_note' => $request->note
            ]);

            // Cập nhật order status nếu cần
            if ($request->status === PaymentStatus::Success->value) {
                $this->orderRepository->markAsPaid($payment->order_id);
            }

            DB::commit();

            return redirect()->route('admin.payments.show', $id)
                ->with('success', 'Đã cập nhật trạng thái giao dịch');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xóa payment (soft delete)
     */
    public function destroy(int $id)
    {
        $payment = $this->paymentRepository->find($id);

        if (!$payment) {
            return redirect()->route('admin.payments.index')
                ->with('error', 'Không tìm thấy giao dịch');
        }

        // Chỉ cho phép xóa payment pending hoặc failed
        if (!in_array($payment->status->value, ['pending', 'failed'])) {
            return redirect()->back()
                ->with('error', 'Không thể xóa giao dịch đã thành công');
        }

        $this->paymentRepository->delete($id);

        return redirect()->route('admin.payments.index')
            ->with('success', 'Đã xóa giao dịch');
    }

    /**
     * Danh sách payments cần xác nhận
     */
    public function pendingVerification()
    {
        $payments = $this->paymentRepository->newQuery()
            ->with(['order.user'])
            ->where('requires_manual_verification', true)
            ->where('is_verified', false)
            ->where('status', PaymentStatus::Pending)
            ->latest()
            ->paginate(20);

        return view('admin.payments.pending-verification', compact('payments'));
    }

    /**
     * Export payments report
     */
    public function export(Request $request)
    {
        // TODO: Implement export functionality
        return redirect()->back()
            ->with('info', 'Chức năng export đang được phát triển');
    }
}
