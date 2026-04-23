<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $documents = Document::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $categories = ['Research Papers', 'Policies', 'Project Reports', 'Technical Guides', 'Administrative'];
        $totalDocuments = Document::count();
        $totalUsers = User::count();

        return view('dashboard', compact('documents', 'categories', 'totalDocuments', 'totalUsers'));
    }
}
