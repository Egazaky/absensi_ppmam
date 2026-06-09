<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RbacPermission extends Model
{
    protected $fillable = [
        'permission',
        'role',
        'allowed',
    ];

    protected $casts = [
        'allowed' => 'boolean',
    ];

    public static function roles()
    {
        return config('rbac.roles', []);
    }

    public static function definitions()
    {
        return config('rbac.permissions', []);
    }

    public static function syncDefaults()
    {
        foreach (self::definitions() as $permission => $definition) {
            foreach (array_keys(self::roles()) as $role) {
                static::firstOrCreate(
                    ['permission' => $permission, 'role' => $role],
                    ['allowed' => in_array($role, $definition['roles'], true)]
                );
            }
        }
    }

    public static function allowed($permission, $role)
    {
        $record = static::where('permission', $permission)
            ->where('role', $role)
            ->first();

        if ($record) {
            return $record->allowed;
        }

        $definition = self::definitions()[$permission] ?? null;

        return $definition && in_array($role, $definition['roles'], true);
    }

    public static function locked($permission, $role)
    {
        $definition = self::definitions()[$permission] ?? [];

        return in_array($role, $definition['locked'] ?? [], true);
    }
}
