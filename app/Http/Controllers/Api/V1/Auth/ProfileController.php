<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Http\Resources\Api\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Get user profile
     */
    public function show()
    {
        try {
            $user = auth('api')->user();

            return response()->json([
                'success' => true,
                'data' => new UserResource($user)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy thông tin profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user profile
     */
    public function update(UpdateProfileRequest $request)
    {
        try {
            $user = auth('api')->user();

            $data = $request->except(['avatar']);

            if ($request->hasFile('avatar')) {
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }

                // Upload avatar mới
                $path = $request->file('avatar')->store('avatars', 'public');
                $data['avatar'] = $path;
            }

            $user->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật profile thành công',
                'data' => new UserResource($user->fresh())
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật profile thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update avatar only
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            $user = auth('api')->user();

            // Xóa avatar cũ
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Upload avatar mới
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar' => $path]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật avatar thành công',
                'data' => [
                    'avatar_url' => $user->avatar_url
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật avatar thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove avatar
     */
    public function removeAvatar()
    {
        try {
            $user = auth('api')->user();

            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->update(['avatar' => null]);

            return response()->json([
                'success' => true,
                'message' => 'Xóa avatar thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xóa avatar thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
