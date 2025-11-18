<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'is_active',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'total_spent' => 'decimal:2',
        'last_visit' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get all sessions for this customer
     */
    public function sessions()
    {
        return $this->hasMany(StudioSession::class, 'customer_id');
    }

    /**
     * Get active session
     */
    public function activeSession()
    {
        return $this->hasOne(StudioSession::class, 'customer_id')->where('status', 'active');
    }

    /**
     * Get customer initials
     */
    public function getInitialsAttribute()
    {
        $names = explode(' ', $this->name);
        $initials = '';
        
        foreach (array_slice($names, 0, 2) as $name) {
            $initials .= strtoupper(substr($name, 0, 1));
        }
        
        return $initials;
    }

    /**
     * Update customer statistics
     */
    public function updateStatistics()
    {
        $this->total_sessions = $this->sessions()->where('status', 'completed')->count();
        $this->total_spent = $this->sessions()
            ->where('status', 'completed')
            ->where('payment_status', 'paid')
            ->sum('total_amount');
        $this->last_visit = $this->sessions()->latest('check_in_time')->first()?->check_in_time;
        $this->save();
    }

    /**
     * Scope: Only active customers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
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
}