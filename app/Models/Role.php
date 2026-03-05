<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'permissions',
        'is_system',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_system' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role', 'slug');
    }

    public function hasPermission(string $permission): bool
    {
        foreach ($this->permissions ?? [] as $grantedPermission) {
            $grantedPermission = trim((string) $grantedPermission);

            if ($grantedPermission === '') {
                continue;
            }

            if ($grantedPermission === '*' || $grantedPermission === $permission || Str::is($grantedPermission, $permission)) {
                return true;
            }
        }

        return false;
    }
}
