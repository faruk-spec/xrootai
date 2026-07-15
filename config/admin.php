<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin Panel Allowed IP Addresses
    |--------------------------------------------------------------------------
    |
    | If this array is not empty, only requests originating from these IP
    | addresses will be permitted to access routes protected by the
    | AdminMiddleware. Leave empty to allow any IP.
    |
    */
    'allowed_ips' => array_filter(explode(',', env('ADMIN_ALLOWED_IPS', ''))),

    /*
    |--------------------------------------------------------------------------
    | Admin Inactivity Timeout (Minutes)
    |--------------------------------------------------------------------------
    |
    | The number of minutes of inactivity before an administrative session
    | is automatically timed out and redirected to login or re-authentication.
    |
    */
    'session_timeout' => (int) env('ADMIN_SESSION_TIMEOUT', 15),

    /*
    |--------------------------------------------------------------------------
    | Enforce Mandatory Two-Factor Authentication
    |--------------------------------------------------------------------------
    |
    | When enabled, users with administrative privileges (Super Admin, Admin)
    | MUST have Two-Factor Authentication enabled to access any /admin routes.
    |
    */
    'require_2fa' => env('ADMIN_REQUIRE_2FA', false),

    /*
    |--------------------------------------------------------------------------
    | Security Alert Email
    |--------------------------------------------------------------------------
    |
    | Email address where security alerts (e.g., repeated failed admin logins,
    | unauthorized IP access attempts, dangerous action audits) are dispatched.
    |
    */
    'alert_email' => env('ADMIN_ALERT_EMAIL', 'security@example.com'),
];
