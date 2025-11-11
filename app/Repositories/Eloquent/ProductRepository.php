<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Repositories\BaseRepository;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    protected function model(): string
    {
        return Product::class;
    }

    /**
     * Tạo sản phẩm mới kèm sync categories và images
     */
    public function create(array $data): Product
    {
        $imageIds = $data['image_ids'] ?? [];
        $primaryImageId = $data['primary_image_id'] ?? ($imageIds[0] ?? null);

        $product = $this->model->create($data);

        if (!empty($data['category_ids'])) {
            $product->categories()->sync($data['category_ids']);
        }

        if (!empty($imageIds)) {
            $syncData = [];
            foreach ($imageIds as $position => $imageId) {
                $syncData[$imageId] = [
                    'is_main' => $imageId == $primaryImageId,
                    'position' => $position + 1
                ];
            }
            $product->images()->sync($syncData);
        }

        return $product;
    }

    /**
     * Cập nhật sản phẩm kèm sync categories và images
     */
    public function updateAndReturn(int $id, array $data): Product
    {
        $product = $this->model->findOrFail($id);

        $imageIds = $data['image_ids'] ?? [];
        $primaryImageId = $data['primary_image_id'] ?? ($imageIds[0] ?? null);

        $product->update($data); // Update dữ liệu cơ bản

        if (isset($data['category_ids'])) {
            $product->categories()->sync($data['category_ids']);
        }

        if (!empty($imageIds)) {
            $syncData = [];
            foreach ($imageIds as $position => $imageId) {
                $syncData[$imageId] = [
                    'is_main' => $imageId == $primaryImageId,
                    'position' => $position + 1
                ];
            }
            $product->images()->sync($syncData);
        } else {
            $product->images()->detach();
        }

        // Cập nhật updated_at ngay cả khi dữ liệu không thay đổi
        $product->touch();

        return $product->fresh(); // Load lại quan hệ
    }

    /**
     * Tìm sản phẩm
     */
    public function find(int $id): Product
    {
        return $this->model->findOrFail($id);
    }

    public function delete(int $id): bool
    {
        $product = $this->find($id);
        return $product->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return $this->model->whereIn('id', $ids)->delete();
    }

    public function bulkUpdateStatus(array $ids, string $status): int
    {
        return $this->model->whereIn('id', $ids)->update([
            'status' => $status,
            'updated_at' => now() // ✅ đảm bảo updated_at thay đổi
        ]);
    }

    public function getActive(): Collection
    {
        return $this->model->where('status', ProductStatus::Active->value)->get();
    }

    public function search(string $keyword): Collection
    {
        return $this->model->where(function ($q) use ($keyword) {
            $q->where('name', 'like', "%$keyword%")
              ->orWhere('description', 'like', "%$keyword%")
              ->orWhereHas('variants', fn($q2) => $q2->where('sku', 'like', "%$keyword%"));
        })->get();
    }

    public function priceBetween(float $min, float $max): Collection
    {
        return $this->model->whereBetween('price', [$min, $max])->get();
    }

    public function byCategory(int $categoryId): Collection
    {
        return $this->model->whereHas('categories', fn($q) => $q->where('categories.id', $categoryId))->get();
    }

    public function hasVariants(): Collection
    {
        return $this->model->has('variants')->get();
    }

    public function searchPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->newQuery();

        // Keyword
        if (!empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%$keyword%")
                  ->orWhere('description', 'like', "%$keyword%")
                  ->orWhereHas('variants', fn($q2) => $q2->where('sku', 'like', "%$keyword%"));
            });
        }

        // Category filter
        if (!empty($filters['category_id'])) {
            $query->whereHas('categories', fn($q) => $q->where('categories.id', $filters['category_id']));
        }

        // Status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Price range filter
        if (!empty($filters['price_range'])) {
            [$min, $max] = explode('-', str_replace(' ', '', $filters['price_range']));
            $query->whereBetween('price', [(float) $min, (float) $max]);
        }

        // Sort
        switch ($filters['sort_by'] ?? 'latest') {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->orderBy('updated_at', 'desc');
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function findBySlug(string $slug): ?Product
    {
        return $this->firstBy('slug', $slug);
    }

    public function countByStatus(string $status): int
    {
        return $this->model->where('status', $status)->count();
    }

    public function countOutOfStock(): int
    {
        return $this->model->with('variants.stockItems')->get()->filter(fn($p) => $p->total_stock <= 0)->count();
    }

    public function getOutOfStock(): Collection
    {
        return $this->model->with('variants.stockItems')->get()->filter(fn($p) => $p->total_stock <= 0);
    }

    public function restoreAll(): int
    {
        return $this->getModel()->onlyTrashed()->restore();
    }

    public function forceDeleteAll(): int
    {
        return $this->getModel()->onlyTrashed()->forceDelete();
    }

    public function restore(int $id): bool
    {
        $product = $this->getModel()->onlyTrashed()->find($id);
        return $product ? $product->restore() : false;
    }

    public function forceDelete(int $id): bool
    {
        $product = $this->getModel()->onlyTrashed()->find($id);
        return $product ? $product->forceDelete() : false;
    }
}