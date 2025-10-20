<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class PropRental extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'rental_id',
        'prop_id',
        'rental_customer_id',
        'start_date',
        'end_date',
        'daily_rate',
        'total_amount',
        'security_deposit',
        'amount_paid',
        'status',
        'notes',
        'agreement_signed',
        'returned_at',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'returned_at' => 'datetime',
        'daily_rate' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'agreement_signed' => 'boolean',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($rental) {
            if (empty($rental->rental_id)) {
                $rental->rental_id = 'rental-' . time() . rand(100, 999);
            }
        });
    }

    /**
     * Get the prop for this rental
     */
    public function prop()
    {
        return $this->belongsTo(Prop::class);
    }

    /**
     * Get the customer for this rental
     */
    public function customer()
    {
        return $this->belongsTo(RentalCustomer::class, 'rental_customer_id');
    }

    /**
     * Get the user who created this rental
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if rental is overdue
     */
    public function isOverdue()
    {
        return $this->status === 'active' && $this->end_date->isPast();
    }

    /**
     * Check if rental is due today
     */
    public function isDueToday()
    {
        return $this->status === 'active' && $this->end_date->isToday();
    }

    /**
     * Get rental duration in days
     */
    public function getDurationAttribute()
    {
        return $this->start_date->diffInDays($this->end_date);
    }

    /**
     * Get days remaining
     */
    public function getDaysRemainingAttribute()
    {
        if ($this->status !== 'active') {
            return 0;
        }
        return max(0, Carbon::now()->diffInDays($this->end_date, false));
    }

    /**
     * Scope: Active rentals
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Due today
     */
    public function scopeDueToday($query)
    {
        return $query->where('status', 'active')
            ->whereDate('end_date', Carbon::today());
    }

    /**
     * Scope: Overdue
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'active')
            ->where('end_date', '<', Carbon::now());
    }

    /**
     * Get formatted total amount
     */
    public function getFormattedTotalAmountAttribute()
    {
        return '₦' . number_format($this->total_amount, 2);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'active' => $this->isOverdue() ? 'bg-danger' : ($this->isDueToday() ? 'bg-warning' : 'bg-success'),
            'completed' => 'bg-secondary',
            'overdue' => 'bg-danger',
            'cancelled' => 'bg-dark',
            default => 'bg-secondary',
        };
    }

    /**
     * Get status display text
     */
    public function getStatusDisplayAttribute()
    {
        if ($this->status === 'active') {
            if ($this->isOverdue()) {
                return 'Overdue';
            }
            if ($this->isDueToday()) {
                return 'Due Today';
            }
            return 'Active';
        }
        return ucfirst($this->status);
    }
}