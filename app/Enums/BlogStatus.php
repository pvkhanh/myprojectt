<?php

namespace App\Enums;

enum BlogStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';

    // Trả về giá trị enum
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    // Trả về nhãn hiển thị thân thiện
    public function label(): string
    {
        return match($this) {
            self::Draft => 'Bản nháp',
            self::Published => 'Đã xuất bản',
            self::Archived => 'Lưu trữ',
        };
    }
}