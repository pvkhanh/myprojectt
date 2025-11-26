<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Notifications\Notifiable;
// use Illuminate\Database\Eloquent\SoftDeletes;
// use App\Models\Scopes\UserScopes;
// use Illuminate\Database\Eloquent\Relations\BelongsToMany;


// class User extends Authenticatable
// {
//     use HasFactory, Notifiable, SoftDeletes, UserScopes;

//     protected $fillable = [
//         'email',
//         'username',
//         'password',
//         'first_name',
//         'last_name',
//         'phone',
//         'gender',
//         'birthday',
//         'bio',
//         'avatar', // 👈 thêm dòng này
//         'role',
//         'is_active',
//         'email_verified_at',
//     ];

//     protected $hidden = ['password', 'remember_token'];

//     protected $casts = [
//         'email_verified_at' => 'datetime',
//         'password' => 'hashed',
//         'birthday' => 'date',
//         'is_active' => 'boolean',
//     ];

//     /**
//      * Relationships
//      */

//     public function addresses()
//     {
//         return $this->hasMany(UserAddress::class);
//     }
//     public function orders()
//     {
//         return $this->hasMany(Order::class);
//     }
//     public function orderItems()
//     {
//         return $this->hasManyThrough(OrderItem::class, Order::class);
//     }
//     public function cartItems()
//     {
//         return $this->hasMany(CartItem::class);
//     }
//     public function wishlists()
//     {
//         return $this->hasMany(Wishlist::class);
//     }
//     public function reviews()
//     {
//         return $this->hasMany(ProductReview::class);
//     }
//     public function notifications()
//     {
//         return $this->hasMany(Notification::class);
//     }
//     public function blogs()
//     {
//         return $this->hasMany(Blog::class, 'author_id');
//     }
//     public function mailRecipients()
//     {
//         return $this->hasMany(MailRecipient::class);
//     }

//     /**
//      * Polymorphic relation for images.
//      * - morphOne: The main avatar.
//      * - morphMany: Other images (if needed).
//      */
//     // public function avatar()
//     // {
//     //     return $this->morphOne(Image::class, 'imageable')
//     //         ->where('type', 'avatar')
//     //         ->latestOfMany();
//     // }

//     // public function images()
//     // {
//     //     return $this->morphMany(Image::class, 'imageable')->orderForDisplay();
//     // }
//     // Lấy tất cả ảnh của user
//     // public function images(): BelongsToMany
//     // {
//     //     return $this->belongsToMany(Image::class, 'imageables', 'imageable_id', 'image_id')
//     //         ->wherePivot('imageable_type', self::class)
//     //         ->withPivot('is_main', 'position')
//     //         ->withTimestamps();
//     // }
//     // // Lấy ảnh chính (avatar)
//     // public function avatar()
//     // {
//     //     return $this->images()->wherePivot('is_main', true)->first();
//     // }

//     // // Thêm avatar mới (ví dụ trong repository)
//     // public function setAvatar(Image $image)
//     // {
//     //     // Xóa avatar cũ
//     //     $this->images()->updateExistingPivot(
//     //         $this->images()->wherePivot('is_main', true)->pluck('id')->toArray(),
//     //         ['is_main' => false]
//     //     );

//     //     // Thêm ảnh mới
//     //     $this->images()->attach($image->id, ['is_main' => true, 'imageable_type' => self::class]);
//     // }


//     /**
//      * =====================
//      * 🖼️ IMAGE RELATIONS
//      * =====================
//      */

//     // Tất cả ảnh gắn với user (qua bảng imageables)
//     public function images(): BelongsToMany
//     {
//         return $this->belongsToMany(Image::class, 'imageables', 'imageable_id', 'image_id')
//             ->wherePivot('imageable_type', self::class)
//             ->withPivot('is_main', 'position')
//             ->withTimestamps();
//     }

//     // Quan hệ avatar chính (dành cho Laravel dùng đúng cách)
//     public function avatarRelation()
//     {
//         return $this->belongsToMany(Image::class, 'imageables', 'imageable_id', 'image_id')
//             ->wherePivot('imageable_type', self::class)
//             ->wherePivot('is_main', true);
//     }

//     // Accessor lấy URL ảnh đại diện
//     // public function getAvatarUrlAttribute()
//     // {
//     //     $image = $this->avatarRelation()->first();
//     //     return $image ? asset('storage/' . $image->path) : asset('images/default-avatar.png');
//     // }

//     // Thêm hoặc thay avatar
//     public function setAvatar(Image $image)
//     {
//         // Bỏ đánh dấu avatar cũ
//         $this->images()->updateExistingPivot(
//             $this->images()->wherePivot('is_main', true)->pluck('id')->toArray(),
//             ['is_main' => false]
//         );

//         // Gắn avatar mới
//         $this->images()->attach($image->id, [
//             'is_main' => true,
//             'imageable_type' => self::class
//         ]);
//     }
//     //Thêm ngày 6/11/2025 
//     protected static function booted()
//     {
//         static::created(function ($user) {
//             // Tìm template Welcome Email
//             $mail = Mail::where('template_key', 'welcome-email')->first();

//             if ($mail) {
//                 $recipient = MailRecipient::create([
//                     'mail_id' => $mail->id,
//                     'user_id' => $user->id,
//                     'email' => $user->email,
//                     'name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: 'User',
//                     'status' => \App\Enums\MailRecipientStatus::Pending->value,
//                 ]);

//                 // Gửi luôn
//                 \App\Helpers\MailHelper::sendToRecipient($mail, $recipient);
//             }
//         });
//     }

//     //Thêm avtar 12/11/2025
//     /**
//      * =====================
//      * 🎨 Avatar quản lý riêng
//      * =====================
//      */

//     // Trả về URL đầy đủ của avatar (ưu tiên ảnh riêng trong cột avatar)
//     // public function getAvatarUrlAttribute(): string
//     // {
//     //     if ($this->avatar) {
//     //         // Nếu avatar đã lưu là URL tuyệt đối
//     //         if (str_starts_with($this->avatar, 'http')) {
//     //             return $this->avatar;
//     //         }
//     //         // Nếu avatar là đường dẫn tương đối (trong storage)
//     //         return asset('storage/' . $this->avatar);
//     //     }

//     //     // Nếu chưa có thì dùng fallback từ quan hệ imageables (nếu có)
//     //     $image = $this->avatarRelation()->first();
//     //     if ($image) {
//     //         return asset('storage/' . $image->path);
//     //     }

//     //     // Ảnh mặc định
//     //     return asset('images/default-avatar.png');
//     // }

//     // app/Models/User.php
//     public function getAvatarUrlAttribute()
//     {
//         if ($this->avatar && file_exists(storage_path('app/public/' . $this->avatar))) {
//             return asset('storage/' . $this->avatar);
//         }

//         return asset('images/default-avatar.png'); // ảnh mặc định nếu không có
//     }

//     /**
//      * Upload avatar file mới
//      */
//     public function updateAvatar(\Illuminate\Http\UploadedFile $file): void
//     {
//         $path = $file->store('avatars', 'public');
//         $this->update(['avatar' => $path]);
//     }
// }




// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Notifications\Notifiable;
// use Illuminate\Database\Eloquent\SoftDeletes;
// use App\Models\Scopes\UserScopes;
// use Illuminate\Database\Eloquent\Relations\BelongsToMany;


// class User extends Authenticatable
// {
//     use HasFactory, Notifiable, SoftDeletes, UserScopes;

//     protected $fillable = [
//         'email',
//         'username',
//         'password',
//         'first_name',
//         'last_name',
//         'phone',
//         'gender',
//         'birthday',
//         'bio',
//         'avatar', // 👈 thêm dòng này
//         'role',
//         'is_active',
//         'email_verified_at',
//     ];

//     protected $hidden = ['password', 'remember_token'];

//     protected $casts = [
//         'email_verified_at' => 'datetime',
//         'password' => 'hashed',
//         'birthday' => 'date',
//         'is_active' => 'boolean',
//     ];

//     /**
//      * Relationships
//      */

//     public function addresses()
//     {
//         return $this->hasMany(UserAddress::class);
//     }
//     public function orders()
//     {
//         return $this->hasMany(Order::class);
//     }
//     public function orderItems()
//     {
//         return $this->hasManyThrough(OrderItem::class, Order::class);
//     }
//     public function cartItems()
//     {
//         return $this->hasMany(CartItem::class);
//     }
//     public function wishlists()
//     {
//         return $this->hasMany(Wishlist::class);
//     }
//     public function reviews()
//     {
//         return $this->hasMany(ProductReview::class);
//     }
//     public function notifications()
//     {
//         return $this->hasMany(Notification::class);
//     }
//     public function blogs()
//     {
//         return $this->hasMany(Blog::class, 'author_id');
//     }
//     public function mailRecipients()
//     {
//         return $this->hasMany(MailRecipient::class);
//     }

//     /**
//      * Polymorphic relation for images.
//      * - morphOne: The main avatar.
//      * - morphMany: Other images (if needed).
//      */
//     // public function avatar()
//     // {
//     //     return $this->morphOne(Image::class, 'imageable')
//     //         ->where('type', 'avatar')
//     //         ->latestOfMany();
//     // }

//     // public function images()
//     // {
//     //     return $this->morphMany(Image::class, 'imageable')->orderForDisplay();
//     // }
//     // Lấy tất cả ảnh của user
//     // public function images(): BelongsToMany
//     // {
//     //     return $this->belongsToMany(Image::class, 'imageables', 'imageable_id', 'image_id')
//     //         ->wherePivot('imageable_type', self::class)
//     //         ->withPivot('is_main', 'position')
//     //         ->withTimestamps();
//     // }
//     // // Lấy ảnh chính (avatar)
//     // public function avatar()
//     // {
//     //     return $this->images()->wherePivot('is_main', true)->first();
//     // }

//     // // Thêm avatar mới (ví dụ trong repository)
//     // public function setAvatar(Image $image)
//     // {
//     //     // Xóa avatar cũ
//     //     $this->images()->updateExistingPivot(
//     //         $this->images()->wherePivot('is_main', true)->pluck('id')->toArray(),
//     //         ['is_main' => false]
//     //     );

//     //     // Thêm ảnh mới
//     //     $this->images()->attach($image->id, ['is_main' => true, 'imageable_type' => self::class]);
//     // }


//     /**
//      * =====================
//      * 🖼️ IMAGE RELATIONS
//      * =====================
//      */

//     // Tất cả ảnh gắn với user (qua bảng imageables)
//     public function images(): BelongsToMany
//     {
//         return $this->belongsToMany(Image::class, 'imageables', 'imageable_id', 'image_id')
//             ->wherePivot('imageable_type', self::class)
//             ->withPivot('is_main', 'position')
//             ->withTimestamps();
//     }

//     // Quan hệ avatar chính (dành cho Laravel dùng đúng cách)
//     public function avatarRelation()
//     {
//         return $this->belongsToMany(Image::class, 'imageables', 'imageable_id', 'image_id')
//             ->wherePivot('imageable_type', self::class)
//             ->wherePivot('is_main', true);
//     }

//     // Accessor lấy URL ảnh đại diện
//     // public function getAvatarUrlAttribute()
//     // {
//     //     $image = $this->avatarRelation()->first();
//     //     return $image ? asset('storage/' . $image->path) : asset('images/default-avatar.png');
//     // }

//     // Thêm hoặc thay avatar
//     public function setAvatar(Image $image)
//     {
//         // Bỏ đánh dấu avatar cũ
//         $this->images()->updateExistingPivot(
//             $this->images()->wherePivot('is_main', true)->pluck('id')->toArray(),
//             ['is_main' => false]
//         );

//         // Gắn avatar mới
//         $this->images()->attach($image->id, [
//             'is_main' => true,
//             'imageable_type' => self::class
//         ]);
//     }
//     //Thêm ngày 6/11/2025 
//     protected static function booted()
//     {
//         static::created(function ($user) {
//             // Tìm template Welcome Email
//             $mail = Mail::where('template_key', 'welcome-email')->first();

//             if ($mail) {
//                 $recipient = MailRecipient::create([
//                     'mail_id' => $mail->id,
//                     'user_id' => $user->id,
//                     'email' => $user->email,
//                     'name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: 'User',
//                     'status' => \App\Enums\MailRecipientStatus::Pending->value,
//                 ]);

//                 // Gửi luôn
//                 \App\Helpers\MailHelper::sendToRecipient($mail, $recipient);
//             }
//         });
//     }

//     //Thêm avtar 12/11/2025
//     /**
//      * =====================
//      * 🎨 Avatar quản lý riêng
//      * =====================
//      */

//     // Trả về URL đầy đủ của avatar (ưu tiên ảnh riêng trong cột avatar)
//     // public function getAvatarUrlAttribute(): string
//     // {
//     //     if ($this->avatar) {
//     //         // Nếu avatar đã lưu là URL tuyệt đối
//     //         if (str_starts_with($this->avatar, 'http')) {
//     //             return $this->avatar;
//     //         }
//     //         // Nếu avatar là đường dẫn tương đối (trong storage)
//     //         return asset('storage/' . $this->avatar);
//     //     }

//     //     // Nếu chưa có thì dùng fallback từ quan hệ imageables (nếu có)
//     //     $image = $this->avatarRelation()->first();
//     //     if ($image) {
//     //         return asset('storage/' . $image->path);
//     //     }

//     //     // Ảnh mặc định
//     //     return asset('images/default-avatar.png');
//     // }

//     // app/Models/User.php
//     public function getAvatarUrlAttribute()
//     {
//         if ($this->avatar && file_exists(storage_path('app/public/' . $this->avatar))) {
//             return asset('storage/' . $this->avatar);
//         }

//         return asset('images/default-avatar.png'); // ảnh mặc định nếu không có
//     }

//     /**
//      * Upload avatar file mới
//      */
//     public function updateAvatar(\Illuminate\Http\UploadedFile $file): void
//     {
//         $path = $file->store('avatars', 'public');
//         $this->update(['avatar' => $path]);
//     }
// }



namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Scopes\UserScopes;
use Laravel\Sanctum\HasApiTokens; // <- Thêm để dùng API token
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\Product;
class User extends Authenticatable implements JWTSubject
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
        'avatar',
        'role',
        'is_active',
        'email_verified_at',
        'remember_token',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'birthday' => 'date',
        'is_active' => 'boolean',
    ];
    // =================== JWT METHODS ===================

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims()
    {
        return [
            'email' => $this->email,
            'role' => $this->role,
            'username' => $this->username,
        ];
    }

    // =================== RELATIONSHIPS ===================

    // public function addresses(): HasMany
    // {
    //     return $this->hasMany(UserAddress::class);
    // }

    public function defaultAddress()
    {
        return $this->hasOne(UserAddress::class)->where('is_default', true);
    }

    // public function orders(): HasMany
    // {
    //     return $this->hasMany(Order::class);
    // }

    // public function cartItems(): HasMany
    // {
    //     return $this->hasMany(CartItem::class);
    // }

    // public function wishlists(): HasMany
    // {
    //     return $this->hasMany(Wishlist::class);
    // }

    // public function reviews(): HasMany
    // {
    //     return $this->hasMany(ProductReview::class);
    // }

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

    // =================== AVATAR ACCESSORS ===================

    /**
     * Lấy URL đầy đủ của avatar
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            // Nếu avatar đã là URL tuyệt đối (từ CDN, etc)
            if (str_starts_with($this->avatar, 'http')) {
                return $this->avatar;
            }

            // Nếu avatar là đường dẫn tương đối trong storage
            if (Storage::disk('public')->exists($this->avatar)) {
                return asset('storage/' . $this->avatar);
            }
        }

        // Ảnh mặc định
        return asset('images/default-avatar.png');
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

    /**
     * Lấy tên đầy đủ của user
     */
    public function getFullNameAttribute(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? '')) ?: $this->username;
    }

    /**
     * Lấy initials (chữ cái đầu) để hiển thị khi không có avatar
     */
    public function getInitialsAttribute(): string
    {
        if ($this->first_name && $this->last_name) {
            return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
        }
        return strtoupper(substr($this->username, 0, 2));
    }

    // =================== AVATAR METHODS ===================

    /**
     * Upload và cập nhật avatar mới
     */
    public function updateAvatar(\Illuminate\Http\UploadedFile $file): void
    {
        // Xóa avatar cũ nếu có
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            Storage::disk('public')->delete($this->avatar);
        }

        // Lưu avatar mới
        $path = $file->store('avatars', 'public');
        $this->update(['avatar' => $path]);
    }

    /**
     * Xóa avatar hiện tại
     */
    public function removeAvatar(): bool
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            Storage::disk('public')->delete($this->avatar);
        }
        return $this->update(['avatar' => null]);
    }
    public function wishlistItems(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'wishlists', 'user_id', 'product_id')
            ->withTimestamps();
    }
}