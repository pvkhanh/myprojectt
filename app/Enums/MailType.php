<?php

namespace App\Enums;

enum MailType: string
{
    case System = 'system';
    case User = 'user';
    case Marketing = 'marketing';
    case Order = 'order'; // thêm dòng này

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}