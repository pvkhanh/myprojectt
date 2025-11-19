<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    /**
     * Danh sách địa chỉ của user
     */
    public function index()
    {
        try {
            $userId = auth('api')->id();
            
            $addresses = UserAddress::where('user_id', $userId)
                ->orderBy('is_default', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $addresses,
                'count' => $addresses->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải danh sách địa chỉ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Chi tiết một địa chỉ
     */
    public function show($id)
    {
        try {
            $userId = auth('api')->id();
            
            $address = UserAddress::where('id', $id)
                ->where('user_id', $userId)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $address
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy địa chỉ',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Thêm địa chỉ mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'receiver_name' => 'required|string|max:100',
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'ward' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'is_default' => 'sometimes|boolean',
        ]);

        try {
            DB::beginTransaction();

            $userId = auth('api')->id();

            // Nếu set làm địa chỉ mặc định, bỏ default của các địa chỉ khác
            if ($request->get('is_default', false)) {
                UserAddress::where('user_id', $userId)
                    ->update(['is_default' => false]);
            }

            // Nếu đây là địa chỉ đầu tiên, tự động set làm default
            $isFirstAddress = !UserAddress::where('user_id', $userId)->exists();

            $address = UserAddress::create([
                'user_id' => $userId,
                'receiver_name' => $request->receiver_name,
                'phone' => $request->phone,
                'address' => $request->address,
                'ward' => $request->ward,
                'district' => $request->district,
                'city' => $request->city,
                'is_default' => $isFirstAddress || $request->get('is_default', false),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã thêm địa chỉ mới',
                'data' => $address
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Không thể thêm địa chỉ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật địa chỉ
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'receiver_name' => 'sometimes|required|string|max:100',
            'phone' => 'sometimes|required|string|max:15',
            'address' => 'sometimes|required|string|max:255',
            'ward' => 'sometimes|required|string|max:100',
            'district' => 'sometimes|required|string|max:100',
            'city' => 'sometimes|required|string|max:100',
            'is_default' => 'sometimes|boolean',
        ]);

        try {
            DB::beginTransaction();

            $userId = auth('api')->id();
            
            $address = UserAddress::where('id', $id)
                ->where('user_id', $userId)
                ->firstOrFail();

            // Nếu set làm địa chỉ mặc định
            if ($request->has('is_default') && $request->is_default) {
                UserAddress::where('user_id', $userId)
                    ->where('id', '!=', $id)
                    ->update(['is_default' => false]);
            }

            $address->update($request->only([
                'receiver_name',
                'phone',
                'address',
                'ward',
                'district',
                'city',
                'is_default'
            ]));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật địa chỉ',
                'data' => $address->fresh()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Không thể cập nhật địa chỉ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa địa chỉ
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $userId = auth('api')->id();
            
            $address = UserAddress::where('id', $id)
                ->where('user_id', $userId)
                ->firstOrFail();

            $wasDefault = $address->is_default;

            $address->delete();

            // Nếu xóa địa chỉ default, set địa chỉ khác làm default
            if ($wasDefault) {
                $newDefault = UserAddress::where('user_id', $userId)
                    ->orderBy('created_at', 'desc')
                    ->first();
                
                if ($newDefault) {
                    $newDefault->update(['is_default' => true]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa địa chỉ'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa địa chỉ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Đặt địa chỉ làm mặc định
     */
    public function setDefault($id)
    {
        try {
            DB::beginTransaction();

            $userId = auth('api')->id();
            
            $address = UserAddress::where('id', $id)
                ->where('user_id', $userId)
                ->firstOrFail();

            // Bỏ default của các địa chỉ khác
            UserAddress::where('user_id', $userId)
                ->where('id', '!=', $id)
                ->update(['is_default' => false]);

            // Set địa chỉ này làm default
            $address->update(['is_default' => true]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã đặt làm địa chỉ mặc định',
                'data' => $address->fresh()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Không thể đặt địa chỉ mặc định',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}