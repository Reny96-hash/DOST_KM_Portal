<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function uploadForm()
    {
        $categories = ['Research Papers', 'Policies', 'Project Reports', 'Technical Guides', 'Administrative'];
        return view('upload', compact('categories'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:500',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'document' => 'required|file|mimes:pdf,doc,docx,xlsx|max:10240'
        ]);

        $file = $request->file('document');
        $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
        $filePath = $file->storeAs('documents', $fileName, 'local');

        $document = Document::create([
            'user_id' => Auth::user()->user_id,
            'doc_title' => $request->title,
            'doc_description' => $request->description,
            'doc_category' => $request->category,
            'doc_file_path' => $filePath,
            'doc_file_name' => $file->getClientOriginalName(),
            'doc_file_type' => $file->getClientOriginalExtension(),
            'doc_file_size' => round($file->getSize() / 1024, 2),
            'security_clearance' => 'Internal',
            'doc_version' => 1.0
        ]);

        Log::info('Document uploaded', ['user_id' => Auth::user()->user_id, 'doc_id' => $document->doc_id]);

        return redirect()->route('dashboard')->with('success', 'Document uploaded successfully!');
    }

    public function download($id)
    {
        $document = Document::findOrFail($id);

        if (!Storage::disk('local')->exists($document->doc_file_path)) {
            abort(404);
        }

        $document->download_count++;
        $document->save();

        Log::info('Document downloaded', ['user_id' => Auth::user()->user_id, 'doc_id' => $document->doc_id]);

        return Storage::disk('local')->download($document->doc_file_path, $document->doc_file_name);
    }

public function search(Request $request)
{
    $query = Document::where('doc_status', 'published');

    if ($request->filled('search')) {
        $query->where('doc_title', 'like', '%' . $request->search . '%');
    }

    if ($request->filled('category')) {
        $query->where('doc_category', $request->category);
    }

    // Sort by date
    $sortOrder = $request->get('sort', 'desc');
    $query->orderBy('created_at', $sortOrder);

    $documents = $query->paginate(10);
    $categories = ['Research Papers', 'Policies', 'Project Reports', 'Technical Guides', 'Administrative'];

    return view('dashboard', compact('documents', 'categories'));
}
}
