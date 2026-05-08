<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Show upload form - ALL authenticated users
    public function uploadForm()
    {
        $categories = DB::table('tbl_categories')->pluck('cat_name')->toArray();
        return view('upload', compact('categories'));
    }

    // Store uploaded document - ALL authenticated users
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

        // Admin and KM Champion get auto-approval
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

        // Auto-approve for Admin and KM Champion
        if ($autoApprove) {
            $document->reviewed_by = auth()->user()->user_id;
            $document->reviewed_at = now();
            $document->save();
        }

        $message = $autoApprove ? 'Document uploaded and published!' : 'Document uploaded! Awaiting approval.';
        return redirect()->route('dashboard')->with('success', $message);
    }

    // Dashboard - Show ONLY approved documents with sorting support
    public function index(Request $request)
    {
        $user = auth()->user();

        // Base query: only approved & published documents
        $query = Document::where('approval_status', 'approved')
                         ->where('doc_status', 'published');

        // Apply sorting (default: newest first)
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Get all matching documents (we will filter by security clearance later)
        $allDocuments = $query->get();

        // Filter by security clearance, but always include user's own approved documents
$visibleDocuments = $allDocuments->filter(function($doc) use ($user) {
    return $user->canViewDocument($doc->security_clearance) ||
           ($doc->user_id == $user->user_id && $doc->approval_status == 'approved');
});

        // Paginate the filtered collection manually
        $currentPage = $request->get('page', 1);
        $perPage = 10;
        $paginatedDocuments = new \Illuminate\Pagination\LengthAwarePaginator(
            $visibleDocuments->forPage($currentPage, $perPage),
            $visibleDocuments->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // ========== STATISTICS ==========
        if (!$user->isAdmin() && !$user->isKmChampion()) {
           $approvedDocumentsCount = Document::where('user_id', $user->user_id)
                                      ->where('approval_status', 'approved')
                                      ->count();
    $pendingDocumentsCount = Document::where('user_id', $user->user_id)
                                     ->where('approval_status', 'pending')
                                     ->count();
    $imagesCount = Document::where('user_id', $user->user_id)
                           ->where('approval_status', 'approved')
                           ->whereIn('doc_file_type', ['jpg', 'jpeg', 'png', 'gif'])
                           ->count();

        $totalUsers = 1;
            $kmChampionCount = 0;
            $adminCount = 0;
            $staffCount = 0;
        } else {
            $approvedDocumentsCount = Document::where('approval_status', 'approved')->count();
            $pendingDocumentsCount = Document::where('approval_status', 'pending')->count();
            $imagesCount = Document::whereIn('doc_file_type', ['jpg', 'jpeg', 'png', 'gif'])->count();
            $totalUsers = User::count();
            $kmChampionCount = User::where('user_role', 'km_champion')->count();
            $adminCount = User::where('user_role', 'admin')->count();
            $staffCount = User::where('user_role', 'staff')->count();
        }
$myDocumentsCount = Document::where('user_id', $user->user_id)->count();

        $categories = DB::table('tbl_categories')->pluck('cat_name')->toArray();

        $recentDocuments = $visibleDocuments->sortByDesc('created_at')->take(5);
        $mostViewedDocuments = $visibleDocuments->sortByDesc('view_count')->take(5);
        // ========== END STATISTICS ==========

        return view('dashboard', compact(
            'paginatedDocuments',
            'categories',
            'approvedDocumentsCount',
            'pendingDocumentsCount',
            'imagesCount',
            'myDocumentsCount',
            'totalUsers',
            'kmChampionCount',
            'adminCount',
            'staffCount',
            'recentDocuments',
            'mostViewedDocuments',
            'sort'
        ));
    }

    // Search - Only approved documents (supports keyword, category filter, sorting, pagination)
    public function search(Request $request)
    {
        $user = auth()->user();

        $query = Document::where('approval_status', 'approved')
                         ->where('doc_status', 'published');

        // Keyword search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('doc_title', 'like', "%{$search}%")
                  ->orWhere('doc_description', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('doc_category', $request->category);
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Get all results
        $allDocuments = $query->get();

        // Apply security clearance filter
        $visibleDocuments = $allDocuments->filter(function($doc) use ($user) {
    return $user->canViewDocument($doc->security_clearance) ||
           ($doc->user_id == $user->user_id && $doc->approval_status == 'approved');
});

        // Paginate manually
        $currentPage = $request->get('page', 1);
        $perPage = 10;
        $paginatedDocuments = new \Illuminate\Pagination\LengthAwarePaginator(
            $visibleDocuments->forPage($currentPage, $perPage),
            $visibleDocuments->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // ========== STATISTICS (same as index) ==========
        if (!$user->isAdmin() && !$user->isKmChampion()) {
            $approvedDocumentsCount = $visibleDocuments->count();
            $pendingDocumentsCount = 0;
            $imagesCount = $visibleDocuments->whereIn('doc_file_type', ['jpg', 'jpeg', 'png', 'gif'])->count();
            $totalUsers = 1;
            $kmChampionCount = 0;
            $adminCount = 0;
            $staffCount = 0;
        } else {
            $approvedDocumentsCount = Document::where('approval_status', 'approved')->count();
            $pendingDocumentsCount = Document::where('approval_status', 'pending')->count();
            $imagesCount = Document::whereIn('doc_file_type', ['jpg', 'jpeg', 'png', 'gif'])->count();
            $totalUsers = User::count();
            $kmChampionCount = User::where('user_role', 'km_champion')->count();
            $adminCount = User::where('user_role', 'admin')->count();
            $staffCount = User::where('user_role', 'staff')->count();
        }

        $categories = DB::table('tbl_categories')->pluck('cat_name')->toArray();

        $recentDocuments = $visibleDocuments->sortByDesc('created_at')->take(5);
        $mostViewedDocuments = $visibleDocuments->sortByDesc('view_count')->take(5);
        // ========== END STATISTICS ==========

        return view('dashboard', compact(
            'paginatedDocuments',
            'categories',
            'approvedDocumentsCount',
            'pendingDocumentsCount',
            'imagesCount',
            'totalUsers',
            'kmChampionCount',
            'adminCount',
            'staffCount',
            'recentDocuments',
            'mostViewedDocuments',
            'sort'
        ));
    }

    // Download document
    public function download($id)
    {
        $document = Document::findOrFail($id);

        if ($document->approval_status !== 'approved') {
            abort(403, 'Document is pending approval.');
        }

        if (!auth()->user()->canViewDocument($document->security_clearance)) {
            abort(403, 'You do not have permission.');
        }

        if (!Storage::disk('local')->exists($document->doc_file_path)) {
            abort(404, 'File not found');
        }

        $document->download_count++;
        $document->save();

        return Storage::disk('local')->download($document->doc_file_path, $document->doc_file_name);
    }

    // Preview document
    public function preview($id)
    {
        $document = Document::findOrFail($id);

        if ($document->approval_status !== 'approved') {
            abort(403, 'Document is pending approval.');
        }

        if (!auth()->user()->canViewDocument($document->security_clearance)) {
            abort(403);
        }

        if (!Storage::disk('local')->exists($document->doc_file_path)) {
            abort(404, 'File not found');
        }

        $document->view_count++;
        $document->save();

        $filePath = storage_path('app/' . $document->doc_file_path);
        $fileType = $document->doc_file_type;

        if (in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
    return response()->file($filePath, ['Content-Type' => mime_content_type($filePath)]);
        }

        if ($fileType === 'pdf') {
  return response()->file($filePath, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="' . $document->doc_file_name . '"'
    ]);        }

        return Storage::disk('local')->download($document->doc_file_path, $document->doc_file_name);
    }

    public function fetchDocuments(Request $request)
{
    $user = auth()->user();

    $query = Document::where('approval_status', 'approved')
                     ->where('doc_status', 'published');

    // Search
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('doc_title', 'like', "%{$search}%")
              ->orWhere('doc_description', 'like', "%{$search}%");
        });
    }

    // Category filter
    if ($request->filled('category')) {
        $query->where('doc_category', $request->category);
    }

    // Sorting
    $sort = $request->get('sort', 'newest');
    if ($sort == 'oldest') {
        $query->orderBy('created_at', 'asc');
    } else {
        $query->orderBy('created_at', 'desc');
    }

    $allDocuments = $query->get();

    // Filter by security clearance
    $visibleDocuments = $allDocuments->filter(function($doc) use ($user) {
        return $user->canViewDocument($doc->security_clearance);
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

    // Return JSON for AJAX, or HTML partial
    if ($request->ajax()) {
        $html = view('partials.document-table', ['paginatedDocuments' => $paginatedDocuments])->render();
        return response()->json(['html' => $html, 'pagination' => (string) $paginatedDocuments->links()]);
    }

    return view('dashboard', compact('paginatedDocuments'));
}
    public function getImage($id)
    {
        $document = Document::findOrFail($id);

        if ($document->approval_status !== 'approved') {
            abort(403);
        }

        $filePath = storage_path('app/' . $document->doc_file_path);

        if (!file_exists($filePath)) {
            abort(404);
        }

        return response()->file($filePath);
    }

    public function showImage($id)
{
    $document = Document::findOrFail($id);
    if ($document->approval_status !== 'approved') {
        abort(403);
    }
    if (!auth()->user()->canViewDocument($document->security_clearance)) {
        abort(403);
    }
    $path = storage_path('app/' . $document->doc_file_path);
    if (!file_exists($path)) {
        abort(404);
    }
    return response()->file($path, ['Content-Type' => mime_content_type($path)]);
}

    // Edit document (owner only)
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

    // Update document
    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);

        if (!auth()->user()->isAdmin() && $document->user_id != auth()->user()->user_id) {
            abort(403);
        }

        $request->validate([
            'doc_title' => 'required|string|max:500',
            'doc_description' => 'nullable|string',
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

        if ($request->hasFile('document')) {
            if (Storage::disk('local')->exists($document->doc_file_path)) {
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

    // Delete document (owner only)
    public function destroy($id)
    {
        $document = Document::findOrFail($id);

        if (auth()->user()->isAdmin()) {
            if (Storage::disk('local')->exists($document->doc_file_path)) {
                Storage::disk('local')->delete($document->doc_file_path);
            }
            $document->delete();
            return redirect()->route('documents.my-uploads')->with('success', 'Document deleted.');
        }

        if ($document->user_id != auth()->user()->user_id) {
            abort(403);
        }

        if ($document->approval_status === 'approved') {
            return redirect()->route('documents.my-uploads')
                ->with('error', 'Approved documents can only be deleted by administrators.');
        }

        if (Storage::disk('local')->exists($document->doc_file_path)) {
            Storage::disk('local')->delete($document->doc_file_path);
        }

        $document->delete();

        return redirect()->route('documents.my-uploads')->with('success', 'Document deleted.');
    }

    // My Uploads - Show user's own documents (including pending)
    public function myUploads()
    {
        $documents = Document::where('user_id', auth()->user()->user_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('documents.my-uploads', compact('documents'));
    }
}
