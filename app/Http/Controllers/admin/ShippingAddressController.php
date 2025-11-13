<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingAddress;
use App\Models\Order;
use App\Repositories\Contracts\ShippingAddressRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShippingAddressController extends Controller
{
    protected $shippingAddressRepository;

    public function __construct(ShippingAddressRepositoryInterface $shippingAddressRepository)
    {
        $this->shippingAddressRepository = $shippingAddressRepository;
    }

    /**
     * Danh sách địa chỉ giao hàng
     */
    public function index(Request $request)
    {
        $query = ShippingAddress::with('order.user');

        // Lọc theo từ khóa
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('receiver_name', 'like', "%{$keyword}%")
                    ->orWhere('phone', 'like', "%{$keyword}%")
                    ->orWhere('address', 'like', "%{$keyword}%")
                    ->orWhereHas('order', function ($subQ) use ($keyword) {
                        $subQ->where('id', 'like', "%{$keyword}%");
                    });
            });
        }

        // Lọc theo tỉnh/thành phố
        if ($request->filled('province')) {
            $query->where('province', $request->province);
        }

        // Lọc theo quận/huyện
        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }

        // Lọc theo trạng thái đơn hàng
        if ($request->filled('order_status')) {
            $query->whereHas('order', function ($q) use ($request) {
                $q->where('status', $request->order_status);
            });
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
            case 'province':
                $query->orderBy('province')->orderBy('district');
                break;
            default:
                $query->latest();
        }

        $addresses = $query->paginate(15);

        // Lấy danh sách tỉnh/thành phố
        $provinces = ShippingAddress::select('province')
            ->distinct()
            ->whereNotNull('province')
            ->orderBy('province')
            ->pluck('province');

        // Thống kê
        $stats = [
            'total' => ShippingAddress::count(),
            'today' => ShippingAddress::whereDate('created_at', today())->count(),
            'provinces' => ShippingAddress::distinct('province')->count('province'),
        ];

        return view('admin.shipping-addresses.index', compact('addresses', 'provinces', 'stats'));
    }

    /**
     * Chi tiết địa chỉ giao hàng
     */
    public function show($id)
    {
        $address = ShippingAddress::with(['order.user', 'order.items.product'])->findOrFail($id);

        // Tìm các đơn hàng khác có địa chỉ tương tự
        $similarAddresses = ShippingAddress::with('order')
            ->where('id', '!=', $id)
            ->where(function ($query) use ($address) {
                $query->where('phone', $address->phone)
                    ->orWhere('address', 'like', '%' . $address->address . '%');
            })
            ->latest()
            ->take(5)
            ->get();

        return view('admin.shipping-addresses.show', compact('address', 'similarAddresses'));
    }

    /**
     * Form chỉnh sửa địa chỉ giao hàng
     */
    public function edit($id)
    {
        $address = ShippingAddress::with('order')->findOrFail($id);

        return view('admin.shipping-addresses.edit', compact('address'));
    }

    /**
     * Cập nhật địa chỉ giao hàng
     */
    public function update(Request $request, $id)
    {
        $address = ShippingAddress::findOrFail($id);

        $validated = $request->validate([
            'receiver_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'province' => 'required|string|max:100',
            'district' => 'nullable|string|max:100',
            'ward' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
        ]);

        try {
            $address->update($validated);

            return redirect()
                ->route('admin.shipping-addresses.show', $address->id)
                ->with('success', 'Cập nhật địa chỉ giao hàng thành công!');

        } catch (\Exception $e) {
            Log::error('Error updating shipping address: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật địa chỉ!');
        }
    }

    /**
     * Thống kê theo khu vực
     */
    public function statistics()
    {
        // Thống kê theo tỉnh/thành phố
        $byProvince = ShippingAddress::select('province', DB::raw('count(*) as total'))
            ->groupBy('province')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Thống kê theo tháng
        $byMonth = ShippingAddress::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('count(*) as total')
        )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        // Top người nhận
        $topReceivers = ShippingAddress::select('receiver_name', 'phone', DB::raw('count(*) as total'))
            ->groupBy('receiver_name', 'phone')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return view('admin.shipping-addresses.statistics', compact('byProvince', 'byMonth', 'topReceivers'));
    }

    /**
     * Export địa chỉ
     */
    public function export(Request $request)
    {
        $query = ShippingAddress::with('order');

        // Áp dụng filters giống như index
        if ($request->filled('province')) {
            $query->where('province', $request->province);
        }

        if ($request->filled('order_status')) {
            $query->whereHas('order', function ($q) use ($request) {
                $q->where('status', $request->order_status);
            });
        }

        $addresses = $query->get();

        // Tạo CSV
        $filename = 'shipping-addresses-' . date('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($addresses) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Headers
            fputcsv($file, [
                'Mã đơn hàng',
                'Người nhận',
                'Số điện thoại',
                'Địa chỉ',
                'Phường/Xã',
                'Quận/Huyện',
                'Tỉnh/TP',
                'Mã bưu điện',
                'Ngày tạo'
            ]);

            // Data
            foreach ($addresses as $address) {
                fputcsv($file, [
                    $address->order_id,
                    $address->receiver_name,
                    $address->phone,
                    $address->address,
                    $address->ward,
                    $address->district,
                    $address->province,
                    $address->postal_code,
                    $address->created_at->format('d/m/Y H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Tìm kiếm địa chỉ để tái sử dụng
     */
    public function search(Request $request)
    {
        $keyword = $request->get('q', '');

        $addresses = ShippingAddress::where(function ($query) use ($keyword) {
            $query->where('receiver_name', 'like', "%{$keyword}%")
                ->orWhere('phone', 'like', "%{$keyword}%")
                ->orWhere('address', 'like', "%{$keyword}%");
        })
            ->with('order')
            ->latest()
            ->limit(20)
            ->get()
            ->map(function ($address) {
                return [
                    'id' => $address->id,
                    'receiver_name' => $address->receiver_name,
                    'phone' => $address->phone,
                    'address' => $address->address,
                    'ward' => $address->ward,
                    'district' => $address->district,
                    'province' => $address->province,
                    'postal_code' => $address->postal_code,
                    'full_address' => "{$address->address}, {$address->ward}, {$address->district}, {$address->province}",
                    'order_id' => $address->order_id
                ];
            });

        return response()->json($addresses);
    }
}