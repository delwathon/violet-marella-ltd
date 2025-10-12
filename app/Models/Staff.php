<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Staff extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'staff';

    protected $fillable = [
        'employee_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'role',
        'is_active',
        'hourly_rate',
        'hire_date',
        'termination_date',
        'address',
        'emergency_contact',
        'emergency_phone',
        'permissions'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'hourly_rate' => 'decimal:2',
        'hire_date' => 'date',
        'termination_date' => 'date',
        'permissions' => 'array',
    ];

    /**
     * Get the sales made by the staff member.
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Get the inventory logs created by the staff member.
     */
    public function inventoryLogs(): HasMany
    {
        return $this->hasMany(InventoryLog::class);
    }

    /**
     * Scope a query to only include active staff.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by role.
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Get the staff member's full name.
     */
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Check if staff member has a specific role.
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Check if staff member has admin privileges.
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if staff member has manager privileges.
     */
    public function isManager()
    {
        return in_array($this->role, ['admin', 'manager']);
    }

    /**
     * Check if staff member can access cashier functions.
     */
    public function canAccessCashier()
    {
        return in_array($this->role, ['admin', 'manager', 'cashier']);
    }

    /**
     * Check if staff member can manage inventory.
     */
    public function canManageInventory()
    {
        return in_array($this->role, ['admin', 'manager', 'stock_keeper']);
    }

    /**
     * Get the total sales count for this staff member.
     */
    public function getTotalSalesAttribute()
    {
        return $this->sales()->count();
    }

    /**
     * Get the total sales amount for this staff member.
     */
    public function getTotalSalesAmountAttribute()
    {
        return $this->sales()->sum('total_amount');
    }

    /**
     * Get the average sale amount for this staff member.
     */
    public function getAverageSaleAmountAttribute()
    {
        $totalSales = $this->total_sales;
        if ($totalSales > 0) {
            return $this->total_sales_amount / $totalSales;
        }
        return 0;
    }

    /**
     * Get the staff member's tenure in days.
     */
    public function getTenureDaysAttribute()
    {
        $endDate = $this->termination_date ?? now();
        return $this->hire_date->diffInDays($endDate);
    }

    /**
     * Check if staff member has a specific permission.
     */
    public function hasPermission($permission)
    {
        if ($this->isAdmin()) {
            return true;
        }

        $permissions = $this->permissions ?? [];
        return in_array($permission, $permissions);
    }
}
