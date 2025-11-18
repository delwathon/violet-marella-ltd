<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Studio extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'status',
        'studio_rate_id',
        'capacity',
        'equipment',
        'is_active',
    ];

    protected $casts = [
        'equipment' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the rate for this studio
     */
    public function rate()
    {
        return $this->belongsTo(StudioRate::class, 'studio_rate_id');
    }

    /**
     * Get active session for this studio
     */
    public function activeSession()
    {
        return $this->hasOne(StudioSession::class)->where('status', 'active');
    }

    /**
     * Get all sessions for this studio
     */
    public function sessions()
    {
        return $this->hasMany(StudioSession::class);
    }

    /**
     * Check if studio is available
     */
    public function isAvailable()
    {
        return $this->status === 'available' && $this->is_active;
    }

    /**
     * Mark studio as occupied
     */
    public function markOccupied()
    {
        $this->update(['status' => 'occupied']);
    }

    /**
     * Mark studio as available
     */
    public function markAvailable()
    {
        $this->update(['status' => 'available']);
    }

    /**
     * Mark studio under maintenance
     */
    public function markMaintenance()
    {
        $this->update(['status' => 'maintenance']);
    }

    /**
     * Get today's sessions
     */
    public function todaySessions()
    {
        return $this->sessions()
            ->whereDate('check_in_time', today())
            ->orderBy('check_in_time', 'desc');
    }

    /**
     * Get today's revenue
     */
    public function todayRevenue()
    {
        return $this->sessions()
            ->whereDate('check_in_time', today())
            ->where('payment_status', 'paid')
            ->sum('total_amount');
    }

    /**
     * Get hourly rate from assigned rate or default
     */
    public function getHourlyRateAttribute()
    {
        if ($this->rate) {
            return $this->rate->hourly_rate;
        }
        
        $defaultRate = StudioRate::getDefault();
        return $defaultRate ? $defaultRate->hourly_rate : 4000;
    }

    /**
     * Scope: Only active studios
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Available studios
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')->where('is_active', true);
    }

    /**
     * Get equipment attribute - ensure it's always an array
     */
    public function getEquipmentAttribute($value)
    {
        if (is_null($value)) {
            return [];
        }
        
        if (is_array($value)) {
            return $value;
        }
        
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }
}