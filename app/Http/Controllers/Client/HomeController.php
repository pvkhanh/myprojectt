<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        // Lấy danh mục, có thể đếm số sản phẩm mỗi danh mục
        $categories = Category::withCount('products')
            ->orderBy('name', 'asc')
            ->get();

        // Lấy sản phẩm nổi bật (featured), ví dụ: status = 'active' và is_featured = 1
        $featuredProducts = Product::where('status', 'active')
            ->orderByDesc('created_at') // hoặc orderByDesc('id')
            ->limit(12)
            ->get();



        // Lấy số lượng wishlist & cart của user đang đăng nhập
        $wishlistCount = auth()->check() ? auth()->user()->wishlistItems()->count() : 0;
        $cartCount = auth()->check() ? auth()->user()->cartItems()->count() : 0;

        return view('client.home', compact(
            'categories',
            'featuredProducts',
            'wishlistCount',
            'cartCount'
        ));
    }
}