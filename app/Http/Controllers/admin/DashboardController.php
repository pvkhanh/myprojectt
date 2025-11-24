<?php

// namespace App\Http\Controllers\Admin;

// use App\Http\Controllers\Controller;
// use App\Repositories\Contracts\ProductRepositoryInterface;
// use App\Repositories\Contracts\CategoryRepositoryInterface;
// use App\Repositories\Contracts\OrderRepositoryInterface;
// use App\Repositories\Contracts\UserRepositoryInterface;

// class DashboardController extends Controller
// {
//     protected $products, $categories, $orders, $users;

//     public function __construct(
//         ProductRepositoryInterface $products,
//         CategoryRepositoryInterface $categories,
//         OrderRepositoryInterface $orders,
//         UserRepositoryInterface $users
//     ) {
//         $this->products = $products;
//         $this->categories = $categories;
//         $this->orders = $orders;
//         $this->users = $users;
//     }

//     public function index()
//     {
//         return view('admin.dashboard.index', [
//             'productsCount' => $this->products->all()->count(),
//             'categoriesCount' => $this->categories->all()->count(),
//             'ordersCount' => $this->orders->all()->count(),
//             'usersCount' => $this->users->all()->count(),
//         ]);
//     }
// }



namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $products, $categories, $orders, $users;

    public function __construct(
        ProductRepositoryInterface $products,
        CategoryRepositoryInterface $categories,
        OrderRepositoryInterface $orders,
        UserRepositoryInterface $users
    ) {
        $this->products = $products;
        $this->categories = $categories;
        $this->orders = $orders;
        $this->users = $users;
    }

    public function index()
    {
        $now = Carbon::now();

        // Tổng số
        $productsCount = $this->products->all()->count();
        $categoriesCount = $this->categories->all()->count();
        $ordersCount = $this->orders->all()->count();
        $usersCount = $this->users->all()->count();

        // Đơn hàng theo trạng thái
        $pendingOrders = $this->orders->withStatus('pending')->count();
        $completedOrders = $this->orders->withStatus('completed')->count();
        $cancelledOrders = $this->orders->withStatus('cancelled')->count();
        $paidOrders = $this->orders->withStatus('paid')->count();
        $shippedOrders = $this->orders->withStatus('shipped')->count();

        // Doanh thu
        $totalRevenue = $this->orders->completed()->sum('total_amount');
        $monthlyRevenue = $this->orders->getRevenueByMonth($now->year)
            ->firstWhere('month', $now->month)->revenue ?? 0;

        // Biểu đồ doanh thu (theo tháng trong năm hiện tại)
        $revenueData = $this->orders->getRevenueByMonth($now->year);
        $revenueChart = [];
        for ($m = 1; $m <= 12; $m++) {
            $revenueChart[$m] = $revenueData->firstWhere('month', $m)->revenue ?? 0;
        }

        // Đơn hàng gần đây
        $recentOrders = $this->orders->getRecentOrders(10);

        return view('admin.dashboard.index', compact(
            'productsCount',
            'categoriesCount',
            'ordersCount',
            'usersCount',
            'pendingOrders',
            'completedOrders',
            'cancelledOrders',
            'paidOrders',
            'shippedOrders',
            'totalRevenue',
            'monthlyRevenue',
            'revenueChart',
            'recentOrders'
        ));
    }
}
