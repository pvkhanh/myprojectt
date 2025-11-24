<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'order_id' => $this->order_id,
            'order_number' => $this->order->order_number ?? null,

            // Payment Info
            'payment_method' => $this->payment_method->value,
            'payment_method_label' => $this->payment_method->label(),
            'payment_gateway' => $this->payment_gateway,
            'transaction_id' => $this->transaction_id,
            'amount' => (float) $this->amount,

            // Status
            'status' => $this->status->value,
            'status_label' => $this->status->label(),

            // Verification
            'requires_manual_verification' => $this->requires_manual_verification,
            'is_verified' => $this->is_verified,
            'verification_status' => $this->verification_status,
            'verification_note' => $this->verification_note,
            'verified_at' => $this->verified_at?->format('Y-m-d H:i:s'),
            'verified_by' => $this->when($this->verified_by, [
                'id' => $this->verifier?->id,
                'name' => $this->verifier?->name,
            ]),

            // Timestamps
            'paid_at' => $this->paid_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),

            // Gateway Response (optional - chỉ show khi cần)
            'gateway_response' => $this->when(
                $request->has('include_gateway_response'),
                $this->gateway_response
            ),

            // Order Details (optional)
            'order' => $this->when($request->has('include_order'), [
                'id' => $this->order->id,
                'order_number' => $this->order->order_number,
                'total_amount' => (float) $this->order->total_amount,
                'status' => $this->order->status->value,
            ]),
        ];
    }
}
