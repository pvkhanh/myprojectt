<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\BlogStatus;
use App\Models\Scopes\BlogScopes;

class Blog extends Model
{
    use HasFactory, SoftDeletes, BlogScopes;

    protected $fillable = [
        'author_id',
        'title',
        'slug',
        'content',
        'status'
    ];

    protected $casts = [
        'status' => BlogStatus::class,
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Các ảnh liên quan đến blog qua bảng imageables
     */
    public function images()
    {
        return $this->morphToMany(Image::class, 'imageable')
                    ->withPivot('is_main', 'position')
                    ->orderBy('position');
    }

    /**
     * Ảnh chính của blog
     */
    public function primaryImage()
    {
        return $this->morphToMany(Image::class, 'imageable')
                    ->withPivot('is_main', 'position')
                    ->wherePivot('is_main', true)
                    ->orderBy('position')
                    ->limit(1);
    }

    /**
     * Các danh mục liên quan đến blog
     */
    public function categories()
    {
        return $this->morphToMany(Category::class, 'categoryable');
    }
}