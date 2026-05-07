<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        // Get all documents
        $allDocuments = Document::where('doc_status', 'published')->get();

        // Filter by security clearance
        $documents = $allDocuments->filter(function($doc) use ($user) {
            return $user->canViewDocument($doc->security_clearance);
        });

        $categories = DB::table('tbl_categories')->pluck('cat_name')->toArray();

        // Statistics
        $totalDocuments = $documents->count();
        $totalUsers = User::count();
        $recentDocuments = $documents->sortByDesc('created_at')->take(5);
        $popularDocuments = $documents->sortByDesc('view_count')->take(5);

        $categoryStats = Document::where('doc_status', 'published')
            ->select('doc_category', DB::raw('count(*) as count'))
            ->groupBy('doc_category')
            ->get();

        return view('dashboard', compact(
            'documents', 'categories', 'totalDocuments', 'totalUsers',
            'recentDocuments', 'popularDocuments', 'categoryStats'
        ));
    }
}
