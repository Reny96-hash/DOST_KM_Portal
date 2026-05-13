<?php
namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Document;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, $docId)
    {
        $request->validate(['comment_text' => 'required|string|max:2000']);
        $doc = Document::findOrFail($docId);
        if (!$doc->allow_comments && !auth()->user()->isAdmin()) abort(403);

        Comment::create([
            'doc_id' => $docId,
            'user_id' => auth()->user()->user_id,
            'parent_comment_id' => $request->parent_comment_id,
            'comment_text' => $request->comment_text,
            'comment_type' => ($doc->is_question && !$request->parent_comment_id) ? 'answer' : 'comment'
        ]);

        return back()->with('success', 'Comment added.');
    }

    public function like($id)
    {
        $comment = Comment::findOrFail($id);
        $liked = $comment->toggleLike(auth()->user()->user_id);
        return response()->json(['liked' => $liked, 'likes' => $comment->likes]);
    }
}
