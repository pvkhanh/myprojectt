<?php

// namespace Database\Factories;

// use App\Models\Notification;
// use App\Models\User;
// use App\Enums\NotificationType;
// use Illuminate\Database\Eloquent\Factories\Factory;

// class NotificationFactory extends Factory
// {
//     protected $model = Notification::class;

//     public function definition(): array
//     {
//         $user = User::inRandomOrder()->first() ?? User::factory()->create();

//         return [
//             'user_id' => $user->id,
//             'type' => $this->faker->randomElement(NotificationType::values()),
//             'title' => $this->faker->sentence(6, true),
//             'message' => $this->faker->paragraph(2, true),
//             'variables' => [
//                 'order_id' => $this->faker->optional()->randomNumber(),
//                 'product_id' => $this->faker->optional()->randomNumber(),
//             ],
//             'is_read' => $this->faker->boolean(30),
//             'read_at' => null,
//             'expires_at' => $this->faker->dateTimeBetween('+1 days', '+30 days'),
//         ];
//     }

//     public function read(): static
//     {
//         return $this->state(fn() => [
//             'is_read' => true,
//             'read_at' => now(),
//         ]);
//     }

//     public function unread(): static
//     {
//         return $this->state(fn() => [
//             'is_read' => false,
//             'read_at' => null,
//         ]);
//     }
// }<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use App\Enums\NotificationType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(NotificationType::values());

        // Đặt tiêu đề và nội dung phù hợp với loại thông báo
        [$title, $message, $variables] = match ($type) {
            NotificationType::System->value => [
                'Thông báo hệ thống',
                'Hệ thống sẽ được bảo trì vào ngày mai lúc 2:00 sáng.',
                null,
            ],
            NotificationType::Order->value => [
                'Cập nhật đơn hàng',
                'Đơn hàng #' . $this->faker->randomNumber(5) . ' của bạn đã được xác nhận.',
                ['order_id' => $this->faker->randomNumber(5)],
            ],
            NotificationType::Promotion->value => [
                'Khuyến mãi mới',
                'Giảm ngay ' . $this->faker->numberBetween(10, 50) . '% cho tất cả sản phẩm tuần này!',
                ['discount' => $this->faker->numberBetween(10, 50)],
            ],
            NotificationType::Activity->value => [
                'Hoạt động gần đây',
                'Bạn vừa đăng nhập từ thiết bị mới.',
                ['device' => $this->faker->userAgent()],
            ],
            default => ['Thông báo chung', $this->faker->sentence(12), null],
        };

        return [
            'user_id' => User::factory(),
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'variables' => $variables ? json_encode($variables) : null,
            'is_read' => $this->faker->boolean(30),
            'read_at' => $this->faker->optional(0.3)->dateTimeBetween('-3 days'),
            'expires_at' => $this->faker->optional()->dateTimeBetween('+1 week', '+1 month'),
        ];
    }

    public function read(): static
    {
        return $this->state(fn() => [
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function unread(): static
    {
        return $this->state(fn() => [
            'is_read' => false,
            'read_at' => null,
        ]);
    }
}