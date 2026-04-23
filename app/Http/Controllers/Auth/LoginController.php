<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            //'email' => 'required|email|regex:/@dost\.gov\.ph$/',
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $user = User::where('user_email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Invalid credentials'])->onlyInput('email');
        }

        // Check lockout
        if ($user->locked_until && now()->lt($user->locked_until)) {
            $minutes = now()->diffInMinutes($user->locked_until);
            return back()->withErrors(['email' => "Account locked. Try again in {$minutes} minutes."]);
        }

        // Check status
        if ($user->user_status !== 'active') {
            return back()->withErrors(['email' => 'Account is not active.']);
        }

        // Check password
        if (!Hash::check($request->password, $user->user_password_hash)) {
            $user->login_attempts++;
            if ($user->login_attempts >= 3) {
                $user->locked_until = now()->addMinutes(15);
            }
            $user->save();
            return back()->withErrors(['email' => 'Invalid credentials'])->onlyInput('email');
        }

        // Reset attempts and login
        $user->login_attempts = 0;
        $user->locked_until = null;
        $user->last_login_at = now();
        $user->last_login_ip = $request->ip();
        $user->save();

        Auth::login($user);

        // Log access
        Log::info('User logged in', ['user_id' => $user->user_id, 'ip' => $request->ip()]);

        // Check if needs password change
        if ($user->user_must_change_password) {
            return redirect()->route('password.change');
        }

        return redirect()->route('dashboard');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
