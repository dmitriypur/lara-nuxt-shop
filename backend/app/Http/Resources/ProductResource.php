<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $firstVariant = $this->whenLoaded('variants', function () {
            return $this->variants->first();
        });

        $price = $firstVariant ? $firstVariant->price : $this->price;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $price,
            'old_price' => $this->old_price,
            'sku' => $this->sku,
            'active' => $this->active,
            'meta' => $this->meta,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),
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
            'images' => $this->whenLoaded('media', function () {
                return $this->getMedia('images')->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'url' => $media->getUrl(),
                        'thumb' => $media->getUrl('thumb'),
                        'large' => $media->getUrl('large'),
                        'webp_thumb' => $media->getUrl('webp_thumb'),
                        'webp_large' => $media->getUrl('webp_large'),
                        'name' => $media->name,
                        'file_name' => $media->file_name,
                        'mime_type' => $media->mime_type,
                        'size' => $media->size,
                    ];
                });
            }),
        ];
    }
}