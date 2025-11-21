<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StoreCategory extends Model
{
    use HasFactory;

    protected $table = 'store_categories';

    protected $fillable = [
        'name',
        'description',
        'image',
        'color',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the products for the category.
     */
    public function products()
    {
        return $this->hasMany(StoreProduct::class, 'store_category_id');
    }

    /**
     * Get active products for the category.
     */
    public function activeProducts()
    {
        return $this->hasMany(StoreProduct::class, 'store_category_id')->where('is_active', true);
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order categories by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get the total products count for this category.
     */
    public function getProductsCountAttribute()
    {
        return $this->products()->count();
    }

    /**
     * Get the active products count for this category.
     */
    public function getActiveProductsCountAttribute()
    {
        return $this->activeProducts()->count();
    }
}