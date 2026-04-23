<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordChangeController extends Controller
{
    public function showForm()
    {
        return view('change-password');
    }

    public function change(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed|regex:/[A-Z]/|regex:/[a-z]/|regex:/[0-9]/|regex:/[@$!%*#?&]/'
        ]);

        $user = Auth::user();
        $user->user_password_hash = Hash::make($request->password);
        $user->user_password_temp = null;
        $user->user_password_temp_expires = null;
        $user->user_must_change_password = false;
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Password changed successfully!');
    }
}
