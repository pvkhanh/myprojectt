<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ReviewResource;
use App\Models\ProductReview;
use App\Models\Order;
use App\Models\Product;
use App\Enums\OrderStatus;
use App\Enums\ReviewStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    /**
     * Danh sách đánh giá của user
     */
    public function myReviews(Request $request)
    {
        try {
            $userId = auth('api')->id();
            $perPage = $request->get('per_page', 15);

            $reviews = ProductReview::where('user_id', $userId)
                ->with(['product', 'order'])
                ->latest()
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => ReviewResource::collection($reviews),
                'meta' => [
                    'current_page' => $reviews->currentPage(),
                    'last_page' => $reviews->lastPage(),
                    'per_page' => $reviews->perPage(),
                    'total' => $reviews->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải danh sách đánh giá',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kiểm tra user có thể đánh giá sản phẩm không
     */
    public function canReview($productId)
    {
        try {
            $userId = auth('api')->id();

            // Kiểm tra đã mua sản phẩm chưa
            $hasPurchased = Order::where('user_id', $userId)
                ->where('status', OrderStatus::Completed)
                ->whereHas('orderItems', function($q) use ($productId) {
                    $q->where('product_id', $productId);
                })
                ->exists();

            if (!$hasPurchased) {
                return response()->json([
                    'success' => true,
                    'can_review' => false,
                    'reason' => 'Bạn chưa mua sản phẩm này'
                ]);
            }

            // Kiểm tra đã đánh giá chưa
            $hasReviewed = ProductReview::where('user_id', $userId)
                ->where('product_id', $productId)
                ->exists();

            if ($hasReviewed) {
                return response()->json([
                    'success' => true,
                    'can_review' => false,
                    'reason' => 'Bạn đã đánh giá sản phẩm này rồi'
                ]);
            }

            return response()->json([
                'success' => true,
                'can_review' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể kiểm tra',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tạo đánh giá mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            $userId = auth('api')->id();

            // Verify order belongs to user
            $order = Order::where('id', $request->order_id)
                ->where('user_id', $userId)
                ->where('status', OrderStatus::Completed)
                ->firstOrFail();

            // Verify product in order
            $productInOrder = $order->orderItems()
                ->where('product_id', $request->product_id)
                ->exists();

            if (!$productInOrder) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sản phẩm không có trong đơn hàng này'
                ], 400);
            }

            // Check if already reviewed
            $existingReview = ProductReview::where('user_id', $userId)
                ->where('product_id', $request->product_id)
                ->exists();

            if ($existingReview) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã đánh giá sản phẩm này rồi'
                ], 400);
            }

            // Upload images
            $imageUrls = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('reviews', 'public');
                    $imageUrls[] = $path;
                }
            }

            // Create review
            $review = ProductReview::create([
                'user_id' => $userId,
                'product_id' => $request->product_id,
                'order_id' => $request->order_id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'images' => !empty($imageUrls) ? json_encode($imageUrls) : null,
                'status' => ReviewStatus::Pending,
                'is_verified_purchase' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đánh giá của bạn đã được gửi và đang chờ duyệt',
                'data' => new ReviewResource($review->load(['product', 'user', 'order']))
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tạo đánh giá',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật đánh giá
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'comment' => 'sometimes|required|string|min:10|max:1000',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            $userId = auth('api')->id();
            
            $review = ProductReview::where('id', $id)
                ->where('user_id', $userId)
                ->firstOrFail();

            // Chỉ cho phép sửa review đang pending hoặc approved
            if ($review->status === ReviewStatus::Rejected) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể sửa đánh giá đã bị từ chối'
                ], 400);
            }

            $updateData = [];

            if ($request->has('rating')) {
                $updateData['rating'] = $request->rating;
            }

            if ($request->has('comment')) {
                $updateData['comment'] = $request->comment;
            }

            // Upload new images
            if ($request->hasFile('images')) {
                // Delete old images
                if ($review->images) {
                    $oldImages = json_decode($review->images, true);
                    foreach ($oldImages as $oldImage) {
                        Storage::disk('public')->delete($oldImage);
                    }
                }

                $imageUrls = [];
                foreach ($request->file('images') as $image) {
                    $path = $image->store('reviews', 'public');
                    $imageUrls[] = $path;
                }
                $updateData['images'] = json_encode($imageUrls);
            }

            // Reset status to pending when updated
            $updateData['status'] = ReviewStatus::Pending;

            $review->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật đánh giá',
                'data' => new ReviewResource($review->fresh())
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể cập nhật đánh giá',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa đánh giá
     */
    public function destroy($id)
    {
        try {
            $userId = auth('api')->id();
            
            $review = ProductReview::where('id', $id)
                ->where('user_id', $userId)
                ->firstOrFail();

            // Delete images
            if ($review->images) {
                $images = json_decode($review->images, true);
                foreach ($images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            $review->delete();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa đánh giá'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa đánh giá',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Đánh dấu đánh giá hữu ích
     */
    public function markHelpful($id)
    {
        try {
            $review = ProductReview::findOrFail($id);
            
            // Tăng helpful count
            $review->increment('helpful_count');

            return response()->json([
                'success' => true,
                'message' => 'Cảm ơn phản hồi của bạn',
                'helpful_count' => $review->helpful_count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể thực hiện',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}