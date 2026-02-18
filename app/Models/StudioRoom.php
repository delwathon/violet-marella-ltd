<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudioRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'code',
        'description',
        'floor',
        'location',
        'size_sqm',
        'equipment',
        'features',
        'status',
        'maintenance_notes',
        'is_active',
    ];

    protected $casts = [
        'floor' => 'integer',
        'size_sqm' => 'integer',
        'equipment' => 'array',
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */

    /**
     * Get the category this room belongs to
     */
    public function category()
    {
        return $this->belongsTo(StudioCategory::class, 'category_id');
    }

    /**
     * Scopes
     */

    /**
     * Scope: Only active rooms
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Only available rooms
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)
                     ->where('status', 'available');
    }

    /**
     * Scope: Rooms in maintenance
     */
    public function scopeInMaintenance($query)
    {
        return $query->where('status', 'maintenance');
    }

    /**
     * Scope: Out of service rooms
     */
    public function scopeOutOfService($query)
    {
        return $query->where('status', 'out_of_service');
    }

    /**
     * Scope: Filter by category
     */
    public function scopeForCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Business Logic Methods
     */

    /**
     * Check if room is available
     * 
     * @return bool
     */
    public function isAvailable(): bool
    {
        return $this->is_active && $this->status === 'available';
    }

    /**
     * Check if room is in maintenance
     * 
     * @return bool
     */
    public function isInMaintenance(): bool
    {
        return $this->status === 'maintenance';
    }

    /**
     * Check if room is out of service
     * 
     * @return bool
     */
    public function isOutOfService(): bool
    {
        return $this->status === 'out_of_service';
    }

    /**
     * Mark room as available
     * 
     * @return bool
     */
    public function markAvailable(): bool
    {
        return $this->update([
            'status' => 'available',
            'maintenance_notes' => null,
        ]);
    }

    /**
     * Mark room for maintenance
     * 
     * @param string|null $notes
     * @return bool
     */
    public function markMaintenance(?string $notes = null): bool
    {
        return $this->update([
            'status' => 'maintenance',
            'maintenance_notes' => $notes,
        ]);
    }

    /**
     * Mark room as out of service
     * 
     * @param string|null $reason
     * @return bool
     */
    public function markOutOfService(?string $reason = null): bool
    {
        return $this->update([
            'status' => 'out_of_service',
            'maintenance_notes' => $reason,
            'is_active' => false,
        ]);
    }

    /**
     * Get equipment list
     * 
     * @return array
     */
    public function getEquipmentList(): array
    {
        return $this->equipment ?? [];
    }

    /**
     * Get features list
     * 
     * @return array
     */
    public function getFeaturesList(): array
    {
        return $this->features ?? [];
    }

    /**
     * Check if room has specific equipment
     * 
     * @param string $equipmentName
     * @return bool
     */
    public function hasEquipment(string $equipmentName): bool
    {
        $equipment = $this->getEquipmentList();
        return in_array($equipmentName, $equipment, true);
    }

    /**
     * Add equipment to room
     * 
     * @param string $equipmentName
     * @return bool
     */
    public function addEquipment(string $equipmentName): bool
    {
        $equipment = $this->getEquipmentList();
        
        if (!in_array($equipmentName, $equipment, true)) {
            $equipment[] = $equipmentName;
            return $this->update(['equipment' => $equipment]);
        }
        
        return false;
    }

    /**
     * Remove equipment from room
     * 
     * @param string $equipmentName
     * @return bool
     */
    public function removeEquipment(string $equipmentName): bool
    {
        $equipment = $this->getEquipmentList();
        $key = array_search($equipmentName, $equipment, true);
        
        if ($key !== false) {
            unset($equipment[$key]);
            return $this->update(['equipment' => array_values($equipment)]);
        }
        
        return false;
    }

    /**
     * Accessors
     */

    /**
     * Get full room name (with category)
     */
    public function getFullNameAttribute(): string
    {
        return $this->category->name . ' - ' . $this->name;
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'available' => 'badge-success',
            'maintenance' => 'badge-warning',
            'out_of_service' => 'badge-danger',
            default => 'badge-secondary',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'available' => 'Available',
            'maintenance' => 'In Maintenance',
            'out_of_service' => 'Out of Service',
            default => 'Unknown',
        };
    }

    /**
     * Get formatted size
     */
    public function getFormattedSizeAttribute(): ?string
    {
        return $this->size_sqm ? $this->size_sqm . ' m²' : null;
    }

    /**
     * Get floor display
     */
    public function getFloorDisplayAttribute(): ?string
    {
        if (!$this->floor) {
            return null;
        }
        
        return match ($this->floor) {
            0 => 'Ground Floor',
            1 => '1st Floor',
            2 => '2nd Floor',
            3 => '3rd Floor',
            default => $this->floor . 'th Floor',
        };
    }

    /**
     * Mutators
     */

    /**
     * Ensure equipment is always an array
     */
    public function setEquipmentAttribute($value)
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }
        
        $this->attributes['equipment'] = json_encode($value ?? []);
    }

    /**
     * Ensure features is always an array
     */
    public function setFeaturesAttribute($value)
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }
        
        $this->attributes['features'] = json_encode($value ?? []);
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        // When a room is deactivated, change status
        static::updating(function ($room) {
            if ($room->isDirty('is_active') && !$room->is_active) {
                $room->status = 'out_of_service';
            }
        });
    }
}