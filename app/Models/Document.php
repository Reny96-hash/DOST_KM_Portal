<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $table = 'tbl_documents';
    protected $primaryKey = 'doc_id';

    protected $fillable = [
        'user_id', 'doc_title', 'doc_description', 'doc_category',
        'doc_file_path', 'doc_file_name', 'doc_file_type', 'doc_file_size',
        'doc_version', 'security_clearance', 'doc_status', 'approved_by',
        'approved_at', 'is_tacit_knowledge', 'expert_name', 'expert_retirement_date',
        'view_count', 'download_count'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
