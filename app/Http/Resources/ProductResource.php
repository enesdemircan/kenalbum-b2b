<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'price' => (float) $this->price,
            'urgent_price' => (float) $this->urgent_price,
            'design_service_price' => (float) $this->design_service_price,
            'main_category_id' => $this->main_category_id,
            'main_category' => [
                'id' => $this->mainCategory?->id,
                'title' => $this->mainCategory?->title,
                'slug' => $this->mainCategory?->slug,
            ],
            'images' => $this->images,
            'thumbnails' => $this->thumbnails,
            'template_url' => $this->template_url,
            'status' => $this->status,
            'status_text' => $this->status == 1 ? 'Aktif' : 'Pasif',
            'order' => $this->order,
            'pricing_info' => [
                'price_difference_per_page' => (float) $this->price_difference_per_page,
                'decreasing_per_page' => (float) $this->decreasing_per_page,
                'max_pages' => $this->max_pages,
                'min_pages' => $this->min_pages,
            ],
            'option1' => $this->option1,
            'option2' => $this->option2,
            'suggested_products' => $this->suggested_products,
            'parent_product_id' => $this->ust_id,
            'parent_product' => $this->whenLoaded('parentProduct', function() {
                return [
                    'id' => $this->parentProduct->id,
                    'title' => $this->parentProduct->title,
                    'slug' => $this->parentProduct->slug,
                ];
            }),
            'child_products' => ProductResource::collection($this->whenLoaded('childProducts')),
            'is_main_product' => $this->isMainProduct(),
            'is_child_product' => $this->isChildProduct(),
            'tags' => $this->tags,
            'stock_status' => $this->stock_status,
            'description' => $this->description,
            'customization_params' => CustomizationPivotParamResource::collection($this->whenLoaded('customizationPivotParams')),
            'details' => $this->whenLoaded('details'),
            'extra_sales' => $this->whenLoaded('extraSales'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

