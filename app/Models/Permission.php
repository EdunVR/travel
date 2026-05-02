<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'module',
        'menu',
        'action'
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions')
            ->withTimestamps();
    }

    public static function generateName(string $module, string $menu, string $action): string
    {
        return strtolower("{$module}.{$menu}.{$action}");
    }

    public function scopeByModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    public function scopeByMenu($query, string $menu)
    {
        return $query->where('menu', $menu);
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public static function getGroupedPermissions()
    {
        return self::all()->groupBy('module')->map(function ($modulePermissions) {
            return $modulePermissions->groupBy('menu');
        });
    }
}
