<?php

namespace App\Models;

use App\Support\AccessControl;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'role',
        'department_id',
        'is_active',
        'hourly_rate',
        'hire_date',
        'termination_date',
        'address',
        'emergency_contact',
        'emergency_phone',
        'permissions',
        'profile_photo',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'department_id' => 'integer',
        'hourly_rate' => 'decimal:2',
        'hire_date' => 'date',
        'termination_date' => 'date',
        'permissions' => 'array',
        'last_login_at' => 'datetime',
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

    public function roleRecord(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role', 'slug');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function businesses(): BelongsToMany
    {
        return $this->belongsToMany(Business::class)->withTimestamps();
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
        $roles = is_array($role) ? $role : [$role];
        return in_array($this->role, $roles, true);
    }

    /**
     * Check if staff member has admin privileges.
     */
    public function isAdmin()
    {
        return in_array($this->role, ['superadmin', 'admin'], true);
    }

    /**
     * Check if staff member is super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    /**
     * Check if staff member has manager privileges.
     */
    public function isManager()
    {
        return in_array($this->role, ['superadmin', 'admin', 'manager'], true);
    }

    /**
     * Check if staff member can access cashier functions.
     */
    public function canAccessCashier()
    {
        return in_array($this->role, ['superadmin', 'admin', 'manager', 'cashier', 'sales_representative'], true);
    }

    /**
     * Check if staff member can manage inventory.
     */
    public function canManageInventory()
    {
        return in_array($this->role, ['superadmin', 'admin', 'manager', 'stock_keeper'], true);
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
        if ($this->hire_date === null) {
            return 0;
        }

        $endDate = $this->termination_date ?? now();
        return $this->hire_date->diffInDays($endDate);
    }

    /**
     * Return business slugs assigned to this user.
     *
     * @return array<int, string>
     */
    public function accessibleBusinessSlugs(): array
    {
        if ($this->isAdmin()) {
            return AccessControl::businessSlugs();
        }

        if (!$this->exists) {
            return [];
        }

        if ($this->relationLoaded('businesses')) {
            return $this->businesses
                ->pluck('slug')
                ->filter()
                ->values()
                ->all();
        }

        return $this->businesses()
            ->pluck('slug')
            ->filter()
            ->values()
            ->all();
    }

    public function hasBusinessAccess(string $business): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $businessSlug = AccessControl::resolveBusinessSlug($business);

        if ($businessSlug === null) {
            return false;
        }

        return in_array($businessSlug, $this->accessibleBusinessSlugs(), true);
    }

    /**
     * Check if staff member has any of the provided permissions.
     *
     * @param array<int, string> $permissions
     */
    public function hasAnyPermission(array $permissions, ?string $business = null): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission, $business)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if staff member has a specific permission.
     */
    public function hasPermission($permission, ?string $business = null): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $required = trim((string) $permission);

        if ($required === '') {
            return false;
        }

        $businessSlug = $business ? AccessControl::resolveBusinessSlug($business) : null;
        if ($businessSlug !== null && !$this->hasBusinessAccess($businessSlug)) {
            return false;
        }

        $directPermissions = $this->normalizePermissions($this->permissions ?? []);

        $rolePermissions = [];
        if ($this->relationLoaded('roleRecord')) {
            $rolePermissions = $this->normalizePermissions($this->roleRecord?->permissions ?? []);
        } elseif ($this->exists) {
            $rolePermissions = $this->normalizePermissions($this->roleRecord()->first()?->permissions ?? []);
        }

        return $this->permissionMatches($directPermissions, $required)
            || $this->permissionMatches($rolePermissions, $required);
    }

    /**
     * @param array<int, mixed> $permissions
     * @return array<int, string>
     */
    private function normalizePermissions(array $permissions): array
    {
        $normalized = [];

        foreach ($permissions as $permission) {
            $value = trim((string) $permission);

            if ($value === '') {
                continue;
            }

            if ($value === 'all') {
                $normalized[] = '*';
                continue;
            }

            if (!str_contains($value, '.')) {
                $legacyMap = [
                    'sales' => '*.sales.*',
                    'inventory' => '*.inventory.*',
                    'customers' => '*.customers.*',
                    'products' => '*.products.*',
                    'reports' => 'reports.*',
                    'users' => 'users.*',
                    'roles' => 'roles.*',
                ];

                $normalized[] = $legacyMap[$value] ?? $value;
                continue;
            }

            $normalized[] = $value;
        }

        return array_values(array_unique($normalized));
    }

    /**
     * @param array<int, string> $grantedPermissions
     */
    private function permissionMatches(array $grantedPermissions, string $required): bool
    {
        foreach ($grantedPermissions as $granted) {
            if ($granted === '*') {
                return true;
            }

            if ($granted === $required || Str::is($granted, $required)) {
                return true;
            }
        }

        return false;
    }
}
