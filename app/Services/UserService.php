<?php

// namespace App\Services;

// use App\Repositories\Contracts\UserRepositoryInterface;
// use App\Models\User;
// use App\Services\MailService;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Str;
// use Illuminate\Support\Facades\Log;

// class UserService
// {
//     protected UserRepositoryInterface $userRepository;
//     protected MailService $mailService;

//     public function __construct(UserRepositoryInterface $userRepository, MailService $mailService)
//     {
//         $this->userRepository = $userRepository;
//         $this->mailService = $mailService;
//     }

//     // =================== INDEX ===================
//     public function index(Request $request)
//     {
//         try {
//             $query = User::query();

//             if ($request->filled('search')) {
//                 $query->where(fn($q) => $q
//                     ->where('username', 'like', "%{$request->search}%")
//                     ->orWhere('email', 'like', "%{$request->search}%")
//                 );
//             }

//             if ($request->filled('status')) {
//                 $query->where('is_active', $request->status);
//             }

//             if ($request->filled('role')) {
//                 $query->where('role', $request->role);
//             }

//             if ($request->filled('verified')) {
//                 $request->verified == '1' 
//                     ? $query->whereNotNull('email_verified_at') 
//                     : $query->whereNull('email_verified_at');
//             }

//             $users = $query->latest()->paginate(15);
//             return view('admin.users.index', compact('users'));
//         } catch (\Exception $e) {
//             Log::error("UserService@index error: {$e->getMessage()}");
//             return back()->with('error', 'Đã xảy ra lỗi khi tải danh sách người dùng!');
//         }
//     }

//     // =================== CREATE ===================
//     public function create()
//     {
//         return view('admin.users.create');
//     }

//     // =================== STORE ===================
//     public function store(Request $request)
//     {
//         try {
//             $validated = $request->validated();

//             $validated['password'] = Hash::make($validated['password']);
//             $validated['remember_token'] = Str::random(60);
//             if (empty($validated['email_verified_at'])) {
//                 $validated['email_verified_at'] = now();
//             }

//             if ($request->hasFile('avatar')) {
//                 $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
//             }

//             $user = $this->userRepository->create($validated);

//             if ($request->input('send_welcome_email', true)) {
//                 $this->mailService->sendWelcomeEmail($user);
//             }

//             return redirect()->route('admin.users.index')
//                 ->with('success', 'Tạo người dùng thành công!');
//         } catch (\Exception $e) {
//             Log::error("UserService@store error: {$e->getMessage()}");
//             return back()->with('error', 'Lỗi khi tạo người dùng!');
//         }
//     }

//     // =================== EDIT ===================
//     public function edit($id)
//     {
//         $user = $this->userRepository->find($id);
//         abort_if(!$user, 404);
//         return view('admin.users.edit', compact('user'));
//     }

//     // =================== UPDATE ===================
//     public function update(Request $request, $id)
// {
//     try {
//         // Lấy user từ repo
//         $user = $this->userRepository->find($id);
//         abort_if(!$user, 404);

//         // Validate dữ liệu
//         $validated = $request->validated();

//         // Hash password nếu có
//         if ($request->filled('password')) {
//             $validated['password'] = Hash::make($validated['password']);
//         } else {
//             unset($validated['password']);
//         }

//         // Xử lý avatar
//         if ($request->hasFile('avatar')) {
//             // Xóa avatar cũ nếu có
//             if ($user->avatar) {
//                 Storage::disk('public')->delete($user->avatar);
//             }
//             // Lưu avatar mới
//             $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
//         }

//         // Gán dữ liệu vào model
//         $user->fill($validated);

//         // ⚡ Lưu luôn, kể cả khi dữ liệu không thay đổi
//         $user->save();

//         return redirect()->route('admin.users.index')
//             ->with('success', 'Cập nhật người dùng thành công!');
//     } catch (\Exception $e) {
//         Log::error("UserService@update error: {$e->getMessage()}");
//         return back()->with('error', 'Lỗi khi cập nhật người dùng!');
//     }
// }


//     // =================== SHOW ===================
//     public function show($id)
//     {
//         $user = $this->userRepository->find($id);
//         abort_if(!$user, 404);
//         return view('admin.users.show', compact('user'));
//     }

//     // =================== DELETE ===================
//     public function destroy($id)
//     {
//         try {
//             $user = $this->userRepository->find($id);
//             abort_if(!$user, 404);
//             abort_if(auth()->id() === $user->id, 400, 'Bạn không thể xóa chính mình!');
//             $user->delete();

//             return redirect()->route('admin.users.index')
//                 ->with('success', "Người dùng '{$user->username}' đã được chuyển vào thùng rác!");
//         } catch (\Exception $e) {
//             Log::error("UserService@destroy error: {$e->getMessage()}");
//             return back()->with('error', 'Xóa người dùng thất bại!');
//         }
//     }

//     // =================== TRASHED ===================
//     public function trashed()
//     {
//         $users = User::onlyTrashed()->paginate(10);
//         return view('admin.users.trashed', compact('users'));
//     }

//     public function restore($id)
//     {
//         User::onlyTrashed()->findOrFail($id)->restore();
//         return response()->json(['message' => 'Khôi phục người dùng thành công!']);
//     }

//     public function restoreAll()
//     {
//         User::onlyTrashed()->restore();
//         return response()->json(['message' => 'Khôi phục tất cả người dùng thành công!']);
//     }

//     public function forceDelete($id)
//     {
//         User::onlyTrashed()->findOrFail($id)->forceDelete();
//         return response()->json(['message' => 'Xóa vĩnh viễn người dùng thành công!']);
//     }

//     public function forceDeleteSelected(Request $request)
//     {
//         $ids = $request->ids ?? [];
//         User::onlyTrashed()->whereIn('id', $ids)->forceDelete();
//         return response()->json(['message' => 'Xóa vĩnh viễn các người dùng đã chọn thành công!']);
//     }

//     // =================== TOGGLE STATUS ===================
//     public function toggleStatus($id, Request $request)
//     {
//         $user = User::findOrFail($id);
//         $user->is_active = $request->is_active;
//         $user->save();
//         return response()->json(['success' => true, 'message' => 'Cập nhật trạng thái thành công!']);
//     }

//     // =================== RESEND WELCOME EMAIL ===================
//     public function resendWelcomeEmail($id)
//     {
//         try {
//             $user = User::findOrFail($id);
//             $this->mailService->sendWelcomeEmail($user);
//             return response()->json(['success' => true, 'message' => 'Email chào mừng đã được gửi đến ' . $user->email]);
//         } catch (\Exception $e) {
//             Log::error("UserService@resendWelcomeEmail error: {$e->getMessage()}");
//             return response()->json(['success' => false, 'message' => 'Lỗi gửi email!'], 500);
//         }
//     }

//     // =================== SEND EMAIL VERIFICATION ===================
//     public function sendEmailVerification($id)
//     {
//         try {
//             $user = User::findOrFail($id);
//             if ($user->email_verified_at) {
//                 return response()->json(['success' => false, 'message' => 'Email đã được xác thực!'], 400);
//             }

//             $verificationUrl = route('verification.verify', ['id' => $user->id]);
//             $this->mailService->sendEmailVerification($user, $verificationUrl);

//             return response()->json(['success' => true, 'message' => 'Email xác thực đã được gửi đến ' . $user->email]);
//         } catch (\Exception $e) {
//             Log::error("UserService@sendEmailVerification error: {$e->getMessage()}");
//             return response()->json(['success' => false, 'message' => 'Lỗi gửi email!'], 500);
//         }
//     }
// }



namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Models\User;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserService
{
    protected UserRepositoryInterface $userRepository;
    protected MailService $mailService;

    public function __construct(UserRepositoryInterface $userRepository, MailService $mailService)
    {
        $this->userRepository = $userRepository;
        $this->mailService = $mailService;
    }

    // =================== INDEX ===================
    public function index(Request $request)
    {
        try {
            $query = User::query();

            if ($request->filled('search')) {
                $query->where(fn($q) => $q
                    ->where('username', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%"));
            }

            if ($request->filled('status')) {
                $query->where('is_active', $request->status);
            }

            if ($request->filled('role')) {
                $query->where('role', $request->role);
            }

            if ($request->filled('verified')) {
                $request->verified == '1'
                    ? $query->whereNotNull('email_verified_at')
                    : $query->whereNull('email_verified_at');
            }

            $users = $query->latest()->paginate(15);
            return view('admin.users.index', compact('users'));
        } catch (\Exception $e) {
            Log::error("UserService@index error: {$e->getMessage()}");
            return back()->with('error', 'Đã xảy ra lỗi khi tải danh sách người dùng!');
        }
    }

    // =================== CREATE ===================
    public function create()
    {
        return view('admin.users.create');
    }

    // =================== STORE ===================
    public function store(Request $request)
    {
        try {
            $validated = $request->validated();
            $validated['password'] = Hash::make($validated['password']);
            $validated['remember_token'] = Str::random(60);
            $validated['email_verified_at'] ??= now();

            // Nếu có avatar
            if ($request->hasFile('avatar')) {
                $validated['avatar'] = $this->updateAvatarPath($request->file('avatar'));
            }

            $user = $this->userRepository->create($validated);

            if ($request->boolean('send_welcome_email', true)) {
                $this->mailService->sendWelcomeEmail($user);
            }

            return redirect()->route('admin.users.index')->with('success', 'Tạo người dùng thành công!');
        } catch (\Exception $e) {
            Log::error("UserService@store error: {$e->getMessage()}");
            return back()->with('error', 'Lỗi khi tạo người dùng!');
        }
    }

    // =================== SHOW ===================
    public function show($id)
    {
        $user = $this->userRepository->find($id);
        abort_if(!$user, 404);
        return view('admin.users.show', compact('user'));
    }
    // =================== EDIT ===================
    public function edit($id)
    {
        $user = $this->userRepository->find($id);
        abort_if(!$user, 404);
        return view('admin.users.edit', compact('user'));
    }
    // =================== UPDATE ===================
    // public function update(Request $request, $id)
    // {
    //     try {
    //         $user = $this->userRepository->find($id);
    //         abort_if(!$user, 404);

    //         $validated = $request->validated();

    //         if ($request->filled('password')) {
    //             $validated['password'] = Hash::make($validated['password']);
    //         } else {
    //             unset($validated['password']);
    //         }

    //         // ⚡ Sử dụng repository avatar
    //         if ($request->hasFile('avatar')) {
    //             $this->userRepository->updateAvatar($user, $request->file('avatar'));
    //         }

    //         $user->fill($validated)->save();

    //         return redirect()->route('admin.users.index')->with('success', 'Cập nhật người dùng thành công!');
    //     } catch (\Exception $e) {
    //         Log::error("UserService@update error: {$e->getMessage()}");
    //         return back()->with('error', 'Lỗi khi cập nhật người dùng!');
    //     }
    // }


    public function update(Request $request, $id)
    {
        try {
            $user = $this->userRepository->find($id);
            abort_if(!$user, 404);

            // Lấy dữ liệu hợp lệ
            $validated = $request->validated();

            // Nếu người dùng đổi mật khẩu
            if ($request->filled('password')) {
                $validated['password'] = Hash::make($request->password);
            } else {
                unset($validated['password']);
            }

            // Nếu có avatar mới upload
            if ($request->hasFile('avatar')) {
                // Cập nhật avatar qua repository, trả về path mới
                $avatarPath = $this->userRepository->updateAvatar($user, $request->file('avatar'));
                $validated['avatar'] = $avatarPath;
            }

            // Nếu user muốn xóa avatar
            if ($request->input('remove_avatar') == '1') {
                $this->userRepository->removeAvatar($user);
                $validated['avatar'] = null;
            }

            // Cập nhật thông tin user
            $user->fill($validated)->save();

            return redirect()->route('admin.users.index')->with('success', 'Cập nhật người dùng thành công!');
        } catch (\Exception $e) {
            Log::error("UserService@update error: {$e->getMessage()}");
            return back()->with('error', 'Lỗi khi cập nhật người dùng!');
        }
    }


    // =================== AVATAR HANDLERS ===================

    /**
     * Upload & cập nhật avatar riêng
     */
    public function updateAvatar(User $user, UploadedFile $file): ?string
    {
        return $this->userRepository->updateAvatar($user, $file);
    }

    /**
     * Xóa avatar của user
     */
    public function removeAvatar(User $user): bool
    {
        return $this->userRepository->removeAvatar($user);
    }

    /**
     * (Hỗ trợ nội bộ)
     * Upload file và trả về path
     */
    protected function updateAvatarPath(UploadedFile $file): string
    {
        return $file->store('avatars', 'public');
    }

    // =================== DELETE ===================
    public function destroy($id)
    {
        try {
            $user = $this->userRepository->find($id);
            abort_if(!$user, 404);
            abort_if(auth()->id() === $user->id, 400, 'Bạn không thể xóa chính mình!');
            $user->delete();

            return redirect()->route('admin.users.index')
                ->with('success', "Người dùng '{$user->username}' đã được chuyển vào thùng rác!");
        } catch (\Exception $e) {
            Log::error("UserService@destroy error: {$e->getMessage()}");
            return back()->with('error', 'Xóa người dùng thất bại!');
        }
    }
    // =================== TRASHED ===================
    public function trashed()
    {
        $users = User::onlyTrashed()->paginate(10);
        return view('admin.users.trashed', compact('users'));
    }

    public function restore($id)
    {
        User::onlyTrashed()->findOrFail($id)->restore();
        return response()->json(['message' => 'Khôi phục người dùng thành công!']);
    }

    public function restoreAll()
    {
        User::onlyTrashed()->restore();
        return response()->json(['message' => 'Khôi phục tất cả người dùng thành công!']);
    }

    public function forceDelete($id)
    {
        User::onlyTrashed()->findOrFail($id)->forceDelete();
        return response()->json(['message' => 'Xóa vĩnh viễn người dùng thành công!']);
    }

    public function forceDeleteSelected(Request $request)
    {
        $ids = $request->ids ?? [];
        User::onlyTrashed()->whereIn('id', $ids)->forceDelete();
        return response()->json(['message' => 'Xóa vĩnh viễn các người dùng đã chọn thành công!']);
    }

    // =================== TOGGLE STATUS ===================
    public function toggleStatus($id, Request $request)
    {
        $user = User::findOrFail($id);
        $user->is_active = $request->is_active;
        $user->save();
        return response()->json(['success' => true, 'message' => 'Cập nhật trạng thái thành công!']);
    }

    // =================== RESEND WELCOME EMAIL ===================
    public function resendWelcomeEmail($id)
    {
        try {
            $user = User::findOrFail($id);
            $this->mailService->sendWelcomeEmail($user);
            return response()->json(['success' => true, 'message' => 'Email chào mừng đã được gửi đến ' . $user->email]);
        } catch (\Exception $e) {
            Log::error("UserService@resendWelcomeEmail error: {$e->getMessage()}");
            return response()->json(['success' => false, 'message' => 'Lỗi gửi email!'], 500);
        }
    }

    // =================== SEND EMAIL VERIFICATION ===================
    public function sendEmailVerification($id)
    {
        try {
            $user = User::findOrFail($id);
            if ($user->email_verified_at) {
                return response()->json(['success' => false, 'message' => 'Email đã được xác thực!'], 400);
            }

            $verificationUrl = route('verification.verify', ['id' => $user->id]);
            $this->mailService->sendEmailVerification($user, $verificationUrl);

            return response()->json(['success' => true, 'message' => 'Email xác thực đã được gửi đến ' . $user->email]);
        } catch (\Exception $e) {
            Log::error("UserService@sendEmailVerification error: {$e->getMessage()}");
            return response()->json(['success' => false, 'message' => 'Lỗi gửi email!'], 500);
        }
    }
}
