<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prop extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'prop_id',
        'name',
        'category',
        'type',
        'brand',
        'model',
        'daily_rate',
        'status',
        'condition',
        'description',
        'image',
        'serial_number',
        'purchase_date',
        'last_maintenance',
    ];

    protected $casts = [
        'daily_rate' => 'decimal:2',
        'purchase_date' => 'date',
        'last_maintenance' => 'date',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($prop) {
            if (empty($prop->prop_id)) {
                $prop->prop_id = 'inst-' . str_pad(Prop::max('id') + 1, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Get all rentals for this prop
     */
    public function rentals()
    {
        return $this->hasMany(PropRental::class);
    }

    /**
     * Get active rental for this prop
     */
    public function activeRental()
    {
        return $this->hasOne(PropRental::class)->where('status', 'active');
    }

    /**
     * Check if prop is available
     */
    public function isAvailable()
    {
        return $this->status === 'available';
    }

    /**
     * Scope: Available props
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope: By category
     */
    public function scopeByCategory($query, $category)
    {
        if ($category && $category !== 'all') {
            return $query->where('category', $category);
        }
        return $query;
    }

    /**
     * Get formatted daily rate
     */
    public function getFormattedDailyRateAttribute()
    {
        return '₦' . number_format($this->daily_rate, 2);
    }
}