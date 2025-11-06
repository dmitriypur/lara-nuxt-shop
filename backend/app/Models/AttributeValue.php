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
        // Все товары, связанные с этим значением атрибута (любой тип)
        return $this->belongsToMany(Product::class, 'attribute_value_product', 'attribute_value_id', 'product_id')
            ->withPivot('type');
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    public function variants(): BelongsToMany
    {
        // Варианты — это те же продукты, для которых назначены выбранные значения (type = 'selected')
        return $this->belongsToMany(Product::class, 'attribute_value_product', 'attribute_value_id', 'product_id')
            ->withPivot('type')
            ->wherePivot('type', 'selected');
    }
}
