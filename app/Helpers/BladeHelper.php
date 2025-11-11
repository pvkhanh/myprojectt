<?php
// =================================================================
// BLADE HELPER
// File: app/Helpers/BladeHelper.php
// =================================================================

namespace App\Helpers;

class BladeHelper
{
    /**
     * Generate mail status badge
     */
    public static function mailStatusBadge($status): string
    {
        $config = [
            'sent' => ['class' => 'success', 'icon' => 'check-circle', 'text' => 'Đã gửi'],
            'pending' => ['class' => 'warning', 'icon' => 'clock', 'text' => 'Chờ gửi'],
            'failed' => ['class' => 'danger', 'icon' => 'times-circle', 'text' => 'Thất bại'],
        ];

        $data = $config[$status] ?? ['class' => 'secondary', 'icon' => 'question', 'text' => $status];

        return sprintf(
            '<span class="badge bg-%s"><i class="fa-solid fa-%s me-1"></i>%s</span>',
            $data['class'],
            $data['icon'],
            $data['text']
        );
    }

    /**
     * Generate mail type badge
     */
    public static function mailTypeBadge($type): string
    {
        $config = [
            'system' => ['class' => 'primary', 'icon' => 'cog', 'text' => 'System'],
            'user' => ['class' => 'info', 'icon' => 'user', 'text' => 'User'],
            'marketing' => ['class' => 'success', 'icon' => 'bullhorn', 'text' => 'Marketing'],
        ];

        $data = $config[$type] ?? ['class' => 'secondary', 'icon' => 'question', 'text' => $type];

        return sprintf(
            '<span class="badge bg-%s"><i class="fa-solid fa-%s me-1"></i>%s</span>',
            $data['class'],
            $data['icon'],
            $data['text']
        );
    }
}
