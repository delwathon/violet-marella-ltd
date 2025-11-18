<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudioRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'base_time',
        'base_amount',
        'per_minute_rate',
        'hourly_rate',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'base_amount' => 'decimal:2',
        'per_minute_rate' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($rate) {
            // Calculate per minute rate
            $rate->per_minute_rate = $rate->base_amount / $rate->base_time;
            
            // Calculate hourly rate
            $rate->hourly_rate = $rate->per_minute_rate * 60;
        });

        static::saved(function ($rate) {
            // If this is set as default, unset others
            if ($rate->is_default) {
                self::where('id', '!=', $rate->id)->update(['is_default' => false]);
            }
        });
    }

    /**
     * Get default rate
     */
    public static function getDefault()
    {
        return self::where('is_default', true)->where('is_active', true)->first()
            ?? self::where('is_active', true)->first();
    }

    /**
     * Scope: Active rates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}