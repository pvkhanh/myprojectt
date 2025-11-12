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

use App\Repositories\Contracts\BannerRepositoryInterface;
use App\Services\ImageService; // service quản lý ảnh chung
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class BannerService
{
    public function __construct(
        protected BannerRepositoryInterface $bannerRepository,
        protected ImageService $imageService, // dùng để lưu/ lấy ảnh
    ) {}

    public function createBanner(array $data)
    {
        try {
            DB::beginTransaction();

            $data['is_active'] = !empty($data['is_active']);

            // Upload image qua ImageService
            if (!empty($data['image'])) {
                $image = $this->imageService->upload($data['image'], 'banners'); 
                $data['image_id'] = $image->id; // lưu liên kết ảnh
            }

            $banner = $this->bannerRepository->create($data);

            DB::commit();
            return $banner;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("BannerService@createBanner error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }

    public function updateBanner(int $id, array $data)
    {
        try {
            DB::beginTransaction();

            $data['is_active'] = !empty($data['is_active']);

            if (!empty($data['image'])) {
                $image = $this->imageService->upload($data['image'], 'banners');
                $data['image_id'] = $image->id;
            }

            $banner = $this->bannerRepository->update($id, $data);

            DB::commit();
            return $banner;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("BannerService@updateBanner error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }

    public function deleteBanner(int $id)
    {
        try {
            $banner = $this->bannerRepository->findOrFail($id);

            // Xóa ảnh liên kết nếu có
            if (!empty($banner->image_id)) {
                $this->imageService->delete($banner->image_id);
            }

            $this->bannerRepository->delete($id);
        } catch (\Throwable $e) {
            Log::error("BannerService@deleteBanner error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }

    public function toggleStatus(int $id)
    {
        try {
            $banner = $this->bannerRepository->findOrFail($id);
            $newStatus = !$banner->is_active;
            $this->bannerRepository->update($id, ['is_active' => $newStatus]);
            return $newStatus;
        } catch (\Throwable $e) {
            Log::error("BannerService@toggleStatus error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }

    public function updatePositions(array $positions)
    {
        try {
            $this->bannerRepository->updatePositions($positions);
        } catch (\Throwable $e) {
            Log::error("BannerService@updatePositions error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }
}
