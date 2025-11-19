<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

// ============ CART ITEM RESOURCE ============
class CartItemResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'slug' => $this->product->slug,
                'price' => $this->product->price,
                'sale_price' => $this->product->sale_price,
                'image' => $this->product->main_image_url,
            ],
            'variant' => $this->variant ? [
                'id' => $this->variant->id,
                'sku' => $this->variant->sku,
                'size' => $this->variant->size,
                'color' => $this->variant->color,
                'price' => $this->variant->price,
            ] : null,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'subtotal' => $this->quantity * $this->price,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}