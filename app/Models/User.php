<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_users';
    protected $primaryKey = 'user_id';
    public $timestamps = true;

    // Role constants - DOST Official Roles
    const ROLE_STAFF = 'staff';
    const ROLE_INFO_OWNER = 'info_owner';
    const ROLE_KM_CHAMPION = 'km_champion';
    const ROLE_EDTS_ADMIN = 'edts_admin';
    const ROLE_DIRECTOR = 'director';
    const ROLE_ADMIN = 'admin';

    protected $fillable = [
        'emp_id', 'user_first_name', 'user_last_name', 'user_middle_initial',
        'user_division', 'user_designation', 'user_email', 'user_password_hash',
        'user_password_temp', 'user_password_temp_expires', 'user_must_change_password',
        'security_clearance', 'user_role', 'user_status', 'login_attempts',
        'locked_until', 'last_login_at', 'last_login_ip', 'created_by', 'approved_by', 'approved_at'
    ];

    protected $hidden = ['user_password_hash', 'user_password_temp', 'remember_token'];

    public function getAuthPassword()
    {
        return $this->user_password_hash;
    }

    public function getFullNameAttribute()
    {
        return $this->user_first_name . ' ' . $this->user_last_name;
    }

    // ========== DOST RBAC METHODS ==========

    /**
     * Check if user has Staff role
     */
    public function isStaff()
    {
        return $this->user_role === self::ROLE_STAFF;
    }

    /**
     * Check if user has Info Owner role
     */
    public function isInfoOwner()
    {
        return $this->user_role === self::ROLE_INFO_OWNER;
    }

    /**
     * Check if user has KM Champion role
     */
    public function isKmChampion()
    {
        return $this->user_role === self::ROLE_KM_CHAMPION;
    }

    /**
     * Check if user has EDTS Admin role
     */
    public function isEdtsAdmin()
    {
        return $this->user_role === self::ROLE_EDTS_ADMIN;
    }

    /**
     * Check if user has Director role
     */
    public function isDirector()
    {
        return $this->user_role === self::ROLE_DIRECTOR;
    }

    /**
     * Check if user has Admin role (System Administrator)
     */
    public function isAdmin()
    {
        return $this->user_role === self::ROLE_ADMIN;
    }

    /**
     * Check if user can approve other users (KM Champion, EDTS Admin, Director, Admin)
     */
    public function canApproveUsers()
    {
        return in_array($this->user_role, [
            self::ROLE_KM_CHAMPION,
            self::ROLE_EDTS_ADMIN,
            self::ROLE_DIRECTOR,
            self::ROLE_ADMIN
        ]);
    }

    /**
     * Check if user can approve documents (Info Owner, KM Champion, Director, Admin)
     */
    public function canApproveDocuments()
    {
        return in_array($this->user_role, [
            self::ROLE_INFO_OWNER,
            self::ROLE_KM_CHAMPION,
            self::ROLE_DIRECTOR,
            self::ROLE_ADMIN
        ]);
    }

    /**
     * Get all available roles
     */
    public static function getAvailableRoles()
    {
        return [
            self::ROLE_STAFF => 'Staff',
            self::ROLE_INFO_OWNER => 'Info Owner',
            self::ROLE_KM_CHAMPION => 'KM Champion',
            self::ROLE_EDTS_ADMIN => 'EDTS Admin',
            self::ROLE_DIRECTOR => 'Director',
            self::ROLE_ADMIN => 'System Administrator',
        ];
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'user_id', 'user_id');
    }
}
