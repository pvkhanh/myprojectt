<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockItem;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::factory()->count(30)->create()->each(function ($product) {
            // Tạo 1-3 biến thể cho mỗi sản phẩm
            $variants = ProductVariant::factory()->count(rand(1, 3))->create([
                'product_id' => $product->id,
            ]);

            // Tạo stock cho mỗi biến thể
            foreach ($variants as $variant) {
                StockItem::factory()->create([
                    'variant_id' => $variant->id,
                ]);
            }
        });
    }
}
// namespace Database\Seeders;

// use Illuminate\Database\Seeder;
// use App\Models\ProductReview;

// class ProductReviewSeeder extends Seeder
// {
//     /**
//      * Run the database seeds.
//      */
//     public function run(): void
//     {
//         // Tạo 50 review ngẫu nhiên
//         ProductReview::factory()->count(50)->create();
//     }
// }