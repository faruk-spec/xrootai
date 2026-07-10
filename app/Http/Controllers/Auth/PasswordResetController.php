<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PasswordResetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PasswordResetController extends Controller
{
    /**
     * Display the form to request a password reset link/OTP.
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Display the dedicated OTP verification & password reset form.
     */
    public function showOtpForm(Request $request)
    {
        return view('auth.passwords.otp', [
            'email' => $request->query('email', ''),
        ]);
    }

    /**
     * Display the token link verification & password reset form.
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    /**
     * Send a reset link and OTP code to the given user.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        // For security against email enumeration, if user does not exist, we still return a success message
        if (!$user) {
            return back()->with('status', 'If an account exists for ' . $request->email . ', a verification code and reset link have been sent.');
        }

        $result = PasswordResetService::requestReset($user, $request->ip(), $request->userAgent());

        if (!$result['status']) {
            return back()->withErrors(['email' => $result['message']])->withInput();
        }

        // Redirect to OTP verification page or back with success status
        return redirect()->route('password.otp', ['email' => $user->email])
            ->with('status', $result['message']);
    }

    /**
     * Reset the user's password using either a token or OTP code.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'token' => ['required', 'string'], // This field will carry either the token or the OTP code
            'password' => PasswordResetService::getPasswordRules(),
        ], [
            'token.required' => 'Please enter your verification code or token.',
        ]);

        $validation = PasswordResetService::validateResetCode($request->email, $request->token);

        if (!$validation['status']) {
            return back()->withErrors(['token' => $validation['message']])->withInput($request->only('email'));
        }

        $resetCode = $validation['resetCode'];
        $result = PasswordResetService::resetPassword($resetCode, $request->password, $request->ip());

        if (!$result['status']) {
            return back()->withErrors(['email' => $result['message']])->withInput($request->only('email'));
        }

        $user = $result['user'];

        // Automatically log the user in if approved & verified, or redirect to login
        if ($user->isApproved() && ($user->isVerified() || !\App\Models\SystemSetting::get('auth_require_email_verification', true))) {
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->route('chat')->with('status', 'Your password has been successfully reset.');
        }

        return redirect()->route('login')->with('status', 'Your password has been successfully reset. Please log in.');
    }
}
