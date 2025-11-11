<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $status = $this->faker->randomElement(OrderStatus::values());
        $createdAt = $this->faker->dateTimeBetween('-2 months', 'now');

        $deliveredAt = null;
        $completedAt = null;
        $cancelledAt = null;

        switch ($status) {
            case OrderStatus::Shipped->value:
                $deliveredAt = $this->faker->dateTimeBetween($createdAt, 'now');
                break;

            case OrderStatus::Completed->value:
                $deliveredAt = $this->faker->dateTimeBetween($createdAt, 'now');
                $completedAt = $this->faker->dateTimeBetween($deliveredAt, 'now');
                break;

            case OrderStatus::Cancelled->value:
                $cancelledAt = $this->faker->dateTimeBetween($createdAt, 'now');
                break;
        }

        $shippingFee = $this->faker->randomFloat(0, 15000, 50000);

        return [
            'user_id' => User::factory(),
            'order_number' => strtoupper(Str::random(10)),
            'total_amount' => 0, // tạm thời, sẽ update sau khi tạo OrderItem
            'shipping_fee' => $shippingFee,
            'customer_note' => $this->faker->optional()->sentence(),
            'admin_note' => $this->faker->optional()->sentence(),
            'status' => $status,
            'delivered_at' => $deliveredAt,
            'completed_at' => $completedAt,
            'cancelled_at' => $cancelledAt,
            'created_at' => $createdAt,
            'updated_at' => now(),
        ];
    }
}
