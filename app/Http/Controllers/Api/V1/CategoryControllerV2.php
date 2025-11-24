<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Resources\Api\ProductResource;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Danh sách categories (tree structure)
     */
    public function index()
    {
        $categories = Category::whereNull('parent_id')
            ->with('children')
            ->orderBy('position')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories->map(function ($category) {
                return $this->formatCategory($category);
            })
        ]);
    }

    /**
     * Chi tiết category
     */
    public function show($slug)
    {
        $category = Category::with('children', 'parent')
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $this->formatCategory($category, true)
        ]);
    }

    /**
     * Lấy products của category
     */
    public function products($slug, Request $request)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $query = $category->products()
            ->with(['images', 'categories', 'variants.stockItems'])
            ->where('status', \App\Enums\ProductStatus::Active);

        // Filter giá
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort
        switch ($request->input('sort_by', 'latest')) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'popular':
                $query->withCount('orders')->orderBy('orders_count', 'desc');
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate($request->input('per_page', 12));

        return ProductResource::collection($products);
    }

    /**
     * Format category data
     */
    private function formatCategory($category, $includeDetails = false)
    {
        $data = [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
            'image' => $category->image ? asset('storage/' . $category->image) : null,
            'position' => $category->position,
            'products_count' => $category->products()->count(),
        ];

        if ($includeDetails) {
            $data['parent'] = $category->parent ? [
                'id' => $category->parent->id,
                'name' => $category->parent->name,
                'slug' => $category->parent->slug,
            ] : null;
        }

        if ($category->relationLoaded('children') && $category->children->isNotEmpty()) {
            $data['children'] = $category->children->map(function ($child) {
                return $this->formatCategory($child);
            });
        }

        return $data;
    }

    /**
     * Categories phổ biến (có nhiều sản phẩm nhất)
     */
    public function popular(Request $request)
    {
        $limit = $request->input('limit', 6);

        $categories = Category::withCount('products')
            ->having('products_count', '>', 0)
            ->orderBy('products_count', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories->map(function ($category) {
                return $this->formatCategory($category);
            })
        ]);
    }
}
