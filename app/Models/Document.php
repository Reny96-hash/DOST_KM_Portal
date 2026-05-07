<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $table = 'tbl_documents';
    protected $primaryKey = 'doc_id';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'doc_title',
        'doc_description',
        'doc_category',
        'doc_author',
        'doc_department',
        'doc_file_path',
        'doc_file_name',
        'doc_file_type',
        'doc_file_size',
        'doc_version',
        'security_clearance',
        'doc_status',
        'approval_status',      // NEW
    'reviewed_by',          // NEW
    'reviewed_at',          // NEW
    'rejection_reason',     // NEW
        'approved_by',
        'approved_at',
        'is_tacit_knowledge',
        'expert_name',
        'expert_retirement_date',
        'expert_methodology',
        'view_count',
        'download_count'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'expert_retirement_date' => 'date',
        'is_tacit_knowledge' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'user_id');
    }

    public function accessLogs()
    {
        return $this->hasMany(AccessLog::class, 'doc_id', 'doc_id');
    }

    public function reviewer()
{
    return $this->belongsTo(User::class, 'reviewed_by', 'user_id');
}
}
