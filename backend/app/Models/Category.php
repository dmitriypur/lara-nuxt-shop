<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Kalnoy\Nestedset\NodeTrait;

class Category extends Model
{
    use NodeTrait;

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function getFullNameAttribute(): string
    {
        $ancestors = $this->ancestors()->pluck('name')->toArray();
        $ancestors[] = $this->name;
        
        return implode(' > ', $ancestors);
    }

    public function getIndentedNameAttribute(): string
    {
        $depth = $this->ancestors()->count();
        $indent = str_repeat('- ', $depth);
        
        return $indent . $this->name;
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }


}
