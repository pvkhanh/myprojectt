<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';     // Đơn mới, chưa xử lý
    case Paid = 'paid';           // Đã thanh toán
    case Shipped = 'shipped';     // Đang giao hàng
    case Completed = 'completed'; // Đã hoàn thành
    case Cancelled = 'cancelled'; // Đã hủy

    /**
     * Lấy danh sách tất cả giá trị enum
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Hiển thị nhãn tiếng Việt
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Chờ xử lý',
            self::Paid => 'Đã thanh toán',
            self::Shipped => 'Đang giao hàng',
            self::Completed => 'Hoàn thành',
            self::Cancelled => 'Đã hủy',
        };
    }

    /**
     * Màu hiển thị (dùng cho badge / UI)
     */
    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',   // vàng
            self::Paid => 'info',      // tím / xanh nhạt
            self::Shipped => 'primary',   // xanh dương
            self::Completed => 'success',   // xanh lá
            self::Cancelled => 'danger',    // đỏ
        };
    }

    /**
     * Icon tương ứng (dành cho FontAwesome hoặc Lucide)
     */
    public function icon(): string
    {
        return match ($this) {
            self::Pending => 'fa-clock',
            self::Paid => 'fa-credit-card',
            self::Shipped => 'fa-truck',
            self::Completed => 'fa-check-circle',
            self::Cancelled => 'fa-times-circle',
        };
    }

    /**
     * Tạo badge HTML nhanh
     */
    public function badge(): string
    {
        return sprintf(
            '<span class="badge bg-%s"><i class="fas %s me-1"></i> %s</span>',
            $this->color(),
            $this->icon(),
            $this->label()
        );
    }
}
