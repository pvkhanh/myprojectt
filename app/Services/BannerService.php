<?php

// namespace App\Services;

// use App\Repositories\Contracts\BannerRepositoryInterface;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\DB;

// class BannerService
// {
//     public function __construct(protected BannerRepositoryInterface $bannerRepository) {}

//     public function createBanner(array $data)
//     {
//         try {
//             DB::beginTransaction();

//             // Xử lý is_active mặc định
//             $data['is_active'] = !empty($data['is_active']);

//             // Upload image
//             if (!empty($data['image'])) {
//                 $path = $data['image']->store('banners', 'public');
//                 $data['image_path'] = $path;
//             }

//             $banner = $this->bannerRepository->create($data);

//             DB::commit();
//             return $banner;
//         } catch (\Throwable $e) {
//             DB::rollBack();
//             Log::error("BannerService@createBanner error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
//             throw $e;
//         }
//     }

//     public function updateBanner(int $id, array $data)
//     {
//         try {
//             DB::beginTransaction();

//             $data['is_active'] = !empty($data['is_active']);

//             if (!empty($data['image'])) {
//                 $path = $data['image']->store('banners', 'public');
//                 $data['image_path'] = $path;
//             }

//             $banner = $this->bannerRepository->update($id, $data);

//             DB::commit();
//             return $banner;
//         } catch (\Throwable $e) {
//             DB::rollBack();
//             Log::error("BannerService@updateBanner error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
//             throw $e;
//         }
//     }

//     public function deleteBanner(int $id)
//     {
//         try {
//             $this->bannerRepository->delete($id);
//         } catch (\Throwable $e) {
//             Log::error("BannerService@deleteBanner error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
//             throw $e;
//         }
//     }

//     public function bulkDelete(array $ids)
//     {
//         $deleted = 0;
//         $errors = [];

//         foreach ($ids as $id) {
//             try {
//                 $this->deleteBanner($id);
//                 $deleted++;
//             } catch (\Throwable $e) {
//                 $errors[] = "Erreur banner ID {$id}: " . $e->getMessage();
//             }
//         }

//         return compact('deleted', 'errors');
//     }

//     public function toggleStatus(int $id)
//     {
//         try {
//             $banner = $this->bannerRepository->findOrFail($id);
//             $newStatus = !$banner->is_active;
//             $this->bannerRepository->update($id, ['is_active' => $newStatus]);
//             return $newStatus;
//         } catch (\Throwable $e) {
//             Log::error("BannerService@toggleStatus error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
//             throw $e;
//         }
//     }

//     public function updatePositions(array $positions)
//     {
//         try {
//             $this->bannerRepository->updatePositions($positions);
//         } catch (\Throwable $e) {
//             Log::error("BannerService@updatePositions error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
//             throw $e;
//         }
//     }
// }




namespace App\Services;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class BannerService
{
    protected Banner $banner;

    public function __construct(Banner $banner)
    {
        $this->banner = $banner;
    }

    /**
     * Lấy tất cả banners dưới dạng Builder (dễ filter & paginate)
     */
    public function getAll(): Builder
    {
        return $this->banner->query()->whereNull('deleted_at');
    }

    /**
     * Lấy tất cả banners đang active
     */
    public function getActive(): Builder
    {
        return $this->banner->query()
            ->whereNull('deleted_at')
            ->where('is_active', true);
    }

    /**
     * Lấy tất cả banners có start_at/end_at và đang trong khoảng thời gian hiển thị
     */
    public function visible(): Builder
    {
        $now = now();
        return $this->banner->query()
            ->whereNull('deleted_at')
            ->where('is_active', true)
            ->where(function($q) use ($now) {
                $q->whereNull('start_at')->orWhere('start_at', '<=', $now);
            })
            ->where(function($q) use ($now) {
                $q->whereNull('end_at')->orWhere('end_at', '>=', $now);
            });
    }

    /**
     * Lấy banners đã lên lịch (có start_at trong tương lai hoặc end_at trong tương lai)
     */
    public function scheduled(): Builder
    {
        $now = now();
        return $this->banner->query()
            ->whereNull('deleted_at')
            ->where(function($q) use ($now) {
                $q->where('start_at', '>', $now)
                  ->orWhere('end_at', '>', $now);
            });
    }

    /**
     * Tạo data mặc định cho form (create/edit)
     */
    public function create(): array
    {
        // Nếu bạn có thư viện hình ảnh hoặc defaults khác
        return [
            'images' => [], // ví dụ lấy danh sách images
        ];
    }

    /**
     * Lưu banner mới
     */
    public function store(array $data): Banner
    {
        $banner = $this->banner->create([
            'title'     => $data['title'],
            'url'       => $data['url'] ?? null,
            'type'      => $data['type'] ?? null,
            'position'  => $data['position'] ?? 0,
            'is_active' => $data['is_active'] ?? false,
            'image_id'  => $data['image_id'] ?? null,
            'start_at'  => $data['start_at'] ?? null,
            'end_at'    => $data['end_at'] ?? null,
            'image_path'=> $data['image_file'] ? $data['image_file']->store('banners', 'public') : null,
        ]);

        return $banner;
    }

    /**
     * Cập nhật banner
     */
    public function update(int $id, array $data): Banner
    {
        $banner = $this->findOrFail($id);

        if (!empty($data['image_file'])) {
            $data['image_path'] = $data['image_file']->store('banners', 'public');
        }

        $banner->update($data);

        return $banner;
    }

    /**
     * Xóa banner
     */
    public function delete(int $id): void
    {
        $banner = $this->findOrFail($id);
        $banner->delete();
    }

    /**
     * Tìm banner hoặc fail
     */
    public function findOrFail(int $id): Banner
    {
        return $this->banner->query()->findOrFail($id);
    }
}
