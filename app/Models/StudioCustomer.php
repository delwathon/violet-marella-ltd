<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class StudioCustomer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'date_of_birth',
        'total_sessions',
        'total_spent',
        'last_visit',
        'notes',
        'preferences',
        'is_active',
        'is_blacklisted',
        'blacklist_reason',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'total_sessions' => 'integer',
        'total_spent' => 'decimal:2',
        'last_visit' => 'datetime',
        'preferences' => 'array',
        'is_active' => 'boolean',
        'is_blacklisted' => 'boolean',
    ];

    /**
     * Relationships
     */

    /**
     * Get all sessions for this customer
     */
    public function sessions()
    {
        return $this->hasMany(StudioSession::class, 'customer_id');
    }

    /**
     * Get active session (if any)
     */
    public function activeSession()
    {
        return $this->hasOne(StudioSession::class, 'customer_id')
                    ->whereIn('status', ['pending', 'active', 'overtime']);
    }

    /**
     * Get completed sessions
     */
    public function completedSessions()
    {
        return $this->hasMany(StudioSession::class, 'customer_id')
                    ->where('status', 'completed');
    }

    /**
     * Scopes
     */

    /**
     * Scope: Only active customers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where('is_blacklisted', false);
    }

    /**
     * Scope: Blacklisted customers
     */
    public function scopeBlacklisted($query)
    {
        return $query->where('is_blacklisted', true);
    }

    /**
     * Scope: Search customers
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('phone', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%");
        });
    }

    /**
     * Scope: Recent customers (visited in last 30 days)
     */
    public function scopeRecent($query)
    {
        return $query->where('last_visit', '>=', Carbon::now()->subDays(30));
    }

    /**
     * Scope: VIP customers (spent over threshold)
     */
    public function scopeVip($query, float $threshold = 100000)
    {
        return $query->where('total_spent', '>=', $threshold);
    }

    /**
     * Business Logic Methods
     */

    /**
     * Check if customer has active session
     * 
     * @return bool
     */
    public function hasActiveSession(): bool
    {
        return $this->activeSession()->exists();
    }

    /**
     * Check if customer is allowed to book
     * 
     * @return bool
     */
    public function canBook(): bool
    {
        return $this->is_active && !$this->is_blacklisted;
    }

    /**
     * Update customer statistics
     * Should be called after a session is completed
     * 
     * @return void
     */
    public function updateStatistics(): void
    {
        $this->total_sessions = $this->completedSessions()->count();
        
        $this->total_spent = $this->completedSessions()
                                  ->where('payment_status', 'paid')
                                  ->sum('total_amount');
        
        $lastSession = $this->sessions()
                            ->whereNotNull('check_in_time')
                            ->latest('check_in_time')
                            ->first();
        
        $this->last_visit = $lastSession?->check_in_time;
        
        $this->save();
    }

    /**
     * Blacklist customer
     * 
     * @param string $reason
     * @return bool
     */
    public function blacklist(string $reason): bool
    {
        return $this->update([
            'is_blacklisted' => true,
            'blacklist_reason' => $reason,
            'is_active' => false,
        ]);
    }

    /**
     * Remove from blacklist
     * 
     * @return bool
     */
    public function removeFromBlacklist(): bool
    {
        return $this->update([
            'is_blacklisted' => false,
            'blacklist_reason' => null,
            'is_active' => true,
        ]);
    }

    /**
     * Get customer's favorite category (most booked)
     * 
     * @return StudioCategory|null
     */
    public function favoriteCategory(): ?StudioCategory
    {
        $categoryId = $this->completedSessions()
                           ->select('category_id')
                           ->groupBy('category_id')
                           ->orderByRaw('COUNT(*) DESC')
                           ->value('category_id');
        
        return $categoryId ? StudioCategory::find($categoryId) : null;
    }

    /**
     * Get average session duration in minutes
     * 
     * @return int
     */
    public function averageSessionDuration(): int
    {
        return (int) $this->completedSessions()
                          ->whereNotNull('actual_duration')
                          ->avg('actual_duration');
    }

    /**
     * Get average spending per session
     * 
     * @return float
     */
    public function averageSpending(): float
    {
        $count = $this->completedSessions()
                      ->where('payment_status', 'paid')
                      ->count();
        
        if ($count === 0) {
            return 0;
        }
        
        return round($this->total_spent / $count, 2);
    }

    /**
     * Get days since last visit
     * 
     * @return int|null
     */
    public function daysSinceLastVisit(): ?int
    {
        if (!$this->last_visit) {
            return null;
        }
        
        return Carbon::parse($this->last_visit)->diffInDays(now());
    }

    /**
     * Check if customer is new (less than 3 sessions)
     * 
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->total_sessions < 3;
    }

    /**
     * Check if customer is regular (10+ sessions)
     * 
     * @return bool
     */
    public function isRegular(): bool
    {
        return $this->total_sessions >= 10;
    }

    /**
     * Accessors
     */

    /**
     * Get customer initials
     */
    public function getInitialsAttribute(): string
    {
        $names = explode(' ', $this->name);
        $initials = '';
        
        foreach (array_slice($names, 0, 2) as $name) {
            $initials .= strtoupper(substr($name, 0, 1));
        }
        
        return $initials ?: 'CU';
    }

    /**
     * Get formatted total spent
     */
    public function getFormattedTotalSpentAttribute(): string
    {
        $symbol = StudioSetting::currencySymbol();
        return $symbol . number_format($this->total_spent, 2);
    }

    /**
     * Get customer status label
     */
    public function getStatusLabelAttribute(): string
    {
        if ($this->is_blacklisted) {
            return 'Blacklisted';
        }
        
        if (!$this->is_active) {
            return 'Inactive';
        }
        
        if ($this->isNew()) {
            return 'New';
        }
        
        if ($this->isRegular()) {
            return 'Regular';
        }
        
        return 'Active';
    }

    /**
     * Get customer tier (Bronze, Silver, Gold, Platinum)
     */
    public function getTierAttribute(): string
    {
        if ($this->total_spent >= 500000) {
            return 'Platinum';
        }
        
        if ($this->total_spent >= 200000) {
            return 'Gold';
        }
        
        if ($this->total_spent >= 100000) {
            return 'Silver';
        }
        
        return 'Bronze';
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeAttribute(): string
    {
        if ($this->is_blacklisted) {
            return 'badge-danger';
        }
        
        if (!$this->is_active) {
            return 'badge-secondary';
        }
        
        if ($this->isRegular()) {
            return 'badge-success';
        }
        
        if ($this->isNew()) {
            return 'badge-info';
        }
        
        return 'badge-primary';
    }

    /**
     * Get age
     */
    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth ? 
               Carbon::parse($this->date_of_birth)->age : 
               null;
    }
}