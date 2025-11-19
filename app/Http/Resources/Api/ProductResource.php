<?php

// namespace App\Http\Resources\Api;

// use Illuminate\Http\Resources\Json\JsonResource;

// // ============ PRODUCT RESOURCE ============
// class ProductResource extends JsonResource
// {
//     public function toArray($request): array
//     {
//         return [
//             'id' => $this->id,
//             'name' => $this->name,
//             'slug' => $this->slug,
//             // 'sku' => $this->sku,
//             'sku' => $this->variants->first()?->sku,
//             'description' => $this->description,
//             'short_description' => $this->short_description,
//             'price' => $this->price,
//             'sale_price' => $this->sale_price,
//             'discount_percentage' => $this->discount_percentage,
//             // 'stock_quantity' => $this->stock_quantity,
//             'stock_quantity' => $this->total_stock,
//             // 'is_featured' => $this->is_featured,
//             // 'is_active' => $this->is_active,
//             'is_active' => $this->status === \App\Enums\ProductStatus::Active,
//             'is_featured' => $this->is_featured ?? false,
//             // 'category' => $this->category ? [
//             //     'id' => $this->category->id,
//             //     'name' => $this->category->name,
//             //     'slug' => $this->category->slug,
//             // ] : null,
//             'category' => $this->categories->first() ? [
//                 'id' => $this->categories->first()->id,
//                 'name' => $this->categories->first()->name,
//                 'slug' => $this->categories->first()->slug,
//             ] : null,

//             'images' => $this->images->map(fn($img) => [
//                 'id' => $img->id,
//                 'url' => asset('storage/' . $img->path),
//                 'is_main' => $img->pivot->is_main ?? false,
//             ]),
//             'average_rating' => $this->average_rating,
//             // 'reviews_count' => $this->reviews_count,
//             'reviews_count' => $this->reviews()->count(),
//             'created_at' => $this->created_at->format('Y-m-d H:i:s'),
//         ];
//     }
// }



//Bản thứ 2 đầy đủ hơn
// namespace App\Http\Resources\Api;

// use Illuminate\Http\Resources\Json\JsonResource;

// class ProductResource extends JsonResource
// {
//     public function toArray($request): array
//     {
//         return [
//             'id' => $this->id,
//             'name' => $this->name,
//             'slug' => $this->slug,
//             'sku' => $this->sku ?? $this->variants->first()?->sku, // lấy SKU đầu tiên nếu product SKU null
//             'description' => $this->description,
//             'short_description' => $this->short_description,
//             'price' => $this->price,
//             'min_price' => $this->min_price, // accessor
//             'max_price' => $this->max_price, // accessor
//             'sale_price' => $this->sale_price,
//             'discount_percentage' => $this->discount_percentage,
//             'stock_quantity' => $this->stock_quantity, // tổng tồn kho qua accessor
//             'in_stock' => $this->in_stock, // boolean còn hàng
//             'is_low_stock' => $this->is_low_stock, // tồn kho thấp
//             'is_active' => $this->status === \App\Enums\ProductStatus::Active,
//             'is_featured' => $this->is_featured ?? false,
//             'status_label' => $this->status_label,
//             'status_color' => $this->status_color,
//             'category' => $this->categories->first() ? [
//                 'id' => $this->categories->first()->id,
//                 'name' => $this->categories->first()->name,
//                 'slug' => $this->categories->first()->slug,
//             ] : null,
//             'category_names' => $this->category_names, // tất cả tên danh mục
//             'images' => $this->images->map(fn($img) => [
//                 'id' => $img->id,
//                 'url' => asset('storage/' . $img->path),
//                 'is_main' => $img->pivot->is_main ?? false,
//                 'position' => $img->pivot->position ?? null,
//             ]),
//             'primary_image_url' => $this->main_image_url, // accessor
//             'average_rating' => $this->average_rating,
//             'reviews_count' => $this->review_count,
//             'created_at' => $this->created_at->format('Y-m-d H:i:s'),
//             'price_range' => $this->price_range, // accessor
//             'related_products' => $this->getRelatedProducts()->map(fn($product) => [
//                 'id' => $product->id,
//                 'name' => $product->name,
//                 'slug' => $product->slug,
//                 'price' => $product->price,
//                 'stock_quantity' => $product->stock_quantity,
//                 'primary_image_url' => $product->main_image_url,
//             ]),
//         ];
//     }
// }



//Bản thứ 3


namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->variants->first()?->sku,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'price' => $this->price,
            'min_price' => $this->min_price,
            'max_price' => $this->max_price,
            'sale_price' => $this->sale_price,
            'discount_percentage' => $this->discount_percentage,
            'stock_quantity' => $this->stock_quantity,
            'in_stock' => $this->in_stock,
            'is_low_stock' => $this->is_low_stock,
            'is_active' => $this->status === \App\Enums\ProductStatus::Active,
            'is_featured' => $this->is_featured ?? false,
            'status_label' => $this->status_label,
            'status_color' => $this->status_color,
            'category' => $this->categories->first() ? [
                'id' => $this->categories->first()->id,
                'name' => $this->categories->first()->name,
                'slug' => $this->categories->first()->slug,
            ] : null,
            'category_names' => $this->category_names,
            'images' => $this->images->map(fn($img) => [
                'id' => $img->id,
                'url' => asset('storage/' . $img->path),
                'is_main' => $img->pivot->is_main ?? false,
                'position' => $img->pivot->position ?? 0,
            ]),
            'primary_image_url' => $this->main_image_url,
            'average_rating' => $this->average_rating,
            'reviews_count' => $this->reviews()->count(),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'price_range' => $this->price_range,
            'related_products' => $this->getRelatedProducts()->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'slug' => $p->slug,
                    'price' => $p->price,
                    'stock_quantity' => $p->stock_quantity,
                    'primary_image_url' => $p->main_image_url,
                ];
            }),
        ];
    }
}