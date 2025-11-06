<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Если это вариант, и у него нет своих изображений, используем изображения родителя
        $media = $this->getMedia('images');
        if ($this->parent_id && $media->isEmpty() && $this->relationLoaded('parent')) {
            $media = $this->parent->getMedia('images');
        }

        // Аналогично для категорий
        $categories = $this->relationLoaded('categories') ? $this->categories : collect();
        if ($this->parent_id && $categories->isEmpty() && $this->relationLoaded('parent')) {
            $categories = $this->parent->categories;
        }

        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => (float) $this->price,
            'old_price' => (float) $this->old_price,
            'stock' => (int) $this->stock,
            'sku' => $this->sku,
            'active' => (bool) $this->active,
            'categories' => CategoryResource::collection($categories),
            'variants' => ProductResource::collection($this->whenLoaded('variants')),
            'images' => $media->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => $image->getUrl(),
                    'thumb' => $image->getUrl('thumb'),
                ];
            }),
        ];
    }
}