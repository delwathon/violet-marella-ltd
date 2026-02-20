<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StudioSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'customer_id',
        'session_code',
        'qr_code',
        'number_of_people',
        'party_names',
        'check_in_time',
        'scheduled_start_time',
        'actual_start_time',
        'check_out_time',
        'booked_duration',
        'offset_time_applied',
        'actual_duration',
        'overtime_duration',
        'rate_base_time',
        'rate_base_price',
        'rate_per_minute',
        'base_amount',
        'overtime_amount',
        'discount_amount',
        'total_amount',
        'payment_status',
        'payment_method',
        'amount_paid',
        'balance',
        'status',
        'notes',
        'cancellation_reason',
        'created_by',
        'checked_out_by',
    ];

    protected $casts = [
        'number_of_people' => 'integer',
        'party_names' => 'array',
        'check_in_time' => 'datetime',
        'scheduled_start_time' => 'datetime',
        'actual_start_time' => 'datetime',
        'check_out_time' => 'datetime',
        'booked_duration' => 'integer',
        'offset_time_applied' => 'integer',
        'actual_duration' => 'integer',
        'overtime_duration' => 'integer',
        'rate_base_time' => 'decimal:2',
        'rate_base_price' => 'decimal:2',
        'rate_per_minute' => 'decimal:2',
        'base_amount' => 'decimal:2',
        'overtime_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    /**
     * Boot method - auto-generate codes and handle status updates
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($session) {
            // Generate session code
            if (!$session->session_code) {
                $session->session_code = 'SS-' . strtoupper(uniqid());
            }
            
            // Generate QR code
            if (!$session->qr_code) {
                $session->qr_code = 'QR-' . time() . '-' . rand(1000, 9999);
            }
            
            // Set scheduled start time (check_in + offset)
            if (!$session->scheduled_start_time) {
                $offsetMinutes = $session->offset_time_applied ?? StudioSetting::offsetTime();
                $session->scheduled_start_time = Carbon::parse($session->check_in_time)
                                                       ->addMinutes($offsetMinutes);
            }
        });
    }

    /**
     * Relationships
     */

    /**
     * Get the category this session belongs to
     */
    public function category()
    {
        return $this->belongsTo(StudioCategory::class, 'category_id');
    }

    /**
     * Get the customer
     */
    public function customer()
    {
        return $this->belongsTo(StudioCustomer::class, 'customer_id');
    }

    /**
     * Get all payments for this session
     */
    public function payments()
    {
        return $this->hasMany(StudioPayment::class, 'session_id');
    }

    /**
     * Get staff who created the session
     */
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Get staff who checked out
     */
    public function checkoutStaff()
    {
        return $this->belongsTo(\App\Models\User::class, 'checked_out_by');
    }

    /**
     * Scopes
     */

    /**
     * Scope: Pending sessions (waiting for offset time)
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Active sessions
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'active', 'overtime']);
    }

    /**
     * Scope: Running sessions (timer is counting)
     */
    public function scopeRunning($query)
    {
        return $query->whereIn('status', ['active', 'overtime']);
    }

    /**
     * Scope: Completed sessions
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope: Today's sessions
     */
    public function scopeToday($query)
    {
        return $query->whereDate('check_in_time', today());
    }

    /**
     * Scope: Filter by category
     */
    public function scopeForCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope: Filter by customer
     */
    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Scope: Filter by payment status
     */
    public function scopePaymentStatus($query, $status)
    {
        return $query->where('payment_status', $status);
    }

    /**
     * Business Logic Methods
     */

    /**
     * Auto-start pending sessions whose preparation window has elapsed.
     * Uses scheduled_start_time as the actual start time so billing stays accurate.
     */
    public static function autoStartDueSessions(?int $sessionId = null): int
    {
        $query = self::query()
            ->where('status', 'pending')
            ->whereNull('actual_start_time')
            ->whereNotNull('scheduled_start_time')
            ->where('scheduled_start_time', '<=', now());

        if ($sessionId !== null) {
            $query->whereKey($sessionId);
        }

        return $query->update([
            'status' => 'active',
            'actual_start_time' => DB::raw('scheduled_start_time'),
            'updated_at' => now(),
        ]);
    }

    /**
     * Check if session timer has started
     * 
     * @return bool
     */
    public function hasTimerStarted(): bool
    {
        return $this->actual_start_time !== null;
    }

    /**
     * Check if offset period is over and timer should start
     * 
     * @return bool
     */
    public function shouldStartTimer(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }
        
        return now()->gte($this->scheduled_start_time);
    }

    /**
     * Start the session timer
     * 
     * @return bool
     */
    public function startTimer(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }
        
        return $this->update([
            'actual_start_time' => now(),
            'status' => 'active',
        ]);
    }

    /**
     * Get current duration in minutes
     * Based on actual_start_time, not check_in_time
     * 
     * @return int
     */
    public function getCurrentDuration(): int
    {
        if (!$this->hasTimerStarted()) {
            return 0;
        }
        
        if ($this->check_out_time) {
            return $this->actual_duration ?? 0;
        }
        
        return Carbon::parse($this->actual_start_time)->diffInMinutes(now());
    }

    /**
     * Get time remaining in booked duration
     * 
     * @return int (negative if overtime)
     */
    public function getTimeRemaining(): int
    {
        $elapsed = $this->getCurrentDuration();
        return $this->booked_duration - $elapsed;
    }

    /**
     * Check if session is in overtime
     * 
     * @return bool
     */
    public function isOvertime(): bool
    {
        return $this->getCurrentDuration() > $this->booked_duration;
    }

    /**
     * Get overtime minutes
     * 
     * @return int
     */
    public function getOvertimeMinutes(): int
    {
        $overtime = $this->getCurrentDuration() - $this->booked_duration;
        return max(0, $overtime);
    }

    /**
     * Calculate total amount based on actual duration
     * Uses rate snapshot from when session was created
     * 
     * @return float
     */
    public function calculateTotalAmount(): float
    {
        $duration = $this->getCurrentDuration();
        
        if ($duration <= 0) {
            return (float) $this->base_amount;
        }
        
        // If within booked time or equals base time, use base amount
        if ($duration <= $this->rate_base_time) {
            return (float) $this->rate_base_price;
        }
        
        // Calculate overtime charges
        $extraMinutes = $duration - $this->rate_base_time;
        $overtimeCharge = $extraMinutes * $this->rate_per_minute;
        
        return round($this->rate_base_price + $overtimeCharge, 2);
    }

    /**
     * Update session amounts
     * 
     * @return void
     */
    public function updateAmounts(): void
    {
        $totalBeforeDiscount = $this->calculateTotalAmount();
        
        // Base amount is always the rate's base price
        $this->base_amount = $this->rate_base_price;
        
        // Overtime amount
        if ($this->getCurrentDuration() > $this->rate_base_time) {
            $extraMinutes = $this->getCurrentDuration() - $this->rate_base_time;
            $this->overtime_amount = round($extraMinutes * $this->rate_per_minute, 2);
        } else {
            $this->overtime_amount = 0;
        }
        
        // Total amount after discount
        $this->total_amount = max(0, $totalBeforeDiscount - $this->discount_amount);
        
        // Balance
        $this->balance = max(0, $this->total_amount - $this->amount_paid);
        
        $this->save();
    }

    /**
     * Checkout session
     * 
     * @param string|null $paymentMethod
     * @param float $discountAmount
     * @param int|null $userId
     * @return bool
     */
    public function checkout(?string $paymentMethod = null, float $discountAmount = 0, ?int $userId = null): bool
    {
        if (!in_array($this->status, ['pending', 'active', 'overtime'])) {
            return false;
        }
        
        $checkoutTime = now();
        
        // Calculate actual duration
        $actualDuration = $this->hasTimerStarted() 
                         ? Carbon::parse($this->actual_start_time)->diffInMinutes($checkoutTime)
                         : 0;
        
        // Calculate overtime
        $overtimeDuration = max(0, $actualDuration - $this->booked_duration);
        
        // Update session
        $this->check_out_time = $checkoutTime;
        $this->actual_duration = $actualDuration;
        $this->overtime_duration = $overtimeDuration;
        $this->discount_amount = $discountAmount;
        $this->status = 'completed';
        $this->checked_out_by = $userId;
        
        if ($paymentMethod) {
            $this->payment_method = $paymentMethod;
        }
        
        $this->save();
        
        // Recalculate amounts
        $this->updateAmounts();
        
        return true;
    }

    /**
     * Process payment
     * 
     * @param float $amount
     * @param string $paymentMethod
     * @param int|null $userId
     * @return StudioPayment
     */
    public function processPayment(float $amount, string $paymentMethod, ?int $userId = null): StudioPayment
    {
        // Create payment record
        $payment = $this->payments()->create([
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'payment_type' => $this->amount_paid > 0 ? 'partial' : 'full',
            'payment_date' => now(),
            'received_by' => $userId,
        ]);
        
        // Update session payment info
        $this->amount_paid += $amount;
        $this->balance = max(0, $this->total_amount - $this->amount_paid);
        
        // Update payment status
        if ($this->balance <= 0) {
            $this->payment_status = 'paid';
        } else {
            $this->payment_status = 'partial';
        }
        
        $this->save();
        
        return $payment;
    }

    /**
     * Cancel session
     * 
     * @param string $reason
     * @return bool
     */
    public function cancel(string $reason): bool
    {
        if (!in_array($this->status, ['pending', 'active'])) {
            return false;
        }
        
        return $this->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
        ]);
    }

    /**
     * Mark as no-show
     * 
     * @return bool
     */
    public function markNoShow(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }
        
        return $this->update([
            'status' => 'no_show',
        ]);
    }

    /**
     * Accessors
     */

    /**
     * Get formatted duration
     */
    public function getFormattedDurationAttribute(): string
    {
        $minutes = $this->getCurrentDuration();
        return $this->formatMinutes($minutes);
    }

    /**
     * Get formatted booked duration
     */
    public function getFormattedBookedDurationAttribute(): string
    {
        return $this->formatMinutes($this->booked_duration);
    }

    /**
     * Get formatted time remaining
     */
    public function getFormattedTimeRemainingAttribute(): string
    {
        $remaining = $this->getTimeRemaining();
        
        if ($remaining < 0) {
            return 'OVERTIME: ' . $this->formatMinutes(abs($remaining));
        }
        
        return $this->formatMinutes($remaining);
    }

    /**
     * Get formatted total amount
     */
    public function getFormattedTotalAmountAttribute(): string
    {
        $symbol = StudioSetting::currencySymbol();
        return $symbol . number_format($this->total_amount, 2);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'badge-warning',
            'active' => 'badge-primary',
            'overtime' => 'badge-danger',
            'completed' => 'badge-success',
            'cancelled' => 'badge-secondary',
            'no_show' => 'badge-dark',
            default => 'badge-secondary',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'active' => 'Active',
            'overtime' => 'Overtime',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'no_show' => 'No Show',
            default => 'Unknown',
        };
    }

    /**
     * Helper Methods
     */

    /**
     * Format minutes to hours and minutes
     * 
     * @param int $minutes
     * @return string
     */
    protected function formatMinutes(int $minutes): string
    {
        if ($minutes < 0) {
            $minutes = 0;
        }
        
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        
        if ($hours > 0) {
            return "{$hours}h {$mins}m";
        }
        
        return "{$mins}m";
    }
}
