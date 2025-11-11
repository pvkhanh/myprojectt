<?php

namespace App\Services;

use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Models\Product;
use App\Models\Image;
use App\Enums\ProductStatus;
use Illuminate\Support\Facades\Log;
use Exception;

class ProductService
{
    protected ProductRepositoryInterface $productRepo;
    protected CategoryRepositoryInterface $categoryRepo;

    public function __construct(
        ProductRepositoryInterface $productRepo,
        CategoryRepositoryInterface $categoryRepo
    ) {
        $this->productRepo = $productRepo;
        $this->categoryRepo = $categoryRepo;
    }

    // ================== INDEX ==================
    public function index(array $filters, int $perPage)
    {
        try {
            $products = $this->productRepo->searchPaginated($filters, $perPage);

            $totalProducts = $this->productRepo->countByStatus(ProductStatus::Active->value)
                + $this->productRepo->countByStatus(ProductStatus::Draft->value)
                + $this->productRepo->countByStatus(ProductStatus::Inactive->value);

            $activeProducts = $this->productRepo->countByStatus(ProductStatus::Active->value);
            $hiddenProducts = $this->productRepo->countByStatus(ProductStatus::Inactive->value);
            $outOfStock = $this->productRepo->countOutOfStock();

            $categories = $this->categoryRepo->getRootCategories();
            $statuses = ProductStatus::cases();

            return compact(
                'products', 'categories', 'totalProducts',
                'activeProducts', 'outOfStock', 'hiddenProducts', 'statuses'
            );
        } catch (Exception $e) {
            Log::error('ProductService index error: '.$e->getMessage());
            throw $e;
        }
    }

    // ================== CREATE ==================
    public function create(?int $selectedCategoryId = null)
    {
        try {
            $categories = $this->categoryRepo->getTree();
            $statuses = ProductStatus::cases();
            $images = Image::all();

            return compact('categories', 'statuses', 'images', 'selectedCategoryId');
        } catch (Exception $e) {
            Log::error('ProductService create error: '.$e->getMessage());
            throw $e;
        }
    }

    // ================== STORE ==================
    public function store(array $validated)
    {
        try {
            // --- Tạo slug nếu chưa có ---
            if (empty($validated['slug']) && isset($validated['name'])) {
                $slug = strtolower($validated['name']);
                $slug = preg_replace('/[\s]+/', '-', $slug);
                $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
                $validated['slug'] = trim($slug, '-');
            }

            // --- Kiểm tra trùng ---
            $existing = Product::where('name', $validated['name'])
                ->orWhere('slug', $validated['slug'])
                ->first();

            if ($existing) {
                throw new Exception('Sản phẩm đã tồn tại (tên hoặc slug trùng)!');
            }

            // --- Xử lý image_ids ---
            $imageIds = $validated['image_ids'] ?? [];
            if (is_string($imageIds)) $imageIds = array_filter(explode(',', $imageIds));
            $validated['image_ids'] = $imageIds;
            $validated['primary_image_id'] = $validated['primary_image_id'] ?? ($imageIds[0] ?? null);

            return $this->productRepo->create($validated);
        } catch (Exception $e) {
            Log::error('ProductService store error: '.$e->getMessage(), $validated ?? []);
            throw $e;
        }
    }

    // ================== SHOW ==================
    public function show(int $id)
    {
        try {
            $product = $this->productRepo->find($id);
            $product->load(['categories', 'images', 'variants.stockItems', 'reviews.user']);
            return $product;
        } catch (Exception $e) {
            Log::error("ProductService show error for ID $id: ".$e->getMessage());
            throw $e;
        }
    }

    // ================== EDIT ==================
    public function edit(int $id)
    {
        try {
            $product = $this->productRepo->find($id);
            $product->load(['categories', 'images']);
            $categories = $this->categoryRepo->getTree();
            $statuses = ProductStatus::cases();
            $selectedImageIds = $product->images->pluck('id')->toArray();
            $primaryImage = $product->images->where('pivot.is_main', true)->first();

            return compact('product', 'categories', 'statuses', 'selectedImageIds', 'primaryImage');
        } catch (Exception $e) {
            Log::error("ProductService edit error for ID $id: ".$e->getMessage());
            throw $e;
        }
    }

    // ================== UPDATE ==================
    public function update(int $id, array $validated)
    {
        try {
            $imageIds = $validated['image_ids'] ?? [];
            if (is_string($imageIds)) $imageIds = array_filter(explode(',', $imageIds));
            $validated['image_ids'] = $imageIds;
            $validated['primary_image_id'] = $validated['primary_image_id'] ?? ($imageIds[0] ?? null);

            return $this->productRepo->updateAndReturn($id, $validated);
        } catch (Exception $e) {
            Log::error("ProductService update error for ID $id: ".$e->getMessage(), $validated ?? []);
            throw $e;
        }
    }

    // ================== TRASH ==================
    public function trash()
    {
        try {
            return Product::onlyTrashed()->with('categories')->paginate(10);
        } catch (Exception $e) {
            Log::error('ProductService trash error: '.$e->getMessage());
            throw $e;
        }
    }

    // ================== DESTROY ==================
    public function destroy(int $id)
    {
        try {
            return $this->productRepo->delete($id);
        } catch (Exception $e) {
            Log::error("ProductService destroy error for ID $id: ".$e->getMessage());
            throw $e;
        }
    }

    // ================== FORCE DESTROY ==================
    public function forceDestroy(int $id)
    {
        try {
            return $this->productRepo->forceDelete($id);
        } catch (Exception $e) {
            Log::error("ProductService forceDestroy error for ID $id: ".$e->getMessage());
            throw $e;
        }
    }

    // ================== RESTORE ==================
    public function restore(int $id)
    {
        try {
            return $this->productRepo->restore($id);
        } catch (Exception $e) {
            Log::error("ProductService restore error for ID $id: ".$e->getMessage());
            throw $e;
        }
    }

    // ================== BULK DELETE ==================
    public function bulkDelete(array $ids)
    {
        try {
            return $this->productRepo->bulkDelete($ids);
        } catch (Exception $e) {
            Log::error('ProductService bulkDelete error: '.$e->getMessage(), $ids);
            throw $e;
        }
    }

    // ================== BULK UPDATE STATUS ==================
    public function bulkUpdateStatus(array $ids, string $status)
    {
        try {
            return $this->productRepo->bulkUpdateStatus($ids, $status);
        } catch (Exception $e) {
            Log::error('ProductService bulkUpdateStatus error: '.$e->getMessage(), compact('ids','status'));
            throw $e;
        }
    }

    // ================== RESTORE ALL ==================
    public function restoreAll()
    {
        try {
            return $this->productRepo->restoreAll();
        } catch (Exception $e) {
            Log::error('ProductService restoreAll error: '.$e->getMessage());
            throw $e;
        }
    }

    // ================== FORCE DELETE ALL ==================
    public function forceDeleteAll()
    {
        try {
            return $this->productRepo->forceDeleteAll();
        } catch (Exception $e) {
            Log::error('ProductService forceDeleteAll error: '.$e->getMessage());
            throw $e;
        }
    }
}