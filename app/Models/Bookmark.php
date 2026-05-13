<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    protected $table = 'tbl_bookmarks';
    protected $fillable = ['user_id', 'doc_id'];
    public $timestamps = true;
}
