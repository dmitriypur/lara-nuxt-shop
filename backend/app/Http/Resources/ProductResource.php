<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $product = $this->resource;

        // Если это вариант, и у него нет своих изображений, используем изображения родителя
        $media = $product->whenLoaded('media');
        if ($product->parent_id && $media->isEmpty()) {
            $media = $product->parent->whenLoaded('media');
        }

        // Аналогично для категорий
        $categories = $product->whenLoaded('categories');
        if ($product->parent_id && $categories->isEmpty()) {
            $categories = $product->parent->whenLoaded('categories');
        }

        return [
            'id' => $product->id,
            'parent_id' => $product->parent_id,
            'title' => $product->title,
            'slug' => $product->slug,
            'description' => $product->description,
            'price' => (float) $product->price,
            'old_price' => (float) $product->old_price,
            'stock' => (int) $product->stock,
            'sku' => $product->sku,
            'active' => (bool) $product->active,
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