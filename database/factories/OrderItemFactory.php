<?php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        // Random product hoặc tạo mới
        $product = Product::inRandomOrder()->first() ?? Product::factory()->create();
        $variant = $product->variants()->inRandomOrder()->first()
            ?? ProductVariant::factory()->create(['product_id' => $product->id]);

        return [
            'order_id' => Order::factory(), // Nếu chưa truyền order_id, sẽ tạo mới
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'quantity' => $this->faker->numberBetween(1, 5),
            'price' => $variant->price ?? $product->price,
        ];
    }
}
