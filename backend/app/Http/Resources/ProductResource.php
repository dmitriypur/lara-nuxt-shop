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
        $variants = $this->relationLoaded('activeVariants') ? $this->whenLoaded('activeVariants') : $this->whenLoaded('variants');

        $price = $this->price;
        if ($variants && $variants->isNotEmpty()) {
            $firstVariant = $variants->first();
            if ($firstVariant) {
                $price = $firstVariant->price;
            }
        }

        $old_price = $this->old_price;
        if ($variants && $variants->isNotEmpty()) {
            $firstVariant = $variants->first();
            if ($firstVariant && $firstVariant->old_price) {
                $old_price = $firstVariant->old_price;
            }
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $price,
            'old_price' => $old_price,
            'sku' => $this->sku,
            'active' => $this->active,
            'meta' => $this->meta,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'variants' => ProductVariantResource::collection($this->whenLoaded('activeVariants')),
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