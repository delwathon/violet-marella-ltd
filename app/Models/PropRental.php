<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * PropRental Model with Balance and Refund Tracking
 * Add these fields and methods to your existing PropRental model
 */
class PropRental extends Model
{
    use HasFactory;

    protected $fillable = [
        'prop_id',
        'rental_customer_id',
        'rental_id',
        'start_date',
        'end_date',
        'daily_rate',
        'total_amount',
        'security_deposit',
        'amount_paid',
        'balance_due',
        'notes',
        'agreement_signed',
        'status',
        'returned_at',
        'cancelled_at',
        'cancelled_by',
        'refund_amount',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'returned_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'daily_rate' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'agreement_signed' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($rental) {
            // Generate unique rental ID if not set
            if (empty($rental->rental_id)) {
                $rental->rental_id = 'RNT-' . strtoupper(uniqid());
            }
            
            // Auto-calculate balance_due if not set
            if (is_null($rental->balance_due)) {
                $rental->balance_due = $rental->total_amount - ($rental->amount_paid ?? 0);
            }
        });
    }

    // Relationships
    public function prop()
    {
        return $this->belongsTo(Prop::class)->withTrashed();
    }

    public function customer()
    {
        return $this->belongsTo(RentalCustomer::class, 'rental_customer_id')->withTrashed();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function canceller()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeDueToday($query)
    {
        return $query->where('status', 'active')
                    ->whereDate('end_date', Carbon::today());
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'active')
                    ->where('end_date', '<', Carbon::now());
    }

    // Accessors
    public function getFormattedTotalAmountAttribute()
    {
        return '₦' . number_format($this->total_amount, 2);
    }

    public function getFormattedAmountPaidAttribute()
    {
        return '₦' . number_format($this->amount_paid, 2);
    }

    public function getFormattedBalanceDueAttribute()
    {
        return '₦' . number_format($this->balance_due, 2);
    }

    public function getFormattedSecurityDepositAttribute()
    {
        return '₦' . number_format($this->security_deposit, 2);
    }

    public function getFormattedRefundAmountAttribute()
    {
        return '₦' . number_format($this->refund_amount, 2);
    }

    public function getDurationAttribute()
    {
        return $this->start_date->diffInDays($this->end_date);
    }

    public function getDaysRemainingAttribute()
    {
        if ($this->status !== 'active') {
            return 0;
        }
        
        $remaining = Carbon::now()->diffInDays($this->end_date, false);
        return max(0, (int) $remaining);
    }

    public function getStatusDisplayAttribute()
    {
        return match($this->status) {
            'active' => 'Active',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'overdue' => 'Overdue',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'active' => 'bg-success',
            'completed' => 'bg-primary',
            'cancelled' => 'bg-danger',
            'overdue' => 'bg-warning',
            default => 'bg-secondary',
        };
    }

    public function getPaymentStatusAttribute()
    {
        if ($this->balance_due <= 0) {
            return 'Fully Paid';
        } elseif ($this->amount_paid > 0) {
            return 'Partially Paid';
        } else {
            return 'Unpaid';
        }
    }

    public function getPaymentStatusBadgeClassAttribute()
    {
        if ($this->balance_due <= 0) {
            return 'bg-success';
        } elseif ($this->amount_paid > 0) {
            return 'bg-warning';
        } else {
            return 'bg-danger';
        }
    }

    // Methods
    public function isOverdue()
    {
        return $this->status === 'active' && $this->end_date->isPast();
    }

    public function hasBalance()
    {
        return $this->balance_due > 0;
    }

    public function canBeCancelled()
    {
        return $this->status === 'active';
    }

    public function canBeExtended()
    {
        return $this->status === 'active';
    }

    public function canBeReturned()
    {
        return $this->status === 'active';
    }

    /**
     * Record balance payment
     */
    public function recordBalancePayment($amount)
    {
        if ($amount <= 0 || $amount > $this->balance_due) {
            return false;
        }

        $this->amount_paid += $amount;
        $this->balance_due -= $amount;
        $this->save();

        return true;
    }

    /**
     * Process refund for cancellation
     */
    public function processRefund()
    {
        if ($this->status !== 'cancelled') {
            return false;
        }

        $this->refund_amount = $this->amount_paid;
        $this->save();

        return true;
    }
}
