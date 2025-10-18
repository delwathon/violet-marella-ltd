<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'action_type',
        'quantity_change',
        'previous_stock',
        'new_stock',
        'unit_cost',
        'reason',
        'reference_number',
        'action_date'
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'action_date' => 'datetime',
    ];

    /**
     * Get the product that owns the inventory log.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the staff member who performed the action.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to filter by action type.
     */
    public function scopeByActionType($query, $actionType)
    {
        return $query->where('action_type', $actionType);
    }

    /**
     * Scope a query to filter by product.
     */
    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope a query to filter by staff member.
     */
    public function scopeByStaff($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('action_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include stock additions.
     */
    public function scopeStockAdditions($query)
    {
        return $query->where('quantity_change', '>', 0);
    }

    /**
     * Scope a query to only include stock deductions.
     */
    public function scopeStockDeductions($query)
    {
        return $query->where('quantity_change', '<', 0);
    }

    /**
     * Get the absolute quantity change.
     */
    public function getAbsoluteQuantityChangeAttribute()
    {
        return abs($this->quantity_change);
    }

    /**
     * Get the action description.
     */
    public function getActionDescriptionAttribute()
    {
        $descriptions = [
            'sale' => 'Sale',
            'purchase' => 'Stock Purchase',
            'adjustment' => 'Stock Adjustment',
            'return' => 'Return',
            'damage' => 'Damaged Goods',
            'expiry' => 'Expired Goods',
            'transfer' => 'Transfer'
        ];

        return $descriptions[$this->action_type] ?? ucfirst($this->action_type);
    }

    /**
     * Get the total cost for this inventory change.
     */
    public function getTotalCostAttribute()
    {
        return $this->unit_cost * $this->absolute_quantity_change;
    }

    /**
     * Create a new inventory log entry.
     */
    public static function logStockChange($productId, $userId, $actionType, $quantityChange, $reason = null, $referenceNumber = null)
    {
        $product = Product::find($productId);
        if (!$product) {
            return false;
        }

        $previousStock = $product->stock_quantity;
        $newStock = max(0, $previousStock + $quantityChange);

        return self::create([
            'product_id' => $productId,
            'user_id' => $userId,
            'action_type' => $actionType,
            'quantity_change' => $quantityChange,
            'previous_stock' => $previousStock,
            'new_stock' => $newStock,
            'reason' => $reason,
            'reference_number' => $referenceNumber,
            'action_date' => now(),
        ]);
    }
}
