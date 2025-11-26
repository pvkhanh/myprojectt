<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Enums\ProductStatus;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Danh sách categories
     */
    public function index()
    {
        try {
            $categories = Category::with(['parent', 'children'])
                ->whereNull('parent_id') // Chỉ lấy root categories
                ->orderBy('position')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $categories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'description' => $category->description,
                        // 'image' => $category->image ? asset('storage/' . $category->image) : null,
                        'position' => $category->position,
                        'children' => $category->children->map(function ($child) {
                            return [
                                'id' => $child->id,
                                'name' => $child->name,
                                'slug' => $child->slug,
                                'position' => $child->position,
                            ];
                        })
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải danh mục',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Chi tiết category
     */
    public function show($id)
    {
        try {
            $category = Category::with(['parent', 'children'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description,
                    //'image' => $category->image ? asset('storage/' . $category->image) : null,
                    'position' => $category->position,
                    'parent' => $category->parent ? [
                        'id' => $category->parent->id,
                        'name' => $category->parent->name,
                        'slug' => $category->parent->slug,
                    ] : null,
                    'children' => $category->children->map(function ($child) {
                        return [
                            'id' => $child->id,
                            'name' => $child->name,
                            'slug' => $child->slug,
                        ];
                    }),
                    'products_count' => $category->products()
                        ->where('status', ProductStatus::Active)
                        ->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy danh mục',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Lấy sản phẩm theo category
     */
    public function products(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);
            $perPage = $request->get('per_page', 15);

            $query = Product::whereHas('categories', function ($q) use ($id) {
                $q->where('categories.id', $id);
            })
                ->where('status', ProductStatus::Active)
                // ->where('stock_quantity', '>', 0)
                ->whereHas('variants.stockItems', function ($q) {
                    $q->where('quantity', '>', 0);
                })
                ->with(['categories', 'images', 'reviews']);

            // Filter by price
            if ($request->has('min_price')) {
                $query->where('price', '>=', $request->min_price);
            }
            if ($request->has('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'newest');
            switch ($sortBy) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'bestseller':
                    $query->withCount('orderItems')->orderBy('order_items_count', 'desc');
                    break;
                default:
                    $query->latest();
            }

            $products = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                ],
                'data' => ProductResource::collection($products),
                'meta' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải sản phẩm',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}