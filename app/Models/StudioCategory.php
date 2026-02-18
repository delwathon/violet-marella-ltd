<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StudioCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'base_time',
        'base_price',
        'per_minute_rate',
        'hourly_rate',
        'max_occupants',
        'max_concurrent_sessions',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'base_time' => 'integer',
        'base_price' => 'decimal:2',
        'per_minute_rate' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'max_occupants' => 'integer',
        'max_concurrent_sessions' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Boot method - auto-calculate rates and generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            // Generate slug if not provided
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::saving(function ($category) {
            // Auto-calculate per_minute_rate
            if ($category->base_time > 0) {
                $category->per_minute_rate = round($category->base_price / $category->base_time, 2);
            }
            
            // Auto-calculate hourly_rate
            if ($category->per_minute_rate > 0) {
                $category->hourly_rate = round($category->per_minute_rate * 60, 2);
            }
        });
    }

    /**
     * Relationships
     */

    /**
     * Get all rooms in this category
     */
    public function rooms()
    {
        return $this->hasMany(StudioRoom::class, 'category_id');
    }

    /**
     * Get all sessions for this category
     */
    public function sessions()
    {
        return $this->hasMany(StudioSession::class, 'category_id');
    }

    /**
     * Get active sessions
     */
    public function activeSessions()
    {
        return $this->hasMany(StudioSession::class, 'category_id')
                    ->whereIn('status', ['pending', 'active', 'overtime']);
    }

    /**
     * Scopes
     */

    /**
     * Scope: Only active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Order by sort_order then name
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Business Logic Methods
     */

    /**
     * Check if category is available for booking
     * 
     * @return bool
     */
    public function isAvailable(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if category can accept more sessions
     * 
     * @return bool
     */
    public function canAcceptMoreSessions(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $activeCount = $this->activeSessions()->count();
        return $activeCount < $this->max_concurrent_sessions;
    }

    /**
     * Get available session slots
     * 
     * @return int
     */
    public function availableSlots(): int
    {
        $activeCount = $this->activeSessions()->count();
        return max(0, $this->max_concurrent_sessions - $activeCount);
    }

    /**
     * Calculate price for given duration
     * 
     * @param int $minutes
     * @return float
     */
    public function calculatePrice(int $minutes): float
    {
        if ($minutes <= 0) {
            return 0;
        }

        // If duration is less than or equal to base time, charge base price
        if ($minutes <= $this->base_time) {
            return (float) $this->base_price;
        }

        // Calculate overtime
        $extraMinutes = $minutes - $this->base_time;
        $extraCharge = $extraMinutes * $this->per_minute_rate;

        return round($this->base_price + $extraCharge, 2);
    }

    /**
     * Get today's statistics
     * 
     * @return array
     */
    public function todayStats(): array
    {
        $today = today();
        
        $sessions = $this->sessions()
                         ->whereDate('check_in_time', $today)
                         ->get();
        
        $completed = $sessions->where('status', 'completed');
        
        return [
            'total_sessions' => $sessions->count(),
            'active_sessions' => $sessions->whereIn('status', ['pending', 'active', 'overtime'])->count(),
            'completed_sessions' => $completed->count(),
            'revenue' => $completed->where('payment_status', 'paid')->sum('total_amount'),
            'total_minutes' => $completed->sum('actual_duration'),
        ];
    }

    /**
     * Get occupancy rate for today
     * 
     * @return float Percentage (0-100)
     */
    public function todayOccupancyRate(): float
    {
        $stats = $this->todayStats();
        
        // Maximum possible minutes = max_concurrent_sessions × 1440 (minutes in a day)
        $maxPossibleMinutes = $this->max_concurrent_sessions * 1440;
        
        if ($maxPossibleMinutes <= 0) {
            return 0;
        }
        
        $occupancyRate = ($stats['total_minutes'] / $maxPossibleMinutes) * 100;
        
        return round($occupancyRate, 2);
    }

    /**
     * Get active rooms count
     * 
     * @return int
     */
    public function activeRoomsCount(): int
    {
        return $this->rooms()->where('is_active', true)->count();
    }

    /**
     * Get available rooms (not in maintenance)
     * 
     * @return int
     */
    public function availableRoomsCount(): int
    {
        return $this->rooms()
                    ->where('is_active', true)
                    ->where('status', 'available')
                    ->count();
    }

    /**
     * Check if has rooms configured
     * 
     * @return bool
     */
    public function hasRooms(): bool
    {
        return $this->rooms()->exists();
    }

    /**
     * Accessor: Get formatted base price
     */
    public function getFormattedBasePriceAttribute(): string
    {
        $symbol = StudioSetting::currencySymbol();
        return $symbol . number_format($this->base_price, 2);
    }

    /**
     * Accessor: Get formatted hourly rate
     */
    public function getFormattedHourlyRateAttribute(): string
    {
        $symbol = StudioSetting::currencySymbol();
        return $symbol . number_format($this->hourly_rate, 2);
    }

    /**
     * Accessor: Get badge class based on color
     */
    public function getBadgeClassAttribute(): string
    {
        // Map colors to Bootstrap/Tailwind badge classes
        $colorMap = [
            '#3b82f6' => 'badge-primary',
            '#8b5cf6' => 'badge-purple',
            '#ec4899' => 'badge-pink',
            '#10b981' => 'badge-success',
            '#f59e0b' => 'badge-warning',
        ];

        return $colorMap[$this->color] ?? 'badge-info';
    }

    /**
     * Validate if party size is acceptable
     * 
     * @param int $numberOfPeople
     * @return bool
     */
    public function canAccommodate(int $numberOfPeople): bool
    {
        return $numberOfPeople > 0 && $numberOfPeople <= $this->max_occupants;
    }

    /**
     * Get pricing breakdown for display
     * 
     * @return array
     */
    public function pricingBreakdown(): array
    {
        return [
            'base_time' => $this->base_time,
            'base_time_formatted' => $this->base_time . ' minutes',
            'base_price' => $this->base_price,
            'base_price_formatted' => $this->formatted_base_price,
            'per_minute_rate' => $this->per_minute_rate,
            'per_minute_formatted' => StudioSetting::currencySymbol() . number_format($this->per_minute_rate, 2),
            'hourly_rate' => $this->hourly_rate,
            'hourly_rate_formatted' => $this->formatted_hourly_rate,
            'overtime_example' => $this->calculatePrice($this->base_time + 15),
        ];
    }
}