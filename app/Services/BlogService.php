<?php

namespace App\Services;

use App\Repositories\Contracts\BlogRepositoryInterface;
use App\Models\Image;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class BlogService
{
    public function __construct(protected BlogRepositoryInterface $blogRepository) {}

    public function createBlog(array $data)
    {
        try {
            DB::beginTransaction();

            $data['author_id'] = auth()->id();
            $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

            $blog = $this->blogRepository->create($data);

            if (!empty($data['categories'])) {
                $blog->categories()->sync($data['categories']);
            }

            if (!empty($data['primary_image'])) {
                $path = $data['primary_image']->store('blogs', 'public');
                $image = Image::create(['path' => $path]);
                $blog->images()->attach($image->id, ['is_main' => true]);
            }

            DB::commit();
            return $blog;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("BlogService@createBlog error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }

    public function updateBlog(int $id, array $data)
    {
        try {
            DB::beginTransaction();

            $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
            $blog = $this->blogRepository->update($id, $data);

            if (isset($data['categories'])) {
                $blog->categories()->sync($data['categories']);
            }

            if (!empty($data['primary_image'])) {
                $path = $data['primary_image']->store('blogs', 'public');
                $image = Image::create(['path' => $path]);

                // Remove old primary image
                $blog->images()->updateExistingPivot(
                    $blog->images()->wherePivot('is_main', true)->pluck('id')->toArray(),
                    ['is_main' => false]
                );

                $blog->images()->attach($image->id, ['is_main' => true]);
            }

            DB::commit();
            return $blog;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("BlogService@updateBlog error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }

    public function deleteBlog(int $id)
    {
        try {
            $this->blogRepository->delete($id);
        } catch (\Throwable $e) {
            Log::error("BlogService@deleteBlog error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }

    public function bulkDelete(array $ids)
    {
        $deleted = 0;
        $errors = [];
        foreach ($ids as $id) {
            try {
                $this->deleteBlog($id);
                $deleted++;
            } catch (\Throwable $e) {
                $errors[] = "Erreur blog ID {$id}: " . $e->getMessage();
            }
        }
        return compact('deleted', 'errors');
    }

    public function bulkUpdateStatus(array $ids, string $status)
    {
        $updated = 0;
        $errors = [];
        foreach ($ids as $id) {
            try {
                $this->blogRepository->update($id, ['status' => $status]);
                $updated++;
            } catch (\Throwable $e) {
                $errors[] = "Erreur blog ID {$id}: " . $e->getMessage();
            }
        }
        return compact('updated', 'errors');
    }
}