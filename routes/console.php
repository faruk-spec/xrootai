<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Conversation;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('security:disable-2fa {--email= : Specific user email to disable 2FA} {--all : Disable 2FA globally and for all users}', function () {
    $email = $this->option('email');
    $all = $this->option('all');

    if (!$email && !$all) {
        $this->info("Disabling 2FA globally in System Settings and for all admin accounts...");
        $all = true;
    }

    if ($all) {
        if (\Illuminate\Support\Facades\Schema::hasTable('system_settings')) {
            \App\Models\SystemSetting::set('auth_2fa_enabled', false, 'auth', 'boolean');
            \App\Models\SystemSetting::set('security_enable_2fa', false, 'security', 'boolean');
            \App\Models\SystemSetting::set('auth_2fa_enforce_roles', '', 'auth', 'string');
        }
        if (\Illuminate\Support\Facades\Schema::hasTable('users')) {
            \App\Models\User::query()->update(['two_factor_enabled' => false, 'two_factor_secret' => null]);
        }
        $this->info("✔ 2FA successfully disabled globally and for all users.");
    } elseif ($email) {
        if (\Illuminate\Support\Facades\Schema::hasTable('users')) {
            $user = \App\Models\User::where('email', $email)->first();
            if ($user) {
                $user->update(['two_factor_enabled' => false, 'two_factor_secret' => null]);
                $this->info("✔ 2FA successfully disabled for user: {$user->email}");
            } else {
                $this->error("User not found: {$email}");
                return 1;
            }
        }
    }

    \Illuminate\Support\Facades\Cache::flush();
    $this->info("✔ Cache cleared. You can now log into the admin panel without 2FA!");
})->purpose('Disable Two-Factor Authentication globally or for specific users');

Schedule::call(function () {
    // Soft delete guest conversations older than 24 hours
    Conversation::whereNull('user_id')
        ->where('created_at', '<', Carbon::now()->subDay())
        ->delete();
})->daily();
