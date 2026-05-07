<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('user_email', $request->email)->first();

        if (!$user) {
            Log::warning('Login failed: User not found', ['email' => $request->email]);
            return back()->withErrors(['email' => 'Invalid email and/or password.'])->onlyInput('email');
        }

        // Check if account is locked
        if ($user->isLocked()) {
            $minutes = now()->diffInMinutes($user->locked_until);
            return back()->withErrors(['email' => "Account locked. Try again in {$minutes} minutes."]);
        }

        // Check account status
        if ($user->user_status === 'disabled') {
            return back()->withErrors(['email' => 'Your account has been disabled.']);
        }

        // Attempt login
        if (!Auth::attempt(['user_email' => $request->email, 'password' => $request->password], $request->boolean('remember'))) {
            $user->incrementLoginAttempts();
            Log::warning('Login failed: Invalid password', ['user_id' => $user->user_id, 'attempts' => $user->login_attempts]);
            return back()->withErrors(['email' => 'Invalid email and/or password.']);
        }

        // Successful login - reset attempts
        $user->resetLoginAttempts();
        $user->last_login_at = now();
        $user->last_login_ip = $request->ip();
        $user->save();

        Log::info('User logged in', ['user_id' => $user->user_id, 'role' => $user->user_role]);

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Log::info('User logged out', ['user_id' => Auth::user()->user_id ?? 'unknown']);
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
