<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\NotificationType;
use App\Models\Scopes\NotificationScopes;

class Notification extends Model
{
    use HasFactory, SoftDeletes, NotificationScopes;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'variables',
        'is_read',
        'read_at',
        'expires_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'expires_at' => 'datetime',
        'variables' => 'array', // 🔥 quan trọng
        'type' => NotificationType::class, // để có thể dùng $notification->type->value
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}