@extends('layouts.admin')

@section('title', 'Authentication & Security Settings')

@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">Authentication & Security Settings</li>
@endsection

@section('content')
<div class="row g-4">
    <!-- Left Navigation Pills for Auth Sections -->
    <div class="col-lg-3">
        <div class="card border-0 p-3 shadow-sm position-sticky" style="top:90px; border-radius: 16px;">
            <div class="d-flex justify-content-between align-items-center mb-3 px-2">
                <h6 class="fw-bold text-muted text-uppercase mb-0" style="font-size:0.75rem;">Auth Modules</h6>
                <span class="badge bg-success-subtle text-success" style="font-size: 0.65rem;">Active</span>
            </div>
            <div class="d-flex flex-column gap-1 nav flex-column nav-pills" id="auth-tabs" role="tablist">
                <button class="btn btn-sm text-start py-3 px-3 border-0 d-flex align-items-center gap-2 nav-link {{ $activeTab === 'registration' ? 'active' : '' }}" 
                        id="tab-registration" 
                        data-bs-toggle="pill" 
                        data-bs-target="#panel-registration" 
                        type="button" 
                        role="tab" 
                        aria-selected="{{ $activeTab === 'registration' ? 'true' : 'false' }}">
                    <i class="bi bi-person-plus-fill fs-5 text-primary"></i>
                    <span class="fw-semibold">Registration & Onboarding</span>
                </button>

                <button class="btn btn-sm text-start py-3 px-3 border-0 d-flex align-items-center gap-2 nav-link {{ $activeTab === 'verification' ? 'active' : '' }}" 
                        id="tab-verification" 
                        data-bs-toggle="pill" 
                        data-bs-target="#panel-verification" 
                        type="button" 
                        role="tab" 
                        aria-selected="{{ $activeTab === 'verification' ? 'true' : 'false' }}">
                    <i class="bi bi-envelope-check-fill fs-5 text-info"></i>
                    <span class="fw-semibold">Email Verification</span>
                </button>

                <button class="btn btn-sm text-start py-3 px-3 border-0 d-flex align-items-center gap-2 nav-link {{ $activeTab === 'password' ? 'active' : '' }}" 
                        id="tab-password" 
                        data-bs-toggle="pill" 
                        data-bs-target="#panel-password" 
                        type="button" 
                        role="tab" 
                        aria-selected="{{ $activeTab === 'password' ? 'true' : 'false' }}">
                    <i class="bi bi-shield-lock-fill fs-5 text-warning"></i>
                    <span class="fw-semibold">Password Security</span>
                </button>

                <button class="btn btn-sm text-start py-3 px-3 border-0 d-flex align-items-center gap-2 nav-link {{ $activeTab === 'session' ? 'active' : '' }}" 
                        id="tab-session" 
                        data-bs-toggle="pill" 
                        data-bs-target="#panel-session" 
                        type="button" 
                        role="tab" 
                        aria-selected="{{ $activeTab === 'session' ? 'true' : 'false' }}">
                    <i class="bi bi-fingerprint fs-5 text-danger"></i>
                    <span class="fw-semibold">Session & Lockout Control</span>
                </button>

                <button class="btn btn-sm text-start py-3 px-3 border-0 d-flex align-items-center gap-2 nav-link {{ $activeTab === 'twofactor' ? 'active' : '' }}" 
                        id="tab-twofactor" 
                        data-bs-toggle="pill" 
                        data-bs-target="#panel-twofactor" 
                        type="button" 
                        role="tab" 
                        aria-selected="{{ $activeTab === 'twofactor' ? 'true' : 'false' }}">
                    <i class="bi bi-shield-check fs-5 text-success"></i>
                    <span class="fw-semibold">Two-Factor Authentication (2FA)</span>
                </button>
            </div>
            
            <hr class="my-3 text-secondary opacity-25">
            
            <div class="px-2">
                <small class="text-muted d-block" style="font-size: 0.72rem; line-height: 1.4;">
                    <i class="bi bi-info-circle me-1"></i> Changes applied here instantly update Laravel authentication gates and security middleware.
                </small>
            </div>
        </div>
    </div>

    <!-- Right Panels for Section Settings -->
    <div class="col-lg-9">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2 rounded-3 mb-4" role="alert">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="tab-content" id="auth-tabs-content">
            <!-- PANEL 1: REGISTRATION & ONBOARDING -->
            <div class="tab-pane fade {{ $activeTab === 'registration' ? 'show active' : '' }}" id="panel-registration" role="tabpanel">
                <div class="card border-0 shadow-sm p-4" style="border-radius: 16px;">
                    <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
                        <div class="bg-primary-subtle text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-person-plus-fill fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Registration & Onboarding Policies</h5>
                            <p class="text-muted small mb-0">Manage user signup workflows, initial role assignments, and domain restrictions.</p>
                        </div>
                    </div>

                    <form action="{{ route('admin.auth-settings.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="_section" value="registration">

                        <div class="mb-4">
                            <div class="form-check form-switch card p-3 border border-light-subtle bg-light-subtle rounded-3 d-flex align-items-center justify-content-between flex-row">
                                <div class="ms-2">
                                    <label class="form-check-label fw-bold d-block" for="reg_enable">Enable Public User Registration</label>
                                    <small class="text-muted">When disabled, new users cannot sign up; only administrators can create accounts via the User Directory.</small>
                                </div>
                                <input class="form-check-input ms-0 mt-0" type="checkbox" role="switch" id="reg_enable" name="auth_enable_registration" value="1" {{ $settings['auth_enable_registration'] ? 'checked' : '' }} style="width: 2.8em; height: 1.5em; cursor: pointer;">
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Default User Role on Signup</label>
                                <select name="auth_default_user_role" class="form-select">
                                    <option value="user" {{ $settings['auth_default_user_role'] === 'user' ? 'selected' : '' }}>Standard User (User)</option>
                                    <option value="editor" {{ $settings['auth_default_user_role'] === 'editor' ? 'selected' : '' }}>Content Editor</option>
                                    <option value="manager" {{ $settings['auth_default_user_role'] === 'manager' ? 'selected' : '' }}>Team Manager</option>
                                    <option value="admin" {{ $settings['auth_default_user_role'] === 'admin' ? 'selected' : '' }}>Administrator</option>
                                </select>
                                <div class="form-text">Role automatically assigned to newly registered users.</div>
                            </div>

                            <div class="col-md-6 d-flex flex-column justify-content-center">
                                <div class="form-check form-switch pt-3">
                                    <input class="form-check-input" type="checkbox" role="switch" id="auto_approve" name="auth_auto_approve_user" value="1" {{ $settings['auth_auto_approve_user'] ? 'checked' : '' }} style="cursor: pointer;">
                                    <label class="form-check-label fw-semibold ms-1" for="auto_approve">Auto-Approve New Accounts</label>
                                    <div class="form-text">If off, new accounts remain in 'Pending Approval' state until an administrator approves them.</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="req_terms" name="auth_require_terms" value="1" {{ $settings['auth_require_terms'] ? 'checked' : '' }} style="cursor: pointer;">
                                <label class="form-check-label fw-semibold ms-1" for="req_terms">Require Terms of Service & Privacy Policy Acceptance</label>
                                <div class="form-text">Enforces checking the Terms checkbox during user registration.</div>
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Allowed Email Domains (Optional Whitelist)</label>
                                <textarea name="auth_allowed_domains" class="form-control" rows="3" placeholder="e.g. company.com, partner.org">{{ $settings['auth_allowed_domains'] }}</textarea>
                                <div class="form-text">Comma-separated domains allowed to register. Leave blank to allow any valid email domain.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Blocked Email Domains (Blacklist)</label>
                                <textarea name="auth_blocked_domains" class="form-control" rows="3" placeholder="e.g. mailinator.com, tempmail.com">{{ $settings['auth_blocked_domains'] }}</textarea>
                                <div class="form-text">Comma-separated disposable/temporary email domains blocked from signing up.</div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold d-flex align-items-center gap-2">
                                <i class="bi bi-save-fill"></i> Save Registration Policies
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- PANEL 2: EMAIL VERIFICATION -->
            <div class="tab-pane fade {{ $activeTab === 'verification' ? 'show active' : '' }}" id="panel-verification" role="tabpanel">
                <div class="card border-0 shadow-sm p-4" style="border-radius: 16px;">
                    <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
                        <div class="bg-info-subtle text-info rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-envelope-check-fill fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Email Verification Settings</h5>
                            <p class="text-muted small mb-0">Configure verification thresholds, one-time passwords (OTP), and token expiration timelines.</p>
                        </div>
                    </div>

                    <form action="{{ route('admin.auth-settings.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="_section" value="verification">

                        <div class="mb-4">
                            <div class="form-check form-switch card p-3 border border-light-subtle bg-light-subtle rounded-3 d-flex align-items-center justify-content-between flex-row">
                                <div class="ms-2">
                                    <label class="form-check-label fw-bold d-block" for="req_verif">Mandatory Email Verification Before Login</label>
                                    <small class="text-muted">Users must verify their email address via OTP or confirmation link before gaining access to AI services.</small>
                                </div>
                                <input class="form-check-input ms-0 mt-0" type="checkbox" role="switch" id="req_verif" name="auth_require_email_verification" value="1" {{ $settings['auth_require_email_verification'] ? 'checked' : '' }} style="width: 2.8em; height: 1.5em; cursor: pointer;">
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="allow_unverif" name="auth_allow_login_unverified" value="1" {{ $settings['auth_allow_login_unverified'] ? 'checked' : '' }} style="cursor: pointer;">
                                <label class="form-check-label fw-semibold ms-1" for="allow_unverif">Allow Grace-Period Login While Unverified</label>
                                <div class="form-text">Allows users to enter the dashboard with a persistent top banner requesting verification.</div>
                            </div>
                        </div>

                        <h6 class="fw-bold text-uppercase mt-4 mb-3" style="font-size: 0.8rem; letter-spacing: 0.5px;">Supported Verification Methods</h6>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="card p-3 border-0 bg-light rounded-3">
                                    <div class="form-check form-switch mb-1">
                                        <input class="form-check-input" type="checkbox" role="switch" id="verif_link" name="auth_verification_by_link" value="1" {{ $settings['auth_verification_by_link'] ? 'checked' : '' }} style="cursor: pointer;">
                                        <label class="form-check-label fw-bold ms-1" for="verif_link">Clickable Token Link</label>
                                    </div>
                                    <small class="text-muted d-block ps-4">Embeds a secure verification URL button directly into verification emails.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card p-3 border-0 bg-light rounded-3">
                                    <div class="form-check form-switch mb-1">
                                        <input class="form-check-input" type="checkbox" role="switch" id="verif_otp" name="auth_verification_by_otp" value="1" {{ $settings['auth_verification_by_otp'] ? 'checked' : '' }} style="cursor: pointer;">
                                        <label class="form-check-label fw-bold ms-1" for="verif_otp">6-Digit OTP Verification Code</label>
                                    </div>
                                    <small class="text-muted d-block ps-4">Generates large numeric code digits for users on mobile devices or strict email clients.</small>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Link Expiry (Minutes)</label>
                                <input type="number" name="auth_verification_link_expiry" class="form-control" value="{{ $settings['auth_verification_link_expiry'] }}" min="5" max="10080">
                                <div class="form-text">Token lifetime.</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">OTP Expiry (Minutes)</label>
                                <input type="number" name="auth_verification_otp_expiry" class="form-control" value="{{ $settings['auth_verification_otp_expiry'] }}" min="5" max="1440">
                                <div class="form-text">Code lifetime.</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">OTP Code Length</label>
                                <input type="number" name="auth_verification_otp_length" class="form-control" value="{{ $settings['auth_verification_otp_length'] }}" min="4" max="8">
                                <div class="form-text">Number of digits.</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Max Verification Attempts</label>
                                <input type="number" name="auth_verification_max_attempts" class="form-control" value="{{ $settings['auth_verification_max_attempts'] }}" min="1" max="20">
                                <div class="form-text">Before token expires.</div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-info text-white px-4 py-2 fw-semibold d-flex align-items-center gap-2">
                                <i class="bi bi-save-fill"></i> Save Verification Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- PANEL 3: PASSWORD SECURITY -->
            <div class="tab-pane fade {{ $activeTab === 'password' ? 'show active' : '' }}" id="panel-password" role="tabpanel">
                <div class="card border-0 shadow-sm p-4" style="border-radius: 16px;">
                    <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
                        <div class="bg-warning-subtle text-warning rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-shield-lock-fill fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Password & Reset Policies</h5>
                            <p class="text-muted small mb-0">Enforce enterprise password complexity requirements, reset throttling, and recovery methods.</p>
                        </div>
                    </div>

                    <form action="{{ route('admin.auth-settings.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="_section" value="password">

                        <h6 class="fw-bold text-uppercase mb-3" style="font-size: 0.8rem; letter-spacing: 0.5px;">Password Reset Methods & Throttling</h6>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="form-check form-switch card p-3 border-0 bg-light rounded-3 d-flex align-items-center justify-content-between flex-row">
                                    <div>
                                        <label class="form-check-label fw-bold d-block" for="pw_otp">Enable Reset via OTP Code</label>
                                        <small class="text-muted">Sends a numeric code for inline password resets.</small>
                                    </div>
                                    <input class="form-check-input ms-0 mt-0" type="checkbox" role="switch" id="pw_otp" name="auth_password_reset_enable_otp" value="1" {{ $settings['auth_password_reset_enable_otp'] ? 'checked' : '' }} style="cursor: pointer;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch card p-3 border-0 bg-light rounded-3 d-flex align-items-center justify-content-between flex-row">
                                    <div>
                                        <label class="form-check-label fw-bold d-block" for="pw_link">Enable Reset via Token Link</label>
                                        <small class="text-muted">Sends a button link valid for 64-character token resets.</small>
                                    </div>
                                    <input class="form-check-input ms-0 mt-0" type="checkbox" role="switch" id="pw_link" name="auth_password_reset_enable_link" value="1" {{ $settings['auth_password_reset_enable_link'] ? 'checked' : '' }} style="cursor: pointer;">
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Reset Code Expiry (Min)</label>
                                <input type="number" name="auth_password_reset_expiry_minutes" class="form-control" value="{{ $settings['auth_password_reset_expiry_minutes'] }}" min="5" max="1440">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Reset Cooldown (Sec)</label>
                                <input type="number" name="auth_password_reset_cooldown_seconds" class="form-control" value="{{ $settings['auth_password_reset_cooldown_seconds'] }}" min="10" max="3600">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Max Requests / Hour</label>
                                <input type="number" name="auth_password_reset_max_requests_per_hour" class="form-control" value="{{ $settings['auth_password_reset_max_requests_per_hour'] }}" min="1" max="50">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Max Reset Attempts</label>
                                <input type="number" name="auth_password_reset_max_attempts" class="form-control" value="{{ $settings['auth_password_reset_max_attempts'] }}" min="1" max="20">
                            </div>
                        </div>

                        <h6 class="fw-bold text-uppercase mt-4 mb-3" style="font-size: 0.8rem; letter-spacing: 0.5px;">Password Strength & Complexity Requirements</h6>

                        <div class="row g-4 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Minimum Password Length</label>
                                <input type="number" name="auth_password_reset_min_length" class="form-control" value="{{ $settings['auth_password_reset_min_length'] }}" min="6" max="32">
                                <div class="form-text">Minimum characters required.</div>
                            </div>
                            <div class="col-md-8 d-flex align-items-center gap-4 pt-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="req_up" name="auth_password_reset_require_uppercase" value="1" {{ $settings['auth_password_reset_require_uppercase'] ? 'checked' : '' }} style="cursor: pointer;">
                                    <label class="form-check-label fw-semibold ms-1" for="req_up">Require Uppercase</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="req_num" name="auth_password_reset_require_numbers" value="1" {{ $settings['auth_password_reset_require_numbers'] ? 'checked' : '' }} style="cursor: pointer;">
                                    <label class="form-check-label fw-semibold ms-1" for="req_num">Require Numbers</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="req_sym" name="auth_password_reset_require_symbols" value="1" {{ $settings['auth_password_reset_require_symbols'] ? 'checked' : '' }} style="cursor: pointer;">
                                    <label class="form-check-label fw-semibold ms-1" for="req_sym">Require Symbols</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-warning px-4 py-2 fw-semibold d-flex align-items-center gap-2">
                                <i class="bi bi-save-fill"></i> Save Password Security
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- PANEL 4: SESSION & LOCKOUT CONTROL -->
            <div class="tab-pane fade {{ $activeTab === 'session' ? 'show active' : '' }}" id="panel-session" role="tabpanel">
                <div class="card border-0 shadow-sm p-4" style="border-radius: 16px;">
                    <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
                        <div class="bg-danger-subtle text-danger rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-fingerprint fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Session & Brute Force Lockout Controls</h5>
                            <p class="text-muted small mb-0">Protect accounts from credential stuffing and control concurrent user sessions.</p>
                        </div>
                    </div>

                    <form action="{{ route('admin.auth-settings.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="_section" value="session">

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Max Login Attempts Before Lockout</label>
                                <input type="number" name="auth_login_max_attempts" class="form-control" value="{{ $settings['auth_login_max_attempts'] }}" min="2" max="20">
                                <div class="form-text">Number of failed password attempts before account IP is temporarily locked.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Lockout Duration (Minutes)</label>
                                <input type="number" name="auth_login_lockout_minutes" class="form-control" value="{{ $settings['auth_login_lockout_minutes'] }}" min="1" max="1440">
                                <div class="form-text">How long the user is locked out after exceeding max attempts.</div>
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Session Lifetime (Minutes)</label>
                                <input type="number" name="auth_session_lifetime_minutes" class="form-control" value="{{ $settings['auth_session_lifetime_minutes'] }}" min="15" max="525600">
                                <div class="form-text">User session inactivity timeout before requiring re-login.</div>
                            </div>
                            <div class="col-md-6 d-flex flex-column justify-content-center">
                                <div class="form-check form-switch pt-3">
                                    <input class="form-check-input" type="checkbox" role="switch" id="single_sess" name="auth_single_session_per_user" value="1" {{ $settings['auth_single_session_per_user'] ? 'checked' : '' }} style="cursor: pointer;">
                                    <label class="form-check-label fw-semibold ms-1" for="single_sess">Enforce Single Session Per User</label>
                                    <div class="form-text">When active, logging in from a new browser/device terminates any existing sessions on other devices.</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="track_hist" name="auth_track_login_history" value="1" {{ $settings['auth_track_login_history'] ? 'checked' : '' }} style="cursor: pointer;">
                                <label class="form-check-label fw-semibold ms-1" for="track_hist">Record Login Activity & IP Audit Trail</label>
                                <div class="form-text">Logs every successful and failed login with timestamp, IP address, and user agent into Audit Logs.</div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-danger px-4 py-2 fw-semibold d-flex align-items-center gap-2">
                                <i class="bi bi-save-fill"></i> Save Session Controls
                            </button>
                        </div>
                    </form>
                </div>

                <!-- TAB 5: TWO-FACTOR AUTHENTICATION (2FA) -->
                <div class="tab-pane fade {{ $activeTab === 'twofactor' ? 'show active' : '' }}" id="panel-twofactor" role="tabpanel" aria-labelledby="tab-twofactor">
                    <form method="POST" action="{{ route('admin.auth-settings.update') }}">
                        @csrf
                        <input type="hidden" name="_section" value="twofactor">

                        <div class="border-bottom pb-3 mb-4">
                            <h5 class="fw-bold mb-1"><i class="bi bi-shield-check text-success me-2"></i>Two-Factor Authentication (2FA) Policy</h5>
                            <p class="text-muted small mb-0">Configure enterprise multi-factor authentication requirements and trusted device settings.</p>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="2fa_enable" name="auth_2fa_enabled" value="1" {{ $settings['auth_2fa_enabled'] ? 'checked' : '' }} style="cursor: pointer;">
                                <label class="form-check-label fw-semibold ms-1" for="2fa_enable">Enable Two-Factor Authentication System-Wide</label>
                                <div class="form-text">When active, users can activate Email OTP or Authenticator App (TOTP) from their account profile.</div>
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Trusted Device Expiry (Days)</label>
                                <input type="number" name="auth_2fa_remember_days" class="form-control" value="{{ $settings['auth_2fa_remember_days'] ?? 30 }}" min="1" max="365">
                                <div class="form-text">Duration for which "Trust this device" remembers verified browsers without asking for 2FA again.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Enforced Roles (Comma-Separated)</label>
                                <input type="text" name="auth_2fa_enforce_roles" class="form-control" value="{{ $settings['auth_2fa_enforce_roles'] ?? 'admin' }}" placeholder="e.g. admin, manager">
                                <div class="form-text">Users matching these roles MUST complete 2FA during every untrusted login. Auto-enables Email OTP if not already setup.</div>
                            </div>
                        </div>

                        <div class="alert alert-info border-0 bg-info-subtle bg-opacity-25 p-3 rounded-4 mb-4 d-flex align-items-center gap-3">
                            <i class="bi bi-info-circle-fill fs-4 text-info"></i>
                            <div class="small">
                                <strong>User Self-Service Management:</strong><br>
                                Users can manage their personal 2FA setup, scan QR codes for Google Authenticator/Authy, and generate backup emergency recovery codes directly at <a href="{{ route('profile.security') }}" class="fw-bold text-decoration-none">Profile Security & 2FA</a>.
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-success px-4 py-2 fw-semibold d-flex align-items-center gap-2">
                                <i class="bi bi-save-fill"></i> Save 2FA Policies
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
