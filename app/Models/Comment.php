<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'tbl_comments';
    protected $primaryKey = 'comment_id';
    protected $fillable = ['doc_id', 'user_id', 'parent_comment_id', 'comment_text', 'comment_type', 'likes'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_comment_id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'comment_id');
    }

    public function toggleLike($userId)
    {
        $like = Like::where('user_id', $userId)->where('comment_id', $this->comment_id)->first();
        if ($like) {
            $like->delete();
            $this->decrement('likes');
            return false;
        }
        Like::create(['user_id' => $userId, 'comment_id' => $this->comment_id]);
        $this->increment('likes');
        return true;
    }
}
