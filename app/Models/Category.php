<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'tbl_categories';
    protected $primaryKey = 'cat_id';
    protected $fillable = ['cat_name', 'cat_description'];

    public function documents()
    {
        return $this->hasMany(Document::class, 'doc_category', 'cat_name');
    }
}
