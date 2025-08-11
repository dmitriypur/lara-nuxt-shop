<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Str;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia, Searchable;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->title);
            }
        });

        static::updating(function ($product) {
            if (empty($product->slug) && $product->isDirty('title')) {
                $product->slug = Str::slug($product->title);
            }
        });
    }

    protected $fillable = [
        'sku',
        'title',
        'slug',
        'description',
        'price_min',
        'price_max',
        'active',
        'meta'
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml']);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(400)
            ->nonQueued();

        $this->addMediaConversion('large')
            ->width(1200)
            ->quality(80);
    }

    public function toSearchableArray(): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => strip_tags($this->description),
            'categories'  => $this->categories->pluck('name')->toArray(),
            'price_min'   => $this->price_min,
            'price_max'   => $this->price_max,
        ];
    }
}
