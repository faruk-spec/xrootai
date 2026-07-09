<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OAuthProvider;
use App\Models\User;
use App\Models\Role;
use App\Services\OAuth\DynamicCustomProvider;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

class OAuthController extends Controller
{
    /**
     * Redirect the user to the provider's authentication screen.
     */
    public function redirect(Request $request, string $providerSlug)
    {
        try {
            $provider = OAuthProvider::where('provider_slug', $providerSlug)
                ->where('is_active', true)
                ->firstOrFail();

            if (!class_exists(\Laravel\Socialite\Facades\Socialite::class)) {
                return redirect()->route('login')->with('error', 'OAuth Login is currently unavailable: The Laravel Socialite package (`laravel/socialite`) is not installed inside the server\'s vendor directory. Please run `composer install` or `composer require laravel/socialite` via terminal on the production server.');
            }

            $this->bootSocialiteDriver($provider);

            $driver = Socialite::driver($providerSlug);

            // Append custom scopes
            if (!empty($provider->scopes)) {
                $driver->scopes($provider->scopes);
            }

            // Append additional parameters
            if (!empty($provider->additional_params)) {
                $driver->with($provider->additional_params);
            }

            try {
                return $driver->redirect();
            } catch (\Exception $e) {
                // Fallback to stateless redirect if session cookie/MAC check fails
                return $driver->stateless()->redirect();
            }
        } catch (\Exception $e) {
            $message = $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException 
                ? "Social login provider '{$providerSlug}' is not enabled or configured in the Admin Panel (`/admin/oauth`)." 
                : $e->getMessage();
            return redirect()->route('login')->with('error', "Authentication Redirect Error: {$message}");
        }
    }

    /**
     * Obtain the user information from the OAuth provider and log the user in.
     */
    public function callback(Request $request, string $providerSlug)
    {
        try {
            $provider = OAuthProvider::where('provider_slug', $providerSlug)
                ->where('is_active', true)
                ->firstOrFail();

            if (!class_exists(\Laravel\Socialite\Facades\Socialite::class)) {
                return redirect()->route('login')->with('error', 'OAuth Login is currently unavailable: The Laravel Socialite package (`laravel/socialite`) is not installed on this server. Please run `composer install` on your server.');
            }

            $this->bootSocialiteDriver($provider);

            try {
                $socialiteUser = Socialite::driver($providerSlug)->user();
            } catch (\Exception $e) {
                // Automatically fall back to stateless mode if session state or MAC validation throws an exception
                $socialiteUser = Socialite::driver($providerSlug)->stateless()->user();
            }
        } catch (\Exception $e) {
            $message = $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException 
                ? "Social login provider '{$providerSlug}' is not enabled or configured in the Admin Panel." 
                : $e->getMessage();
            return redirect()->route('login')->with('error', "Authentication Callback Error: {$message}");
        }

        $email = $socialiteUser->getEmail() ?: ($socialiteUser->getId() . "@{$providerSlug}.social");
        
        // Find existing user or register a new user
        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'name' => $socialiteUser->getName() ?: ($socialiteUser->getNickname() ?: 'OAuth User'),
                'email' => $email,
                'password' => Hash::make(Str::random(24)),
                'role' => 'user',
            ]);

            // Sync database default user role pivots
            if (Schema::hasTable('roles')) {
                $roleRecord = Role::where('name', 'user')->first();
                if ($roleRecord) {
                    $user->roles()->sync([$roleRecord->id]);
                }
            }
        }

        auth()->login($user);

        return redirect()->route('chat')->with('success', "Authenticated successfully via {$provider->provider_name}.");
    }

    /**
     * Bootstrap the Socialite driver configuration dynamically.
     */
    protected function bootSocialiteDriver(OAuthProvider $provider): void
    {
        $slug = $provider->provider_slug;

        // Dynamic configs binding
        config([
            "services.{$slug}" => [
                'client_id' => trim((string) $provider->client_id),
                'client_secret' => trim((string) $provider->client_secret),
                'redirect' => trim((string) ($provider->redirect_url ?: url("/auth/{$slug}/callback"))),
            ]
        ]);

        // Register custom driver configuration dynamically
        if ($slug === 'custom') {
            $socialite = resolve(\Laravel\Socialite\Contracts\Factory::class);
            $socialite->extend('custom', function ($app) use ($provider) {
                return new DynamicCustomProvider(
                    $app['request'],
                    $provider->client_id,
                    $provider->client_secret,
                    $provider->redirect_url,
                    $provider->auth_url ?: '',
                    $provider->token_url ?: '',
                    $provider->user_info_url ?: '',
                    $provider->scopes ?: []
                );
            });
        }
    }
}
