<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Banner;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Load banners hiển thị ở trang chủ
        $banners = Banner::where('is_active', 1)
            ->orderBy('position', 'asc')
            ->get();

        // Load danh mục nổi bật (tuỳ app của bạn)
        $categories = Category::orderBy('name')
            ->take(10)
            ->get();


        // Load sản phẩm mới nhất
        $latestProducts = Product::latest()
            ->where('status', 'active')
            ->take(12)
            ->get();

        return view('client.home', compact(
            'banners',
            'categories',
            'latestProducts'
        ));
    }
}
