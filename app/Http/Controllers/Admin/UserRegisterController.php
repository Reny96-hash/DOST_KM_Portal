<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserRegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function showRegisterForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:200',
            //'email' => 'required|email|regex:/@dost\.gov\.ph$/|unique:tbl_users,user_email',
            'email' => 'required|email|unique:tbl_users,user_email',
            'division' => 'required|string|max:200',
        'role' => 'required|in:staff,info_owner,km_champion,edts_admin,director,admin'
        ]);



        $nameParts = explode(' ', $request->full_name, 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';

        $tempPassword = Str::random(12);

        $user = User::create([
            'emp_id' => 'EMP-' . strtoupper(Str::random(8)),
            'user_first_name' => $firstName,
            'user_last_name' => $lastName,
            'user_division' => $request->division,
            'user_email' => $request->email,
            'user_password_hash' => Hash::make($tempPassword),
            'user_password_temp' => Hash::make($tempPassword),
            'user_password_temp_expires' => now()->addHours(24),
            'user_must_change_password' => true,
            'security_clearance' => 'Internal',
            'user_role' => $request->role,
            'user_status' => 'active',
            'created_by' => auth()->user()->user_id
        ]);

        // Send email to @dost.gov.ph address
        // Send email to @dost.gov.ph address
// Send email via Mailtrap
try {
    $emailBody = "Dear {$user->user_first_name} {$user->user_last_name},\n\n";
    $emailBody .= "Your account has been created in the DOST Knowledge Management Portal.\n\n";
    $emailBody .= "Login URL: http://127.0.0.1:8000\n";
    $emailBody .= "Your Email: {$user->user_email}\n";
    $emailBody .= "Temporary Password: {$tempPassword}\n\n";
    $emailBody .= "IMPORTANT: You will be required to change your password upon first login.\n\n";
    $emailBody .= "Password Requirements:\n";
    $emailBody .= "- Minimum 8 characters\n";
    $emailBody .= "- At least one uppercase letter\n";
    $emailBody .= "- At least one lowercase letter\n";
    $emailBody .= "- At least one number\n";
    $emailBody .= "- At least one special character (@\$!%*#?&)\n\n";
    $emailBody .= "For security reasons, this temporary password will expire in 24 hours.\n\n";
    $emailBody .= "If you did not request this account, please contact your DOST KM Champion immediately.\n\n";
    $emailBody .= "Thank you,\n";
    $emailBody .= "DOST Knowledge Management Team";

    Mail::raw($emailBody, function ($message) use ($user) {
        $message->to($user->user_email)
                ->subject('Welcome to DOST Knowledge Management Portal');
    });

    \Log::info('Email sent to: ' . $user->user_email);

} catch (\Exception $e) {
    \Log::error('Email sending failed: ' . $e->getMessage());
}

        return redirect()->route('dashboard')->with('success', 'User registered. Credentials sent to ' . $user->user_email);
    }
}
