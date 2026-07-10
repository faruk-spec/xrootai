<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class AuthSettingController extends Controller
{
    /**
     * Display the enterprise Authentication & Security Settings central hub.
     */
    public function index(Request $request)
    {
        $activeTab = $request->query('tab', 'registration');

        // Load all auth related settings
        $keys = [
            // Registration
            'auth_enable_registration',
            'auth_default_user_role',
            'auth_auto_approve_user',
            'auth_require_terms',
            'auth_allowed_domains',
            'auth_blocked_domains',

            // Email Verification
            'auth_require_email_verification',
            'auth_allow_login_unverified',
            'auth_verification_by_link',
            'auth_verification_by_otp',
            'auth_verification_link_expiry',
            'auth_verification_otp_length',
            'auth_verification_otp_expiry',
            'auth_verification_max_attempts',

            // Password Security
            'auth_password_reset_enable_otp',
            'auth_password_reset_enable_link',
            'auth_password_reset_otp_length',
            'auth_password_reset_expiry_minutes',
            'auth_password_reset_cooldown_seconds',
            'auth_password_reset_max_requests_per_hour',
            'auth_password_reset_max_attempts',
            'auth_password_reset_require_uppercase',
            'auth_password_reset_require_numbers',
            'auth_password_reset_require_symbols',
            'auth_password_reset_min_length',

            // Session & Login Security
            'auth_session_lifetime_minutes',
            'auth_single_session_per_user',
            'auth_login_max_attempts',
            'auth_login_lockout_minutes',
            'auth_track_login_history',

            // Two-Factor Authentication (2FA)
            'auth_2fa_enabled',
            'auth_2fa_remember_days',
            'auth_2fa_enforce_roles',
        ];

        $settings = [];
        foreach ($keys as $key) {
            $settings[$key] = SystemSetting::get($key);
        }

        return view('admin.auth_settings.index', compact('settings', 'activeTab'));
    }

    /**
     * Update the authentication settings.
     */
    public function update(Request $request)
    {
        $section = $request->input('_section', 'registration');

        // Define boolean checkboxes per section that might be omitted if unchecked
        $booleansBySection = [
            'registration' => [
                'auth_enable_registration',
                'auth_auto_approve_user',
                'auth_require_terms',
            ],
            'verification' => [
                'auth_require_email_verification',
                'auth_allow_login_unverified',
                'auth_verification_by_link',
                'auth_verification_by_otp',
            ],
            'password' => [
                'auth_password_reset_enable_otp',
                'auth_password_reset_enable_link',
                'auth_password_reset_require_uppercase',
                'auth_password_reset_require_numbers',
                'auth_password_reset_require_symbols',
            ],
            'session' => [
                'auth_single_session_per_user',
                'auth_track_login_history',
            ],
            'twofactor' => [
                'auth_2fa_enabled',
            ],
        ];

        $inputs = $request->except(['_token', '_section']);

        // First process all submitted inputs
        foreach ($inputs as $key => $value) {
            if (array_key_exists($key, SystemSetting::$defaults)) {
                $defaultValue = SystemSetting::$defaults[$key];
                $type = 'string';

                if (is_bool($defaultValue)) {
                    $type = 'boolean';
                    $value = (bool) $value;
                } elseif (is_int($defaultValue)) {
                    $type = 'integer';
                    $value = (int) $value;
                }

                SystemSetting::set($key, $value, 'auth', $type);
            }
        }

        // Handle unchecked boolean toggles for the current section
        if (isset($booleansBySection[$section])) {
            foreach ($booleansBySection[$section] as $boolKey) {
                if (!$request->has($boolKey)) {
                    SystemSetting::set($boolKey, false, 'auth', 'boolean');
                }
            }
        }

        // Log action
        ActivityLog::log(
            'auth_settings_updated',
            "Authentication settings updated [Section: {$section}]",
            auth()->id()
        );

        // Clear config cache so changes propagate instantly across queues and middleware
        Artisan::call('config:clear');

        return redirect()->route('admin.auth-settings.index', ['tab' => $section])
            ->with('success', 'Authentication settings updated successfully.');
    }
}
