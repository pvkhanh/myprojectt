<?php

// namespace App\Http\Resources\Api;

// use Illuminate\Http\Resources\Json\JsonResource;

// // ============ ORDER RESOURCE ============
// class OrderResource extends JsonResource
// {
//     public function toArray($request): array
//     {
//         return [
//             'id' => $this->id,
//             'order_code' => $this->order_code,
//             'status' => $this->status,
//             'payment_status' => $this->payment_status,
//             'payment_method' => $this->payment_method,
//             'total_amount' => $this->total_amount,
//             'shipping_fee' => $this->shipping_fee,
//             'discount_amount' => $this->discount_amount,
//             'final_amount' => $this->final_amount,
//             'shipping_address' => [
//                 'name' => $this->shipping_name,
//                 'phone' => $this->shipping_phone,
//                 'address' => $this->shipping_address,
//                 'city' => $this->shipping_city,
//                 'district' => $this->shipping_district,
//                 'ward' => $this->shipping_ward,
//             ],
//             'items' => OrderItemResource::collection($this->whenLoaded('items')),
//             'created_at' => $this->created_at->format('Y-m-d H:i:s'),
//             'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
//         ];
//     }
// }




namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status->value ?? $this->status,
            'payment_status' => $this->payments->first()->status->value ?? null,
            'payment_method' => $this->payments->first()->payment_method->value ?? null,

            'subtotal' => $this->subtotal,
            'shipping_fee' => $this->shipping_fee,
            'discount_amount' => $this->discount_amount,
            'total_amount' => $this->total_amount,

            'shipping_address' => [
                'name' => $this->shipping_name,
                'phone' => $this->shipping_phone,
                'address' => $this->shipping_address,
                'ward' => $this->shipping_ward,
                'district' => $this->shipping_district,
                'province' => $this->shipping_city, // vì đã lưu city = province
            ],

            'items' => OrderItemResource::collection($this->whenLoaded('orderItems')),

            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
