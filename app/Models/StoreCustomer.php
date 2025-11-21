<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoreCustomer extends Model
{
    use HasFactory;

    protected $table = 'store_customers';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'city',
        'state',
        'postal_code',
        'customer_type',
        'loyalty_points',
        'total_spent',
        'total_orders',
        'last_purchase_date',
        'is_active',
        'notes'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'loyalty_points' => 'decimal:2',
        'total_spent' => 'decimal:2',
        'date_of_birth' => 'date',
        'last_purchase_date' => 'date',
    ];

    /**
     * Get the sales for the customer.
     */
    public function sales(): HasMany
    {
        return $this->hasMany(StoreSale::class, 'store_customer_id');
    }

    /**
     * Scope a query to only include active customers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to search customers by name, email, or phone.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    /**
     * Scope a query to filter by customer type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('customer_type', $type);
    }

    /**
     * Get the customer's full name.
     */
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get the customer's full address.
     */
    public function getFullAddressAttribute()
    {
        $address = [];

        if ($this->address) {
            $address[] = $this->address;
        }
        if ($this->city) {
            $address[] = $this->city;
        }
        if ($this->state) {
            $address[] = $this->state;
        }
        if ($this->postal_code) {
            $address[] = $this->postal_code;
        }

        return implode(', ', $address);
    }

    /**
     * Get the customer's average order value.
     */
    public function getAverageOrderValueAttribute()
    {
        if ($this->total_orders > 0) {
            return $this->total_spent / $this->total_orders;
        }
        return 0;
    }

    /**
     * Add loyalty points to the customer.
     */
    public function addLoyaltyPoints($points)
    {
        $this->increment('loyalty_points', $points);
    }

    /**
     * Deduct loyalty points from the customer.
     */
    public function deductLoyaltyPoints($points)
    {
        $this->decrement('loyalty_points', $points);
    }

    /**
     * Update customer statistics after a sale.
     */
    public function updateStats($saleAmount)
    {
        $this->increment('total_spent', $saleAmount);
        $this->increment('total_orders');
        $this->update(['last_purchase_date' => now()]);

        // Award loyalty points (1 point per ₦100 spent)
        $pointsEarned = floor($saleAmount / 100);
        if ($pointsEarned > 0) {
            $this->addLoyaltyPoints($pointsEarned);
        }
    }

    /**
     * Check if customer is eligible for loyalty discount.
     */
    public function isEligibleForLoyaltyDiscount()
    {
        return $this->loyalty_points >= 100; // 100 points = 5% discount
    }

    /**
     * Get the loyalty discount amount.
     */
    public function getLoyaltyDiscountAmount($totalAmount)
    {
        if ($this->isEligibleForLoyaltyDiscount()) {
            return $totalAmount * 0.05; // 5% discount
        }
        return 0;
    }
}