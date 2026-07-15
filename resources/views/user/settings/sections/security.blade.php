<!-- Security Section -->
<div class="space-y-6 animate-fade-in" x-data="{
        showTwoFactorModal: false,
        showDisable2FaModal: false,
        showPasskeyModal: false,
        twoFactorCode: '',
        disablePasswordConfirm: '',
        verifyAndEnable2FA() {
            if (this.twoFactorCode.length < 6) {
                $dispatch('notify', { message: 'Please enter a valid 6-digit authenticator code.', type: 'warning' });
                return;
            }
            $dispatch('notify', { message: 'Two-Factor Authentication successfully verified and enabled!', type: 'success' });
            this.showTwoFactorModal = false;
        },
        confirmDisable2FA() {
            if (!this.disablePasswordConfirm) {
                $dispatch('notify', { message: 'Current account password is required to disable 2FA.', type: 'danger' });
                return;
            }
            $dispatch('notify', { message: 'Two-Factor Authentication has been disabled.', type: 'warning' });
            this.showDisable2FaModal = false;
        },
        registerPasskey() {
            $dispatch('notify', { message: 'Opening WebAuthn biometric prompt (Touch ID / Windows Hello)...', type: 'accent' });
            setTimeout(() => {
                $dispatch('notify', { message: 'Passkey registered successfully! You can now sign in passwordless.', type: 'success' });
            }, 2000);
        },
        signOutAllDevices() {
            if (confirm('Are you sure you want to sign out all active sessions across all devices and browsers?')) {
                $dispatch('notify', { message: 'All remote sessions revoked immediately.', type: 'warning' });
            }
        }
    }">

    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b" style="border-color: var(--clay-card-border);">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight" style="color: var(--text-primary);">Security Dashboard & Authentication Controls</h2>
            <p class="text-sm mt-1" style="color: var(--text-secondary);">Protect your account with multi-factor authentication, passkeys, session auditing, and strong cryptographic credentials.</p>
        </div>
        <x-settings.badge variant="success" size="lg" icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>'>
            Score: 94/100 Excellent
        </x-settings.badge>
    </div>

    <!-- Security Score Box & Identity Verification Status -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 p-6 rounded-[28px] border flex flex-col justify-between space-y-6"
             style="background: var(--clay-card-bg); border-color: var(--clay-card-border); box-shadow: var(--clay-outer-shadow);">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider" style="color: var(--text-secondary);">Account Armor Rating</span>
                    <h3 class="text-3xl font-extrabold tracking-tight mt-1" style="color: var(--success);">94 / 100</h3>
                </div>
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl font-black bg-emerald-500/20 text-emerald-500 shadow-inner">
                    A+
                </div>
            </div>

            <x-settings.progress-bar :percentage="94" color="success" label="Security Checkpoints Passed" valueLabel="6 of 7 Verified" height="h-3" />

            <div class="space-y-2 pt-3 border-t text-xs" style="border-color: var(--clay-card-border);">
                <div class="flex items-center gap-2 text-emerald-500"><svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> <span>Email Address Verified</span></div>
                <div class="flex items-center gap-2 text-emerald-500"><svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> <span>Strong Password Enforced</span></div>
                <div class="flex items-center gap-2 text-emerald-500"><svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> <span>No Leaked API Credentials</span></div>
                <div class="flex items-center gap-2 text-[var(--accent)] font-semibold"><svg class="w-4 h-4 shrink-0 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> <span>Tip: Add Hardware Passkey</span></div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <!-- Two Factor Authentication Card -->
            <x-settings.card title="Two-Factor Authentication (2FA & TOTP)" description="Add a mandatory secondary verification step using Google Authenticator, Authy, or 1Password." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/></svg>'>
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            @if($hasTwoFactorEnabled ?? false)
                                <span class="text-sm font-bold text-emerald-500">Enabled & Protected</span>
                                <x-settings.status-dot status="success" />
                            @else
                                <span class="text-sm font-bold text-[var(--warning)]">Currently Disabled</span>
                                <x-settings.status-dot status="warning" />
                            @endif
                        </div>
                        <p class="text-xs sm:text-sm leading-relaxed" style="color: var(--text-secondary);">
                            When 2FA is active, you will be prompted for a secure 6-digit Time-Based One-Time Password (TOTP) during sign in.
                        </p>
                    </div>

                    <div class="shrink-0">
                        @if($hasTwoFactorEnabled ?? false)
                            <button type="button"
                                    @click="showDisable2FaModal = true"
                                    class="px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-transform hover:scale-105 border bg-red-500/10 text-red-500 border-red-500/20">
                                Disable 2FA Protection
                            </button>
                        @else
                            <button type="button"
                                    @click="showTwoFactorModal = true"
                                    class="px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-transform hover:scale-105 shadow"
                                    style="background: var(--accent); color: white;">
                                Enable Authenticator App
                            </button>
                        @endif
                    </div>
                </div>
            </x-settings.card>

            <!-- Passkeys Card -->
            <x-settings.card title="Hardware Passkeys (Touch ID / Windows Hello)" description="Register WebAuthn biometric passkeys for instant passwordless enterprise login." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>'>
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <h6 class="text-sm font-bold tracking-tight" style="color: var(--text-primary);">Zero-Trust Biometric Authentication</h6>
                        <p class="text-xs" style="color: var(--text-secondary);">Passkeys eliminate phishing vectors using public-key cryptography stored securely on your device hardware.</p>
                    </div>
                    <button type="button"
                            @click="registerPasskey()"
                            class="px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-transform hover:scale-105 border shrink-0 flex items-center gap-2"
                            style="background: var(--clay-card-bg); color: var(--accent); border-color: var(--clay-card-border);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Register New Passkey
                    </button>
                </div>
            </x-settings.card>
        </div>
    </div>

    <!-- Password Management Card -->
    <x-settings.card title="Update Password & Cryptographic Armor" description="Change your primary account password. Ensure at least 12 characters with symbols and numbers." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>'>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="space-y-2">
                <label class="block text-sm font-semibold tracking-tight" style="color: var(--text-primary);">Current Password</label>
                <input type="password"
                       name="current_password"
                       placeholder="••••••••••••"
                       class="w-full rounded-2xl py-3 px-4 text-sm transition-all focus:outline-none focus:ring-2 focus:ring-[var(--accent)]"
                       style="background: var(--clay-input-bg); color: var(--text-primary); border: 1px solid var(--clay-card-border); box-shadow: var(--clay-input-shadow);">
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-semibold tracking-tight" style="color: var(--text-primary);">New Password</label>
                <input type="password"
                       name="password"
                       placeholder="••••••••••••"
                       class="w-full rounded-2xl py-3 px-4 text-sm transition-all focus:outline-none focus:ring-2 focus:ring-[var(--accent)]"
                       style="background: var(--clay-input-bg); color: var(--text-primary); border: 1px solid var(--clay-card-border); box-shadow: var(--clay-input-shadow);">
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-semibold tracking-tight" style="color: var(--text-primary);">Confirm New Password</label>
                <input type="password"
                       name="password_confirmation"
                       placeholder="••••••••••••"
                       class="w-full rounded-2xl py-3 px-4 text-sm transition-all focus:outline-none focus:ring-2 focus:ring-[var(--accent)]"
                       style="background: var(--clay-input-bg); color: var(--text-primary); border: 1px solid var(--clay-card-border); box-shadow: var(--clay-input-shadow);">
            </div>
        </div>
    </x-settings.card>

    <!-- Active Sessions & Trusted Devices Card -->
    <x-settings.card title="Active Sessions & Trusted Devices" description="Audit currently logged in browsers, IP locations, and revoke suspicious sessions instantly." icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>'>
        <div class="space-y-4">
            <!-- Current Session -->
            <div class="p-4 sm:p-5 rounded-2xl border flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 transition-all"
                 style="background: var(--clay-card-bg); border-color: var(--accent); box-shadow: var(--clay-outer-shadow);">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0 bg-blue-500/20 text-blue-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h6 class="text-sm font-bold tracking-tight" style="color: var(--text-primary);">Windows 11 • Google Chrome 126.0 (Current Device)</h6>
                            <x-settings.badge variant="success" size="sm">Active Now</x-settings.badge>
                        </div>
                        <p class="text-xs font-mono mt-0.5 opacity-75" style="color: var(--text-secondary);">IP Address: 192.168.1.104 • Location: New York, USA</p>
                    </div>
                </div>
            </div>

            <!-- Remote Device 1 -->
            <div class="p-4 sm:p-5 rounded-2xl border flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 transition-all"
                 style="background: var(--clay-input-bg); border-color: var(--clay-card-border);">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0 bg-purple-500/20 text-purple-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h6 class="text-sm font-bold tracking-tight" style="color: var(--text-primary);">Apple iPhone 15 Pro • Safari iOS 18.0</h6>
                        <p class="text-xs font-mono mt-0.5 opacity-75" style="color: var(--text-secondary);">IP Address: 172.56.21.89 • Last Active: Yesterday at 4:15 PM</p>
                    </div>
                </div>
                <button type="button" @click="$dispatch('notify', { message: 'Session revoked.', type: 'warning' })" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold border opacity-80 hover:opacity-100 hover:bg-red-500 hover:text-white" style="background: var(--clay-card-bg); color: var(--danger); border-color: var(--clay-card-border);">
                    Revoke Session
                </button>
            </div>

            <div class="pt-4 border-t flex justify-end" style="border-color: var(--clay-card-border);">
                <button type="button" @click="signOutAllDevices()" class="px-4 py-2.5 rounded-xl text-xs font-semibold transition-transform hover:scale-105 border flex items-center gap-2" style="background: rgba(239, 68, 68, 0.12); border-color: rgba(239, 68, 68, 0.3); color: var(--danger);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Sign Out All Other Devices
                </button>
            </div>
        </div>
    </x-settings.card>

    <!-- Modal: Enable 2FA Setup -->
    <div x-show="showTwoFactorModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-md bg-black/60" style="display: none;">
        <div class="max-w-lg w-full rounded-[28px] p-6 sm:p-8 border shadow-2xl space-y-6" style="background: var(--clay-card-bg); border-color: var(--clay-card-border);">
            <div class="flex items-center justify-between pb-3 border-b" style="border-color: var(--clay-card-border);">
                <h3 class="text-lg font-bold tracking-tight" style="color: var(--text-primary);">Configure Authenticator (2FA)</h3>
                <button type="button" @click="showTwoFactorModal = false" class="opacity-60 hover:opacity-100"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>

            <div class="space-y-4 text-center sm:text-left">
                <p class="text-xs sm:text-sm leading-relaxed" style="color: var(--text-secondary);">
                    Scan the QR code below with your authenticator application (Google Authenticator, Authy, or 1Password). Or manually enter the secret setup key:
                </p>

                <!-- Mock QR Code & Key Box -->
                <div class="flex flex-col sm:flex-row items-center gap-6 p-4 rounded-2xl border" style="background: var(--clay-input-bg); border-color: var(--clay-card-border);">
                    <div class="w-32 h-32 bg-white p-2 rounded-xl shrink-0 flex items-center justify-center shadow-md">
                        <!-- SVG QR Code pattern -->
                        <svg class="w-full h-full text-black" viewBox="0 0 100 100" fill="currentColor">
                            <rect x="10" y="10" width="30" height="30" /><rect x="60" y="10" width="30" height="30" /><rect x="10" y="60" width="30" height="30" />
                            <rect x="18" y="18" width="14" height="14" fill="white" /><rect x="68" y="18" width="14" height="14" fill="white" /><rect x="18" y="68" width="14" height="14" fill="white" />
                            <rect x="46" y="46" width="10" height="10" /><rect x="60" y="60" width="20" height="20" /><rect x="45" y="20" width="8" height="8" />
                        </svg>
                    </div>
                    <div class="space-y-2 text-left w-full min-w-0">
                        <span class="text-xs font-bold uppercase tracking-wider block" style="color: var(--text-secondary);">Manual Setup Key</span>
                        <div class="flex items-center gap-2">
                            <code class="px-3 py-2 rounded-xl text-xs sm:text-sm font-mono font-bold block bg-black/20 dark:bg-white/10 select-all truncate w-full" style="color: var(--accent);">
                                {{ $twoFactorSecret ?? 'JBSWY3DPEHPK3PXP' }}
                            </code>
                        </div>
                        <span class="text-[11px] opacity-75 block">Type: TOTP • Algorithm: SHA1</span>
                    </div>
                </div>

                <div class="space-y-2 pt-2">
                    <label class="block text-sm font-bold tracking-tight text-left" style="color: var(--text-primary);">Enter 6-Digit Verification Code</label>
                    <input type="text"
                           x-model="twoFactorCode"
                           maxlength="6"
                           placeholder="123456"
                           class="w-full rounded-2xl py-3 px-4 text-center text-lg font-mono tracking-[0.4em] font-bold transition-all focus:outline-none focus:ring-2 focus:ring-[var(--accent)]"
                           style="background: var(--clay-input-bg); color: var(--text-primary); border: 1px solid var(--clay-card-border);">
                </div>
            </div>

            <div class="pt-4 border-t flex items-center justify-end gap-3" style="border-color: var(--clay-card-border);">
                <button type="button" @click="showTwoFactorModal = false" class="px-4 py-2.5 rounded-xl text-sm font-semibold border" style="background: var(--clay-input-bg); color: var(--text-primary);">Cancel</button>
                <button type="button" @click="verifyAndEnable2FA()" class="px-5 py-2.5 rounded-xl text-sm font-bold shadow transition-transform hover:scale-105" style="background: var(--accent); color: white;">Verify & Enable 2FA</button>
            </div>
        </div>
    </div>

    <!-- Modal: Disable 2FA Confirmation -->
    <div x-show="showDisable2FaModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-md bg-black/60" style="display: none;">
        <div class="max-w-md w-full rounded-[28px] p-6 sm:p-8 border shadow-2xl space-y-6" style="background: var(--clay-card-bg); border-color: var(--clay-card-border);">
            <div class="flex items-center gap-3 text-[var(--danger)]">
                <svg class="w-8 h-8 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <h3 class="text-lg font-bold tracking-tight" style="color: var(--text-primary);">Disable Two-Factor Armor?</h3>
            </div>
            <p class="text-xs sm:text-sm leading-relaxed" style="color: var(--text-secondary);">
                Disabling two-factor authentication significantly lowers your account security. Please confirm your current password to proceed.
            </p>
            <div class="space-y-2">
                <label class="block text-xs font-bold uppercase" style="color: var(--text-primary);">Current Account Password</label>
                <input type="password" x-model="disablePasswordConfirm" placeholder="••••••••••••" class="w-full rounded-2xl py-3 px-4 text-sm transition-all focus:ring-2 focus:ring-red-500" style="background: var(--clay-input-bg); color: var(--text-primary); border: 1px solid var(--clay-card-border);">
            </div>
            <div class="pt-4 border-t flex justify-end gap-3" style="border-color: var(--clay-card-border);">
                <button type="button" @click="showDisable2FaModal = false" class="px-4 py-2.5 rounded-xl text-sm font-semibold border" style="background: var(--clay-input-bg); color: var(--text-primary);">Cancel</button>
                <button type="button" @click="confirmDisable2FA()" class="px-5 py-2.5 rounded-xl text-sm font-bold bg-red-600 text-white hover:bg-red-700 transition-all">Revoke 2FA Protection</button>
            </div>
        </div>
    </div>
</div>
