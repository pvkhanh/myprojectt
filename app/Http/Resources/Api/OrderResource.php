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



// //Bản theo cập nhật OrderController
// namespace App\Http\Resources\Api;

// use Illuminate\Http\Resources\Json\JsonResource;

// class OrderResource extends JsonResource
// {
//     public function toArray($request): array
//     {
//         return [
//             'id' => $this->id,
//             'order_number' => $this->order_number,
//             'status' => $this->status->value ?? $this->status,
//             'payment_status' => $this->payments->first()->status->value ?? null,
//             'payment_method' => $this->payments->first()->payment_method->value ?? null,

//             'subtotal' => $this->subtotal,
//             'shipping_fee' => $this->shipping_fee,
//             'discount_amount' => $this->discount_amount,
//             'total_amount' => $this->total_amount,

//             'shipping_address' => [
//                 'name' => $this->shipping_name,
//                 'phone' => $this->shipping_phone,
//                 'address' => $this->shipping_address,
//                 'ward' => $this->shipping_ward,
//                 'district' => $this->shipping_district,
//                 'province' => $this->shipping_city, // vì đã lưu city = province
//             ],

//             'items' => OrderItemResource::collection($this->whenLoaded('orderItems')),

//             'created_at' => $this->created_at->format('Y-m-d H:i:s'),
//             'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
//         ];
//     }
// }



//Bản hoàn thiện


namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'subtotal' => (float) $this->subtotal,
            'shipping_fee' => (float) $this->shipping_fee,
            'discount_amount' => (float) ($this->discount_amount ?? 0),
            'total_amount' => (float) $this->total_amount,
            'note' => $this->note,
            'created_at' => $this->created_at->toDateTimeString(),
            'paid_at' => $this->paid_at?->toDateTimeString(),
            'shipped_at' => $this->shipped_at?->toDateTimeString(),
            'completed_at' => $this->completed_at?->toDateTimeString(),
            'cancelled_at' => $this->cancelled_at?->toDateTimeString(),

            // Shipping Address
            'shipping_address' => $this->shippingAddress ? [
                'receiver_name' => $this->shippingAddress->receiver_name,
                'phone' => $this->shippingAddress->phone,
                'address' => $this->shippingAddress->address,
                'ward' => $this->shippingAddress->ward,
                'district' => $this->shippingAddress->district,
                'province' => $this->shippingAddress->province,
                'postal_code' => $this->shippingAddress->postal_code,
            ] : null,

            // Order Items
            'items' => $this->orderItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'quantity' => $item->quantity,
                    'price' => (float) $item->price,
                    'total' => (float) ($item->price * $item->quantity),
                    'product' => $item->product ? [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'sku' => $item->product->sku ?? null,
                        'sale_price' => (float) ($item->product->sale_price ?? 0),
                    ] : null,
                    'variant' => $item->variant ? [
                        'id' => $item->variant->id,
                        'name' => $item->variant->name,
                        'price' => (float) $item->variant->price,
                    ] : null,
                ];
            }),

            // Payments
            'payments' => $this->payments->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'method' => $payment->payment_method->value,
                    'method_label' => $payment->payment_method->label(),
                    'amount' => (float) $payment->amount,
                    'status' => $payment->status->value,
                    'status_label' => $payment->status->label(),
                    'created_at' => $payment->created_at->toDateTimeString(),
                ];
            }),

            // Payment summary (latest)
            'payment_status' => $this->payment_status->value,
            'payment_status_label' => $this->payment_label,
            'payment_method' => $this->payment_method->value,
            'payment_method_label' => $this->payment_method_label,
        ];
    }
}