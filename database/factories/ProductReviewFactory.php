<?php

// namespace Database\Factories;

// use Illuminate\Database\Eloquent\Factories\Factory;
// use App\Models\Product;
// use App\Models\User;
// use App\Enums\ReviewStatus;

// class ProductReviewFactory extends Factory
// {
//     /**
//      * Define the model's default state.
//      */
//     public function definition(): array
//     {
//         return [
//             'product_id' => Product::query()->inRandomOrder()->value('id') ?? Product::factory(),
//             'user_id' => fake()->boolean(90) // 90% review có user
//                 ? (User::query()->inRandomOrder()->value('id') ?? User::factory())
//                 : null,
//             'rating' => fake()->numberBetween(1, 5),
//             'comment' => fake()->optional()->paragraph(),
//             'status' => fake()->randomElement(ReviewStatus::values()),
//         ];
//     }

//     /**
//      * Trạng thái review đang chờ duyệt.
//      */
//     public function pending(): static
//     {
//         return $this->state(fn () => ['status' => ReviewStatus::Pending->value]);
//     }

//     /**
//      * Trạng thái review được duyệt.
//      */
//     public function approved(): static
//     {
//         return $this->state(fn () => ['status' => ReviewStatus::Approved->value]);
//     }

//     /**
//      * Trạng thái review bị từ chối.
//      */
//     public function rejected(): static
//     {
//         return $this->state(fn () => ['status' => ReviewStatus::Rejected->value]);
//     }
// }


namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use App\Models\User;
use App\Enums\ReviewStatus;

class ProductReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::query()->inRandomOrder()->value('id') ?? Product::factory(),
            'user_id' => fake()->boolean(90) // 90% review có user
                ? (User::query()->inRandomOrder()->value('id') ?? User::factory())
                : null,
            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->optional()->paragraph(),
            'status' => fake()->randomElement(ReviewStatus::values()),
        ];
    }

    /**
     * Trạng thái review đang chờ duyệt.
     */
    public function pending(): static
    {
        return $this->state(fn () => ['status' => ReviewStatus::Pending->value]);
    }

    /**
     * Trạng thái review được duyệt.
     */
    public function approved(): static
    {
        return $this->state(fn () => ['status' => ReviewStatus::Approved->value]);
    }

    /**
     * Trạng thái review bị từ chối.
     */
    public function rejected(): static
    {
        return $this->state(fn () => ['status' => ReviewStatus::Rejected->value]);
    }
}