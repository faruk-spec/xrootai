<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Security & 2FA - {{ \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI') }}</title>
    @if(\App\Models\SystemSetting::get('general_site_icon'))
        <link rel="icon" href="{{ \App\Models\SystemSetting::get('general_site_icon') }}">
    @endif
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/claymorphism.css') }}">
    <script src="{{ asset('js/alpine.min.js') }}" defer></script>
    <style>
        body { background-color: #f8fafc; font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; color: #1e293b; min-height: 100vh; }
        .ambient-bubble { position: absolute; border-radius: 50%; filter: blur(90px); z-index: -1; opacity: 0.35; pointer-events: none; }
        .bubble-1 { top: 5%; left: 8%; width: 350px; height: 350px; background: rgba(59, 130, 246, 0.4); }
        .bubble-2 { bottom: 10%; right: 10%; width: 400px; height: 400px; background: rgba(16, 185, 129, 0.35); }
        .security-card { background: #ffffff; border-radius: 24px; box-shadow: 0 12px 35px rgba(0, 0, 0, 0.05); border: 1px solid rgba(226, 232, 240, 0.8); padding: 36px; margin-bottom: 28px; transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .security-card:hover { box-shadow: 0 16px 45px rgba(0, 0, 0, 0.08); }
        .recovery-box { font-family: monospace; font-size: 1.15rem; letter-spacing: 3px; font-weight: 700; background: #f1f5f9; border: 2px dashed #cbd5e1; border-radius: 16px; padding: 22px; display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; text-align: center; color: #334155; }
        .qr-container { background: #ffffff; border: 3px solid #e2e8f0; padding: 20px; border-radius: 20px; display: inline-block; margin: 15px 0; box-shadow: 0 8px 24px rgba(0,0,0,0.06); }
        .status-badge { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 50px; font-weight: 600; font-size: 0.9rem; }
        .status-badge.active { background: rgba(16, 185, 129, 0.15); color: #059669; border: 1px solid rgba(16, 185, 129, 0.3); }
        .status-badge.inactive { background: rgba(245, 158, 11, 0.15); color: #d97706; border: 1px solid rgba(245, 158, 11, 0.3); }
        .btn-modern { border-radius: 12px; font-weight: 600; padding: 12px 24px; transition: all 0.2s ease; display: inline-flex; align-items: center; justify-content: center; gap: 8px; }
        .btn-modern:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.12); }
    </style>
</head>
<body>
    <div class="ambient-bubble bubble-1"></div>
    <div class="ambient-bubble bubble-2"></div>

    <div class="container py-5" style="max-width: 880px;">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-5 pb-4 border-bottom">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 text-primary rounded-4 d-flex align-items-center justify-content-center shadow-sm" style="width: 60px; height: 60px;">
                    <i class="bi bi-shield-lock-fill fs-2"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1 text-dark">Security & Two-Factor Authentication</h2>
                    <p class="text-muted small mb-0">Protect your account with extra login security, authenticator apps, and emergency backup codes.</p>
                </div>
            </div>
            <div>
                <a href="{{ route('chat') }}" class="btn btn-outline-secondary btn-modern px-4 py-2">
                    <i class="bi bi-arrow-left"></i> Back to Chat
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 p-3 d-flex align-items-center gap-3 mb-4" role="alert" style="background-color: #dcfce7; color: #15803d; border-left: 5px solid #16a34a !important;">
                <i class="bi bi-check-circle-fill fs-4 text-success"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-4 p-3 d-flex align-items-center gap-3 mb-4" role="alert" style="background-color: #fee2e2; color: #b91c1c; border-left: 5px solid #dc2626 !important;">
                <i class="bi bi-exclamation-triangle-fill fs-4 text-danger"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-4 p-3 d-flex align-items-center gap-3 mb-4" role="alert" style="background-color: #fee2e2; color: #b91c1c; border-left: 5px solid #dc2626 !important;">
                <i class="bi bi-exclamation-triangle-fill fs-4 text-danger"></i>
                <div>{{ $errors->first() }}</div>
            </div>
        @endif

        <!-- SECTION 1: STATUS SUMMARY -->
        <div class="security-card">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-4">
                <div class="d-flex align-items-center gap-4">
                    <div style="width: 64px; height: 64px;" class="rounded-4 d-flex align-items-center justify-content-center shadow-sm {{ $user->hasTwoFactorEnabled() ? 'bg-success bg-opacity-10 text-success' : 'bg-warning bg-opacity-10 text-warning' }}">
                        <i class="bi {{ $user->hasTwoFactorEnabled() ? 'bi-shield-check' : 'bi-shield-exclamation' }} fs-1"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <h4 class="fw-bold mb-0">Two-Factor Authentication</h4>
                            @if($user->hasTwoFactorEnabled())
                                <span class="status-badge active"><i class="bi bi-check-circle-fill"></i> Active</span>
                            @else
                                <span class="status-badge inactive"><i class="bi bi-exclamation-circle-fill"></i> Disabled</span>
                            @endif
                        </div>
                        <p class="text-muted mb-0">
                            @if($user->hasTwoFactorEnabled())
                                Protected via <strong class="text-dark">{{ $user->two_factor_type === 'totp' ? 'Authenticator App (TOTP)' : 'Email OTP Verification' }}</strong> since {{ $user->two_factor_confirmed_at?->format('M d, Y') ?? 'recently' }}.
                            @else
                                Add an extra layer of security to your account. You will need a 6-digit verification code each time you log in.
                            @endif
                        </p>
                    </div>
                </div>

                <div>
                    @if($user->hasTwoFactorEnabled())
                        <button type="button" data-bs-toggle="modal" data-bs-target="#disable2FAModal" class="btn btn-outline-danger btn-modern px-4">
                            <i class="bi bi-shield-slash-fill"></i> Disable 2FA
                        </button>
                    @else
                        <div class="d-flex flex-wrap gap-3">
                            <form method="POST" action="{{ route('profile.security.update') }}">
                                @csrf
                                <input type="hidden" name="action" value="enable_email">
                                <button type="submit" class="btn btn-primary btn-modern px-4">
                                    <i class="bi bi-envelope-check-fill"></i> Enable Email OTP
                                </button>
                            </form>
                            <a href="{{ route('profile.security', ['setup' => 'totp']) }}" class="btn btn-success btn-modern px-4">
                                <i class="bi bi-qr-code-scan"></i> Setup Authenticator App
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- SECTION 2: TOTP SETUP PANEL (WHEN SETUP = TOTP) -->
        @if($qrCodeUrl)
            <div class="security-card border-success border-2" style="background: linear-gradient(180deg, #f0fdf4 0%, #ffffff 100%);">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <span class="badge bg-success px-3 py-2 rounded-pill fs-6"><i class="bi bi-qr-code me-1"></i> Step-by-Step Authenticator Setup</span>
                    <a href="{{ route('profile.security') }}" class="text-muted small text-decoration-none"><i class="bi bi-x-lg me-1"></i>Close Setup</a>
                </div>
                <h4 class="fw-bold mb-2">Scan QR Code with your Authenticator App</h4>
                <p class="text-muted">Open your 2FA app (Google Authenticator, Authy, 1Password, or Microsoft Authenticator) and scan the QR code below. Or copy the manual secret key if your camera is unavailable.</p>

                <div class="text-center my-4">
                    <div class="qr-container">
                        <img src="{{ $qrCodeUrl }}" alt="2FA QR Code" width="220" height="220">
                    </div>
                    <div class="mt-3">
                        <small class="text-muted d-block fw-semibold mb-1">Manual Secret Key:</small>
                        <code class="fs-4 fw-bold text-dark px-4 py-2 bg-white border rounded-4 d-inline-block shadow-sm">{{ implode(' ', str_split($secretKey, 4)) }}</code>
                    </div>
                </div>

                <hr class="my-4">

                <form method="POST" action="{{ route('profile.security.totp-confirm') }}" class="mt-3">
                    @csrf
                    <label class="form-label fw-bold fs-6 mb-3">Enter the 6-digit verification code from your app to confirm activation:</label>
                    <div class="d-flex gap-3 align-items-center flex-wrap">
                        <input type="text" name="totp_code" class="form-control text-center fw-bold fs-3 shadow-sm rounded-3" style="max-width: 220px; letter-spacing: 8px;" placeholder="123456" maxlength="6" required autofocus autocomplete="one-time-code">
                        <button type="submit" class="btn btn-success btn-modern px-4 py-3 fs-6">
                            <i class="bi bi-check-circle-fill"></i> Confirm & Activate TOTP
                        </button>
                        <a href="{{ route('profile.security') }}" class="btn btn-outline-secondary btn-modern px-4 py-3">Cancel</a>
                    </div>
                </form>
            </div>
        @endif

        <!-- SECTION 3: RECOVERY CODES -->
        @if($user->hasTwoFactorEnabled())
            <div class="security-card">
                <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
                    <div>
                        <h4 class="fw-bold mb-1"><i class="bi bi-key-fill text-warning me-2"></i>Emergency Recovery Codes</h4>
                        <p class="text-muted small mb-0">Store these backup codes in a safe place (like a password manager). Each code can only be used once to log in if you lose your phone or email access.</p>
                    </div>
                    <form method="POST" action="{{ route('profile.security.update') }}" onsubmit="return confirm('Regenerating will invalidate all existing recovery codes immediately. Continue?');">
                        @csrf
                        <input type="hidden" name="action" value="regenerate_codes">
                        <button type="submit" class="btn btn-outline-secondary btn-modern px-3 py-2 small">
                            <i class="bi bi-arrow-clockwise"></i> Regenerate Codes
                        </button>
                    </form>
                </div>

                @if(!empty($recoveryCodes))
                    <div class="recovery-box my-3">
                        @foreach($recoveryCodes as $code)
                            <div class="p-2 bg-white rounded-3 border shadow-sm"><code>{{ $code }}</code></div>
                        @endforeach
                    </div>
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
                        <small class="text-muted"><i class="bi bi-shield-check text-success me-1"></i> Showing {{ count($recoveryCodes) }} emergency codes available.</small>
                    </div>
                @else
                    <div class="alert alert-warning rounded-4 p-3 d-flex align-items-center gap-3 mb-0 mt-3 border-0 shadow-sm" style="background-color: #fef3c7; color: #92400e; border-left: 5px solid #f59e0b !important;">
                        <i class="bi bi-exclamation-circle-fill fs-4 text-warning"></i>
                        <div>You currently have no emergency recovery codes generated. Click "Regenerate Codes" above to create a fresh set immediately.</div>
                    </div>
                @endif
            </div>
        @endif

        <!-- DISABLE 2FA CONFIRMATION MODAL -->
        <div class="modal fade" id="disable2FAModal" tabindex="-1" aria-labelledby="disable2FAModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header bg-danger bg-opacity-10 px-4 py-3 border-bottom-0">
                        <h5 class="modal-title fw-bold text-danger d-flex align-items-center gap-2" id="disable2FAModalLabel">
                            <i class="bi bi-exclamation-triangle-fill fs-4"></i> Disable Two-Factor Authentication?
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('profile.security.update') }}">
                        @csrf
                        <input type="hidden" name="action" value="disable_2fa">
                        <div class="modal-body p-4">
                            <p class="text-muted">Turning off Two-Factor Authentication will remove extra login protection from your account and invalidate all of your current emergency recovery codes.</p>
                            @if($user->password)
                                <div class="mt-4">
                                    <label class="form-label fw-bold text-dark small">Current Password (Optional if currently verified via OAuth/2FA)</label>
                                    <input type="password" name="current_password" class="form-control form-control-lg rounded-3" placeholder="Enter password to confirm">
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer bg-light px-4 py-3 border-top-0 d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-outline-secondary rounded-3 px-4 py-2 fw-semibold" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger rounded-3 px-4 py-2 fw-bold shadow-sm">Turn Off 2FA</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
