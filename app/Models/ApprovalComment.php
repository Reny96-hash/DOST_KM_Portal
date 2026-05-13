<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalComment extends Model
{
    protected $table = 'tbl_approval_comments';
    protected $fillable = ['doc_id', 'admin_id', 'comment'];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
