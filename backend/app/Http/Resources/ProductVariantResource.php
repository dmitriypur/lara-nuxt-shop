<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'sku' => $this->sku,
            'price' => $this->price,
            'old_price' => $this->old_price,
            'stock_quantity' => $this->stock_quantity,
            'weight' => $this->weight,
            'dimensions' => $this->dimensions,
            'active' => $this->active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'product' => new ProductResource($this->whenLoaded('product')), 
            'attribute_values' => ProductAttributeValueResource::collection($this->whenLoaded('attributeValues')),
            'image' => $this->whenLoaded('media', function () {
                $media = $this->getFirstMedia('images');
                if (!$media) {
                    return null;
                }
                return [
                    'id' => $media->id,
                    'url' => $media->getUrl(),
                    'thumb' => $media->getUrl('thumb'),
                    'large' => $media->getUrl('large'),
                    'webp_thumb' => $media->getUrl('webp_thumb'),
                    'webp_large' => $media->getUrl('webp_large'),
                ];
            }),
        ];
    }
}