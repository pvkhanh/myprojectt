<?php

namespace App\Enums;

// CẬP NHẬT PaymentMethod.php ĐÃ CÓ SẴN - THÊM METHOD

enum PaymentMethod: string
{
    case Card = 'card';
    case Bank = 'bank';
    case COD = 'cod';
    case Wallet = 'wallet';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::Card => 'Thẻ tín dụng/Ghi nợ (Stripe)',
            self::Bank => 'Chuyển khoản ngân hàng',
            self::COD => 'Thanh toán khi nhận hàng',
            self::Wallet => 'Ví điện tử',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Card => 'credit-card',
            self::Bank => 'university',
            self::COD => 'money-bill-wave',
            self::Wallet => 'wallet',
        };
    }

    // ✅ THÊM: Check xem có cần xác minh thủ công không
    public function requiresVerification(): bool
    {
        return match ($this) {
            self::COD => true,
            self::Bank => true,
            self::Card => false,  // Stripe tự động verify
            self::Wallet => false,
        };
    }

    // ✅ THÊM: Check xem có phải thanh toán online không
    public function isOnline(): bool
    {
        return match ($this) {
            self::Card => true,
            self::Wallet => true,
            default => false,
        };
    }

    // ✅ THÊM: Gateway name cho Stripe
    public function gateway(): ?string
    {
        return match ($this) {
            self::Card => 'stripe',
            self::Wallet => 'momo', // Nếu có tích hợp
            default => null,
        };
    }
}