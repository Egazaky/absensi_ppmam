<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, Uuids;

    public const ROLE_SUPER_ADMIN = 'SuperAdmin';

    public const ROLE_ADMINISTRATOR = 'Administrator';

    public const ROLE_PENGURUS = 'Pengurus';

    public const ROLE_SANTRI = 'Santri';

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
        'role',
        'santri_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function santris()
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    public function log_activities()
    {
        return $this->hasMany(LogActivity::class);
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function hasAnyRole(array $roles)
    {
        return in_array($this->role, $roles, true);
    }

    public function isSuperAdmin()
    {
        return $this->hasRole(self::ROLE_SUPER_ADMIN);
    }

    public function isAdmin()
    {
        return $this->hasAnyRole([self::ROLE_SUPER_ADMIN, self::ROLE_ADMINISTRATOR]);
    }

    public function isStaff()
    {
        return $this->hasAnyRole([self::ROLE_SUPER_ADMIN, self::ROLE_ADMINISTRATOR, self::ROLE_PENGURUS]);
    }

    public function isSantri()
    {
        return $this->hasRole(self::ROLE_SANTRI);
    }

    public function canAccess($permission)
    {
        return RbacPermission::allowed($permission, $this->role);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
