<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\PaymentStatus;
use App\Enums\PaymentMethod;
use App\Models\Scopes\PaymentScopes;

class Payment extends Model
{
    use HasFactory, PaymentScopes;

    protected $fillable = [
        'order_id',
        'payment_method',
        'payment_gateway',
        'transaction_id',
        'amount',
        'paid_at',
        'status',
        'requires_manual_verification',
        'is_verified',
        'verified_at',
        'verified_by',
        'verification_note',
        'gateway_response'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'verified_at' => 'datetime',
        'payment_method' => PaymentMethod::class,
        'status' => PaymentStatus::class,
        'requires_manual_verification' => 'boolean',
        'is_verified' => 'boolean',
        'gateway_response' => 'array'
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Scopes
    public function scopePendingVerification($query)
    {
        return $query->where('requires_manual_verification', true)
            ->where('is_verified', false)
            ->where('status', PaymentStatus::Pending);
    }

    public function scopeAutoVerified($query)
    {
        return $query->where('requires_manual_verification', false)
            ->where('status', PaymentStatus::Success);
    }

    // Methods
    public function needsVerification(): bool
    {
        return $this->requires_manual_verification && !$this->is_verified;
    }

    // public function canBeVerified(): bool
    // {
    //     return $this->requires_manual_verification
    //         && !$this->is_verified
    //         && $this->status->value !== 'failed';
    // }


    public function canBeVerified(): bool
    {
        return !$this->is_verified
            && $this->status->value !== 'failed';
    }

    /**
     * Đánh dấu payment đã được xác nhận
     *
     * @param User|null $verifier - Người xác nhận (nullable)
     * @param string|null $note - Ghi chú
     * @return void
     */
    public function markAsVerified(?User $verifier = null, ?string $note = null): void
    {
        $updateData = [
            'is_verified' => true,
            'verified_at' => now(),
            'verification_note' => $note,
            'status' => PaymentStatus::Success,
            // 'paid_at' => $this->paid_at ?? now()
            'paid_at' => $this->paid_at ? $this->paid_at : now(),

        ];

        // Chỉ set verified_by nếu có verifier
        if ($verifier) {
            $updateData['verified_by'] = $verifier->id;
        }

        $this->update($updateData);
    }

    public function markAsFailed(?string $reason = null): void
    {
        $this->update([
            'status' => PaymentStatus::Failed,
            'verification_note' => $reason
        ]);
    }

    // Accessor
    public function getVerificationStatusAttribute(): string
    {
        if (!$this->requires_manual_verification) {
            return 'auto';
        }

        if ($this->is_verified) {
            return 'verified';
        }

        if ($this->status->value === 'failed') {
            return 'failed';
        }

        return 'pending';
    }

    public function getVerificationBadgeClassAttribute(): string
    {
        return match ($this->verification_status) {
            'auto' => 'success',
            'verified' => 'success',
            'failed' => 'danger',
            'pending' => 'warning',
            default => 'secondary'
        };
    }

    /**
     * Accessor cho gateway_response - đảm bảo luôn trả về array
     */
    public function getGatewayResponseAttribute($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        return is_array($value) ? $value : [];
    }

    // Boot method để tự động set requires_manual_verification
    protected static function booted()
    {
        static::creating(function ($payment) {
            // COD luôn cần xác nhận thủ công
            if ($payment->payment_method->value === 'cod') {
                $payment->requires_manual_verification = true;
            }

            // Chuyển khoản ngân hàng tự động verify nếu có transaction_id
            if (in_array($payment->payment_method->value, ['bank', 'wallet', 'card'])) {
                $payment->requires_manual_verification = empty($payment->transaction_id);
            }
        });
    }

}
