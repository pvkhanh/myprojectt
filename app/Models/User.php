<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Scopes\UserScopes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, UserScopes;

    protected $fillable = [
        'email',
        'username',
        'password',
        'first_name',
        'last_name',
        'phone',
        'gender',
        'birthday',
        'bio',
        'role',
        'is_active',
        'email_verified_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'birthday' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */

    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function orderItems()
    {
        return $this->hasManyThrough(OrderItem::class, Order::class);
    }
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }
    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
    public function blogs()
    {
        return $this->hasMany(Blog::class, 'author_id');
    }
    public function mailRecipients()
    {
        return $this->hasMany(MailRecipient::class);
    }

    /**
     * Polymorphic relation for images.
     * - morphOne: The main avatar.
     * - morphMany: Other images (if needed).
     */
    // public function avatar()
    // {
    //     return $this->morphOne(Image::class, 'imageable')
    //         ->where('type', 'avatar')
    //         ->latestOfMany();
    // }

    // public function images()
    // {
    //     return $this->morphMany(Image::class, 'imageable')->orderForDisplay();
    // }
    // Lấy tất cả ảnh của user
    // public function images(): BelongsToMany
    // {
    //     return $this->belongsToMany(Image::class, 'imageables', 'imageable_id', 'image_id')
    //         ->wherePivot('imageable_type', self::class)
    //         ->withPivot('is_main', 'position')
    //         ->withTimestamps();
    // }
    // // Lấy ảnh chính (avatar)
    // public function avatar()
    // {
    //     return $this->images()->wherePivot('is_main', true)->first();
    // }

    // // Thêm avatar mới (ví dụ trong repository)
    // public function setAvatar(Image $image)
    // {
    //     // Xóa avatar cũ
    //     $this->images()->updateExistingPivot(
    //         $this->images()->wherePivot('is_main', true)->pluck('id')->toArray(),
    //         ['is_main' => false]
    //     );

    //     // Thêm ảnh mới
    //     $this->images()->attach($image->id, ['is_main' => true, 'imageable_type' => self::class]);
    // }


    /**
     * =====================
     * 🖼️ IMAGE RELATIONS
     * =====================
     */

    // Tất cả ảnh gắn với user (qua bảng imageables)
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(Image::class, 'imageables', 'imageable_id', 'image_id')
            ->wherePivot('imageable_type', self::class)
            ->withPivot('is_main', 'position')
            ->withTimestamps();
    }

    // Quan hệ avatar chính (dành cho Laravel dùng đúng cách)
    public function avatarRelation()
    {
        return $this->belongsToMany(Image::class, 'imageables', 'imageable_id', 'image_id')
            ->wherePivot('imageable_type', self::class)
            ->wherePivot('is_main', true);
    }

    // Accessor lấy URL ảnh đại diện
    public function getAvatarUrlAttribute()
    {
        $image = $this->avatarRelation()->first();
        return $image ? asset('storage/' . $image->path) : asset('images/default-avatar.png');
    }

    // Thêm hoặc thay avatar
    public function setAvatar(Image $image)
    {
        // Bỏ đánh dấu avatar cũ
        $this->images()->updateExistingPivot(
            $this->images()->wherePivot('is_main', true)->pluck('id')->toArray(),
            ['is_main' => false]
        );

        // Gắn avatar mới
        $this->images()->attach($image->id, [
            'is_main' => true,
            'imageable_type' => self::class
        ]);
    }
    //Thêm ngày 6/11/2025 
    protected static function booted()
    {
        static::created(function ($user) {
            // Tìm template Welcome Email
            $mail = Mail::where('template_key', 'welcome-email')->first();

            if ($mail) {
                $recipient = MailRecipient::create([
                    'mail_id' => $mail->id,
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: 'User',
                    'status' => \App\Enums\MailRecipientStatus::Pending->value,
                ]);

                // Gửi luôn
                \App\Helpers\MailHelper::sendToRecipient($mail, $recipient);
            }
        });
    }
}