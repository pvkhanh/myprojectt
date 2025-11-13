<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Enums\NotificationType;
use App\Events\NotificationSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications
     */
    public function index(Request $request)
    {
        try {
            $query = Notification::with('user')->latest();

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($q) use ($search) {
                            $q->where('email', 'like', "%{$search}%")
                                ->orWhere('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        });
                });
            }

            // Type filter
            if ($request->filled('type')) {
                $query->ofType($request->type);
            }

            // Read status filter
            if ($request->filled('is_read')) {
                if ($request->is_read === '1') {
                    $query->read();
                } else {
                    $query->unread();
                }
            }

            // Date range filter
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // User filter
            if ($request->filled('user_id')) {
                $query->byUser($request->user_id);
            }

            // Expired filter
            if ($request->filled('show_expired') && $request->show_expired === '1') {
                $query->expired();
            } else {
                $query->notExpired();
            }

            // Sort
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $notifications = $query->paginate($request->per_page ?? 15);

            // Calculate stats
            $stats = [
                'total' => Notification::notExpired()->count(),
                'today' => Notification::whereDate('created_at', today())->count(),
                'unread' => Notification::unread()->notExpired()->count(),
                'read' => Notification::read()->notExpired()->count(),
                'expired' => Notification::expired()->count(),
                'by_type' => []
            ];

            // Stats by type
            foreach (NotificationType::cases() as $type) {
                $stats['by_type'][$type->value] = Notification::ofType($type)->notExpired()->count();
            }

            $types = NotificationType::cases();
            $users = User::where('is_active', true)
                ->select('id', 'email', 'first_name', 'last_name')
                ->orderBy('email')
                ->get();

            Log::info('Admin viewed notifications list', [
                'admin_id' => auth()->id(),
                'filters' => $request->except(['page', '_token'])
            ]);

            return view('admin.notifications.index', compact('notifications', 'stats', 'types', 'users'));

        } catch (Exception $e) {
            Log::error('Error loading notifications list', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'admin_id' => auth()->id()
            ]);

            return back()->with('error', 'Có lỗi xảy ra khi tải danh sách thông báo: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new notification
     */
    public function create()
    {
        try {
            $types = NotificationType::cases();
            $users = User::where('is_active', true)
                ->select('id', 'email', 'first_name', 'last_name')
                ->orderBy('email')
                ->get();

            Log::info('Admin accessed notification create form', [
                'admin_id' => auth()->id()
            ]);

            return view('admin.notifications.create', compact('types', 'users'));

        } catch (Exception $e) {
            Log::error('Error loading notification create form', [
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);

            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created notification
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'type' => 'required|in:' . implode(',', NotificationType::values()),
                'title' => 'required|string|max:255',
                'message' => 'required|string|max:2000',
                'variables' => 'nullable|json',
                'expires_at' => 'nullable|date|after:now',
                'user_ids' => 'required|array|min:1',
                'user_ids.*' => 'exists:users,id',
                'send_immediately' => 'boolean',
            ], [
                'type.required' => 'Vui lòng chọn loại thông báo',
                'title.required' => 'Vui lòng nhập tiêu đề',
                'title.max' => 'Tiêu đề không được quá 255 ký tự',
                'message.required' => 'Vui lòng nhập nội dung',
                'message.max' => 'Nội dung không được quá 2000 ký tự',
                'variables.json' => 'Biến động phải là JSON hợp lệ',
                'expires_at.after' => 'Thời gian hết hạn phải sau thời điểm hiện tại',
                'user_ids.required' => 'Vui lòng chọn ít nhất 1 người nhận',
                'user_ids.min' => 'Vui lòng chọn ít nhất 1 người nhận',
            ]);

            $variables = null;
            if ($request->filled('variables')) {
                try {
                    $variables = json_decode($request->variables, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new Exception('JSON không hợp lệ: ' . json_last_error_msg());
                    }
                } catch (Exception $e) {
                    Log::warning('Invalid JSON in notification variables', [
                        'error' => $e->getMessage(),
                        'variables' => $request->variables
                    ]);
                    return back()->withInput()->with('error', 'Biến động JSON không hợp lệ');
                }
            }

            DB::beginTransaction();

            $notifications = [];
            $successCount = 0;
            $failedCount = 0;

            foreach ($validated['user_ids'] as $userId) {
                try {
                    $notification = Notification::create([
                        'user_id' => $userId,
                        'type' => $validated['type'],
                        'title' => $validated['title'],
                        'message' => $validated['message'],
                        'variables' => $variables,
                        'expires_at' => $validated['expires_at'] ?? null,
                        'is_read' => false,
                    ]);

                    $notifications[] = $notification;
                    $successCount++;

                    // Broadcast real-time notification
                    if ($request->boolean('send_immediately')) {
                        try {
                            event(new NotificationSent($notification));
                        } catch (Exception $e) {
                            Log::error('Failed to broadcast notification', [
                                'notification_id' => $notification->id,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }

                } catch (Exception $e) {
                    $failedCount++;
                    Log::error('Failed to create notification for user', [
                        'user_id' => $userId,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            DB::commit();

            Log::info('Notifications created successfully', [
                'admin_id' => auth()->id(),
                'success_count' => $successCount,
                'failed_count' => $failedCount,
                'type' => $validated['type'],
                'title' => $validated['title']
            ]);

            $message = "Đã tạo {$successCount} thông báo thành công!";
            if ($failedCount > 0) {
                $message .= " ({$failedCount} thất bại)";
            }

            return redirect()->route('admin.notifications.index')->with('success', $message);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed for notification creation', [
                'errors' => $e->errors(),
                'admin_id' => auth()->id()
            ]);
            throw $e;

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error creating notifications', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'admin_id' => auth()->id(),
                'request_data' => $request->except(['_token'])
            ]);

            return back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified notification
     */
    public function show(Notification $notification)
    {
        try {
            $notification->load('user');

            Log::info('Admin viewed notification detail', [
                'admin_id' => auth()->id(),
                'notification_id' => $notification->id
            ]);

            return view('admin.notifications.show', compact('notification'));

        } catch (Exception $e) {
            Log::error('Error loading notification detail', [
                'error' => $e->getMessage(),
                'notification_id' => $notification->id ?? null,
                'admin_id' => auth()->id()
            ]);

            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified notification
     */
    public function edit(Notification $notification)
    {
        try {
            $types = NotificationType::cases();

            Log::info('Admin accessed notification edit form', [
                'admin_id' => auth()->id(),
                'notification_id' => $notification->id
            ]);

            return view('admin.notifications.edit', compact('notification', 'types'));

        } catch (Exception $e) {
            Log::error('Error loading notification edit form', [
                'error' => $e->getMessage(),
                'notification_id' => $notification->id ?? null,
                'admin_id' => auth()->id()
            ]);

            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified notification
     */
    public function update(Request $request, Notification $notification)
    {
        try {
            $validated = $request->validate([
                'type' => 'required|in:' . implode(',', NotificationType::values()),
                'title' => 'required|string|max:255',
                'message' => 'required|string|max:2000',
                'variables' => 'nullable|json',
                'expires_at' => 'nullable|date',
            ], [
                'type.required' => 'Vui lòng chọn loại thông báo',
                'title.required' => 'Vui lòng nhập tiêu đề',
                'message.required' => 'Vui lòng nhập nội dung',
            ]);

            $variables = null;
            if ($request->filled('variables')) {
                try {
                    $variables = json_decode($request->variables, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new Exception('JSON không hợp lệ');
                    }
                } catch (Exception $e) {
                    return back()->withInput()->with('error', 'Biến động JSON không hợp lệ');
                }
            }

            $oldData = $notification->toArray();

            $notification->update([
                'type' => $validated['type'],
                'title' => $validated['title'],
                'message' => $validated['message'],
                'variables' => $variables,
                'expires_at' => $validated['expires_at'] ?? null,
            ]);

            Log::info('Notification updated successfully', [
                'admin_id' => auth()->id(),
                'notification_id' => $notification->id,
                'old_data' => $oldData,
                'new_data' => $notification->fresh()->toArray()
            ]);

            return redirect()->route('admin.notifications.show', $notification)
                ->with('success', 'Cập nhật thông báo thành công!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed for notification update', [
                'errors' => $e->errors(),
                'notification_id' => $notification->id,
                'admin_id' => auth()->id()
            ]);
            throw $e;

        } catch (Exception $e) {
            Log::error('Error updating notification', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'notification_id' => $notification->id,
                'admin_id' => auth()->id()
            ]);

            return back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified notification
     */
    public function destroy(Notification $notification)
    {
        try {
            $notificationData = [
                'id' => $notification->id,
                'title' => $notification->title,
                'user_id' => $notification->user_id
            ];

            $notification->delete();

            Log::info('Notification deleted successfully', [
                'admin_id' => auth()->id(),
                'notification_data' => $notificationData
            ]);

            if (request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Đã xóa thông báo!']);
            }

            return redirect()->route('admin.notifications.index')
                ->with('success', 'Đã xóa thông báo!');

        } catch (Exception $e) {
            Log::error('Error deleting notification', [
                'error' => $e->getMessage(),
                'notification_id' => $notification->id ?? null,
                'admin_id' => auth()->id()
            ]);

            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }

            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Send multiple notifications immediately
     */
    public function bulkSend(Request $request)
    {
        try {
            $request->validate([
                'notification_ids' => 'required|array|min:1',
                'notification_ids.*' => 'exists:notifications,id',
            ]);

            $notifications = Notification::whereIn('id', $request->notification_ids)->get();

            if ($notifications->isEmpty()) {
                return back()->with('warning', 'Không tìm thấy thông báo nào!');
            }

            $successCount = 0;
            $failedCount = 0;

            foreach ($notifications as $notification) {
                try {
                    event(new NotificationSent($notification));
                    $successCount++;
                } catch (Exception $e) {
                    $failedCount++;
                    Log::error('Failed to send notification', [
                        'notification_id' => $notification->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info('Bulk notification send completed', [
                'admin_id' => auth()->id(),
                'success_count' => $successCount,
                'failed_count' => $failedCount,
                'notification_ids' => $request->notification_ids
            ]);

            $message = "Đã gửi {$successCount} thông báo!";
            if ($failedCount > 0) {
                $message .= " ({$failedCount} thất bại)";
            }

            return back()->with('success', $message);

        } catch (Exception $e) {
            Log::error('Error in bulk send notifications', [
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);

            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Delete multiple notifications
     */
    public function bulkDelete(Request $request)
    {
        try {
            $request->validate([
                'notification_ids' => 'required|array|min:1',
                'notification_ids.*' => 'exists:notifications,id',
            ]);

            $count = Notification::whereIn('id', $request->notification_ids)->delete();

            Log::info('Bulk notification delete completed', [
                'admin_id' => auth()->id(),
                'deleted_count' => $count,
                'notification_ids' => $request->notification_ids
            ]);

            return back()->with('success', "Đã xóa {$count} thông báo!");

        } catch (Exception $e) {
            Log::error('Error in bulk delete notifications', [
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);

            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Show notification dashboard
     */
    public function dashboard()
    {
        try {
            $stats = [
                'total' => Notification::count(),
                'today' => Notification::whereDate('created_at', today())->count(),
                'this_week' => Notification::whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count(),
                'this_month' => Notification::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'unread' => Notification::unread()->notExpired()->count(),
                'read' => Notification::read()->notExpired()->count(),
                'expired' => Notification::expired()->count(),
                'by_type' => [],
                'recent' => Notification::with('user')
                    ->latest()
                    ->take(10)
                    ->get(),
                'top_users' => Notification::select('user_id', DB::raw('count(*) as total'))
                    ->with('user:id,email,first_name,last_name')
                    ->groupBy('user_id')
                    ->orderByDesc('total')
                    ->take(10)
                    ->get(),
            ];

            // Stats by type
            foreach (NotificationType::cases() as $type) {
                $stats['by_type'][$type->value] = Notification::ofType($type)->count();
            }

            Log::info('Admin viewed notification dashboard', [
                'admin_id' => auth()->id()
            ]);

            return view('admin.notifications.dashboard', compact('stats'));

        } catch (Exception $e) {
            Log::error('Error loading notification dashboard', [
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);

            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}