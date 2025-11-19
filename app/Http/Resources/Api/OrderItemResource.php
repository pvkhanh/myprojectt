<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

// ============ ORDER ITEM RESOURCE ============
class OrderItemResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'slug' => $this->product->slug,
                'image' => $this->product->main_image_url,
            ],
            'variant' => $this->variant ? [
                'sku' => $this->variant->sku,
                'size' => $this->variant->size,
                'color' => $this->variant->color,
            ] : null,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'subtotal' => $this->quantity * $this->price,
        ];
    }
}