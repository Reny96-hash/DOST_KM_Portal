<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAnalyticsController extends Controller
{
    public function index()
    {
        // Documents per category
        $categoryStats = Document::select('doc_category', DB::raw('count(*) as count'))
            ->whereNotNull('doc_category')
            ->groupBy('doc_category')
            ->pluck('count', 'doc_category');

        // Documents per security clearance
        $clearanceStats = Document::select('security_clearance', DB::raw('count(*) as count'))
            ->groupBy('security_clearance')
            ->pluck('count', 'security_clearance');

        // Uploads per month (last 6 months)
        $monthlyUploads = Document::select(DB::raw("strftime('%Y-%m', created_at) as month"), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->pluck('count', 'month');

        // User roles distribution
        $roleStats = User::select('user_role', DB::raw('count(*) as count'))
            ->groupBy('user_role')
            ->pluck('count', 'user_role');

        return view('admin.analytics', compact('categoryStats', 'clearanceStats', 'monthlyUploads', 'roleStats'));
    }
}
