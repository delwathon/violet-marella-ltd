<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudioPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'reference',
        'amount',
        'payment_method',
        'payment_type',
        'transaction_reference',
        'payment_date',
        'status',
        'notes',
        'received_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
    ];

    /**
     * Boot method - auto-generate payment reference
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            // Generate payment reference
            if (!$payment->reference) {
                $payment->reference = 'PAY-' . strtoupper(uniqid());
            }
            
            // Set payment date if not provided
            if (!$payment->payment_date) {
                $payment->payment_date = now();
            }
            
            // Default status
            if (!$payment->status) {
                $payment->status = 'completed';
            }
        });
    }

    /**
     * Relationships
     */

    /**
     * Get the session this payment belongs to
     */
    public function session()
    {
        return $this->belongsTo(StudioSession::class, 'session_id');
    }

    /**
     * Get staff who received payment
     */
    public function receivedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'received_by');
    }

    /**
     * Scopes
     */

    /**
     * Scope: Only completed payments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope: Failed payments
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope: Refunded payments
     */
    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    /**
     * Scope: Filter by payment method
     */
    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Scope: Today's payments
     */
    public function scopeToday($query)
    {
        return $query->whereDate('payment_date', today());
    }

    /**
     * Scope: Date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    /**
     * Business Logic Methods
     */

    /**
     * Check if payment is completed
     * 
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if payment is refunded
     * 
     * @return bool
     */
    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    /**
     * Check if payment is a partial payment
     * 
     * @return bool
     */
    public function isPartial(): bool
    {
        return $this->payment_type === 'partial';
    }

    /**
     * Check if payment is a refund
     * 
     * @return bool
     */
    public function isRefund(): bool
    {
        return $this->payment_type === 'refund';
    }

    /**
     * Process refund
     * 
     * @param float|null $amount
     * @param string|null $reason
     * @return StudioPayment
     */
    public function refund(?float $amount = null, ?string $reason = null): StudioPayment
    {
        // If no amount specified, refund full amount
        $refundAmount = $amount ?? $this->amount;
        
        // Create refund payment record
        $refundPayment = self::create([
            'session_id' => $this->session_id,
            'amount' => -$refundAmount,  // Negative amount for refund
            'payment_method' => $this->payment_method,
            'payment_type' => 'refund',
            'payment_date' => now(),
            'status' => 'completed',
            'notes' => $reason ?? 'Refund for payment ' . $this->reference,
        ]);
        
        // Mark original payment as refunded if full refund
        if ($refundAmount >= $this->amount) {
            $this->update(['status' => 'refunded']);
        }
        
        // Update session payment info
        $session = $this->session;
        $session->amount_paid = max(0, $session->amount_paid - $refundAmount);
        $session->balance = $session->total_amount - $session->amount_paid;
        
        // Update payment status
        if ($session->amount_paid <= 0) {
            $session->payment_status = 'pending';
        } elseif ($session->balance > 0) {
            $session->payment_status = 'partial';
        } else {
            $session->payment_status = 'paid';
        }
        
        $session->save();
        
        return $refundPayment;
    }

    /**
     * Mark payment as failed
     * 
     * @param string $reason
     * @return bool
     */
    public function markFailed(string $reason): bool
    {
        return $this->update([
            'status' => 'failed',
            'notes' => $reason,
        ]);
    }

    /**
     * Accessors
     */

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        $symbol = StudioSetting::currencySymbol();
        $amount = number_format(abs($this->amount), 2);
        
        if ($this->amount < 0) {
            return '-' . $symbol . $amount;
        }
        
        return $symbol . $amount;
    }

    /**
     * Get payment method label
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'cash' => 'Cash',
            'card' => 'Card',
            'transfer' => 'Bank Transfer',
            'other' => 'Other',
            default => ucfirst($this->payment_method),
        };
    }

    /**
     * Get payment type label
     */
    public function getPaymentTypeLabelAttribute(): string
    {
        return match ($this->payment_type) {
            'booking' => 'Booking Payment',
            'overtime' => 'Overtime Payment',
            'full' => 'Full Payment',
            'partial' => 'Partial Payment',
            'refund' => 'Refund',
            default => ucfirst($this->payment_type),
        };
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'badge-success',
            'pending' => 'badge-warning',
            'failed' => 'badge-danger',
            'refunded' => 'badge-info',
            default => 'badge-secondary',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'Completed',
            'pending' => 'Pending',
            'failed' => 'Failed',
            'refunded' => 'Refunded',
            default => 'Unknown',
        };
    }

    /**
     * Static Methods
     */

    /**
     * Get total revenue for date range
     * 
     * @param string $startDate
     * @param string $endDate
     * @return float
     */
    public static function totalRevenue($startDate, $endDate): float
    {
        return self::completed()
                   ->dateRange($startDate, $endDate)
                   ->whereIn('payment_type', ['booking', 'overtime', 'full', 'partial'])
                   ->sum('amount');
    }

    /**
     * Get payments breakdown by method
     * 
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public static function paymentsByMethod($startDate, $endDate): array
    {
        $payments = self::completed()
                        ->dateRange($startDate, $endDate)
                        ->whereIn('payment_type', ['booking', 'overtime', 'full', 'partial'])
                        ->get()
                        ->groupBy('payment_method');
        
        $breakdown = [];
        foreach ($payments as $method => $items) {
            $breakdown[$method] = [
                'count' => $items->count(),
                'total' => $items->sum('amount'),
            ];
        }
        
        return $breakdown;
    }
}