<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductReview;
use App\Models\Product;

class ProductReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Chỉ tạo review nếu đã có sản phẩm trong database
        if (Product::count() === 0) {
            $this->command->warn('⚠️ Không có sản phẩm nào để tạo review. Hãy seed Product trước.');
            return;
        }

        // Tạo mỗi sản phẩm từ 3–10 reviews ngẫu nhiên
        Product::all()->each(function ($product) {
            ProductReview::factory()
                ->count(fake()->numberBetween(3, 10))
                ->create([
                    'product_id' => $product->id,
                ]);
        });

        $this->command->info('✅ Product reviews seeded successfully!');
    }
}