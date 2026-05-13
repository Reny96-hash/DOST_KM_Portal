<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Category;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function create()
    {
        $categories = Category::pluck('cat_name')->toArray();
        return view('questions.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:500',
            'content_rich' => 'required|string',
            'category' => 'required|string|exists:tbl_categories,cat_name',
            'security_clearance' => 'required|in:Public,Internal,Confidential,Secret,Top Secret',
        ]);

        $document = Document::create([
            'user_id' => auth()->id(),
            'doc_title' => $request->title,
            'doc_description' => $request->description ?? null,
            'doc_category' => $request->category,
            'security_clearance' => $request->security_clearance,
            'content_type' => 'article',
            'is_question' => true,
            'content_rich' => $request->content_rich,
            'doc_status' => 'published',
            'approval_status' => 'approved',
            'allow_comments' => true,
            'likes_count' => 0,
        ]);

        return redirect()->route('document.show', $document->doc_id)
            ->with('success', 'Your question has been posted.');
    }
}
