<?php

namespace App\Services;

use App\Repositories\Contracts\ProductReviewRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Enums\ReviewStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductReviewService
{
    public function __construct(
        protected ProductReviewRepositoryInterface $reviewRepository,
        protected ProductRepositoryInterface $productRepository,
        protected UserRepositoryInterface $userRepository
    ) {}

    /**
     * Lấy danh sách đánh giá
     */
    public function list(Request $request): array
    {
        try {
            $query = $this->reviewRepository->getModel()
                ->with(['product', 'user'])
                ->orderBy('created_at', 'desc');

            if ($request->filled('status')) $query->where('status', $request->status);
            if ($request->filled('rating')) $query->where('rating', $request->rating);
            if ($request->filled('product_id')) $query->where('product_id', $request->product_id);
            if ($request->filled('user_id')) $query->where('user_id', $request->user_id);

            if ($request->filled('keyword')) {
                $keyword = $request->keyword;
                $query->where(function ($q) use ($keyword) {
                    $q->where('comment', 'like', "%{$keyword}%")
                      ->orWhereHas('product', fn($q2) => $q2->where('name', 'like', "%{$keyword}%"))
                      ->orWhereHas('user', fn($q3) => $q3->where('username', 'like', "%{$keyword}%")
                                                         ->orWhere('email', 'like', "%{$keyword}%"));
                });
            }

            if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
            if ($request->filled('date_to')) $query->whereDate('created_at', '<=', $request->date_to);

            $sortBy = $request->get('sort_by', 'latest');
            switch ($sortBy) {
                case 'oldest': $query->orderBy('created_at','asc'); break;
                case 'rating_high': $query->orderBy('rating','desc'); break;
                case 'rating_low': $query->orderBy('rating','asc'); break;
                default: $query->orderBy('created_at','desc');
            }

            $reviews = $query->paginate(20)->withQueryString();

            $stats = [
                'total' => $this->reviewRepository->count(),
                'pending' => $this->reviewRepository->getModel()->where('status', ReviewStatus::Pending)->count(),
                'approved' => $this->reviewRepository->getModel()->where('status', ReviewStatus::Approved)->count(),
                'rejected' => $this->reviewRepository->getModel()->where('status', ReviewStatus::Rejected)->count(),
                'avg_rating' => round($this->reviewRepository->getModel()->avg('rating') ?? 0,1),
            ];

            $products = $this->productRepository->getModel()->select('id','name')->get();
            $users = $this->userRepository->getModel()->select('id','username','email')->get();

            return [
                'success' => true,
                'data' => compact('reviews','stats','products','users')
            ];
        } catch (\Exception $e) {
            return ['success'=>false,'message'=>'Có lỗi xảy ra khi tải danh sách đánh giá: '.$e->getMessage()];
        }
    }

    /**
     * Lấy review chi tiết
     */
    public function show(int $id): array
    {
        try {
            $review = $this->reviewRepository->findOrFail($id);
            $review->load(['product','user']);

            $userReviews = $this->reviewRepository->getByUser($review->user_id)
                ->where('id','!=',$review->id)
                ->take(5);

            $productReviews = $this->reviewRepository->getApprovedByProduct($review->product_id)
                ->where('id','!=',$review->id)
                ->take(5);

            return ['success'=>true,'data'=>compact('review','userReviews','productReviews')];
        } catch (\Exception $e) {
            return ['success'=>false,'message'=>'Có lỗi xảy ra: '.$e->getMessage()];
        }
    }

    /**
     * Cập nhật review
     */
    public function update(array $data, int $id): array
    {
        try {
            $review = $this->reviewRepository->findOrFail($id);
            $this->reviewRepository->update($review->id, $data);
            return ['success'=>true,'message'=>'Đã cập nhật đánh giá thành công!','data'=>$review];
        } catch (\Exception $e) {
            return ['success'=>false,'message'=>'Có lỗi xảy ra: '.$e->getMessage()];
        }
    }

    /**
     * Phê duyệt
     */
    public function approve(int $id): array
    {
        try {
            $review = $this->reviewRepository->findOrFail($id);
            $this->reviewRepository->update($review->id,['status'=>ReviewStatus::Approved]);
            return ['success'=>true,'message'=>'Đã phê duyệt đánh giá!'];
        } catch (\Exception $e) {
            return ['success'=>false,'message'=>'Có lỗi xảy ra: '.$e->getMessage()];
        }
    }

    /**
     * Từ chối
     */
    public function reject(int $id): array
    {
        try {
            $review = $this->reviewRepository->findOrFail($id);
            $this->reviewRepository->update($review->id,['status'=>ReviewStatus::Rejected]);
            return ['success'=>true,'message'=>'Đã từ chối đánh giá!'];
        } catch (\Exception $e) {
            return ['success'=>false,'message'=>'Có lỗi xảy ra: '.$e->getMessage()];
        }
    }

    /**
     * Xóa (soft delete)
     */
    public function destroy(int $id): array
    {
        try {
            $this->reviewRepository->delete($id);
            return ['success'=>true,'message'=>'Đã xóa đánh giá!'];
        } catch (\Exception $e) {
            return ['success'=>false,'message'=>'Có lỗi xảy ra: '.$e->getMessage()];
        }
    }

    /**
     * Bulk action
     */
    public function bulkAction(array $data): array
    {
        try {
            $ids = $data['ids'];
            $action = $data['action'];
            DB::beginTransaction();
            switch ($action) {
                case 'approve':
                    $this->reviewRepository->getModel()->whereIn('id',$ids)->update(['status'=>ReviewStatus::Approved]);
                    $message = 'Đã phê duyệt '.count($ids).' đánh giá!';
                    break;
                case 'reject':
                    $this->reviewRepository->getModel()->whereIn('id',$ids)->update(['status'=>ReviewStatus::Rejected]);
                    $message = 'Đã từ chối '.count($ids).' đánh giá!';
                    break;
                case 'delete':
                    $this->reviewRepository->getModel()->whereIn('id',$ids)->delete();
                    $message = 'Đã xóa '.count($ids).' đánh giá!';
                    break;
                default: throw new \Exception('Hành động không hợp lệ');
            }
            DB::commit();
            return ['success'=>true,'message'=>$message];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success'=>false,'message'=>'Có lỗi xảy ra: '.$e->getMessage()];
        }
    }

    /**
     * Trash, restore, forceDelete
     */
    public function trash(Request $request): array
    {
        try {
            $query = $this->reviewRepository->getModel()
                ->onlyTrashed()
                ->with(['product','user'])
                ->orderBy('deleted_at','desc');

            if ($request->filled('keyword')) {
                $keyword = $request->keyword;
                $query->where(function($q) use ($keyword) {
                    $q->where('comment','like',"%{$keyword}%")
                      ->orWhereHas('product', fn($q2)=>$q2->where('name','like',"%{$keyword}%"));
                });
            }

            $trashedReviews = $query->paginate(20)->withQueryString();
            $trashedCount = $this->reviewRepository->getModel()->onlyTrashed()->count();

            return ['success'=>true,'data'=>compact('trashedReviews','trashedCount')];
        } catch (\Exception $e) {
            return ['success'=>false,'message'=>'Có lỗi xảy ra: '.$e->getMessage()];
        }
    }

    public function restore(int $id): array
    {
        try {
            $review = $this->reviewRepository->getModel()->onlyTrashed()->findOrFail($id);
            $review->restore();
            return ['success'=>true,'message'=>'Đã khôi phục đánh giá!'];
        } catch (\Exception $e) {
            return ['success'=>false,'message'=>'Có lỗi xảy ra: '.$e->getMessage()];
        }
    }

    public function forceDelete(int $id): array
    {
        try {
            $review = $this->reviewRepository->getModel()->onlyTrashed()->findOrFail($id);
            $review->forceDelete();
            return ['success'=>true,'message'=>'Đã xóa vĩnh viễn đánh giá!'];
        } catch (\Exception $e) {
            return ['success'=>false,'message'=>'Có lỗi xảy ra: '.$e->getMessage()];
        }
    }
}
