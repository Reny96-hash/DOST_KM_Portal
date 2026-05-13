<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $table = 'tbl_likes';
    protected $fillable = ['user_id', 'doc_id', 'comment_id'];
}
