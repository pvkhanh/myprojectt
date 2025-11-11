<?php

// =================================================================
// CONFIG FILE
// File: config/mail-system.php
// =================================================================

return [
    /*
    |--------------------------------------------------------------------------
    | Mail System Configuration
    |--------------------------------------------------------------------------
    */

    // Default sender email
    'default_sender' => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),

    // Default sender name
    'default_sender_name' => env('MAIL_FROM_NAME', 'Mail System'),

    // Pagination
    'pagination' => [
        'mails' => 15,
        'recipients' => 50,
    ],

    // Mail sending
    'sending' => [
        // Number of mails to send per batch
        'batch_size' => 100,

        // Delay between batches (seconds)
        'batch_delay' => 5,

        // Enable queue for sending
        'use_queue' => env('MAIL_QUEUE_ENABLED', false),

        // Queue name
        'queue_name' => 'mail',

        // Retry failed mails
        'retry_failed' => true,
        'max_retries' => 3,
    ],

    // Templates
    'templates' => [
        // Path to custom templates
        'path' => resource_path('views/emails/templates'),

        // Available template variables
        'variables' => [
            'name' => 'Tên người nhận',
            'email' => 'Email người nhận',
            'first_name' => 'Tên',
            'last_name' => 'Họ',
            'phone' => 'Số điện thoại',
            'app_name' => 'Tên ứng dụng',
            'app_url' => 'URL ứng dụng',
        ],
    ],

    // Segments
    'segments' => [
        'all_users' => [
            'name' => 'Tất cả người dùng',
            'icon' => 'users',
            'color' => 'primary',
            'query' => function () {
                return \App\Models\User::query();
            }
        ],
        'verified_users' => [
            'name' => 'Người dùng đã xác thực',
            'icon' => 'user-check',
            'color' => 'success',
            'query' => function () {
                return \App\Models\User::whereNotNull('email_verified_at');
            }
        ],
        'active_users' => [
            'name' => 'Người dùng đang hoạt động',
            'icon' => 'user-clock',
            'color' => 'info',
            'query' => function () {
                return \App\Models\User::where('status', 'active');
            }
        ],
    ],

    // Analytics
    'analytics' => [
        'enabled' => true,
        'track_opens' => true,
        'track_clicks' => true,
    ],

    // Email validation
    'validation' => [
        'check_dns' => false,
        'check_disposable' => false,
    ],
];
