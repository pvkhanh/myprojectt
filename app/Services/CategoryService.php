<?php

namespace App\Services;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryService
{
    public function __construct(protected CategoryRepositoryInterface $categoryRepository)
    {
    }

    public function createCategory(array $data)
    {
        try {
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }

            $data['level'] = !empty($data['parent_id'])
                ? $this->categoryRepository->findOrFail($data['parent_id'])->level + 1
                : 0;

            $parentId = $data['parent_id'] ?? null;

            if (empty($data['position'])) {
                $maxPosition = $this->categoryRepository->newQuery()
                    ->where('parent_id', $parentId)
                    ->max('position') ?? 0;
                $data['position'] = $maxPosition + 1;
            } else {
                $this->categoryRepository->newQuery()
                    ->where('parent_id', $parentId)
                    ->where('position', '>=', $data['position'])
                    ->increment('position');
            }

            return $this->categoryRepository->create($data);
        } catch (\Exception $e) {
            throw new \RuntimeException("Lỗi khi tạo danh mục: " . $e->getMessage());
        }
    }

    public function updateCategory(int $id, array $data)
    {
        try {
            $category = $this->categoryRepository->findOrFail($id);

            $data['level'] = !empty($data['parent_id'])
                ? $this->categoryRepository->findOrFail($data['parent_id'])->level + 1
                : 0;

            $parentId = $data['parent_id'] ?? null;

            if (isset($data['position']) && $data['position'] != $category->position) {
                $this->categoryRepository->newQuery()
                    ->where('parent_id', $parentId)
                    ->where('id', '!=', $id)
                    ->where('position', '>=', $data['position'])
                    ->increment('position');
            }

            return $this->categoryRepository->update($id, $data);
        } catch (ModelNotFoundException $e) {
            throw new \RuntimeException("Danh mục không tồn tại.");
        } catch (\Exception $e) {
            throw new \RuntimeException("Lỗi khi cập nhật danh mục: " . $e->getMessage());
        }
    }

    public function deleteCategory(int $id)
    {
        try {
            $category = $this->categoryRepository->findOrFail($id);

            if ($category->children()->count() > 0) {
                throw new \RuntimeException('Không thể xóa danh mục có danh mục con!');
            }
            if ($category->products()->count() > 0) {
                throw new \RuntimeException('Không thể xóa danh mục đang có sản phẩm!');
            }

            $parentId = $category->parent_id;

            $this->categoryRepository->delete($id);

            $categories = $this->categoryRepository->newQuery()
                ->where('parent_id', $parentId)
                ->orderBy('position', 'asc')
                ->get();

            $position = 1;
            foreach ($categories as $cat) {
                $this->categoryRepository->update($cat->id, ['position' => $position]);
                $position++;
            }

            return true;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function bulkDelete(array $ids)
    {
        $deleted = 0;
        $errors = [];

        foreach ($ids as $id) {
            try {
                $category = $this->categoryRepository->findOrFail((int)$id);
                if ($category->children()->count() > 0 || $category->products()->count() > 0) {
                    $errors[] = "Danh mục '{$category->name}' có danh mục con hoặc sản phẩm";
                    continue;
                }
                $this->categoryRepository->delete($id);
                $deleted++;
            } catch (\Exception $e) {
                $errors[] = "Lỗi khi xóa ID {$id}: " . $e->getMessage();
            }
        }

        // Sắp xếp lại danh mục root
        $this->categoryRepository->newQuery()
            ->whereNull('parent_id')
            ->orderBy('position', 'asc')
            ->get()
            ->each(function ($cat, $i) {
                $this->categoryRepository->update($cat->id, ['position' => $i + 1]);
            });

        return ['deleted' => $deleted, 'errors' => $errors];
    }

    public function updatePosition(array $positions)
    {
        foreach ($positions as $id => $position) {
            $this->categoryRepository->update((int)$id, ['position' => (int)$position]);
        }
        return true;
    }
}