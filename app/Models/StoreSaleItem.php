<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreSaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_sale_id',
        'store_product_id',
        'product_name',
        'product_sku',
        'unit_price',
        'quantity',
        'total_price',
        'discount_amount',
        'tax_amount'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
    ];

    /**
     * Get the sale that owns the sale item.
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(StoreSale::class);
    }

    /**
     * Get the product that owns the sale item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(StoreProduct::class);
    }

    /**
     * Get the total price before discount and tax.
     */
    public function getSubtotalAttribute()
    {
        return $this->unit_price * $this->quantity;
    }

    /**
     * Get the final price after discount and tax.
     */
    public function getFinalPriceAttribute()
    {
        return $this->total_price;
    }

    /**
     * Calculate the total price including tax and discount.
     */
    public function calculateTotal()
    {
        $subtotal = $this->unit_price * $this->quantity;
        $discountedAmount = $subtotal - $this->discount_amount;
        $this->tax_amount = $discountedAmount * ($this->product->tax_rate / 100);
        $this->total_price = $discountedAmount + $this->tax_amount;
    }

    /**
     * Scope a query to filter by sale.
     */
    public function scopeBySale($query, $saleId)
    {
        return $query->where('store_sale_id', $saleId);
    }

    /**
     * Scope a query to filter by product.
     */
    public function scopeByProduct($query, $productId)
    {
        return $query->where('store_product_id', $productId);
    }

    /**
     * Get the profit for this sale item.
     */
    public function getProfitAttribute()
    {
        $costPrice = $this->product->cost_price ?? 0;
        return ($this->unit_price - $costPrice) * $this->quantity;
    }

    /**
     * Get the profit margin for this sale item.
     */
    public function getProfitMarginAttribute()
    {
        $costPrice = $this->product->cost_price ?? 0;
        if ($costPrice > 0) {
            return (($this->unit_price - $costPrice) / $this->unit_price) * 100;
        }
        return 0;
    }
}
