<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AttributeValue extends Model
{
    protected $fillable = ['attribute_id', 'value', 'slug'];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    public function variants(): BelongsToMany
    {
        // Варианты представлены моделью Product с parent_id
        return $this->belongsToMany(Product::class, 'attribute_value_product_variant', 'attribute_value_id', 'product_variant_id');
    }
}
