<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\ApprovalComment;
use Illuminate\Http\Request;

class AdminDocumentReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin-or-kmchampion');
    }

    // List pending documents
    public function pending()
{
    $pendingDocuments = Document::where('doc_status', 'pending')
        ->orderBy('created_at', 'asc')
        ->paginate(10);
    return view('admin.pending-documents', compact('pendingDocuments'));
}

    // Show single document for review
    public function show($id)
    {
        $document = Document::with(['attachments', 'approvalComments.admin', 'user'])->findOrFail($id);
        return view('admin.review-document', compact('document'));
    }

    // Add inline comment/feedback
    public function addComment(Request $request, $id)
    {
        $request->validate(['comment' => 'required|string|max:1000']);
        ApprovalComment::create([
            'doc_id' => $id,
            'admin_id' => auth()->user()->user_id,
            'comment' => $request->comment
        ]);
        return back()->with('success', 'Feedback added.');
    }

    // Approve document with optional final comment
    public function approve(Request $request, $id)
    {
        $document = Document::findOrFail($id);
        if ($request->filled('admin_comment')) {
            ApprovalComment::create([
                'doc_id' => $id,
                'admin_id' => auth()->user()->user_id,
                'comment' => $request->admin_comment
            ]);
        }
        $document->approval_status = 'approved';
        $document->doc_status = 'published';
        $document->reviewed_by = auth()->user()->user_id;
        $document->reviewed_at = now();
        $document->save();
        return redirect()->route('admin.documents.pending')->with('success', 'Document approved.');
    }

    // Reject document with required final comment
    public function reject(Request $request, $id)
    {
        $request->validate(['admin_comment' => 'required|string|max:1000']);
        ApprovalComment::create([
            'doc_id' => $id,
            'admin_id' => auth()->user()->user_id,
            'comment' => $request->admin_comment
        ]);
        $document = Document::findOrFail($id);
        $document->approval_status = 'rejected';
        $document->doc_status = 'draft';
        $document->save();
        return redirect()->route('admin.documents.pending')->with('warning', 'Document rejected.');
    }
}
