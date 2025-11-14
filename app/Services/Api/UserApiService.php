<?php

// namespace App\Services\Api;

// use App\Models\User;

// class UserApiService
// {
//     public function index()
//     {
//         return User::latest()->get();   // API luôn trả JSON-friendly data
//     }

//     public function show($id)
//     {
//         return User::findOrFail($id);
//     }

//     public function store($data)
//     {
//         return User::create($data);
//     }

//     public function update($id, $data)
//     {
//         $user = User::findOrFail($id);
//         $user->update($data);

//         return $user;
//     }

//     public function destroy($id)
//     {
//         $user = User::findOrFail($id);
//         $user->delete();

//         return true;
//     }
// }



// namespace App\Services\Api;

// use App\Models\User;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Storage;

// class UserApiService
// {
//     /**
//      * Lấy danh sách user (không phân trang)
//      */
//     public function getAll()
//     {
//         return User::latest()->get();
//     }

//     /**
//      * Lấy chi tiết user
//      */
//     public function getById($id)
//     {
//         return User::findOrFail($id);
//     }

//     /**
//      * Tạo user mới
//      */
//     public function create($data)
//     {
//         // Hash password nếu có
//         if (!empty($data['password'])) {
//             $data['password'] = Hash::make($data['password']);
//         }

//         // Upload avatar nếu có
//         if (!empty($data['avatar']) && $data['avatar'] instanceof \Illuminate\Http\UploadedFile) {
//             $data['avatar'] = $data['avatar']->store('avatars', 'public');
//         }

//         return User::create($data);
//     }

//     /**
//      * Cập nhật user
//      */
//     public function update($id, $data)
//     {
//         $user = User::findOrFail($id);

//         // Hash password nếu có
//         if (!empty($data['password'])) {
//             $data['password'] = Hash::make($data['password']);
//         } else {
//             unset($data['password']);
//         }

//         // Avatar mới
//         if (!empty($data['avatar']) && $data['avatar'] instanceof \Illuminate\Http\UploadedFile) {
//             if ($user->avatar) {
//                 Storage::disk('public')->delete($user->avatar);
//             }

//             $data['avatar'] = $data['avatar']->store('avatars', 'public');
//         }

//         $user->update($data);
//         return $user;
//     }

//     /**
//      * Xóa user
//      */
//     public function delete($id)
//     {
//         $user = User::findOrFail($id);

//         if ($user->avatar) {
//             Storage::disk('public')->delete($user->avatar);
//         }

//         return $user->delete();
//     }
// }





namespace App\Services\Api;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserApiService
{
    /**
     * Lấy danh sách user
     */
    public function getAll()
    {
        return response()->json([
            'success' => true,
            'data' => User::latest()->get(),
        ], 200);
    }

    /**
     * Lấy chi tiết user
     */
    public function getById($id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json(['success' => true, 'data' => $user], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
    }

    /**
     * Tạo user mới
     */
    public function create($data)
    {
        try {
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            if (!empty($data['avatar']) && $data['avatar'] instanceof \Illuminate\Http\UploadedFile) {
                $data['avatar'] = $data['avatar']->store('avatars', 'public');
            }

            $user = User::create($data);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật user
     */
    public function update($id, $data)
    {
        try {
            $user = User::findOrFail($id);

            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            if (!empty($data['avatar']) && $data['avatar'] instanceof \Illuminate\Http\UploadedFile) {
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $data['avatar'] = $data['avatar']->store('avatars', 'public');
            }

            $user->update($data);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa user
     */
    public function delete($id)
    {
        try {
            $user = User::findOrFail($id);

            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ], 500);
        }
    }
}
