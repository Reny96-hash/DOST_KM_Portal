<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentAttachment extends Model
{
    protected $table = 'tbl_document_attachments';
    protected $fillable = ['doc_id', 'file_name', 'file_path', 'file_type', 'file_size'];

    public function document()
    {
        return $this->belongsTo(Document::class, 'doc_id');
    }
}
