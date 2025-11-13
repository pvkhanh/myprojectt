<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use App\Models\User;
use App\Repositories\Contracts\UserAddressRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserAddressController extends Controller
{
    protected $userAddressRepository;

    public function __construct(UserAddressRepositoryInterface $userAddressRepository)
    {
        $this->userAddressRepository = $userAddressRepository;
    }

    /**
     * Danh sách tất cả địa chỉ hoặc theo user
     */
    public function index(Request $request)
    {
        $query = UserAddress::with('user');

        // Lọc theo user
        if ($request->filled('user_id')) {
            $query->byUser($request->user_id);
        }

        // Lọc theo từ khóa
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('receiver_name', 'like', "%{$keyword}%")
                    ->orWhere('phone', 'like', "%{$keyword}%")
                    ->orWhere('address', 'like', "%{$keyword}%")
                    ->orWhere('province', 'like', "%{$keyword}%")
                    ->orWhere('district', 'like', "%{$keyword}%");
            });
        }

        // Lọc theo tỉnh/thành phố
        if ($request->filled('province')) {
            $query->where('province', $request->province);
        }

        // Lọc địa chỉ mặc định
        if ($request->filled('is_default')) {
            $query->where('is_default', $request->is_default);
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'latest');
        switch ($sortBy) {
            case 'oldest':
                $query->oldest();
                break;
            case 'name':
                $query->orderBy('receiver_name');
                break;
            default:
                $query->latest();
        }

        $addresses = $query->paginate(15);

        // Lấy danh sách users để filter
        $users = User::select('id', 'username', 'email')->orderBy('username')->get();

        // Lấy danh sách tỉnh/thành phố
        $provinces = UserAddress::select('province')
            ->distinct()
            ->whereNotNull('province')
            ->orderBy('province')
            ->pluck('province');

        // Thống kê
        $stats = [
            'total' => UserAddress::count(),
            'default' => UserAddress::where('is_default', true)->count(),
            'active_users' => UserAddress::distinct('user_id')->count('user_id'),
        ];

        return view('admin.user-addresses.index', compact('addresses', 'users', 'provinces', 'stats'));
    }

    /**
     * Form tạo địa chỉ mới
     */
    public function create(Request $request)
    {
        $users = User::select('id', 'username', 'email')->orderBy('username')->get();
        $userId = $request->get('user_id');

        return view('admin.user-addresses.create', compact('users', 'userId'));
    }

    /**
     * Lưu địa chỉ mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'receiver_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'province' => 'required|string|max:100',
            'district' => 'nullable|string|max:100',
            'ward' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'is_default' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Nếu đặt làm mặc định, bỏ default của các địa chỉ khác
            if ($request->is_default) {
                UserAddress::where('user_id', $request->user_id)
                    ->update(['is_default' => false]);
            }

            $address = $this->userAddressRepository->create($validated);

            DB::commit();

            return redirect()
                ->route('admin.user-addresses.show', $address->id)
                ->with('success', 'Thêm địa chỉ thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating user address: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi thêm địa chỉ!');
        }
    }

    /**
     * Chi tiết địa chỉ
     */
    public function show($id)
    {
        $address = UserAddress::with(['user.orders'])->findOrFail($id);

        // Lấy các đơn hàng sử dụng địa chỉ tương tự
        $relatedOrders = $address->user->orders()
            ->with('shippingAddress')
            ->whereHas('shippingAddress', function ($query) use ($address) {
                $query->where('phone', $address->phone)
                    ->orWhere('address', 'like', '%' . $address->address . '%');
            })
            ->latest()
            ->take(5)
            ->get();

        return view('admin.user-addresses.show', compact('address', 'relatedOrders'));
    }

    /**
     * Form chỉnh sửa
     */
    public function edit($id)
    {
        $address = UserAddress::with('user')->findOrFail($id);
        $users = User::select('id', 'username', 'email')->orderBy('username')->get();

        return view('admin.user-addresses.edit', compact('address', 'users'));
    }

    /**
     * Cập nhật địa chỉ
     */
    public function update(Request $request, $id)
    {
        $address = UserAddress::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'receiver_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'province' => 'required|string|max:100',
            'district' => 'nullable|string|max:100',
            'ward' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'is_default' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Nếu đặt làm mặc định, bỏ default của các địa chỉ khác
            if ($request->is_default && !$address->is_default) {
                UserAddress::where('user_id', $request->user_id)
                    ->where('id', '!=', $id)
                    ->update(['is_default' => false]);
            }

            $address->update($validated);

            DB::commit();

            return redirect()
                ->route('admin.user-addresses.show', $address->id)
                ->with('success', 'Cập nhật địa chỉ thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating user address: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật địa chỉ!');
        }
    }

    /**
     * Xóa địa chỉ (soft delete)
     */
    public function destroy($id)
    {
        try {
            $address = UserAddress::findOrFail($id);
            $userId = $address->user_id;

            $address->delete();

            return redirect()
                ->route('admin.users.show', $userId)
                ->with('success', 'Xóa địa chỉ thành công!');

        } catch (\Exception $e) {
            Log::error('Error deleting user address: ' . $e->getMessage());

            return back()->with('error', 'Có lỗi xảy ra khi xóa địa chỉ!');
        }
    }

    /**
     * Đặt làm địa chỉ mặc định
     */
    public function setDefault($id)
    {
        try {
            DB::beginTransaction();

            $address = UserAddress::findOrFail($id);

            // Bỏ default của các địa chỉ khác
            UserAddress::where('user_id', $address->user_id)
                ->update(['is_default' => false]);

            // Đặt địa chỉ hiện tại làm mặc định
            $address->update(['is_default' => true]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã đặt làm địa chỉ mặc định!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error setting default address: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra!'
            ], 500);
        }
    }

    /**
     * Xóa nhiều địa chỉ
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:user_addresses,id'
        ]);

        try {
            DB::beginTransaction();

            UserAddress::whereIn('id', $request->ids)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa ' . count($request->ids) . ' địa chỉ!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error bulk deleting addresses: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa!'
            ], 500);
        }
    }

    /**
     * Danh sách địa chỉ đã xóa
     */
    public function trashed()
    {
        $addresses = UserAddress::onlyTrashed()
            ->with('user')
            ->latest('deleted_at')
            ->paginate(15);

        return view('admin.user-addresses.trashed', compact('addresses'));
    }

    /**
     * Khôi phục địa chỉ
     */
    public function restore($id)
    {
        try {
            $address = UserAddress::onlyTrashed()->findOrFail($id);
            $address->restore();

            return redirect()
                ->route('admin.user-addresses.trashed')
                ->with('success', 'Khôi phục địa chỉ thành công!');

        } catch (\Exception $e) {
            Log::error('Error restoring address: ' . $e->getMessage());

            return back()->with('error', 'Có lỗi xảy ra khi khôi phục!');
        }
    }

    /**
     * Xóa vĩnh viễn
     */
    public function forceDelete($id)
    {
        try {
            $address = UserAddress::onlyTrashed()->findOrFail($id);
            $address->forceDelete();

            return redirect()
                ->route('admin.user-addresses.trashed')
                ->with('success', 'Xóa vĩnh viễn địa chỉ thành công!');

        } catch (\Exception $e) {
            Log::error('Error force deleting address: ' . $e->getMessage());

            return back()->with('error', 'Có lỗi xảy ra khi xóa!');
        }
    }
}