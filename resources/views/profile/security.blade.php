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
        body { background-color: #f8fafc; font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; color: #1e293b; }
        .ambient-bubble { position: absolute; border-radius: 50%; filter: blur(90px); z-index: -1; opacity: 0.35; pointer-events: none; }
        .bubble-1 { top: 5%; left: 8%; width: 350px; height: 350px; background: rgba(59, 130, 246, 0.4); }
        .bubble-2 { bottom: 10%; right: 10%; width: 400px; height: 400px; background: rgba(16, 185, 129, 0.35); }
        .security-card { background: #ffffff; border-radius: 20px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04); border: 1px solid rgba(226, 232, 240, 0.8); padding: 32px; margin-bottom: 28px; }
        .recovery-box { font-family: monospace; font-size: 1.1rem; letter-spacing: 2px; font-weight: 700; background: #f1f5f9; border: 1px dashed #cbd5e1; border-radius: 12px; padding: 18px; display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; text-align: center; color: #334155; }
        .qr-container { background: #ffffff; border: 2px solid #e2e8f0; padding: 16px; border-radius: 16px; display: inline-block; margin: 15px 0; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    </style>
</head>
<body x-data="{ showDisableModal: false, showTotpSetup: {{ $qrCodeUrl ? 'true' : 'false' }} }">
    <div class="ambient-bubble bubble-1"></div>
    <div class="ambient-bubble bubble-2"></div>

    <div class="container py-5" style="max-width: 860px;">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                    <i class="bi bi-shield-lock-fill fs-3"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-1">Security & Two-Factor Authentication</h3>
                    <p class="text-muted small mb-0">Manage account safety, authenticator apps, and emergency backup codes.</p>
                </div>
            </div>
            <div>
                <a href="{{ route('chat') }}" class="btn btn-outline-secondary px-3 py-2 fw-semibold d-flex align-items-center gap-2">
                    <i class="bi bi-arrow-left"></i> Back to Chat
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 p-3 d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-4 p-3 d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-4 p-3 d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                <div>{{ $errors->first() }}</div>
            </div>
        @endif

        <!-- SECTION 1: STATUS SUMMARY -->
        <div class="security-card d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div style="width: 48px; height: 48px;" class="rounded-3 d-flex align-items-center justify-content-center {{ $user->hasTwoFactorEnabled() ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }}">
                    <i class="bi {{ $user->hasTwoFactorEnabled() ? 'bi-check-shield-fill' : 'bi-shield-exclamation' }} fs-3"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1">Two-Factor Authentication Status</h5>
                    <p class="text-muted small mb-0">
                        @if($user->hasTwoFactorEnabled())
                            Active via <strong class="text-success">{{ $user->two_factor_type === 'totp' ? 'Authenticator App (TOTP)' : 'Email OTP Verification' }}</strong> since {{ $user->two_factor_confirmed_at?->format('M d, Y') ?? 'recently' }}.
                        @else
                            Currently <strong class="text-warning">Disabled</strong>. We strongly recommend activating 2FA to protect your account.
                        @endif
                    </p>
                </div>
            </div>

            <div>
                @if($user->hasTwoFactorEnabled())
                    <button @click="showDisableModal = true" class="btn btn-outline-danger fw-semibold px-4 py-2">
                        <i class="bi bi-shield-slash-fill me-1"></i> Disable 2FA
                    </button>
                @else
                    <div class="d-flex gap-2">
                        <form method="POST" action="{{ route('profile.security.update') }}">
                            @csrf
                            <input type="hidden" name="action" value="enable_email">
                            <button type="submit" class="btn btn-primary fw-semibold px-3 py-2">
                                <i class="bi bi-envelope-check-fill me-1"></i> Enable Email OTP
                            </button>
                        </form>
                        <a href="{{ route('profile.security', ['setup' => 'totp']) }}" class="btn btn-success fw-semibold px-3 py-2">
                            <i class="bi bi-qr-code-scan me-1"></i> Setup Authenticator App
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- SECTION 2: TOTP SETUP PANEL (WHEN SETUP = TOTP) -->
        @if($qrCodeUrl)
            <div class="security-card border-success border-2 bg-success-subtle bg-opacity-10">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge bg-success px-3 py-2 fs-6">Step-by-Step Authenticator Setup</span>
                </div>
                <h5 class="fw-bold">Scan QR Code with your Authenticator App</h5>
                <p class="text-muted small">Open your 2FA app (Google Authenticator, Authy, 1Password, or Microsoft Authenticator) and scan the QR code below. Or enter the manual secret key if your camera is unavailable.</p>

                <div class="text-center my-3">
                    <div class="qr-container">
                        <img src="{{ $qrCodeUrl }}" alt="2FA QR Code" width="220" height="220">
                    </div>
                    <div class="mt-2">
                        <small class="text-muted d-block">Manual Secret Key:</small>
                        <code class="fs-5 fw-bold text-dark px-3 py-1 bg-white border rounded-3 d-inline-block mt-1">{{ implode(' ', str_split($secretKey, 4)) }}</code>
                    </div>
                </div>

                <hr class="my-4">

                <form method="POST" action="{{ route('profile.security.totp-confirm') }}" class="mt-3">
                    @csrf
                    <label class="form-label fw-bold">Enter the 6-digit code from your app to confirm and activate:</label>
                    <div class="d-flex gap-3 align-items-center flex-wrap">
                        <input type="text" name="totp_code" class="form-control text-center fw-bold fs-4" style="max-width: 200px; letter-spacing: 6px;" placeholder="123456" maxlength="6" required autofocus autocomplete="one-time-code">
                        <button type="submit" class="btn btn-success px-4 py-2 fw-bold fs-6">
                            <i class="bi bi-check-lg me-1"></i> Confirm & Activate TOTP
                        </button>
                        <a href="{{ route('profile.security') }}" class="btn btn-outline-secondary px-3 py-2">Cancel</a>
                    </div>
                </form>
            </div>
        @endif

        <!-- SECTION 3: RECOVERY CODES -->
        @if($user->hasTwoFactorEnabled())
            <div class="security-card">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold mb-1"><i class="bi bi-key-fill text-warning me-2"></i>Emergency Recovery Codes</h5>
                        <p class="text-muted small mb-0">Store these backup codes in a safe place (like a password manager). Each code can only be used once to log in if you lose your phone or email access.</p>
                    </div>
                    <form method="POST" action="{{ route('profile.security.update') }}" onsubmit="return confirm('Regenerating will invalidate all existing recovery codes. Continue?');">
                        @csrf
                        <input type="hidden" name="action" value="regenerate_codes">
                        <button type="submit" class="btn btn-sm btn-outline-secondary px-3 py-2 fw-semibold">
                            <i class="bi bi-arrow-clockwise me-1"></i> Regenerate Codes
                        </button>
                    </form>
                </div>

                @if(!empty($recoveryCodes))
                    <div class="recovery-box mt-3">
                        @foreach($recoveryCodes as $code)
                            <div><code>{{ $code }}</code></div>
                        @endforeach
                    </div>
                    <div class="text-end mt-2">
                        <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Showing {{ count($recoveryCodes) }} unused emergency codes remaining.</small>
                    </div>
                @else
                    <div class="alert alert-warning small mb-0 mt-3">
                        You have no remaining recovery codes. Please click "Regenerate Codes" above to generate a new set immediately.
                    </div>
                @endif
            </div>
        @endif

        <!-- DISABLE 2FA CONFIRMATION MODAL -->
        <div x-show="showDisableModal" x-cloak class="modal fade show" style="display: block; background: rgba(0,0,0,0.5); z-index: 1050;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4 p-3">
                    <div class="modal-header border-bottom-0">
                        <h5 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Disable Two-Factor Authentication?</h5>
                        <button type="button" class="btn-close" @click="showDisableModal = false"></button>
                    </div>
                    <form method="POST" action="{{ route('profile.security.update') }}">
                        @csrf
                        <input type="hidden" name="action" value="disable_2fa">
                        <div class="modal-body py-2">
                            <p class="text-muted small">Disabling 2FA will remove extra login protection from your account and invalidate your current emergency recovery codes. To confirm, please enter your account password:</p>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Current Password</label>
                                <input type="password" name="current_password" class="form-control" required placeholder="Enter your password">
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary px-3 py-2" @click="showDisableModal = false">Cancel</button>
                            <button type="submit" class="btn btn-danger fw-bold px-4 py-2">Turn Off 2FA</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
