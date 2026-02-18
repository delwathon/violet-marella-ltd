<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class StudioSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    /**
     * Get a setting value by key
     * Supports type casting based on the 'type' field
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        // Cache settings for 1 hour
        $cacheKey = "studio_setting_{$key}";
        
        return Cache::remember($cacheKey, 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            
            if (!$setting) {
                return $default;
            }
            
            return self::castValue($setting->value, $setting->type);
        });
    }

    /**
     * Set a setting value
     * 
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @return bool
     */
    public static function set(string $key, $value, string $type = 'string'): bool
    {
        // Convert value to string for storage
        $stringValue = self::valueToString($value, $type);
        
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $stringValue,
                'type' => $type,
            ]
        );
        
        // Clear cache
        Cache::forget("studio_setting_{$key}");
        
        return $setting->wasRecentlyCreated || $setting->wasChanged();
    }

    /**
     * Get multiple settings at once
     * 
     * @param array $keys
     * @return array
     */
    public static function getMany(array $keys): array
    {
        $settings = self::whereIn('key', $keys)->get();
        
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting->key] = self::castValue($setting->value, $setting->type);
        }
        
        return $result;
    }

    /**
     * Get all settings grouped by key
     * 
     * @return array
     */
    public static function getAll(): array
    {
        return Cache::remember('studio_settings_all', 3600, function () {
            $settings = self::all();
            $result = [];
            
            foreach ($settings as $setting) {
                $result[$setting->key] = [
                    'value' => self::castValue($setting->value, $setting->type),
                    'type' => $setting->type,
                    'description' => $setting->description,
                ];
            }
            
            return $result;
        });
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache(): void
    {
        $settings = self::all();
        foreach ($settings as $setting) {
            Cache::forget("studio_setting_{$setting->key}");
        }
        Cache::forget('studio_settings_all');
    }

    /**
     * Cast value based on type
     * 
     * @param string|null $value
     * @param string $type
     * @return mixed
     */
    protected static function castValue($value, string $type)
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'float', 'decimal' => (float) $value,
            'json' => json_decode($value, true),
            'array' => json_decode($value, true) ?: [],
            default => $value,
        };
    }

    /**
     * Convert value to string for storage
     * 
     * @param mixed $value
     * @param string $type
     * @return string
     */
    protected static function valueToString($value, string $type): string
    {
        return match ($type) {
            'boolean' => $value ? 'true' : 'false',
            'json', 'array' => json_encode($value),
            default => (string) $value,
        };
    }

    /**
     * Boot method - clear cache on save/delete
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($setting) {
            Cache::forget("studio_setting_{$setting->key}");
            Cache::forget('studio_settings_all');
        });

        static::deleted(function ($setting) {
            Cache::forget("studio_setting_{$setting->key}");
            Cache::forget('studio_settings_all');
        });
    }

    /**
     * Quick access methods for common settings
     */
    public static function offsetTime(): int
    {
        return self::get('offset_time', 10);
    }

    public static function defaultBaseTime(): int
    {
        return self::get('default_base_time', 30);
    }

    public static function defaultBasePrice(): float
    {
        return self::get('default_base_price', 30000);
    }

    public static function allowOvertime(): bool
    {
        return self::get('allow_overtime', true);
    }

    public static function currencySymbol(): string
    {
        return self::get('currency_symbol', '₦');
    }
}