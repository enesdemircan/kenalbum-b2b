<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            'product_id' => $this->product_id,
            'product' => [
                'id' => $this->product?->id,
                'title' => $this->product?->title,
                'slug' => $this->product?->slug,
                'price' => $this->product?->price,
                'images' => $this->product?->images,
            ],
            'quantity' => $this->quantity,
            'unit_price' => (float) $this->unit_price,
            'total_price' => (float) $this->total_price,
            'barcode' => $this->barcode,
            'cart_status' => $this->cart_status,
            'customization_params' => $this->whenLoaded('customizationParamsCustomers'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

