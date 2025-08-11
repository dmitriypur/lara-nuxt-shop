<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'full_name' => $this->full_name,
            'parent_id' => $this->parent_id,
            'level' => $this->depth ?? 0,
            'has_children' => $this->children()->exists(),
            'products_count' => $this->products()->count(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            
            // Включаем родителя только при необходимости
            'parent' => $this->when(
                $this->relationLoaded('parent') && $this->parent,
                fn() => new CategoryResource($this->parent)
            ),
            
            // Включаем детей только при необходимости
            'children' => $this->when(
                $this->relationLoaded('children') || isset($this->children),
                fn() => CategoryResource::collection($this->children ?? collect())
            ),
            
            // Дополнительные поля для админки
            'path' => $this->when(
                $request->routeIs('admin.*'),
                fn() => $this->ancestors()->pluck('name')->push($this->name)->implode(' > ')
            ),
        ];
    }
}