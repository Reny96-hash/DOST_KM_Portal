<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class TechnicalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index()
    {
        // System logs
        $logFile = storage_path('logs/laravel.log');
        $logs = [];
        if (File::exists($logFile)) {
            $content = File::get($logFile);
            $lines = explode("\n", $content);
            $logs = array_slice(array_reverse($lines), 0, 100);
        }

        return view('admin.technical', compact('logs'));
    }

    public function backupDatabase()
    {
        $dbPath = database_path('database.sqlite');
        if (!File::exists($dbPath)) {
            return back()->with('error', 'Database file not found.');
        }
        return Response::download($dbPath, 'dost_km_backup_' . date('Y-m-d_H-i-s') . '.sqlite');
    }
}
