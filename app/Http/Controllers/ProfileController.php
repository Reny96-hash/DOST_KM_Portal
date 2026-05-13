<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'user_first_name' => 'required|string|max:100',
            'user_last_name' => 'required|string|max:100',
            'user_email' => 'required|email|unique:tbl_users,user_email,' . $user->user_id . ',user_id',
            'user_division' => 'nullable|string|max:200',
            'user_designation' => 'nullable|string|max:200',
        ]);
        $user->update($request->only(['user_first_name', 'user_last_name', 'user_email', 'user_division', 'user_designation']));
        return back()->with('success', 'Profile updated.');
    }
}
