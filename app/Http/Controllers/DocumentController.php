<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use App\Models\Category;
use App\Models\DocumentAttachment;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ------------------------------------------------------------------
    // EXISTING METHODS (upload, uploadForm, edit, update, destroy, myUploads)
    // ------------------------------------------------------------------

    public function uploadForm()
    {
        $categories = DB::table('tbl_categories')->pluck('cat_name')->toArray();
        return view('upload', compact('categories'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:500',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'author' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'security_clearance' => 'required|in:Public,Internal,Confidential,Secret,Top Secret',
            'document' => 'required|file|mimes:pdf,doc,docx,xlsx,jpg,jpeg,png|max:10240'
        ]);

        $file = $request->file('document');
        $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
        $filePath = $file->storeAs('documents', $fileName, 'local');

        $autoApprove = auth()->user()->isAdmin() || auth()->user()->isKmChampion();

        $document = Document::create([
            'user_id' => auth()->user()->user_id,
            'doc_title' => $request->title,
            'doc_description' => $request->description,
            'doc_category' => $request->category,
            'doc_author' => $request->author,
            'doc_department' => $request->department,
            'doc_file_path' => $filePath,
            'doc_file_name' => $file->getClientOriginalName(),
            'doc_file_type' => $file->getClientOriginalExtension(),
            'doc_file_size' => round($file->getSize() / 1024, 2),
            'security_clearance' => $request->security_clearance,
            'doc_status' => $autoApprove ? 'published' : 'draft',
            'approval_status' => $autoApprove ? 'approved' : 'pending',
            'doc_version' => 1.0,
        ]);

        if ($autoApprove) {
            $document->reviewed_by = auth()->user()->user_id;
            $document->reviewed_at = now();
            $document->save();
        }

        $message = $autoApprove ? 'Document uploaded and published!' : 'Document uploaded! Awaiting approval.';
        return redirect()->route('dashboard')->with('success', $message);
    }

    public function edit($id)
    {
        $document = Document::findOrFail($id);

        if (auth()->user()->isAdmin()) {
            $categories = DB::table('tbl_categories')->pluck('cat_name')->toArray();
            return view('documents.edit', compact('document', 'categories'));
        }

        if ($document->user_id != auth()->user()->user_id) {
            abort(403, 'You can only edit your own documents.');
        }

        if ($document->approval_status === 'approved') {
            return redirect()->route('documents.my-uploads')
                ->with('error', 'Approved documents cannot be edited. Contact administrator for changes.');
        }

        $categories = DB::table('tbl_categories')->pluck('cat_name')->toArray();
        return view('documents.edit', compact('document', 'categories'));
    }

    public function update(Request $request, $id)
{
    $document = Document::findOrFail($id);

    // Permission checks (unchanged)
    if (!auth()->user()->isAdmin() && $document->user_id != auth()->user()->user_id) {
        abort(403);
    }

    $request->validate([
        'doc_title' => 'required|string|max:500',
        'doc_description' => 'required|string',
        'doc_category' => 'required|string',
        'security_clearance' => 'required|in:Public,Internal,Confidential,Secret,Top Secret',
        'document' => 'nullable|file|mimes:pdf,doc,docx,xlsx,jpg,jpeg,png|max:10240'
    ]);

    $updateData = [
        'doc_title' => $request->doc_title,
        'doc_description' => $request->doc_description,
        'doc_category' => $request->doc_category,
        'security_clearance' => $request->security_clearance,
    ];

    // For articles: update rich content and validate not empty
    if ($document->content_type == 'article') {
        if ($request->filled('content_rich')) {
            $plainText = strip_tags($request->content_rich);
            if (trim($plainText) === '') {
                return back()->withErrors(['content_rich' => 'The content cannot be empty.'])->withInput();
            }
            $updateData['content_rich'] = $request->content_rich;
        } else {
            return back()->withErrors(['content_rich' => 'The content field is required.'])->withInput();
        }
    }

    $autoApprove = auth()->user()->isAdmin() || auth()->user()->isKmChampion();
    if ($autoApprove) {
        $updateData['approval_status'] = 'approved';
        $updateData['doc_status'] = 'published';
        $updateData['reviewed_by'] = auth()->user()->user_id;
        $updateData['reviewed_at'] = now();
    } else {
        $updateData['approval_status'] = 'pending';
        $updateData['doc_status'] = 'draft';
    }

    // Handle file replacement (only for file documents)
    if ($document->content_type == 'file' && $request->hasFile('document')) {
        if ($document->doc_file_path && Storage::disk('local')->exists($document->doc_file_path)) {
            Storage::disk('local')->delete($document->doc_file_path);
        }
        $file = $request->file('document');
        $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
        $filePath = $file->storeAs('documents', $fileName, 'local');
        $updateData['doc_file_path'] = $filePath;
        $updateData['doc_file_name'] = $file->getClientOriginalName();
        $updateData['doc_file_type'] = $file->getClientOriginalExtension();
        $updateData['doc_file_size'] = round($file->getSize() / 1024, 2);
        $updateData['doc_version'] = $document->doc_version + 0.1;
    }

    $document->update($updateData);

    $message = $autoApprove
        ? 'Document updated and published immediately.'
        : 'Document updated. Pending approval.';

    return redirect()->route('documents.my-uploads')->with('success', $message);
}

 public function destroy($id)
{
    $document = Document::findOrFail($id);

    if (auth()->user()->isAdmin()) {
        // Delete physical file if exists
        if ($document->doc_file_path && Storage::disk('local')->exists($document->doc_file_path)) {
            Storage::disk('local')->delete($document->doc_file_path);
        }
        // Delete attachments
        foreach ($document->attachments as $attachment) {
            if ($attachment->file_path && Storage::disk('local')->exists($attachment->file_path)) {
                Storage::disk('local')->delete($attachment->file_path);
            }
            $attachment->delete();
        }
        $document->delete();
        // ✅ Redirect back to the previous page
        return redirect()->back()->with('success', 'Document deleted.');
    }

    // Non-admin: only owner can delete, and only if NOT approved
    if ($document->user_id != auth()->user()->user_id) {
        abort(403);
    }

    if ($document->approval_status === 'approved') {
        return redirect()->back()->with('error', 'Approved documents can only be deleted by administrators.');
    }

    if ($document->doc_file_path && Storage::disk('local')->exists($document->doc_file_path)) {
        Storage::disk('local')->delete($document->doc_file_path);
    }

    $document->delete();

    return redirect()->back()->with('success', 'Document deleted.');
}
public function myUploads(Request $request)
{
    $type = $request->get('type', 'all');
    $status = $request->get('status'); // draft, pending, approved, rejected
    $sort = $request->get('sort', 'date');
    $direction = $request->get('direction', 'desc');

    $query = Document::where('user_id', auth()->id());

    if ($type !== 'all') {
        $query->where('content_type', $type);
    }

    // Filter by status (distinguish draft and rejected)
    if ($status === 'draft') {
    $query->where('doc_status', 'draft')->where('approval_status', 'pending');
} elseif ($status === 'pending') {
        $query->where('doc_status', 'pending');
    } elseif ($status === 'approved') {
        $query->where('approval_status', 'approved');
    } elseif ($status === 'rejected') {
        $query->where('approval_status', 'rejected');
    }

    // Sorting
    switch ($sort) {
        case 'title': $query->orderBy('doc_title', $direction); break;
        case 'category': $query->orderBy('doc_category', $direction); break;
        case 'status': $query->orderBy('doc_status', $direction); break;
        case 'date': default: $query->orderBy('created_at', $direction); break;
    }

    $documents = $query->paginate(10);
    return view('documents.my-uploads', compact('documents'));
}

    // ------------------------------------------------------------------
    // DASHBOARD (UPDATED with  questions, and new stats)
    // ------------------------------------------------------------------
public function index(Request $request)
{
    $user = auth()->user();

    // Get all approved & published documents (for recently added & most viewed)
    $allDocs = Document::where('doc_status', 'published')
                       ->where('approval_status', 'approved')
                       ->get();

    $visibleDocuments = $allDocs->filter(function($doc) use ($user) {
        return $user->canViewDocument($doc->security_clearance) ||
               ($doc->user_id == $user->user_id && $doc->approval_status == 'approved');
    });

    $recentDocuments = $visibleDocuments->sortByDesc('created_at')->take(5);
    $mostViewedDocuments = $visibleDocuments->sortByDesc('view_count')->take(5);

    // Staff-specific summary (counts for the dashboard cards)
    $myDocsSummary = [
        'drafts'   => Document::where('user_id', $user->user_id)
                        ->where('doc_status', 'draft')
                        ->where('approval_status', 'pending')   // true drafts only
                        ->count(),
        'pending'  => Document::where('user_id', $user->user_id)
                        ->where('doc_status', 'pending')
                        ->count(),
        'approved' => Document::where('user_id', $user->user_id)
                        ->where('approval_status', 'approved')
                        ->count(),
        'rejected' => Document::where('user_id', $user->user_id)
                        ->where('approval_status', 'rejected')
                        ->count(),
    ];

    $activities = collect();

    if (!$user->isAdmin() && !$user->isKmChampion()) {
        // 1. Approval/rejection comments
        $approvalEvents = DB::table('tbl_approval_comments as ac')
            ->join('tbl_documents as d', 'ac.doc_id', '=', 'd.doc_id')
            ->where('d.user_id', $user->user_id)
            ->select('ac.created_at', 'ac.comment', 'd.doc_title', 'd.doc_id')
            ->orderBy('ac.created_at', 'desc')
            ->limit(20)
            ->get();

        foreach ($approvalEvents as $event) {
            $activities->push((object)[
                'doc_id'    => $event->doc_id,
                'doc_title' => $event->doc_title,
                'message'   => 'Admin comment: ' . Str::limit($event->comment, 80),
                'type'      => 'comment',
                'created_at'=> $event->created_at,
            ]);
        }

        // 2. Comments from other users
        $commentEvents = DB::table('tbl_comments as c')
            ->join('tbl_documents as d', 'c.doc_id', '=', 'd.doc_id')
            ->leftJoin('tbl_users as u', 'c.user_id', '=', 'u.user_id')
            ->where('d.user_id', $user->user_id)
            ->where('c.user_id', '!=', $user->user_id)
            ->select('c.created_at', 'c.comment_text', 'd.doc_title', 'd.doc_id', 'u.user_first_name')
            ->orderBy('c.created_at', 'desc')
            ->limit(20)
            ->get();

        foreach ($commentEvents as $event) {
            $activities->push((object)[
                'doc_id'    => $event->doc_id,
                'doc_title' => $event->doc_title,
                'message'   => ($event->user_first_name ?? 'Someone') . ' commented: ' . Str::limit($event->comment_text, 80),
                'type'      => 'comment',
                'created_at'=> $event->created_at,
            ]);
        }

        // Sort activities and take latest 10
        $activities = $activities->sortByDesc('created_at')->take(10);
    }

    // Charts data for Admin and KM Champion
    $categoryStats = collect();
    $clearanceStats = collect();
    if ($user->isAdmin() || $user->isKmChampion()) {
        $categoryStats = Document::where('approval_status', 'approved')
            ->select('doc_category', \DB::raw('count(*) as count'))
            ->whereNotNull('doc_category')
            ->groupBy('doc_category')
            ->pluck('count', 'doc_category');

        $clearanceStats = Document::where('approval_status', 'approved')
            ->select('security_clearance', \DB::raw('count(*) as count'))
            ->groupBy('security_clearance')
            ->pluck('count', 'security_clearance');
    }

    return view('dashboard', compact(
        'recentDocuments',
        'mostViewedDocuments',
        'myDocsSummary',
        'activities',
        'categoryStats',
        'clearanceStats'
    ));
}

    // ------------------------------------------------------------------
    // NEW METHODS FOR UNIFIED CREATION (ARTICLE / FILE / LINK)
    // ------------------------------------------------------------------
    public function createContent(Request $request)
    {
        $categories = Category::pluck('cat_name')->toArray();
    $selectedCategory = $request->get('category');
    return view('content.create', compact('categories', 'selectedCategory'));
    }

public function categoryShow(Request $request, $name)
{
    $user = auth()->user();
    $type = $request->get('type', 'all');

    // Base query
    $query = Document::where('doc_category', $name)
        ->where('approval_status', 'approved')
        ->where('doc_status', 'published');

    // Apply type filter
    if ($type === 'article') {
        $query->where('content_type', 'article')->where('is_question', false);
    } elseif ($type === 'file') {
        $query->where('content_type', 'file')->where('is_question', false);
    } elseif ($type === 'link') {
        $query->where('content_type', 'link')->where('is_question', false);
    } elseif ($type === 'question') {
        $query->where('is_question', true);
    }
    // 'all' shows everything (including questions)

    // Apply search (if any)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('doc_title', 'like', "%{$search}%")
              ->orWhere('doc_description', 'like', "%{$search}%");
        });
    }

    // Sorting
    $sort = $request->get('sort', 'newest');
    $query->orderBy('created_at', $sort === 'oldest' ? 'asc' : 'desc');

    // Get results and filter by clearance
    $allDocs = $query->get();
    $visibleDocs = $allDocs->filter(fn($doc) => $user->canViewDocument($doc->security_clearance));

    // Paginate
    $perPage = 12;
    $currentPage = $request->get('page', 1);
    $documents = new \Illuminate\Pagination\LengthAwarePaginator(
        $visibleDocs->forPage($currentPage, $perPage),
        $visibleDocs->count(),
        $perPage,
        $currentPage,
        ['path' => $request->url(), 'query' => $request->query()]
    );

    return view('categories.show', compact('documents', 'name', 'type'));
}
public function autocompleteCategory(Request $request, $name)
{
    $query = $request->get('q');
    if (strlen($query) < 2) return response()->json([]);

    $documents = Document::where('doc_category', $name)
        ->where('approval_status', 'approved')
        ->where('doc_status', 'published')
        ->where('doc_title', 'like', "%{$query}%")
        ->limit(10)
        ->get(['doc_id', 'doc_title']);

    return response()->json($documents);
}
public function storeContent(Request $request)
{
    $isDraft = $request->has('save_draft');
    $contentType = $request->input('content_type');

    // Base validation rules (always required)
    $rules = [
        'content_type' => 'required|in:file,article',
        'title' => 'required|string|max:500',
        'category' => 'required|string|exists:tbl_categories,cat_name',
        'security_clearance' => 'required|in:Public,Internal,Confidential,Secret,Top Secret',
    ];

    // Description required only when publishing
    if (!$isDraft) {
        $rules['description'] = 'required|string';
    }

    $request->validate($rules);

    // Prepare data
    $autoApprove = auth()->user()->isAdmin() || auth()->user()->isKmChampion();
    $status = $isDraft ? 'draft' : ($autoApprove ? 'published' : 'pending');
    $approvalStatus = $status == 'published' ? 'approved' : 'pending';

    $data = [
        'user_id' => auth()->id(),
        'doc_title' => $request->title,
        'doc_description' => $request->description ?? null, // may be null for drafts
        'doc_category' => $request->category,
        'security_clearance' => $request->security_clearance,
        'content_type' => $contentType,
        'doc_status' => $status,
        'approval_status' => $approvalStatus,
        'doc_version' => 1.0,
        'allow_comments' => true,
        'is_question' => false,
    ];

    // Handle article vs file
    if ($contentType === 'article') {
        if (!$isDraft) {
            // Validate non‑empty content (strip tags)
            $contentRich = $request->input('content_rich');
            $plainText = strip_tags($contentRich);
            if (trim($plainText) === '') {
                return back()->withErrors(['content_rich' => 'The content cannot be empty. Please write something.'])->withInput();
            }
            $data['content_rich'] = $contentRich;
        } else {
            // Draft may have content or not – store if provided
            if ($request->filled('content_rich')) {
                $data['content_rich'] = $request->content_rich;
            }
        }
    } else { // file
        if (!$isDraft) {
            $request->validate([
                'document' => 'required|file|max:10240|mimes:pdf,doc,docx,xlsx,jpg,jpeg,png'
            ]);
            $file = $request->file('document');
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $filePath = $file->storeAs('documents', $fileName, 'local');
            $data['doc_file_path'] = $filePath;
            $data['doc_file_name'] = $file->getClientOriginalName();
            $data['doc_file_type'] = $file->getClientOriginalExtension();
            $data['doc_file_size'] = round($file->getSize() / 1024, 2);
        } else {
            // Draft may have a file or not – optional
            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
                $filePath = $file->storeAs('documents', $fileName, 'local');
                $data['doc_file_path'] = $filePath;
                $data['doc_file_name'] = $file->getClientOriginalName();
                $data['doc_file_type'] = $file->getClientOriginalExtension();
                $data['doc_file_size'] = round($file->getSize() / 1024, 2);
            }
        }
    }

    $document = Document::create($data);

    // Additional attachments (if any)
    if ($request->hasFile('attachments')) {
        foreach ($request->file('attachments') as $file) {
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $filePath = $file->storeAs('attachments', $fileName, 'local');
            \App\Models\DocumentAttachment::create([
                'doc_id' => $document->doc_id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_type' => $file->getClientOriginalExtension(),
                'file_size' => round($file->getSize() / 1024, 2),
            ]);
        }
    }

    if ($autoApprove && !$isDraft) {
        $document->reviewed_by = auth()->id();
        $document->reviewed_at = now();
        $document->save();
    }

    $message = $isDraft ? 'Saved as draft.' : ($autoApprove ? 'Published!' : 'Submitted for approval.');
    return redirect()->route('dashboard')->with('success', $message);
}

    public function storeLink(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:500',
            'url' => 'required|url',
            'description' => 'required|string',
            'category' => 'required|string|exists:tbl_categories,cat_name',
            'security_clearance' => 'required|in:Public,Internal,Confidential,Secret,Top Secret',
        ]);

        $autoApprove = auth()->user()->isAdmin() || auth()->user()->isKmChampion();
        $status = $autoApprove ? 'published' : 'pending';

        $document = Document::create([
            'user_id' => auth()->id(),
            'doc_title' => $request->title,
            'doc_description' => $request->description,
            'doc_category' => $request->category,
            'security_clearance' => $request->security_clearance,
            'content_type' => 'link',
            'content_rich' => $request->url,
            'doc_status' => $status,
            'approval_status' => $autoApprove ? 'approved' : 'pending',
            'allow_comments' => true,
            'is_question' => false,
            'doc_version' => 1.0,
        ]);

        if ($autoApprove) {
            $document->reviewed_by = auth()->id();
            $document->reviewed_at = now();
            $document->save();
        }

        return redirect()->route('dashboard')->with('success', $autoApprove ? 'Link added.' : 'Link submitted for approval.');
    }

    // ------------------------------------------------------------------
    // SINGLE DOCUMENT VIEW (handles links, articles, files)
    // ------------------------------------------------------------------
    public function show($id)
    {
        $document = Document::with(['attachments', 'comments.user', 'comments.replies.user', 'user',  'approvalComments.admin'])
            ->findOrFail($id);

        if ($document->content_type == 'link') {
            return redirect()->away($document->content_rich);
        }

        if ($document->approval_status !== 'approved' && $document->user_id != auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $document->increment('view_count');
        $related = Document::where(function($q) use ($document) {
            $q->where('parent_doc_id', $document->doc_id)
              ->orWhere('doc_id', $document->parent_doc_id);
        })->where('approval_status', 'approved')->get();

$bookmarked = \DB::table('tbl_bookmarks')
    ->where('doc_id', $document->doc_id)
    ->where('user_id', auth()->id())
    ->exists();        return view('documents.show', compact('document', 'related', 'bookmarked'));
    }

    // ------------------------------------------------------------------
    // AJAX & UTILITY METHODS
    // ------------------------------------------------------------------
public function fetchDocuments(Request $request)
{
    // Only allow AJAX requests
    if (!$request->ajax()) {
        return redirect()->route('dashboard');
    }

    $user = auth()->user();
    $query = Document::where('approval_status', 'approved')
                     ->where('doc_status', 'published');

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('doc_title', 'like', "%{$search}%")
              ->orWhere('doc_description', 'like', "%{$search}%");
        });
    }

    if ($request->filled('category')) {
        $query->where('doc_category', $request->category);
    }

    $sort = $request->get('sort', 'newest');
    $query->orderBy('created_at', $sort === 'oldest' ? 'asc' : 'desc');

    $allDocuments = $query->get();
    $visibleDocuments = $allDocuments->filter(function($doc) use ($user) {
        return $user->canViewDocument($doc->security_clearance) ||
               ($doc->user_id == $user->user_id && $doc->approval_status == 'approved');
    });

    $currentPage = $request->get('page', 1);
    $perPage = 10;
    $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
        $visibleDocuments->forPage($currentPage, $perPage),
        $visibleDocuments->count(),
        $perPage,
        $currentPage,
        ['path' => $request->url(), 'query' => $request->query()]
    );

    $html = view('partials.document-table', ['paginatedDocuments' => $paginated])->render();
    return response()->json(['html' => $html]);
}

    public function autocomplete(Request $request)
    {
        $query = $request->get('q');
        if (strlen($query) < 2) return response()->json([]);

        $documents = Document::where('approval_status', 'approved')
            ->where('doc_title', 'like', "%{$query}%")
            ->limit(10)
            ->get(['doc_id', 'doc_title']);

        return response()->json($documents);
    }

    public function toggleBookmark($id)
    {
        $doc = Document::findOrFail($id);
        $bookmarked = $doc->toggleBookmark(auth()->id());
        return response()->json(['bookmarked' => $bookmarked]);
    }

    public function toggleDocumentLike($id)
    {
        $doc = Document::findOrFail($id);
        $like = Like::where('user_id', auth()->id())->where('doc_id', $id)->first();
        if ($like) {
            $like->delete();
            $doc->decrement('likes_count');
            $liked = false;
        } else {
            Like::create(['user_id' => auth()->id(), 'doc_id' => $id]);
            $doc->increment('likes_count');
            $liked = true;
        }
        return response()->json(['liked' => $liked, 'count' => $doc->likes_count]);
    }

   public function submitForApproval($id)
{
    $doc = Document::findOrFail($id);
    if ($doc->user_id != auth()->id()) abort(403);
    if ($doc->doc_status == 'draft') {
        $doc->doc_status = 'pending';
        $doc->approval_status = 'pending';
        $doc->save();
        return back()->with('success', 'Document submitted for approval.');
    }
    return back()->with('error', 'Invalid action.');
}

    // ------------------------------------------------------------------
    // EXISTING PREVIEW, DOWNLOAD, IMAGE METHODS (unmodified but kept)
    // ------------------------------------------------------------------
    public function download($id)
    {
        $document = Document::findOrFail($id);
        if ($document->approval_status !== 'approved') abort(403);
        if (!auth()->user()->canViewDocument($document->security_clearance)) abort(403);
        if (!Storage::disk('local')->exists($document->doc_file_path)) abort(404);
        $document->increment('download_count');
        return Storage::disk('local')->download($document->doc_file_path, $document->doc_file_name);
    }

    public function preview($id)
    {
        $document = Document::findOrFail($id);
        if ($document->approval_status !== 'approved') abort(403);
        if (!auth()->user()->canViewDocument($document->security_clearance)) abort(403);
        if (!Storage::disk('local')->exists($document->doc_file_path)) abort(404);
        $document->increment('view_count');
        $filePath = storage_path('app/' . $document->doc_file_path);
        $fileType = $document->doc_file_type;
        if (in_array($fileType, ['jpg','jpeg','png','gif'])) {
            return response()->file($filePath, ['Content-Type' => mime_content_type($filePath)]);
        }
        if ($fileType === 'pdf') {
            return response()->file($filePath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $document->doc_file_name . '"'
            ]);
        }
        return Storage::disk('local')->download($document->doc_file_path, $document->doc_file_name);
    }

    public function getImage($id)
    {
        $document = Document::findOrFail($id);
        if ($document->approval_status !== 'approved') abort(403);
        $filePath = storage_path('app/' . $document->doc_file_path);
        if (!file_exists($filePath)) abort(404);
        return response()->file($filePath);
    }

    public function showImage($id)
    {
        $document = Document::findOrFail($id);
        if ($document->approval_status !== 'approved') abort(403);
        if (!auth()->user()->canViewDocument($document->security_clearance)) abort(403);
        $path = storage_path('app/' . $document->doc_file_path);
        if (!file_exists($path)) abort(404);
        return response()->file($path, ['Content-Type' => mime_content_type($path)]);
    }

    public function search(Request $request)
{
    $user = auth()->user();

    $query = Document::where('approval_status', 'approved')
                     ->where('doc_status', 'published');

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('doc_title', 'like', "%{$search}%")
              ->orWhere('doc_description', 'like', "%{$search}%");
        });
    }

    if ($request->filled('category')) {
        $query->where('doc_category', $request->category);
    }

    $sort = $request->get('sort', 'newest');
    $query->orderBy('created_at', $sort === 'oldest' ? 'asc' : 'desc');

    $allDocuments = $query->get();
    $visibleDocuments = $allDocuments->filter(function($doc) use ($user) {
        return $user->canViewDocument($doc->security_clearance) ||
               ($doc->user_id == $user->user_id && $doc->approval_status == 'approved');
    });

    // Paginate
    $currentPage = $request->get('page', 1);
    $perPage = 10;
    $paginatedDocuments = new \Illuminate\Pagination\LengthAwarePaginator(
        $visibleDocuments->forPage($currentPage, $perPage),
        $visibleDocuments->count(),
        $perPage,
        $currentPage,
        ['path' => $request->url(), 'query' => $request->query()]
    );

    // === STATISTICS (same as index) ===
    if (!$user->isAdmin() && !$user->isKmChampion()) {
        $approvedDocumentsCount = $visibleDocuments->count();
        $pendingDocumentsCount = 0;
        $imagesCount = $visibleDocuments->whereIn('doc_file_type', ['jpg','jpeg','png','gif'])->count();
        $totalUsers = 1;
        $kmChampionCount = 0;
        $adminCount = 0;
        $staffCount = 0;
    } else {
        $approvedDocumentsCount = Document::where('approval_status', 'approved')->count();
        $pendingDocumentsCount = Document::where('approval_status', 'pending')->count();
        $imagesCount = Document::whereIn('doc_file_type', ['jpg','jpeg','png','gif'])->count();
        $totalUsers = User::count();
        $kmChampionCount = User::where('user_role', 'km_champion')->count();
        $adminCount = User::where('user_role', 'admin')->count();
        $staffCount = User::where('user_role', 'staff')->count();
    }

    $categories = DB::table('tbl_categories')->pluck('cat_name')->toArray();

    $recentDocuments = $visibleDocuments->sortByDesc('created_at')->take(5);
    $mostViewedDocuments = $visibleDocuments->sortByDesc('view_count')->take(5);

    // Chart data for admin/km champion
    $categoryStats = collect();
    $clearanceStats = collect();
    if ($user->isAdmin() || $user->isKmChampion()) {
        $categoryStats = Document::where('approval_status', 'approved')
            ->select('doc_category', \DB::raw('count(*) as count'))
            ->whereNotNull('doc_category')
            ->groupBy('doc_category')
            ->pluck('count', 'doc_category');

        $clearanceStats = Document::where('approval_status', 'approved')
            ->select('security_clearance', \DB::raw('count(*) as count'))
            ->groupBy('security_clearance')
            ->pluck('count', 'security_clearance');
    }
    // =================================

    return view('dashboard', compact(
        'paginatedDocuments', 'categories', 'approvedDocumentsCount', 'pendingDocumentsCount',
        'imagesCount', 'totalUsers', 'kmChampionCount', 'adminCount', 'staffCount',
        'recentDocuments', 'mostViewedDocuments', 'sort', 'categoryStats', 'clearanceStats'
    ));
}
}
