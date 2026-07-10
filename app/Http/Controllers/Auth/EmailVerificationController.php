<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SystemSetting;
use App\Services\EmailVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailVerificationController extends Controller
{
    /**
     * Display the email verification notice screen.
     */
    public function show(Request $request)
    {
        $user = $request->user();

        if ($user && $user->isVerified()) {
            return redirect()->route('chat')->with('status', 'Your email address is already verified.');
        }

        // If no verification request currently active or expired, auto-generate one
        if ($user) {
            $activeVerification = $user->verifications()->active()->where('type', 'email_verification')->first();
            if (!$activeVerification) {
                EmailVerificationService::generateAndSend($user);
            }
        }

        return view('auth.verify', [
            'user' => $user,
            'targetEmail' => $user->pending_email ?: $user->email,
        ]);
    }

    /**
     * Verify submitted OTP string.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'max:32'],
        ]);

        $user = $request->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Session expired. Please log in again.');
        }

        $result = EmailVerificationService::verifyOtp($user, $request->input('otp'), 'email_verification');

        if ($result['status']) {
            return view('auth.verified-success', ['message' => $result['message']]);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    /**
     * Verify submitted token from verification link.
     */
    public function verifyLink(Request $request, string $token)
    {
        $result = EmailVerificationService::verifyToken($token);

        if ($result['status']) {
            return view('auth.verified-success', ['message' => $result['message']]);
        } else {
            return view('auth.verified-failed', ['message' => $result['message']]);
        }
    }

    /**
     * Resend verification email and OTP to the user.
     */
    public function resend(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to resend verification.');
        }

        if ($user->isVerified()) {
            return redirect()->route('chat')->with('status', 'Your email is already verified.');
        }

        $result = EmailVerificationService::generateAndSend($user);

        if ($result['status']) {
            return redirect()->back()->with('status', 'A new verification code and link have been sent to your email address.');
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    /**
     * Allow user to change email before verification if they made a typo during signup.
     */
    public function changeEmail(Request $request)
    {
        $request->validate([
            'new_email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
        ]);

        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $newEmail = $request->input('new_email');
        $user->update([
            'email' => $newEmail,
            'pending_email' => null,
            'email_verified_at' => null,
            'otp_verified_at' => null,
        ]);

        EmailVerificationService::generateAndSend($user, 'email_verification', $newEmail);

        return redirect()->route('verification.notice')->with('status', "We updated your email to {$newEmail} and sent a fresh verification code.");
    }
}
