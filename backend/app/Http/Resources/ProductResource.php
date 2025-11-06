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

        // Базовые атрибуты (варианты выбора) берём у родителя, если это вариант.
        // ВАЖНО: раньше мы брали пустые baseAttributeValues варианта, из-за чего на фронте группы были пустыми.
        // Предпочитаем родителя, если он есть, иначе — текущий товар.
        $baseAttributeValues = collect();
        if ($this->parent_id && $this->relationLoaded('parent') && $this->parent->relationLoaded('baseAttributeValues')) {
            $baseAttributeValues = $this->parent->baseAttributeValues;
        } elseif ($this->relationLoaded('baseAttributeValues')) {
            $baseAttributeValues = $this->baseAttributeValues;
        }

        // Оставляем ключами названия атрибутов (без values()), чтобы фронт получил объект { "Размер": [...] }
        $baseAttributes = $baseAttributeValues
            ->groupBy(fn ($v) => optional($v->attribute)->name)
            ->map(fn ($group) => $group->map(fn ($v) => [
                'id' => $v->id,
                'slug' => $v->slug,
                'value' => $v->value,
            ])->values())
            ->filter();

        // Атрибуты, выбранные у текущего товара/варианта
        $selectedAttributes = collect();
        if ($this->relationLoaded('attributeValues')) {
            $selectedAttributes = $this->attributeValues->map(fn ($v) => [
                'attribute' => optional($v->attribute)->name,
                'id' => $v->id,
                'slug' => $v->slug,
                'value' => $v->value,
            ]);
        }

        // Унифицированное представление для отображения на фронте:
        // если есть выбранные у текущего товара/варианта — покажем их, иначе базовые группы
        $selectedGrouped = $selectedAttributes
            ->groupBy('attribute')
            ->map(fn ($group) => $group->map(fn ($v) => [
                'id' => $v['id'],
                'slug' => $v['slug'],
                'value' => $v['value'],
            ])->values())
            ->filter();
        $displayAttributes = $selectedAttributes->isNotEmpty() ? $selectedGrouped : $baseAttributes;

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
            'attributes' => [
                'base' => $baseAttributes,
                'selected' => $selectedAttributes,
                'display' => $displayAttributes,
            ],
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