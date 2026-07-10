<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\UserSetting;
use App\Models\SystemSetting;
use App\Models\ActivityLog;
use App\Services\EmailVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    public function create()
    {
        if (Auth::check()) {
            return redirect()->route('chat');
        }

        if (!SystemSetting::get('auth_enable_registration', true)) {
            return view('auth.register')->with('registrationDisabled', true);
        }

        return view('auth.register');
    }

    public function store(Request $request)
    {
        if (!SystemSetting::get('auth_enable_registration', true)) {
            return redirect()->route('login')->with('error', 'New user registration is currently disabled by the system administrator.');
        }

        $allowedDomainsRaw = SystemSetting::get('auth_allowed_domains', '');
        $blockedDomainsRaw = SystemSetting::get('auth_blocked_domains', 'mailinator.com, tempmail.com, 10minutemail.com, yopmail.com, guerrillamail.com');
        $domain = strtolower(substr(strrchr($request->email, "@"), 1));

        if (!empty(trim($allowedDomainsRaw))) {
            $allowedList = array_map('trim', explode(',', strtolower($allowedDomainsRaw)));
            if (!in_array($domain, $allowedList)) {
                return back()->withErrors(['email' => "Registration is restricted to allowed company domains ({$allowedDomainsRaw})."])->withInput();
            }
        }

        if (!empty(trim($blockedDomainsRaw))) {
            $blockedList = array_map('trim', explode(',', strtolower($blockedDomainsRaw)));
            if (in_array($domain, $blockedList)) {
                return back()->withErrors(['email' => 'Disposable or temporary email domains are not allowed. Please use your permanent work/personal address.'])->withInput();
            }
        }

        $termsRule = SystemSetting::get('auth_require_terms', true) ? ['required', 'accepted'] : ['nullable'];

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms_and_conditions' => $termsRule,
        ], [
            'terms_and_conditions.required' => 'You must accept the Terms and Conditions to create an account.',
            'terms_and_conditions.accepted' => 'You must accept the Terms and Conditions to create an account.',
        ]);

        $defaultRole = SystemSetting::get('auth_default_user_role', 'user');
        $autoApprove = SystemSetting::get('auth_auto_approve_user', true);
        $requireVerification = SystemSetting::get('auth_require_email_verification', true);
        $allowLoginUnverified = SystemSetting::get('auth_allow_login_unverified', false);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $defaultRole,
            'is_approved' => $autoApprove,
            'status' => $autoApprove ? 'active' : 'pending_approval',
        ]);

        // Sync dynamic roles relation
        $roleRecord = Role::where('name', $defaultRole)->first();
        if ($roleRecord) {
            $user->roles()->sync([$roleRecord->id]);
        }

        // Create default user settings
        UserSetting::create([
            'user_id' => $user->id,
            'theme' => 'system',
            'default_model' => 'mock',
            'system_prompt' => 'You are XrootAI, a helpful, advanced AI coding and conversation assistant.',
            'preferences' => [],
        ]);

        ActivityLog::log('user_registered', "New user registered: {$user->name} ({$user->email})", $user->id);

        if (!$autoApprove) {
            return redirect()->route('login')->with('status', 'Your account has been registered and is pending administrator approval before you can log in.');
        }

        if ($requireVerification) {
            EmailVerificationService::generateAndSend($user);

            if ($allowLoginUnverified) {
                Auth::login($user);
                return redirect()->route('chat')->with('status', 'Welcome! We have sent a verification code to your email.');
            } else {
                Auth::login($user);
                return redirect()->route('verification.notice');
            }
        }

        Auth::login($user);

        return redirect()->route('chat');
    }
}
