<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class StudioSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'studio_id',
        'customer_id',
        'session_code',
        'check_in_time',
        'check_out_time',
        'expected_duration',
        'actual_duration',
        'base_amount',
        'extra_amount',
        'discount_amount',
        'total_amount',
        'payment_status',
        'payment_method',
        'status',
        'notes',
        'qr_code',
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'base_amount' => 'decimal:2',
        'extra_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($session) {
            if (!$session->session_code) {
                $session->session_code = 'SS-' . strtoupper(uniqid());
            }
            if (!$session->qr_code) {
                $session->qr_code = 'QR-' . time() . '-' . rand(1000, 9999);
            }
        });
    }

    /**
     * Get studio relationship
     */
    public function studio()
    {
        return $this->belongsTo(Studio::class);
    }

    /**
     * Get customer relationship
     */
    public function customer()
    {
        return $this->belongsTo(StudioCustomer::class, 'customer_id');
    }

    /**
     * Get payments
     */
    public function payments()
    {
        return $this->hasMany(StudioPayment::class, 'session_id');
    }

    /**
     * Get current duration in minutes
     */
    public function getCurrentDuration()
    {
        if ($this->check_out_time) {
            return $this->actual_duration;
        }
        
        return Carbon::parse($this->check_in_time)->diffInMinutes(now());
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDurationAttribute()
    {
        $minutes = $this->getCurrentDuration();
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        
        if ($hours > 0) {
            return "{$hours}h {$mins}m";
        }
        return "{$mins}m";
    }

    /**
     * Check if session is overtime
     */
    public function isOvertime()
    {
        return $this->getCurrentDuration() > $this->expected_duration;
    }

    /**
     * Calculate total amount
     */
    public function calculateAmount($baseTime = 30, $baseAmount = 2000)
    {
        $duration = $this->getCurrentDuration();
        
        if ($duration <= $baseTime) {
            $this->base_amount = $baseAmount;
            $this->extra_amount = 0;
        } else {
            $this->base_amount = $baseAmount;
            $extraMinutes = $duration - $baseTime;
            $this->extra_amount = ($baseAmount / $baseTime) * $extraMinutes;
        }
        
        $this->total_amount = $this->base_amount + $this->extra_amount - $this->discount_amount;
        $this->actual_duration = $duration;
    }

    /**
     * Check out session
     */
    public function checkout($paymentMethod = null, $discountAmount = 0)
    {
        $this->check_out_time = now();
        $this->discount_amount = $discountAmount;
        $this->calculateAmount();
        $this->status = 'completed';
        
        if ($paymentMethod) {
            $this->payment_method = $paymentMethod;
            $this->payment_status = 'paid';
        }
        
        $this->save();
        
        // Update studio status
        $this->studio->markAvailable();
        
        // Update customer statistics
        $this->customer->updateStatistics();
        
        return $this;
    }

    /**
     * Scope: Active sessions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Today's sessions
     */
    public function scopeToday($query)
    {
        return $query->whereDate('check_in_time', today());
    }

    /**
     * Scope: Completed sessions
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}