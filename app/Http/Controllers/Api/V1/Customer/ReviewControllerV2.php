<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Danh sách review của product
     */
    public function productReviews($productId, Request $request)
    {
        $query = ProductReview::with('user')
            ->where('product_id', $productId)
            ->where('is_approved', true);

        // Filter theo rating
        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }

        // Sort
        $sortBy = $request->input('sort_by', 'latest');
        switch ($sortBy) {
            case 'helpful':
                $query->orderBy('helpful_count', 'desc');
                break;
            case 'rating_high':
                $query->orderBy('rating', 'desc');
                break;
            case 'rating_low':
                $query->orderBy('rating', 'asc');
                break;
            default:
                $query->latest();
        }

        $reviews = $query->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $reviews->map(function ($review) {
                return [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'is_verified_purchase' => $review->is_verified_purchase,
                    'helpful_count' => $review->helpful_count,
                    'user' => [
                        'name' => $review->user->name,
                        'avatar' => $review->user->avatar ?? null,
                    ],
                    'created_at' => $review->created_at->format('Y-m-d H:i:s'),
                ];
            }),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ],
            'summary' => [
                'average_rating' => ProductReview::where('product_id', $productId)
                    ->where('is_approved', true)
                    ->avg('rating'),
                'total_reviews' => ProductReview::where('product_id', $productId)
                    ->where('is_approved', true)
                    ->count(),
                'rating_distribution' => [
                    5 => ProductReview::where('product_id', $productId)->where('rating', 5)->count(),
                    4 => ProductReview::where('product_id', $productId)->where('rating', 4)->count(),
                    3 => ProductReview::where('product_id', $productId)->where('rating', 3)->count(),
                    2 => ProductReview::where('product_id', $productId)->where('rating', 2)->count(),
                    1 => ProductReview::where('product_id', $productId)->where('rating', 1)->count(),
                ]
            ]
        ]);
    }

    /**
     * Review của user hiện tại
     */
    public function myReviews()
    {
        $reviews = ProductReview::with('product.images')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $reviews->map(function ($review) {
                return [
                    'id' => $review->id,
                    'product' => [
                        'id' => $review->product->id,
                        'name' => $review->product->name,
                        'slug' => $review->product->slug,
                        'image' => $review->product->main_image_url,
                    ],
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'is_approved' => $review->is_approved,
                    'helpful_count' => $review->helpful_count,
                    'created_at' => $review->created_at->format('Y-m-d H:i:s'),
                ];
            }),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ]
        ]);
    }

    /**
     * Tạo review mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Kiểm tra user đã mua sản phẩm chưa
        $hasPurchased = Order::where('user_id', Auth::id())
            ->whereHas('orderItems', function ($q) use ($validated) {
                $q->where('product_id', $validated['product_id']);
            })
            ->where('status', \App\Enums\OrderStatus::Completed)
            ->exists();

        // Kiểm tra đã review chưa
        $existingReview = ProductReview::where('user_id', Auth::id())
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã đánh giá sản phẩm này rồi'
            ], 400);
        }

        $review = ProductReview::create([
            'user_id' => Auth::id(),
            'product_id' => $validated['product_id'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'is_verified_purchase' => $hasPurchased,
            'is_approved' => false, // Chờ admin duyệt
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đánh giá của bạn đang chờ duyệt',
            'data' => [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'is_verified_purchase' => $review->is_verified_purchase,
            ]
        ], 201);
    }

    /**
     * Cập nhật review
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'sometimes|string|min:10|max:1000',
        ]);

        $review = ProductReview::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $review->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật đánh giá',
            'data' => [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
            ]
        ]);
    }

    /**
     * Xóa review
     */
    public function destroy($id)
    {
        $review = ProductReview::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa đánh giá'
        ]);
    }

    /**
     * Đánh dấu review hữu ích
     */
    public function markHelpful($id)
    {
        $review = ProductReview::findOrFail($id);
        $review->increment('helpful_count');

        return response()->json([
            'success' => true,
            'message' => 'Cảm ơn phản hồi của bạn',
            'helpful_count' => $review->helpful_count
        ]);
    }

    /**
     * Kiểm tra user có thể review không
     */
    public function canReview($productId)
    {
        // Kiểm tra đã mua
        $hasPurchased = Order::where('user_id', Auth::id())
            ->whereHas('orderItems', function ($q) use ($productId) {
                $q->where('product_id', $productId);
            })
            ->where('status', \App\Enums\OrderStatus::Completed)
            ->exists();

        // Kiểm tra đã review
        $hasReviewed = ProductReview::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->exists();

        return response()->json([
            'success' => true,
            'can_review' => $hasPurchased && !$hasReviewed,
            'has_purchased' => $hasPurchased,
            'has_reviewed' => $hasReviewed,
        ]);
    }
}
