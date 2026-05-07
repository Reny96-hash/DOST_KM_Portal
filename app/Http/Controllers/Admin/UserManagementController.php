<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin'); // Only admin can access
    }

    public function index()
    {
        $users = User::paginate(10);
        return view('admin.users', compact('users'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.edit-user', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'user_division' => 'nullable|string|max:200',
            'user_role' => 'required|in:staff,km_champion,admin',
            'security_clearance' => 'required|in:Public,Internal,Confidential,Secret,Top Secret',
            'user_status' => 'required|in:active,disabled'
        ]);

        $user->user_division = $request->user_division;
        $user->user_role = $request->user_role;
        $user->security_clearance = $request->security_clearance;
        $user->user_status = $request->user_status;
        $user->save();

        return redirect()->route('admin.users')->with('success', 'User updated successfully');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->user_id == auth()->user()->user_id) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        return redirect()->route('admin.users')->with('success', 'User deleted successfully');
    }

}
