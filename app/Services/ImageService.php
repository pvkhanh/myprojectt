<?php

namespace App\Services;

use App\Models\Image;
use App\Models\Imageable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image as InterventionImage;
use App\Http\Requests\ImageRequest;

class ImageService
{
    /**
     * Lấy danh sách ảnh với filter, sort, stats
     */
    public function list(ImageRequest $request)
    {
        try {
            $query = Image::query()->latest();

            // Filter theo type
            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            // Filter theo active
            if ($request->filled('is_active')) {
                $query->where('is_active', $request->is_active);
            }

            // Search
            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('path', 'like', '%' . $request->search . '%')
                      ->orWhere('alt_text', 'like', '%' . $request->search . '%');
                });
            }

            // Sort
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $images = $query->paginate(15)->withQueryString();

            // Stats
            $stats = [
                'total' => Image::count(),
                'active' => Image::where('is_active', true)->count(),
                'by_type' => Image::select('type', DB::raw('count(*) as count'))
                                ->groupBy('type')
                                ->pluck('count', 'type')
                                ->toArray(),
            ];

            return [
                'success' => true,
                'data' => [
                    'images' => $images,
                    'stats' => $stats,
                ],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải danh sách ảnh: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Tạo nhiều ảnh
     */
    public function store(ImageRequest $request)
    {
        DB::beginTransaction();
        try {
            $uploadedImages = [];

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $file) {
                    $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

                    // Optimize image nếu có
                    if ($request->has('optimize')) {
                        $image = InterventionImage::make($file);
                        if ($image->width() > 1920 || $image->height() > 1920) {
                            $image->resize(1920, 1920, function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            });
                        }
                        $image->save(storage_path('app/public/images/' . $filename), 85);
                        $path = 'images/' . $filename;
                    } else {
                        $path = $file->storeAs('images', $filename, 'public');
                    }

                    $imageModel = Image::create([
                        'path' => $path,
                        'type' => $request->type,
                        'alt_text' => $request->alt_text[$index] ?? null,
                        'is_active' => true,
                    ]);

                    $uploadedImages[] = $imageModel;
                }
            }

            DB::commit();

            return [
                'success' => true,
                'message' => count($uploadedImages) . ' ảnh đã được tải lên thành công!',
                'data' => $uploadedImages,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lưu ảnh: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Cập nhật ảnh
     */
    public function update(ImageRequest $request, Image $image)
    {
        DB::beginTransaction();
        try {
            $data = [
                'type' => $request->type,
                'alt_text' => $request->alt_text,
                'is_active' => $request->boolean('is_active'),
            ];

            if ($request->hasFile('image')) {
                if ($image->path && Storage::disk('public')->exists($image->path)) {
                    Storage::disk('public')->delete($image->path);
                }

                $file = $request->file('image');
                $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('images', $filename, 'public');
                $data['path'] = $path;
            }

            $image->update($data);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Cập nhật ảnh thành công!',
                'data' => $image,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật ảnh: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Xóa ảnh
     */
    public function destroy(Image $image)
    {
        DB::beginTransaction();
        try {
            $usageCount = Imageable::where('image_id', $image->id)->count();

            if ($usageCount > 0) {
                return [
                    'success' => false,
                    'message' => "Không thể xóa ảnh vì đang được sử dụng ở {$usageCount} nơi.",
                ];
            }

            if ($image->path && Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }

            $image->delete();
            DB::commit();

            return [
                'success' => true,
                'message' => 'Xóa ảnh thành công!',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa ảnh: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Bulk action
     */
    public function bulkAction(ImageRequest $request)
    {
        DB::beginTransaction();
        try {
            $images = Image::whereIn('id', $request->image_ids)->get();
            $count = 0;

            foreach ($images as $image) {
                switch ($request->action) {
                    case 'delete':
                        $usageCount = Imageable::where('image_id', $image->id)->count();
                        if ($usageCount == 0) {
                            if ($image->path && Storage::disk('public')->exists($image->path)) {
                                Storage::disk('public')->delete($image->path);
                            }
                            $image->delete();
                            $count++;
                        }
                        break;

                    case 'activate':
                        $image->update(['is_active' => true]);
                        $count++;
                        break;

                    case 'deactivate':
                        $image->update(['is_active' => false]);
                        $count++;
                        break;

                    case 'change_type':
                        $image->update(['type' => $request->new_type]);
                        $count++;
                        break;
                }
            }

            DB::commit();

            $actionNames = [
                'delete' => 'xóa',
                'activate' => 'kích hoạt',
                'deactivate' => 'vô hiệu hóa',
                'change_type' => 'thay đổi loại',
            ];

            return [
                'success' => true,
                'message' => "Đã {$actionNames[$request->action]} {$count} ảnh thành công!",
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thực hiện bulk action: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Upload ảnh API 1 ảnh
     */
    public function upload(ImageRequest $request)
    {
        DB::beginTransaction();
        try {
            $file = $request->file('image');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('images', $filename, 'public');

            $image = Image::create([
                'path' => $path,
                'type' => $request->type,
                'is_active' => true,
            ]);

            DB::commit();

            return [
                'success' => true,
                'data' => $image,
                'message' => 'Tải ảnh thành công!',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi upload ảnh: ' . $e->getMessage(),
            ];
        }
    }
}
