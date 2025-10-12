<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_number',
        'customer_id',
        'staff_id',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'amount_paid',
        'change_amount',
        'payment_method',
        'payment_status',
        'status',
        'notes',
        'payment_details',
        'sale_date'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'payment_details' => 'array',
        'sale_date' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sale) {
            if (empty($sale->receipt_number)) {
                $sale->receipt_number = 'RCP-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }
            if (empty($sale->sale_date)) {
                $sale->sale_date = now();
            }
        });
    }

    /**
     * Get the customer that owns the sale.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the staff member who made the sale.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    /**
     * Get the sale items for the sale.
     */
    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Get the payments for the sale.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Scope a query to only include completed sales.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to filter sales by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('sale_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to filter sales by payment method.
     */
    public function scopePaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Scope a query to filter sales by staff member.
     */
    public function scopeByStaff($query, $staffId)
    {
        return $query->where('staff_id', $staffId);
    }

    /**
     * Scope a query to filter sales by customer.
     */
    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Get the total items count for this sale.
     */
    public function getTotalItemsAttribute()
    {
        return $this->saleItems()->sum('quantity');
    }

    /**
     * Get the unique products count for this sale.
     */
    public function getUniqueProductsAttribute()
    {
        return $this->saleItems()->count();
    }

    /**
     * Calculate and update sale totals.
     */
    public function calculateTotals()
    {
        $subtotal = $this->saleItems()->sum('total_price');
        $taxAmount = $this->saleItems()->sum('tax_amount');
        $discountAmount = $this->saleItems()->sum('discount_amount');

        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $subtotal + $taxAmount - $discountAmount,
        ]);
    }

    /**
     * Mark sale as completed.
     */
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'payment_status' => 'completed'
        ]);

        // Update inventory
        foreach ($this->saleItems as $item) {
            if ($item->product->track_stock) {
                $item->product->decrement('stock_quantity', $item->quantity);

                // Log inventory change
                InventoryLog::create([
                    'product_id' => $item->product_id,
                    'staff_id' => $this->staff_id,
                    'action_type' => 'sale',
                    'quantity_change' => -$item->quantity,
                    'previous_stock' => $item->product->stock_quantity + $item->quantity,
                    'new_stock' => $item->product->stock_quantity,
                    'reference_number' => $this->receipt_number,
                    'action_date' => now(),
                ]);
            }
        }

        // Update customer stats if customer exists
        if ($this->customer) {
            $this->customer->updateStats($this->total_amount);
        }
    }

    /**
     * Get the profit margin for this sale.
     */
    public function getProfitMarginAttribute()
    {
        $totalCost = $this->saleItems()->sum(function ($item) {
            return $item->product->cost_price * $item->quantity;
        });

        if ($totalCost > 0) {
            return (($this->total_amount - $totalCost) / $this->total_amount) * 100;
        }
        return 0;
    }
}
