<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'signature_path',
        'role_id',
        'is_active',
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
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'akses' => 'array',
            'akses_outlet' => 'array',
            'akses_khusus' => 'array',
        ];
    }

    // Relationships
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function userOutlets()
    {
        return $this->hasMany(UserOutlet::class);
    }

    public function outlets()
    {
        return $this->belongsToMany(Outlet::class, 'user_outlets', 'user_id', 'outlet_id', 'id', 'id_outlet');
    }

    public function activityLogs()
    {
        return $this->hasMany(UserActivityLog::class);
    }

    // Accessors
    public function getOutletIdsAttribute()
    {
        return $this->outlets()->pluck('id_outlet')->toArray();
    }

    public function getRolesAttribute()
    {
        // Return role as collection for blade compatibility
        return $this->role ? collect([$this->role]) : collect([]);
    }

    public function getSignatureUrlAttribute()
    {
        if ($this->signature_path && file_exists(public_path($this->signature_path))) {
            return asset($this->signature_path);
        }
        return null;
    }

    // Permission Methods
    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->name === $roleName;
    }

    public function hasPermission(string $permissionName): bool
    {
        if (!$this->role) {
            return false;
        }

        // Super admin has all permissions
        if ($this->role->name === 'super_admin') {
            return true;
        }

        return $this->role->hasPermission($permissionName);
    }

    public function can($ability, $arguments = [])
    {
        // Check Laravel's built-in authorization first
        if (parent::can($ability, $arguments)) {
            return true;
        }

        // Check our custom permission system
        return $this->hasPermission($ability);
    }

    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    public function hasAccessToOutlet(int $outletId): bool
    {
        // Super admin has access to all outlets
        if ($this->hasRole('super_admin')) {
            return true;
        }

        return in_array($outletId, $this->outlet_ids);
    }

    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, $roleId)
    {
        return $query->where('role_id', $roleId);
    }

    public function scopeHasOutletAccess($query, $outletId)
    {
        return $query->whereHas('outlets', function($q) use ($outletId) {
            $q->where('outlet_id', $outletId);
        });
    }

    public function scopeIsNotAdmin($query)
    {
        return $query->where('level', '!=', 1);
    }

    /**
     * Get user's notifications
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get unread notifications count
     */
    public function unreadNotificationsCount()
    {
        return $this->notifications()->unread()->count();
    }
}
