<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\ReviewStatus;
use App\Models\Scopes\ProductReviewScopes;

class ProductReview extends Model
{
    use HasFactory, SoftDeletes, ProductReviewScopes;

    protected $fillable = [
        'product_id',
        'user_id',
        'rating',
        'comment',
        'status',
    ];

    protected $casts = [
        'rating' => 'integer',
        'status' => ReviewStatus::class,
    ];

    /* =======================
     |  RELATIONSHIPS
     ======================= */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        // 👉 Dùng withTrashed() để tránh lỗi khi user bị xóa mềm
        return $this->belongsTo(User::class)->withTrashed();
    }

    /* =======================
     |  ACCESSORS
     ======================= */
    public function getUserFullNameAttribute(): string
    {
        return trim(($this->user?->first_name ?? '') . ' ' . ($this->user?->last_name ?? '')) ?: 'Ẩn danh';
    }

    public function getUserEmailAttribute(): string
    {
        return $this->user?->email ?? 'Ẩn danh';
    }

    public function getUserNameAttribute(): string
    {
        return $this->user?->username ?? 'Ẩn danh';
    }

    public function getUserAvatarAttribute(): string
    {
        return $this->user?->avatar_url ?? asset('images/default-avatar.png');
    }
}