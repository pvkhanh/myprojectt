<?php

namespace App\Enums;

enum ReviewStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    // Trả về danh sách giá trị (để validate hoặc select)
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    // Trả về nhãn hiển thị tiếng Việt
    public function label(): string
    {
        return match ($this) {
            self::Pending  => 'Chờ duyệt',
            self::Approved => 'Đã duyệt',
            self::Rejected => 'Từ chối',
        };
    }

    // Trả về màu Bootstrap badge
    public function color(): string
    {
        return match ($this) {
            self::Pending  => 'warning',
            self::Approved => 'success',
            self::Rejected => 'danger',
        };
    }

    // Tuỳ chọn: icon Font Awesome (nếu bạn muốn hiển thị kèm biểu tượng)
    public function icon(): string
    {
        return match ($this) {
            self::Pending  => 'clock',
            self::Approved => 'check-circle',
            self::Rejected => 'times-circle',
        };
    }
}