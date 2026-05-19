<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\ApprovalComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminDocumentReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin-or-kmchampion');
    }

    // List pending documents
public function pending(Request $request)
{
    $query = Document::where('doc_status', 'pending')
                     ->where('approval_status', 'pending')
                     ->with('user');

    // Search (title or uploader name/email)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('doc_title', 'like', "%{$search}%")
              ->orWhereHas('user', function($u) use ($search) {
                  $u->where('user_first_name', 'like', "%{$search}%")
                    ->orWhere('user_last_name', 'like', "%{$search}%")
                    ->orWhere('user_email', 'like', "%{$search}%");
              });
        });
    }

    // Sorting
    $sort = $request->get('sort', 'created_at');
    $direction = $request->get('dir', 'desc');
    $allowed = ['created_at', 'doc_title', 'doc_category'];
    if (in_array($sort, $allowed)) {
        $query->orderBy($sort, $direction);
    }

    $pendingDocuments = $query->paginate(10);
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
// app/Http/Controllers/Admin/AdminDocumentReviewController.php
public function reject(Request $request, $id)
{
    $request->validate([
        'admin_comment' => 'required|string|max:1000'
    ]);

    $document = Document::findOrFail($id);

    // Save the rejection comment
    \App\Models\ApprovalComment::create([
        'doc_id' => $id,
        'admin_id' => auth()->id(),
        'comment' => $request->admin_comment
    ]);

    // Update document status
    $document->approval_status = 'rejected';
    $document->doc_status = 'draft';
    $document->save();

    return redirect()->route('admin.documents.pending')->with('warning', 'Document rejected.');
}
public function bulkDelete(Request $request)
{
    $ids = $request->input('ids');
    if (empty($ids)) {
        return back()->with('error', 'No documents selected.');
    }
    $ids = explode(',', $ids);
    $documents = Document::whereIn('doc_id', $ids)->get();
    foreach ($documents as $doc) {
        if ($doc->doc_file_path && Storage::disk('local')->exists($doc->doc_file_path)) {
            Storage::disk('local')->delete($doc->doc_file_path);
        }
        foreach ($doc->attachments as $att) {
            if (Storage::disk('local')->exists($att->file_path)) {
                Storage::disk('local')->delete($att->file_path);
            }
            $att->delete();
        }
        $doc->delete();
    }
    return back()->with('success', count($ids) . ' document(s) deleted.');
}
}
