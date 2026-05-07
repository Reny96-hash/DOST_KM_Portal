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

    // Dashboard - Show ONLY approved documents
public function index()
{
    $user = auth()->user();

    // Get counts based on user's role and clearance
    $allDocuments = Document::where('approval_status', 'approved')
        ->where('doc_status', 'published')
        ->get();

    // Filter documents by security clearance (what user can see)
    $visibleDocuments = $allDocuments->filter(function($doc) use ($user) {
        return $user->canViewDocument($doc->security_clearance);
    });

    // For regular users - only show stats based on their clearance
    if (!$user->isAdmin() && !$user->isKmChampion()) {
        // Regular users see only documents they can access
        $approvedDocumentsCount = $visibleDocuments->count();
        $pendingDocumentsCount = 0;  // Regular users don't see pending
        $imagesCount = $visibleDocuments->whereIn('doc_file_type', ['jpg', 'jpeg', 'png', 'gif'])->count();

        // User counts - regular users don't see other users
        $totalUsers = 1;  // Only themselves
        $kmChampionCount = 0;
        $adminCount = 0;
        $staffCount = 0;
    } else {
        // Admin and KM Champion see global stats
        $approvedDocumentsCount = Document::where('approval_status', 'approved')->count();
        $pendingDocumentsCount = Document::where('approval_status', 'pending')->count();
        $imagesCount = Document::whereIn('doc_file_type', ['jpg', 'jpeg', 'png', 'gif'])->count();

        $totalUsers = User::count();
        $kmChampionCount = User::where('user_role', 'km_champion')->count();
        $adminCount = User::where('user_role', 'admin')->count();
        $staffCount = User::where('user_role', 'staff')->count();
    }

    // Get categories
    $categories = DB::table('tbl_categories')->pluck('cat_name')->toArray();

    // For pagination (only approved documents user can see)
    $currentPage = request()->get('page', 1);
    $perPage = 10;
    $paginatedDocuments = new \Illuminate\Pagination\LengthAwarePaginator(
        $visibleDocuments->forPage($currentPage, $perPage),
        $visibleDocuments->count(),
        $perPage,
        $currentPage,
        ['path' => request()->url(), 'query' => request()->query()]
    );

    // Recently added and most viewed (only what user can see)
    $recentDocuments = $visibleDocuments->sortByDesc('created_at')->take(5);
    $mostViewedDocuments = $visibleDocuments->sortByDesc('view_count')->take(5);

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
        'mostViewedDocuments'
    ));
}

    // Search - Only approved documents
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

        $allDocuments = $query->orderBy('created_at', 'desc')->get();

        $documents = $allDocuments->filter(function($doc) use ($user) {
            return $user->canViewDocument($doc->security_clearance);
        });

        $categories = DB::table('tbl_categories')->pluck('cat_name')->toArray();
        $totalUsers = User::count();

        return view('dashboard', compact('documents', 'categories', 'totalUsers'));
    }

    // Download document
    public function download($id)
    {
        $document = Document::findOrFail($id);

        // Only approved documents can be downloaded
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

    // For images - display inline
    if (in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        return response()->file($filePath);
    }

    // For PDF - display inline
    if ($fileType === 'pdf') {
        return response()->file($filePath, ['Content-Type' => 'application/pdf']);
    }

    // For other files - force download
    return Storage::disk('local')->download($document->doc_file_path, $document->doc_file_name);
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

    // Only show images from approved documents
    if ($document->approval_status !== 'approved') {
        abort(403, 'Document is pending approval.');
    }

    // Check security clearance
    if (!auth()->user()->canViewDocument($document->security_clearance)) {
        abort(403, 'You do not have permission to view this document.');
    }

    $path = storage_path('app/' . $document->doc_file_path);

    if (!file_exists($path)) {
        abort(404, 'Image file not found.');
    }

    $file = file_get_contents($path);
    $type = mime_content_type($path);

    return response($file, 200)->header('Content-Type', $type);
}

    // Edit document (owner only)
public function edit($id)
{
    $document = Document::findOrFail($id);

    // Admin can edit any document
    if (auth()->user()->isAdmin()) {
        $categories = DB::table('tbl_categories')->pluck('cat_name')->toArray();
        return view('documents.edit', compact('document', 'categories'));
    }

    // Non-admin: only owner can edit, and only if NOT approved
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

    // Check permission
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

    // Prepare update data
    $updateData = [
        'doc_title' => $request->doc_title,
        'doc_description' => $request->doc_description,
        'doc_category' => $request->doc_category,
        'security_clearance' => $request->security_clearance,
    ];

    // Check if user is Admin or KM Champion (auto-approve)
    $autoApprove = auth()->user()->isAdmin() || auth()->user()->isKmChampion();

    if ($autoApprove) {
        // Admin/KM Champion: auto-approve
        $updateData['approval_status'] = 'approved';
        $updateData['doc_status'] = 'published';
        $updateData['reviewed_by'] = auth()->user()->user_id;
        $updateData['reviewed_at'] = now();
    } else {
        // Regular user: needs re-approval
        $updateData['approval_status'] = 'pending';
        $updateData['doc_status'] = 'draft';
    }

    // If a new file is uploaded
    if ($request->hasFile('document')) {
        // Delete old file
        if (Storage::disk('local')->exists($document->doc_file_path)) {
            Storage::disk('local')->delete($document->doc_file_path);
        }

        // Upload new file
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

    // Admin can delete any document
    if (auth()->user()->isAdmin()) {
        if (Storage::disk('local')->exists($document->doc_file_path)) {
            Storage::disk('local')->delete($document->doc_file_path);
        }
        $document->delete();
        return redirect()->route('documents.my-uploads')->with('success', 'Document deleted.');
    }

    // Non-admin: only owner can delete, and only if NOT approved
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
