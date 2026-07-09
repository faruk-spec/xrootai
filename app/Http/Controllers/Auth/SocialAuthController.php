<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Redirect to the specified OAuth provider.
     */
    public function redirectToProvider(string $provider)
    {
        if (!in_array($provider, ['google', 'github'])) {
            abort(404, 'Provider not supported.');
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle the provider callback.
     */
    public function handleProviderCallback(string $provider)
    {
        if (!in_array($provider, ['google', 'github'])) {
            abort(404, 'Provider not supported.');
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Social login failed. Please try again.',
            ]);
        }

        // 1. Look up user by provider and provider_id
        $user = User::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if (!$user) {
            // 2. Look up by email (to link existing traditional accounts)
            if ($socialUser->getEmail()) {
                $user = User::where('email', $socialUser->getEmail())->first();
                if ($user) {
                    // Link provider credentials
                    $user->update([
                        'provider' => $provider,
                        'provider_id' => $socialUser->getId(),
                    ]);
                }
            }
        }

        if (!$user) {
            // 3. Create a new user account if not found
            $user = User::create([
                'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'SSO User',
                'email' => $socialUser->getEmail(),
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'password' => null, // password is nullable in migration
                'role' => 'user',
            ]);
        }

        // Log the user in
        Auth::login($user, true);

        request()->session()->regenerate();

        return redirect()->intended(route('chat'));
    }
}
