<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StoreProduct extends Model
{
    use HasFactory;

    protected $table = 'store_products';

    protected $fillable = [
        'name',
        'sku',
        'barcode',
        'description',
        'store_category_id',
        'price',
        'cost_price',
        'wholesale_price',
        'stock_quantity',
        'minimum_stock_level',
        'maximum_stock_level',
        'unit',
        'image',
        'images',
        'is_active',
        'track_stock',
        'is_featured',
        'tax_rate',
        'brand',
        'supplier',
        'expiry_date',
        'attributes'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'track_stock' => 'boolean',
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'images' => 'array',
        'attributes' => 'array',
        'expiry_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->sku)) {
                $product->sku = 'STORE-' . strtoupper(Str::random(8));
            }
        });
    }

    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(StoreCategory::class, 'store_category_id');
    }

    /**
     * Get the sale items for the product.
     */
    public function saleItems()
    {
        return $this->hasMany(StoreSaleItem::class, 'store_product_id');
    }

    /**
     * Get the inventory logs for the product.
     */
    public function inventoryLogs()
    {
        return $this->hasMany(StoreInventoryLog::class, 'store_product_id');
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include low stock products.
     */
    public function scopeLowStock($query)
    {
        return $query->whereRaw('stock_quantity <= minimum_stock_level');
    }

    /**
     * Scope a query to search products by name or SKU.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%")
              ->orWhere('barcode', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Get the total sales count for this product.
     */
    public function getTotalSalesAttribute()
    {
        return $this->saleItems()->sum('quantity');
    }

    /**
     * Get the total revenue for this product.
     */
    public function getTotalRevenueAttribute()
    {
        return $this->saleItems()->sum('total_price');
    }

    /**
     * Check if product is low in stock.
     */
    public function isLowStock()
    {
        return $this->track_stock && $this->stock_quantity <= $this->minimum_stock_level;
    }

    /**
     * Check if product is out of stock.
     */
    public function isOutOfStock()
    {
        return $this->track_stock && $this->stock_quantity <= 0;
    }

    /**
     * Get the profit margin for this product.
     */
    public function getProfitMarginAttribute()
    {
        if ($this->cost_price && $this->cost_price > 0) {
            return (($this->price - $this->cost_price) / $this->price) * 100;
        }
        return 0;
    }

    /**
     * Get the selling price after tax.
     */
    public function getPriceWithTaxAttribute()
    {
        return $this->price + ($this->price * $this->tax_rate / 100);
    }
}