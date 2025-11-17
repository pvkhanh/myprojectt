<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProductService;

class ProductController extends Controller
{
    protected ProductService $service;

    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }

    // ================== LIST PRODUCTS (FOR USER) ==================
    public function index(Request $request)
    {
        $filters = $request->only(['keyword', 'category_id', 'price_min', 'price_max', 'sort_by']);
        $perPage = $request->input('per_page', 15);

        // Chỉ lấy sản phẩm public: active hoặc pending_approval hoặc out_of_stock (nếu muốn)
        $filters['status'] = ['active', 'pending_approval'];

        $data = $this->service->index($filters, $perPage);

        // Format product
        $products = $data['products']->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'price' => (float)$product->price,
                'status' => $product->status,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ];
        });

        $meta = [
            'current_page' => $data['products']->currentPage(),
            'last_page' => $data['products']->lastPage(),
            'per_page' => $data['products']->perPage(),
            'total' => $data['products']->total(),
            'next_page_url' => $data['products']->nextPageUrl(),
            'prev_page_url' => $data['products']->previousPageUrl(),
        ];

        return response()->json([
            'success' => true,
            'data' => $products,
            'meta' => $meta,
            'filters' => [
                'categories' => $data['categories'],
                'statuses' => ['active', 'pending_approval'],
            ],
            'stats' => [
                'total' => $data['totalProducts'],
                'active' => $data['activeProducts'],
                'hidden' => $data['hiddenProducts'] ?? 0,
                'out_of_stock' => $data['outOfStock'] ?? 0,
            ],
        ]);
    }

    // ================== SHOW PRODUCT ==================
    public function show(int $id)
    {
        try {
            $product = $this->service->show($id);

            // Chỉ trả về nếu sản phẩm public
            if (!in_array($product->status, ['active', 'pending_approval'])) {
                return response()->json(['success' => false, 'message' => 'Sản phẩm không tồn tại'], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'description' => $product->description,
                    'price' => (float)$product->price,
                    'status' => $product->status,
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm'], 404);
        }
    }
}
