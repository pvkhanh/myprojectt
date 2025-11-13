<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Banner;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Banner>
 */
class BannerFactory extends Factory
{
    protected $model = Banner::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'url' => $this->faker->url(),
            'image_id' => null,
            'type' => $this->faker->randomElement(['hero', 'sidebar', 'popup', 'footer']),
            'is_active' => $this->faker->boolean(80),
            'position' => $this->faker->numberBetween(0, 10),
            'start_at' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'end_at' => $this->faker->dateTimeBetween('+1 month', '+3 months'),
        ];
    }


    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }
}
