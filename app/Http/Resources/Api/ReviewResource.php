<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;


// ============ REVIEW RESOURCE ============
class ReviewResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->full_name,
                'avatar' => $this->user->avatar_url,
            ],
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'slug' => $this->product->slug,
            ],
            'rating' => $this->rating,
            'comment' => $this->comment,
            'images' => $this->images,
            'is_verified_purchase' => $this->is_verified_purchase,
            'helpful_count' => $this->helpful_count,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}