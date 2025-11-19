<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;


// ============ WISHLIST RESOURCE ============
class WishlistResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'product' => new ProductResource($this->whenLoaded('product')),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}