<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomizationPivotParamResource extends JsonResource
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
            ],
            'params_id' => $this->params_id,
            'param' => [
                'id' => $this->param?->id,
                'title' => $this->param?->title,
                'ust_id' => $this->param?->ust_id,
            ],
            'customization_category_id' => $this->customization_category_id,
            'category' => [
                'id' => $this->category?->id,
                'title' => $this->category?->title,
            ],
            'price' => (float) $this->price,
            'option1' => $this->option1,
            'option2' => $this->option2,
            'customization_params_ust_id' => $this->customization_params_ust_id,
            'order' => $this->order,
            'is_main_param' => $this->isMainParam(),
            'has_parent' => $this->hasParent(),
            'has_children' => $this->hasChildren(),
            'children_count' => $this->getChildrenCount(),
            'children' => CustomizationPivotParamResource::collection($this->whenLoaded('children')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

