<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\MailRepositoryInterface;
use App\Repositories\Contracts\MailRecipientRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Enums\MailType;
use App\Enums\MailRecipientStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail as MailFacade;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class MailController extends Controller
{
    public function __construct(
        private MailRepositoryInterface $mailRepo,
        private MailRecipientRepositoryInterface $recipientRepo,
        private UserRepositoryInterface $userRepo
    ) {}

    /**
     * 📊 DASHBOARD - Tổng quan hệ thống mail
     */
    public function dashboard()
    {
        $totalMails = $this->mailRepo->count();
        $totalRecipients = DB::table('mail_recipients')->count();
        $sentToday = DB::table('mail_recipients')
            ->where('status', 'sent')
            ->whereDate('updated_at', today())
            ->count();
        $failedToday = DB::table('mail_recipients')
            ->where('status', 'failed')
            ->whereDate('updated_at', today())
            ->count();

        $mailByType = DB::table('mails')
            ->select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->get();

        $recentMails = $this->mailRepo->newQuery()
            ->with('recipients')
            ->latest()
            ->limit(10)
            ->get();

        $last7Days = collect(range(6, 0))->map(function($day) {
            $date = Carbon::today()->subDays($day);
            return [
                'date' => $date->format('d/m'),
                'sent' => DB::table('mail_recipients')
                    ->where('status', 'sent')
                    ->whereDate('updated_at', $date)
                    ->count(),
                'failed' => DB::table('mail_recipients')
                    ->where('status', 'failed')
                    ->whereDate('updated_at', $date)
                    ->count(),
            ];
        });

        $topTemplates = DB::table('mails')
            ->select('template_key', DB::raw('count(*) as usage_count'))
            ->whereNotNull('template_key')
            ->groupBy('template_key')
            ->orderByDesc('usage_count')
            ->limit(5)
            ->get();

        return view('admin.mails.dashboard', compact(
            'totalMails',
            'totalRecipients',
            'sentToday',
            'failedToday',
            'mailByType',
            'recentMails',
            'last7Days',
            'topTemplates'
        ));
    }

    /**
     * 📋 INDEX - Danh sách mail với advanced filters
     */
    public function index(Request $request)
    {
        $query = $this->mailRepo->newQuery()->with('recipients');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('template_key')) {
            $query->where('template_key', 'like', "%{$request->template_key}%");
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('subject', 'like', "%{$request->search}%")
                  ->orWhere('content', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            $query->whereHas('recipients', function($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $mails = $query->paginate($request->get('per_page', 15));
        $types = MailType::cases();
        $statuses = MailRecipientStatus::cases();

        return view('admin.mails.index', compact('mails', 'types', 'statuses'));
    }

    /**
     * 📚 TEMPLATES LIBRARY - Thư viện mẫu email
     */
    public function templates()
    {
        $templates = $this->mailRepo->newQuery()
            ->whereNotNull('template_key')
            ->distinct('template_key')
            ->get()
            ->groupBy('template_key');

        return view('admin.mails.templates', compact('templates'));
    }

    /**
     * 🎯 SEGMENTS - Phân nhóm người nhận
     */
    public function segments()
    {
        $segments = [
            'all_users' => [
                'name' => 'Tất cả người dùng',
                'count' => $this->userRepo->count(),
                'icon' => 'users',
                'color' => 'primary'
            ],
            'verified_users' => [
                'name' => 'Người dùng đã xác thực',
                'count' => $this->userRepo->getVerified()->count(),
                'icon' => 'user-check',
                'color' => 'success'
            ],
            'active_users' => [
                'name' => 'Người dùng đang hoạt động',
                'count' => $this->userRepo->getActive()->count(),
                'icon' => 'user-clock',
                'color' => 'info'
            ],
            'new_users' => [
                'name' => 'Người dùng mới (30 ngày)',
                'count' => DB::table('users')
                    ->where('created_at', '>=', Carbon::now()->subDays(30))
                    ->count(),
                'icon' => 'user-plus',
                'color' => 'warning'
            ],
            'buyers' => [
                'name' => 'Khách hàng đã mua hàng',
                'count' => DB::table('users')
                    ->whereExists(function($query) {
                        $query->select(DB::raw(1))
                            ->from('orders')
                            ->whereColumn('orders.user_id', 'users.id');
                    })
                    ->count(),
                'icon' => 'shopping-cart',
                'color' => 'danger'
            ],
        ];

        return view('admin.mails.segments', compact('segments'));
    }

    /**
     * ➕ CREATE
     */
    public function create(Request $request)
    {
        $types = MailType::cases();

        $segment = $request->get('segment', 'all_users');
        $users = $this->getUsersBySegment($segment);

        $templateKey = $request->get('template');
        $template = null;
        if ($templateKey) {
            $template = $this->mailRepo->newQuery()
                ->where('template_key', $templateKey)
                ->first();
        }

        return view('admin.mails.create', compact('types', 'users', 'segment', 'template'));
    }

    /**
     * 💾 STORE
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'template_key' => 'nullable|string|max:100',
            'type' => 'required|in:' . implode(',', array_map(fn($t) => $t->value, MailType::cases())),
            'sender_email' => 'nullable|email',
            'recipients' => 'required|array|min:1',
            'recipients.*' => 'required|exists:users,id',
            'variables' => 'nullable|json',
            'schedule_at' => 'nullable|date|after:now',
        ]);

        DB::beginTransaction();
        try {
            $mail = $this->mailRepo->create([
                'subject' => $validated['subject'],
                'content' => $validated['content'],
                'template_key' => $validated['template_key'],
                'type' => $validated['type'],
                'sender_email' => $validated['sender_email'] ?? config('mail.from.address'),
                'variables' => $validated['variables'] ? json_decode($validated['variables'], true) : null,
            ]);

            foreach ($validated['recipients'] as $userId) {
                $user = $this->userRepo->find($userId);

                $this->recipientRepo->create([
                    'mail_id' => $mail->id,
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'status' => MailRecipientStatus::Pending->value,
                ]);
            }

            DB::commit();

            if (!empty($validated['schedule_at'])) {
                return redirect()->route('admin.mails.show', $mail->id)
                    ->with('success', "Mail đã được lên lịch gửi vào {$validated['schedule_at']}");
            }

            return redirect()->route('admin.mails.show', $mail->id)
                ->with('success', 'Mail đã được tạo thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * 👁️ SHOW
     */
    public function show(int $id)
    {
        $mail = $this->mailRepo->find($id);

        if (!$mail) {
            return redirect()->route('admin.mails.index')
                ->with('error', 'Mail không tồn tại!');
        }

        $recipients = $mail->recipients()->paginate(50);

        $stats = [
            'total' => $mail->recipients()->count(),
            'sent' => $mail->recipients()->where('status', 'sent')->count(),
            'pending' => $mail->recipients()->where('status', 'pending')->count(),
            'failed' => $mail->recipients()->where('status', 'failed')->count(),
        ];

        return view('admin.mails.show', compact('mail', 'recipients', 'stats'));
    }

    /**
     * ✏️ EDIT
     */
    public function edit(int $id)
    {
        $mail = $this->mailRepo->find($id);

        if (!$mail) {
            return redirect()->route('admin.mails.index')
                ->with('error', 'Mail không tồn tại!');
        }

        $types = MailType::cases();
        $users = $this->userRepo->all();
        $selectedUsers = $mail->recipients->pluck('user_id')->toArray();

        return view('admin.mails.edit', compact('mail', 'types', 'users', 'selectedUsers'));
    }

    /**
     * 🔄 UPDATE
     */
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'template_key' => 'nullable|string|max:100',
            'type' => 'required|in:' . implode(',', array_map(fn($t) => $t->value, MailType::cases())),
            'sender_email' => 'nullable|email',
            'variables' => 'nullable|json',
        ]);

        $this->mailRepo->update($id, [
            'subject' => $validated['subject'],
            'content' => $validated['content'],
            'template_key' => $validated['template_key'],
            'type' => $validated['type'],
            'sender_email' => $validated['sender_email'] ?? config('mail.from.address'),
            'variables' => $validated['variables'] ? json_decode($validated['variables'], true) : null,
        ]);

        return redirect()->route('admin.mails.show', $id)
            ->with('success', 'Mail đã được cập nhật!');
    }

    /**
     * 🗑️ DELETE
     */
    public function destroy(int $id)
    {
        $this->mailRepo->delete($id);
        return redirect()->route('admin.mails.index')
            ->with('success', 'Mail đã được xóa!');
    }

    /**
     * 🖼️ UPLOAD IMAGE - Xử lý upload ảnh từ CKEditor
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'upload' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            $image = $request->file('upload');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            
            $path = $image->storeAs('mail-images', $imageName, 'public');
            
            $url = asset('storage/' . $path);
            
            return response()->json([
                'uploaded' => true,
                'url' => $url
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'uploaded' => false,
                'error' => [
                    'message' => 'Upload failed: ' . $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * 📧 SEND MAIL
     */
    public function send(int $id)
    {
        $mail = $this->mailRepo->find($id);

        if (!$mail) {
            return redirect()->route('admin.mails.index')
                ->with('error', 'Mail không tồn tại!');
        }

        $recipients = $mail->recipients()
            ->where('status', MailRecipientStatus::Pending->value)
            ->get();

        if ($recipients->isEmpty()) {
            return redirect()->route('admin.mails.show', $id)
                ->with('warning', 'Không có người nhận nào đang chờ gửi!');
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($recipients as $recipient) {
            try {
                $content = $this->replaceVariables($mail->content, $recipient);
                $content = $this->convertImagesToAbsoluteUrls($content);

                MailFacade::html($content, function (Message $message) use ($recipient, $mail) {
                    $message->to($recipient->email, $recipient->name)
                        ->subject($mail->subject)
                        ->from($mail->sender_email, config('app.name'));
                });

                $this->recipientRepo->update($recipient->id, [
                    'status' => MailRecipientStatus::Sent->value,
                ]);

                $successCount++;

            } catch (\Exception $e) {
                $this->recipientRepo->update($recipient->id, [
                    'status' => MailRecipientStatus::Failed->value,
                    'error_log' => $e->getMessage(),
                ]);

                $failCount++;
            }
        }

        return redirect()->route('admin.mails.show', $id)
            ->with('success', "✅ Gửi thành công: {$successCount} | ❌ Thất bại: {$failCount}");
    }

    /**
     * 🔁 RESEND FAILED
     */
    public function resendFailed(int $id)
    {
        $mail = $this->mailRepo->find($id);

        if (!$mail) {
            return redirect()->route('admin.mails.index')
                ->with('error', 'Mail không tồn tại!');
        }

        $failedRecipients = $mail->recipients()
            ->where('status', MailRecipientStatus::Failed->value)
            ->get();

        if ($failedRecipients->isEmpty()) {
            return redirect()->route('admin.mails.show', $id)
                ->with('info', 'Không có email nào bị lỗi!');
        }

        $successCount = 0;

        foreach ($failedRecipients as $recipient) {
            try {
                $content = $this->replaceVariables($mail->content, $recipient);
                $content = $this->convertImagesToAbsoluteUrls($content);

                MailFacade::html($content, function (Message $message) use ($recipient, $mail) {
                    $message->to($recipient->email, $recipient->name)
                        ->subject($mail->subject)
                        ->from($mail->sender_email, config('app.name'));
                });

                $this->recipientRepo->update($recipient->id, [
                    'status' => MailRecipientStatus::Sent->value,
                    'error_log' => null,
                ]);

                $successCount++;

            } catch (\Exception $e) {
                $this->recipientRepo->update($recipient->id, [
                    'error_log' => $e->getMessage(),
                ]);
            }
        }

        return redirect()->route('admin.mails.show', $id)
            ->with('success', "Đã gửi lại thành công {$successCount} email!");
    }

    /**
     * 👀 PREVIEW
     */
    public function preview(int $id)
    {
        $mail = $this->mailRepo->find($id);

        if (!$mail) {
            abort(404, 'Mail không tồn tại');
        }
        
        $recipient = $mail->recipients()->first();
        $user = $recipient?->user;

        $replacements = [];
        if ($user) {
            $replacements = [
                '{{username}}'   => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
                '{{email}}'      => $user->email,
                '{{first_name}}' => $user->first_name ?? '',
                '{{last_name}}'  => $user->last_name ?? '',
            ];
        }

        if ($mail->variables) {
            foreach ($mail->variables as $key => $value) {
                $replacements["{{{$key}}}"] = $value;
            }
        }

        $content = strtr($mail->content, $replacements);
        $wrappedContent = '<div class="mail-content-scope">' . $content . '</div>';

        return view('admin.mails.preview', compact('mail', 'content', 'wrappedContent'));
    }

    /**
     * 📊 ANALYTICS
     */
    public function analytics(int $id)
    {
        $mail = $this->mailRepo->find($id);

        if (!$mail) {
            return redirect()->route('admin.mails.index')
                ->with('error', 'Mail không tồn tại!');
        }

        return view('admin.mails.analytics', compact('mail'));
    }

    /**
     * HELPER: Lấy users theo segment
     */
    private function getUsersBySegment(string $segment)
    {
        return match($segment) {
            'verified_users' => $this->userRepo->newQuery()->whereNotNull('email_verified_at')->get(),
            'active_users' => $this->userRepo->all(),
            'new_users' => $this->userRepo->newQuery()
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->get(),
            'buyers' => $this->userRepo->newQuery()
                ->whereExists(function($query) {
                    $query->select(DB::raw(1))
                        ->from('orders')
                        ->whereColumn('orders.user_id', 'users.id');
                })
                ->get(),
            default => $this->userRepo->all(),
        };
    }

    /**
     * HELPER: Chuyển đổi relative image URLs thành absolute
     */
    private function convertImagesToAbsoluteUrls(string $content): string
    {
        $content = preg_replace_callback(
            '/<img[^>]+src=["\']([^"\']+)["\']/i',
            function ($matches) {
                $src = $matches[1];
                if (preg_match('/^https?:\/\//', $src)) {
                    return $matches[0];
                }
                $absoluteSrc = url($src);
                return str_replace($src, $absoluteSrc, $matches[0]);
            },
            $content
        );

        return $content;
    }

    /**
     * HELPER: Thay thế biến trong content
     */
    private function replaceVariables(string $content, $recipient): string
    {
        $user = $recipient->user;

        $variables = [
            '{{name}}' => $recipient->name,
            '{{email}}' => $recipient->email,
            '{{first_name}}' => $user->first_name ?? '',
            '{{last_name}}' => $user->last_name ?? '',
            '{{username}}' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
        ];

        return str_replace(array_keys($variables), array_values($variables), $content);
    }
}