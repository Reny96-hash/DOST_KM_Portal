<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'emp_id' => ['required', 'string', 'max:20', 'unique:tbl_users,emp_id'],
            'user_first_name' => ['required', 'string', 'max:100'],
            'user_last_name' => ['required', 'string', 'max:100'],
            'user_middle_initial' => ['nullable', 'string', 'max:5'],
            'user_division' => ['nullable', 'string', 'max:200'],
            'user_email' => ['required', 'string', 'email', 'max:255', 'unique:tbl_users,user_email', 'regex:/@dost\.gov\.ph$/'],
            'password' => ['required', 'confirmed', 'min:8', 'regex:/[A-Z]/', 'regex:/[a-z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'],
        ]);

        $user = User::create([
            'emp_id' => $request->emp_id,
            'user_first_name' => $request->user_first_name,
            'user_last_name' => $request->user_last_name,
            'user_middle_initial' => $request->user_middle_initial,
            'user_division' => $request->user_division,
            'user_email' => $request->user_email,
            'user_password_hash' => Hash::make($request->password),
            'security_clearance' => 'Internal',
            'user_role' => 'staff',
            'user_status' => 'active',
        ]);

        event(new Registered($user));

        return redirect()->route('login')->with('status', 'Registration successful! Please login.');
    }
}
