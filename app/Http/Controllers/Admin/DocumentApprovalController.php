<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;

class DocumentApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Show pending documents
    public function index()
    {
        $allowedRoles = ['admin', 'km_champion'];
        if (!in_array(auth()->user()->user_role, $allowedRoles)) {
            abort(403, 'Only Admin and KM Champion can approve documents.');
        }

        $pendingDocuments = Document::where('approval_status', 'pending')
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        return view('admin.pending-documents', compact('pendingDocuments'));
    }

    // Approve document
   public function approve($id)
{
    $allowedRoles = ['admin', 'km_champion'];
    if (!in_array(auth()->user()->user_role, $allowedRoles)) {
        return redirect()->back()->with('error', 'Unauthorized.');
    }

    $document = Document::findOrFail($id);

    // Prevent approving own document
    if ($document->user_id == auth()->user()->user_id) {
        return redirect()->back()->with('error', 'You cannot approve your own document.');
    }

    $document->approval_status = 'approved';
    $document->doc_status = 'published';  // Valid enum value
    $document->reviewed_by = auth()->user()->user_id;
    $document->reviewed_at = now();
    $document->save();

    return redirect()->back()->with('success', "Document '{$document->doc_title}' approved.");
}

    // Reject document
    public function reject(Request $request, $id)
    {
        $allowedRoles = ['admin', 'km_champion'];
        if (!in_array(auth()->user()->user_role, $allowedRoles)) {
            return redirect()->back()->with('error', 'Unauthorized.');
        }

        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $document = Document::findOrFail($id);

        // Prevent rejecting own document
        if ($document->user_id == auth()->user()->user_id) {
            return redirect()->back()->with('error', 'You cannot reject your own document. Please ask another reviewer.');
        }

        $document->approval_status = 'rejected';
        $document->doc_status = 'archived';  // 'archived' is valid in your enum
        $document->reviewed_by = auth()->user()->user_id;
        $document->reviewed_at = now();
        $document->rejection_reason = $request->reason;
        $document->save();

        return redirect()->back()->with('warning', "Document '{$document->doc_title}' rejected.");
    }
}
