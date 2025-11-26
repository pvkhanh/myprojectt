<?php

// namespace App\Http\Resources\Api;

// use Illuminate\Http\Resources\Json\JsonResource;

// // ============ CART ITEM RESOURCE ============
// class CartItemResource extends JsonResource
// {
//     public function toArray($request): array
//     {
//         return [
//             'id' => $this->id,
//             'product' => [
//                 'id' => $this->product->id,
//                 'name' => $this->product->name,
//                 'slug' => $this->product->slug,
//                 'price' => $this->product->price,
//                 'sale_price' => $this->product->sale_price,
//                 'image' => $this->product->main_image_url,
//             ],
//             'variant' => $this->variant ? [
//                 'id' => $this->variant->id,
//                 'sku' => $this->variant->sku,
//                 'size' => $this->variant->size,
//                 'color' => $this->variant->color,
//                 'price' => $this->variant->price,
//             ] : null,
//             'quantity' => $this->quantity,
//             'price' => $this->price,
//             'subtotal' => $this->quantity * $this->price,
//             'created_at' => $this->created_at->format('Y-m-d H:i:s'),
//         ];
//     }
// }



namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray($request): array
    {
        $product = $this->product;
        $variant = $this->variant;

        $unitPrice = $variant ? ($variant->sale_price ?? $variant->price) : ($product->sale_price ?? $product->price);
        $availableStock = $variant ? $variant->stockItems->sum('quantity') : $product->stock_quantity;
        $isOutOfStock = $availableStock <= 0;

        return [
            'id' => $this->id,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->price,
                'sale_price' => $product->sale_price,
                'image' => $product->main_image_url,
            ],
            'variant' => $variant ? [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'size' => $variant->size,
                'color' => $variant->color,
                'price' => $variant->price,
                'sale_price' => $variant->sale_price,
            ] : null,
            'quantity' => $this->quantity,
            'unit_price' => $unitPrice,
            'subtotal' => $unitPrice * $this->quantity,
            'available_stock' => $availableStock,
            'is_out_of_stock' => $isOutOfStock,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
