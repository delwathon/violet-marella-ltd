<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RentalCustomer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'name',
        'email',
        'phone',
        'address',
        'id_number',
        'total_rentals',
        'current_rentals',
        'total_spent',
        'status',
    ];

    protected $casts = [
        'total_spent' => 'decimal:2',
        'total_rentals' => 'integer',
        'current_rentals' => 'integer',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            if (empty($customer->customer_id)) {
                $customer->customer_id = 'cust-' . str_pad(RentalCustomer::max('id') + 1, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Get all rentals for this customer
     */
    public function rentals()
    {
        return $this->hasMany(PropRental::class, 'rental_customer_id');
    }

    /**
     * Get active rentals for this customer
     */
    public function activeRentals()
    {
        return $this->hasMany(PropRental::class, 'rental_customer_id')->where('status', 'active');
    }

    /**
     * Increment rental stats
     */
    public function incrementRentalStats($amount)
    {
        $this->increment('total_rentals');
        $this->increment('current_rentals');
        $this->increment('total_spent', $amount);
    }

    /**
     * Decrement current rentals
     */
    public function decrementCurrentRentals()
    {
        if ($this->current_rentals > 0) {
            $this->decrement('current_rentals');
        }
    }

    /**
     * Get formatted total spent
     */
    public function getFormattedTotalSpentAttribute()
    {
        return '₦' . number_format($this->total_spent, 2);
    }

    /**
     * Get initials for avatar
     */
    public function getInitialsAttribute()
    {
        $names = explode(' ', $this->name);
        if (count($names) >= 2) {
            return strtoupper(substr($names[0], 0, 1) . substr($names[1], 0, 1));
        }
        return strtoupper(substr($this->name, 0, 2));
    }
}