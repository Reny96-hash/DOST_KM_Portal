<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    protected $table = 'tbl_users';
    protected $primaryKey = 'user_id';
    public $timestamps = true;

    protected $fillable = [
        'emp_id',
        'user_first_name',
        'user_last_name',
        'user_middle_initial',
        'user_division',
        'user_designation',
        'user_email',
        'user_password_hash',
        'user_password_temp',
        'user_password_temp_expires',
        'user_must_change_password',
        'security_clearance',
        'user_role',
        'user_status',
        'login_attempts',
        'locked_until',
        'last_login_at',
        'last_login_ip',
        'created_by',
        'approved_by',
        'approved_at'
    ];

    protected $hidden = [
        'user_password_hash',
        'user_password_temp',
        'remember_token',
    ];

    protected $casts = [
        'locked_until' => 'datetime',
        'last_login_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->user_password_hash;
    }

    public function getFullNameAttribute()
    {
        return $this->user_first_name . ' ' . $this->user_last_name;
    }

    // ========== ROLE METHODS ==========

    public function isStaff()
    {
        return $this->user_role === 'staff';
    }

    public function isKmChampion()
    {
        return $this->user_role === 'km_champion';
    }

    public function isAdmin()
    {
        return $this->user_role === 'admin';
    }

    // ========== PERMISSION METHODS ==========

    public function canUpload()
    {
        return $this->isAdmin();
    }

    public function canManageUsers()
    {
        return $this->isAdmin();
    }

    public function canEditDocuments()
    {
        return $this->isAdmin();
    }

    public function canDeleteDocuments()
    {
        return $this->isAdmin();
    }

    // ========== SECURITY CLEARANCE ==========

    public function canViewDocument($documentClearance)
    {
        $levels = [
            'Public' => 1,
            'Internal' => 2,
            'Confidential' => 3,
            'Secret' => 4,
            'Top Secret' => 5
        ];

        $userLevel = $levels[$this->security_clearance] ?? 1;
        $docLevel = $levels[$documentClearance] ?? 1;

        return $userLevel >= $docLevel;
    }

    public function getClearanceLevel()
    {
        $levels = [
            'Public' => 1,
            'Internal' => 2,
            'Confidential' => 3,
            'Secret' => 4,
            'Top Secret' => 5
        ];

        return $levels[$this->security_clearance] ?? 1;
    }

    // ========== BRUTE FORCE PROTECTION ==========

    public function isLocked()
    {
        return $this->locked_until && now()->lt($this->locked_until);
    }

    public function incrementLoginAttempts()
    {
        $this->login_attempts++;
        if ($this->login_attempts >= 3) {
            $this->locked_until = now()->addMinutes(15);
        }
        $this->save();
    }

    public function resetLoginAttempts()
    {
        $this->login_attempts = 0;
        $this->locked_until = null;
        $this->save();
    }

    // ========== RELATIONSHIPS ==========

    public function documents()
    {
        return $this->hasMany(Document::class, 'user_id', 'user_id');
    }

    public function approvedDocuments()
    {
        return $this->hasMany(Document::class, 'approved_by', 'user_id');
    }

    public function accessLogs()
    {
        return $this->hasMany(AccessLog::class, 'user_id', 'user_id');
    }
}
