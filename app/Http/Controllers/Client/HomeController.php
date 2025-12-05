<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        // Lấy tất cả category, không filter is_active
        $categories = Category::all();

        // Lấy sản phẩm mới nhất, active() vẫn dùng scope nếu bạn đã định nghĩa
        $products = Product::latest()->limit(12)->get();

        return view('client.home.index', compact('categories', 'products'));
    }
}
