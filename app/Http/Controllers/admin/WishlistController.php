<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\WishlistRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WishlistController extends Controller
{
    protected $wishlistRepo;
    protected $userRepo;
    protected $productRepo;

    public function __construct(
        WishlistRepositoryInterface $wishlistRepo,
        UserRepositoryInterface $userRepo,
        ProductRepositoryInterface $productRepo
    ) {
        $this->wishlistRepo = $wishlistRepo;
        $this->userRepo = $userRepo;
        $this->productRepo = $productRepo;
    }

    /**
     * Display a listing of wishlists
     */
    public function index(Request $request)
    {
        $query = $this->wishlistRepo->getModel()
            ->with(['user', 'product', 'variant']);

        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($qq) use ($search) {
                    $qq->where('email', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                })
                ->orWhereHas('product', function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%");
                });
            });
        }

        // User filter
        if ($userId = $request->input('user_id')) {
            $query->byUser($userId);
        }

        // Product filter
        if ($productId = $request->input('product_id')) {
            $query->forProduct($productId);
        }

        // Date range filter
        if ($from = $request->input('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        // Sort
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $wishlists = $query->paginate(20)->withQueryString();

        // Statistics
        $stats = [
            'total' => $this->wishlistRepo->getModel()->count(),
            'today' => $this->wishlistRepo->getModel()->whereDate('created_at', today())->count(),
            'this_week' => $this->wishlistRepo->getModel()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => $this->wishlistRepo->getModel()->whereMonth('created_at', now()->month)->count(),
            'total_users' => $this->wishlistRepo->getModel()->distinct('user_id')->count('user_id'),
            'total_products' => $this->wishlistRepo->getModel()->distinct('product_id')->count('product_id'),
        ];

        // Top wishlisted products
        $topProducts = DB::table('wishlists')
            ->select('product_id', DB::raw('count(*) as total'))
            ->groupBy('product_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('admin.wishlists.index', compact('wishlists', 'stats', 'topProducts'));
    }

    /**
     * Display wishlist details for a specific user
     */
    public function show($id)
    {
        $wishlist = $this->wishlistRepo->getModel()
            ->with(['user', 'product.images', 'variant'])
            ->findOrFail($id);

        return view('admin.wishlists.show', compact('wishlist'));
    }

    /**
     * Show wishlists by user
     */
    public function userWishlists($userId)
    {
        $user = $this->userRepo->find($userId);
        
        if (!$user) {
            return redirect()->route('admin.wishlists.index')
                ->with('error', 'Không tìm thấy người dùng');
        }

        $wishlists = $this->wishlistRepo->getByUser($userId);

        return view('admin.wishlists.user', compact('user', 'wishlists'));
    }

    /**
     * Show wishlists by product
     */
    public function productWishlists($productId)
    {
        $product = $this->productRepo->find($productId);
        
        if (!$product) {
            return redirect()->route('admin.wishlists.index')
                ->with('error', 'Không tìm thấy sản phẩm');
        }

        $wishlists = $this->wishlistRepo->forProduct($productId);

        return view('admin.wishlists.product', compact('product', 'wishlists'));
    }

    /**
     * Remove wishlist entry
     */
    public function destroy($id)
    {
        try {
            $wishlist = $this->wishlistRepo->find($id);
            
            if (!$wishlist) {
                return redirect()->back()->with('error', 'Không tìm thấy mục yêu thích');
            }

            $this->wishlistRepo->delete($id);

            return redirect()->back()->with('success', 'Đã xóa khỏi danh sách yêu thích');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete wishlists
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:wishlists,id'
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->ids as $id) {
                $this->wishlistRepo->delete($id);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa ' . count($request->ids) . ' mục yêu thích'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export wishlists to Excel
     */
    public function export(Request $request)
    {
        // Implement export logic here using Laravel Excel
        // This is a placeholder
        return response()->download('path/to/export.xlsx');
    }

    /**
     * Get statistics
     */
    public function statistics()
    {
        $stats = [
            'total' => $this->wishlistRepo->getModel()->count(),
            'by_day' => $this->wishlistRepo->getModel()
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->whereBetween('created_at', [now()->subDays(30), now()])
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'top_products' => DB::table('wishlists')
                ->join('products', 'wishlists.product_id', '=', 'products.id')
                ->select('products.name', DB::raw('count(*) as total'))
                ->groupBy('products.id', 'products.name')
                ->orderByDesc('total')
                ->limit(10)
                ->get(),
            'top_users' => DB::table('wishlists')
                ->join('users', 'wishlists.user_id', '=', 'users.id')
                ->select('users.email', DB::raw('count(*) as total'))
                ->groupBy('users.id', 'users.email')
                ->orderByDesc('total')
                ->limit(10)
                ->get(),
        ];

        return view('admin.wishlists.statistics', compact('stats'));
    }
}