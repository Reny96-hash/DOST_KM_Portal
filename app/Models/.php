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

    // Relationships
    public function attachments() { return $this->hasMany(DocumentAttachment::class, 'doc_id'); }
    public function comments() { return $this->hasMany(Comment::class, 'doc_id')->whereNull('parent_comment_id'); }
    public function allComments() { return $this->hasMany(Comment::class, 'doc_id'); }
    public function bookmarkedBy() { return $this->belongsToMany(User::class, 'tbl_bookmarks', 'doc_id', 'user_id'); }
    public function likes() { return $this->hasMany(Like::class, 'doc_id'); }
    public function approvalComments() { return $this->hasMany(ApprovalComment::class, 'doc_id'); }
    public function parent() { return $this->belongsTo(Document::class, 'parent_doc_id'); }
    public function children() { return $this->hasMany(Document::class, 'parent_doc_id'); }
    public function folder() { return $this->belongsTo(Folder::class, 'folder_id'); }

    // Helper methods
    public function toggleBookmark($userId)
    {
        if ($this->bookmarkedBy()->where('user_id', $userId)->exists()) {
            $this->bookmarkedBy()->detach($userId);
            return false;
        }
        $this->bookmarkedBy()->attach($userId);
        return true;
    }

    public function isFile() { return $this->content_type === 'file'; }
    public function isArticle() { return $this->content_type === 'article'; }
}


