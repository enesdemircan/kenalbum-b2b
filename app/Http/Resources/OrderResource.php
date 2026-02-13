<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'user_id' => $this->user_id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email
            ],
            'customer_info' => [
                'name' => $this->customer_name,
                'surname' => $this->customer_surname,
                'phone' => $this->customer_phone,
                'full_name' => $this->customer_name . ' ' . $this->customer_surname
            ],
            'shipping_info' => [
                'city' => $this->city,
                'district' => $this->district,
                'address' => $this->shipping_address
            ],
            'payment_method' => $this->payment_method,
            'notes' => $this->notes,
            'total_price' => (float) $this->total_price,
            'discount_amount' => (float) $this->discount_amount,
            'final_price' => (float) ($this->total_price - $this->discount_amount),
            'status' => $this->status,
            'status_text' => $this->status_text,
            'status_badge_class' => $this->status_badge_class,
            'cart_items' => CartResource::collection($this->whenLoaded('cartItems')),
            'cart_items_count' => $this->cartItems ? $this->cartItems->count() : 0,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'created_at_human' => $this->created_at?->diffForHumans(),
        ];
    }
}

