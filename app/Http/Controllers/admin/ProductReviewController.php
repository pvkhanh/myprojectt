<?php

// namespace App\Http\Controllers\Admin;

// use App\Http\Controllers\Controller;
// use App\Repositories\Contracts\ProductReviewRepositoryInterface;
// use App\Repositories\Contracts\ProductRepositoryInterface;
// use App\Repositories\Contracts\UserRepositoryInterface;
// use App\Enums\ReviewStatus;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;

// class ProductReviewController extends Controller
// {
//     public function __construct(
//         private ProductReviewRepositoryInterface $reviewRepository,
//         private ProductRepositoryInterface $productRepository,
//         private UserRepositoryInterface $userRepository
//     ) {}

//     /**
//      * Hiển thị danh sách đánh giá
//      */
//     public function index(Request $request)
//     {
//         $query = $this->reviewRepository->getModel()
//             ->with(['product', 'user'])
//             ->orderBy('created_at', 'desc');

//         // Lọc theo trạng thái
//         if ($request->filled('status')) {
//             $query->where('status', $request->status);
//         }

//         // Lọc theo rating
//         if ($request->filled('rating')) {
//             $query->where('rating', $request->rating);
//         }

//         // Lọc theo sản phẩm
//         if ($request->filled('product_id')) {
//             $query->where('product_id', $request->product_id);
//         }

//         // Lọc theo user
//         if ($request->filled('user_id')) {
//             $query->where('user_id', $request->user_id);
//         }

//         // Tìm kiếm
//         if ($request->filled('keyword')) {
//             $keyword = $request->keyword;
//             $query->where(function ($q) use ($keyword) {
//                 $q->where('comment', 'like', "%{$keyword}%")
//                     ->orWhereHas('product', function ($q2) use ($keyword) {
//                         $q2->where('name', 'like', "%{$keyword}%");
//                     })
//                     ->orWhereHas('user', function ($q3) use ($keyword) {
//                         $q3->where('username', 'like', "%{$keyword}%")
//                             ->orWhere('email', 'like', "%{$keyword}%");
//                     });
//             });
//         }

//         // Lọc theo khoảng thời gian
//         if ($request->filled('date_from')) {
//             $query->whereDate('created_at', '>=', $request->date_from);
//         }
//         if ($request->filled('date_to')) {
//             $query->whereDate('created_at', '<=', $request->date_to);
//         }

//         // Sắp xếp
//         $sortBy = $request->get('sort_by', 'latest');
//         switch ($sortBy) {
//             case 'oldest':
//                 $query->orderBy('created_at', 'asc');
//                 break;
//             case 'rating_high':
//                 $query->orderBy('rating', 'desc');
//                 break;
//             case 'rating_low':
//                 $query->orderBy('rating', 'asc');
//                 break;
//             default:
//                 $query->orderBy('created_at', 'desc');
//         }

//         $reviews = $query->paginate(20)->withQueryString();

//         // Thống kê
//         $stats = [
//             'total' => $this->reviewRepository->count(),
//             'pending' => $this->reviewRepository->getModel()->where('status', ReviewStatus::Pending)->count(),
//             'approved' => $this->reviewRepository->getModel()->where('status', ReviewStatus::Approved)->count(),
//             'rejected' => $this->reviewRepository->getModel()->where('status', ReviewStatus::Rejected)->count(),
//             'avg_rating' => round($this->reviewRepository->getModel()->avg('rating') ?? 0, 1),
//         ];

//         // Danh sách sản phẩm và users cho filter
//         $products = $this->productRepository->getModel()->select('id', 'name')->get();
//         $users = $this->userRepository->getModel()->select('id', 'username', 'email')->get();

//         return view('admin.reviews.index', compact(
//             'reviews',
//             'stats',
//             'products',
//             'users'
//         ));
//     }

//     /**
//      * Hiển thị chi tiết đánh giá
//      */
//     public function show($id)
//     {
//         $review = $this->reviewRepository->findOrFail($id);
//         $review->load(['product', 'user']);

//         // Lấy các đánh giá khác của user này
//         $userReviews = $this->reviewRepository->getByUser($review->user_id)
//             ->where('id', '!=', $review->id)
//             ->take(5);

//         // Lấy các đánh giá khác của sản phẩm này
//         $productReviews = $this->reviewRepository->getApprovedByProduct($review->product_id)
//             ->where('id', '!=', $review->id)
//             ->take(5);

//         return view('admin.reviews.show', compact('review', 'userReviews', 'productReviews'));
//     }

//     /**
//      * Hiển thị form chỉnh sửa đánh giá
//      */
//     public function edit($id)
//     {
//         $review = $this->reviewRepository->findOrFail($id);
//         $review->load(['product', 'user']);

//         $products = $this->productRepository->getModel()->select('id', 'name')->get();
//         $statuses = ReviewStatus::cases();

//         return view('admin.reviews.edit', compact('review', 'products', 'statuses'));
//     }

//     /**
//      * Cập nhật đánh giá
//      */
//     public function update(Request $request, $id)
//     {
//         $request->validate([
//             'rating' => 'required|integer|min:1|max:5',
//             'comment' => 'required|string|min:10|max:1000',
//             'status' => 'required|in:' . implode(',', array_column(ReviewStatus::cases(), 'value')),
//         ], [
//             'rating.required' => 'Vui lòng chọn số sao đánh giá',
//             'rating.min' => 'Đánh giá tối thiểu là 1 sao',
//             'rating.max' => 'Đánh giá tối đa là 5 sao',
//             'comment.required' => 'Vui lòng nhập nội dung đánh giá',
//             'comment.min' => 'Nội dung đánh giá phải có ít nhất 10 ký tự',
//             'comment.max' => 'Nội dung đánh giá không được quá 1000 ký tự',
//             'status.required' => 'Vui lòng chọn trạng thái',
//         ]);

//         try {
//             $review = $this->reviewRepository->findOrFail($id);

//             $this->reviewRepository->update($review->id, [
//                 'rating' => $request->rating,
//                 'comment' => $request->comment,
//                 'status' => $request->status,
//             ]);

//             return redirect()->route('admin.reviews.show', $review->id)
//                 ->with('success', 'Đã cập nhật đánh giá thành công!');
//         } catch (\Exception $e) {
//             return redirect()->back()
//                 ->withInput()
//                 ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
//         }
//     }

//     /**
//      * Phê duyệt đánh giá
//      */
//     public function approve($id)
//     {
//         try {
//             $review = $this->reviewRepository->findOrFail($id);
//             $this->reviewRepository->update($review->id, [
//                 'status' => ReviewStatus::Approved
//             ]);

//             return redirect()->back()->with('success', 'Đã phê duyệt đánh giá!');
//         } catch (\Exception $e) {
//             return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
//         }
//     }

//     /**
//      * Từ chối đánh giá
//      */
//     public function reject($id)
//     {
//         try {
//             $review = $this->reviewRepository->findOrFail($id);
//             $this->reviewRepository->update($review->id, [
//                 'status' => ReviewStatus::Rejected
//             ]);

//             return redirect()->back()->with('success', 'Đã từ chối đánh giá!');
//         } catch (\Exception $e) {
//             return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
//         }
//     }

//     /**
//      * Xóa đánh giá (soft delete)
//      */
//     public function destroy($id)
//     {
//         try {
//             $this->reviewRepository->delete($id);
//             return redirect()->route('admin.reviews.index')
//                 ->with('success', 'Đã xóa đánh giá!');
//         } catch (\Exception $e) {
//             return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
//         }
//     }

//     /**
//      * Thao tác hàng loạt
//      */
//     public function bulkAction(Request $request)
//     {
//         $request->validate([
//             'ids' => 'required|array',
//             'action' => 'required|in:approve,reject,delete',
//         ]);

//         try {
//             $ids = $request->ids;
//             $action = $request->action;

//             DB::beginTransaction();

//             switch ($action) {
//                 case 'approve':
//                     $this->reviewRepository->getModel()
//                         ->whereIn('id', $ids)
//                         ->update(['status' => ReviewStatus::Approved]);
//                     $message = 'Đã phê duyệt ' . count($ids) . ' đánh giá!';
//                     break;

//                 case 'reject':
//                     $this->reviewRepository->getModel()
//                         ->whereIn('id', $ids)
//                         ->update(['status' => ReviewStatus::Rejected]);
//                     $message = 'Đã từ chối ' . count($ids) . ' đánh giá!';
//                     break;

//                 case 'delete':
//                     $this->reviewRepository->getModel()
//                         ->whereIn('id', $ids)
//                         ->delete();
//                     $message = 'Đã xóa ' . count($ids) . ' đánh giá!';
//                     break;

//                 default:
//                     throw new \Exception('Hành động không hợp lệ');
//             }

//             DB::commit();

//             return response()->json([
//                 'success' => true,
//                 'message' => $message
//             ]);
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
//             ], 500);
//         }
//     }

//     /**
//      * Thùng rác
//      */
//     public function trash(Request $request)
//     {
//         $query = $this->reviewRepository->getModel()
//             ->onlyTrashed()
//             ->with(['product', 'user'])
//             ->orderBy('deleted_at', 'desc');

//         // Tìm kiếm trong thùng rác
//         if ($request->filled('keyword')) {
//             $keyword = $request->keyword;
//             $query->where(function ($q) use ($keyword) {
//                 $q->where('comment', 'like', "%{$keyword}%")
//                     ->orWhereHas('product', function ($q2) use ($keyword) {
//                         $q2->where('name', 'like', "%{$keyword}%");
//                     });
//             });
//         }

//         $trashedReviews = $query->paginate(20)->withQueryString();
//         $trashedCount = $this->reviewRepository->getModel()->onlyTrashed()->count();

//         return view('admin.reviews.trash', compact('trashedReviews', 'trashedCount'));
//     }

//     /**
//      * Khôi phục đánh giá
//      */
//     public function restore($id)
//     {
//         try {
//             $review = $this->reviewRepository->getModel()->onlyTrashed()->findOrFail($id);
//             $review->restore();

//             return redirect()->back()->with('success', 'Đã khôi phục đánh giá!');
//         } catch (\Exception $e) {
//             return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
//         }
//     }

//     /**
//      * Xóa vĩnh viễn
//      */
//     public function forceDelete($id)
//     {
//         try {
//             $review = $this->reviewRepository->getModel()->onlyTrashed()->findOrFail($id);
//             $review->forceDelete();

//             return redirect()->back()->with('success', 'Đã xóa vĩnh viễn đánh giá!');
//         } catch (\Exception $e) {
//             return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
//         }
//     }
// }


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductReviewRequest;
use App\Services\ProductReviewService;
use App\Models\ProductReview;

class ProductReviewController extends Controller
{
    public function __construct(private ProductReviewService $service){}

    public function index(ProductReviewRequest $request)
    {
        $result = $this->service->list($request);
        return $result['success']
            ? view('admin.reviews.index', $result['data'])
            : back()->with('error',$result['message']);
    }

    public function show($id)
    {
        $result = $this->service->show($id);
        return $result['success']
            ? view('admin.reviews.show',$result['data'])
            : back()->with('error',$result['message']);
    }

    public function edit($id)
    {
        $review = $this->service->show($id)['data']['review'] ?? null;
        $products = $this->service->list(request())['data']['products'] ?? [];
        $statuses = \App\Enums\ReviewStatus::cases();
        return view('admin.reviews.edit',compact('review','products','statuses'));
    }

    public function update(ProductReviewRequest $request, $id)
    {
        $result = $this->service->update($request->validated(),$id);
        return $result['success']
            ? redirect()->route('admin.reviews.show',$id)->with('success',$result['message'])
            : back()->withInput()->with('error',$result['message']);
    }

    public function approve($id)
    {
        $result = $this->service->approve($id);
        return redirect()->back()->with($result['success'] ? 'success':'error',$result['message']);
    }

    public function reject($id)
    {
        $result = $this->service->reject($id);
        return redirect()->back()->with($result['success'] ? 'success':'error',$result['message']);
    }

    public function destroy($id)
    {
        $result = $this->service->destroy($id);
        return redirect()->route('admin.reviews.index')->with($result['success'] ? 'success':'error',$result['message']);
    }

    public function bulkAction(ProductReviewRequest $request)
    {
        $result = $this->service->bulkAction($request->validated());
        return response()->json($result);
    }

    public function trash(ProductReviewRequest $request)
    {
        $result = $this->service->trash($request);
        return $result['success']
            ? view('admin.reviews.trash', $result['data'])
            : back()->with('error',$result['message']);
    }

    public function restore($id)
    {
        $result = $this->service->restore($id);
        return redirect()->back()->with($result['success'] ? 'success':'error',$result['message']);
    }

    public function forceDelete($id)
    {
        $result = $this->service->forceDelete($id);
        return redirect()->back()->with($result['success'] ? 'success':'error',$result['message']);
    }
}
