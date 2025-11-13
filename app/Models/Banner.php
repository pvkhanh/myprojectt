<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Banner extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'banners';

    /**
     * Các trường có thể gán hàng loạt.
     */
    protected $fillable = [
        'title',
        'url',
        'type',
        'position',
        'is_active',
        'image_id',
        'start_at',
        'end_at',
    ];

    /**
     * Các trường cần cast kiểu dữ liệu.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'start_at'  => 'datetime',
        'end_at'    => 'datetime',
        'position'  => 'integer',
    ];

    // ================== RELATIONSHIPS ==================

    /**
     * Banner có một ảnh.
     */
    public function image()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }

    // ================== SCOPES ==================

    /**
     * Scope chỉ lấy banner đang hoạt động.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope chỉ lấy banner theo loại.
     */
    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope chỉ lấy banner có thời gian hiển thị hợp lệ.
     */
    public function scopeScheduled($query)
    {
        $now = Carbon::now();
        return $query->where(function ($q) use ($now) {
            $q->whereNull('start_at')->orWhere('start_at', '<=', $now);
        })->where(function ($q) use ($now) {
            $q->whereNull('end_at')->orWhere('end_at', '>=', $now);
        });
    }

    // ================== MUTATORS ==================

    /**
     * Mutator để set trạng thái is_active.
     */
    public function setIsActiveAttribute($value)
    {
        $this->attributes['is_active'] = (bool) $value;
    }

    /**
     * Mutator để set vị trí.
     */
    public function setPositionAttribute($value)
    {
        $this->attributes['position'] = $value ?? 0;
    }
}
