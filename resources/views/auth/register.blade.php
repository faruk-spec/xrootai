<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - {{ \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI') }}</title>
    @if(\App\Models\SystemSetting::get('general_site_icon'))
        <link rel="icon" href="{{ \App\Models\SystemSetting::get('general_site_icon') }}">
    @endif
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/claymorphism.css') }}">
    <script src="{{ asset('js/alpine.min.js') }}" defer></script>
    <style>
        /* Decorative ambient glow bubbles */
        .ambient-bubble {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: -1;
            opacity: 0.5;
        }
        .bubble-1 {
            top: 10%;
            left: 10%;
            width: 250px;
            height: 250px;
            background: rgba(126, 182, 255, 0.4);
        }
        .bubble-2 {
            bottom: 10%;
            right: 10%;
            width: 300px;
            height: 300px;
            background: rgba(168, 224, 99, 0.3);
        }
    </style>
</head>
<body x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark-mode': darkMode }">
    <div class="ambient-bubble bubble-1"></div>
    <div class="ambient-bubble bubble-2"></div>

    <div class="auth-container">
        <div class="clay-card auth-card" style="max-width: 480px;">
            <!-- Back to Home / Chat Button -->
            <div style="display: flex; justify-content: flex-start; margin-bottom: 12px;">
                <a href="{{ route('chat') }}" class="clay-btn clay-btn-secondary" style="display: inline-flex; align-items: center; gap: 6px; text-decoration: none; padding: 6px 12px; border-radius: 10px; font-size: 0.85rem; color: var(--text-color);">
                    <i class="bi bi-arrow-left"></i> Back to Home
                </a>
            </div>

            <!-- Brand Logo -->
            <div style="display: flex; justify-content: center; margin-bottom: 20px;">
                <a href="{{ route('chat') }}" class="app-brand" style="text-decoration: none; display: flex; align-items: center; gap: 8px;">
                    @php 
                        $lightLogo = \App\Models\SystemSetting::get('general_logo_light') ?: \App\Models\SystemSetting::get('general_chatbot_logo'); 
                        $darkLogo = \App\Models\SystemSetting::get('general_logo_dark') ?: \App\Models\SystemSetting::get('general_chatbot_logo'); 
                    @endphp
                    @if($lightLogo || $darkLogo)
                        <img :src="darkMode ? '{{ $darkLogo ?: $lightLogo }}' : '{{ $lightLogo ?: $darkLogo }}'" alt="Logo" style="width:36px; height:36px; border-radius:10px; object-fit:contain;">
                    @else
                        <div class="app-brand-icon">{{ substr(\App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI'), 0, 1) }}</div>
                    @endif
                    <span style="color: var(--text-color); font-weight: 700;">{{ \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI') }}</span>
                </a>
            </div>

            @if(session('error'))
                <div style="background: #fee2e2; color: #b91c1c; border: 1px solid #f87171; padding: 12px 14px; border-radius: 12px; margin-bottom: 16px; font-size: 0.88rem; text-align: left; line-height: 1.4; display: flex; align-items: flex-start; gap: 8px;">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif
            @if(session('status'))
                <div style="background: #dcfce7; color: #15803d; border: 1px solid #86efac; padding: 12px 14px; border-radius: 12px; margin-bottom: 16px; font-size: 0.88rem; text-align: left; line-height: 1.4;">
                    {{ session('status') }}
                </div>
            @endif

            <h1 class="auth-title">Create Account</h1>
            <p class="auth-subtitle">Join us to start chatting with multiple AI models</p>

            <form method="POST" action="{{ route('register.store') }}">
                @csrf

                <!-- Name -->
                <div class="clay-input-group">
                    <label for="name" class="clay-input-label">Full Name</label>
                    <input id="name" class="clay-inset clay-input" type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="John Doe">
                    @error('name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email Address -->
                <div class="clay-input-group">
                    <label for="email" class="clay-input-label">Email Address</label>
                    <input id="email" class="clay-inset clay-input" type="email" name="email" value="{{ old('email') }}" required placeholder="name@example.com">
                    @error('email')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="clay-input-group">
                    <label for="password" class="clay-input-label">Password</label>
                    <input id="password" class="clay-inset clay-input" type="password" name="password" required placeholder="Min. 8 characters">
                    @error('password')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="clay-input-group">
                    <label for="password_confirmation" class="clay-input-label">Confirm Password</label>
                    <input id="password_confirmation" class="clay-inset clay-input" type="password" name="password_confirmation" required placeholder="Re-enter password">
                    @error('password_confirmation')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Terms & Conditions Checkbox -->
                <div style="margin-top: 16px; margin-bottom: 20px; text-align: left; display: flex; align-items: flex-start; gap: 10px;">
                    <input type="checkbox" name="terms_and_conditions" id="terms_and_conditions" value="1" required style="margin-top: 3px; width: 18px; height: 18px; accent-color: #3b82f6; cursor: pointer;">
                    <label for="terms_and_conditions" style="font-size: 0.86rem; color: var(--text-color); cursor: pointer; line-height: 1.4;">
                        I have read and agree to the <a href="{{ route('terms') }}" target="_blank" style="color: #3b82f6; font-weight: 600; text-decoration: underline;">Terms of Service</a> & <a href="{{ route('privacy') }}" target="_blank" style="color: #3b82f6; font-weight: 600; text-decoration: underline;">Privacy Policy</a>.
                    </label>
                </div>
                @error('terms_and_conditions')
                    <div class="text-danger" style="margin-bottom: 12px; font-size: 0.85rem;">{{ $message }}</div>
                @enderror

                @if(isset($registrationDisabled) && $registrationDisabled)
                    <div style="background: #fee2e2; color: #b91c1c; border: 1px solid #f87171; padding: 12px 14px; border-radius: 12px; margin-bottom: 16px; font-size: 0.88rem; text-align: left;">
                        ⚠️ New user registration is currently disabled by the system administrator.
                    </div>
                @else
                    <!-- Submit Button -->
                    <button type="submit" class="clay-btn clay-btn-primary" style="width: 100%; padding: 14px; margin-top: 10px; margin-bottom: 16px;">
                        Create Account
                    </button>
                @endif
            </form>

            <div style="display: flex; align-items: center; text-align: center; margin: 16px 0; color: var(--text-muted); font-size: 0.85rem;">
                <hr style="flex: 1; border: none; border-top: 1px solid rgba(0,0,0,0.15); margin-right: 8px;">
                <span>or sign up with</span>
                <hr style="flex: 1; border: none; border-top: 1px solid rgba(0,0,0,0.15); margin-left: 8px;">
            </div>

            <!-- Social Login Buttons -->
            <div style="display: flex; gap: 12px; margin-bottom: 16px;">
                <a href="{{ route('auth.redirect', 'google') }}" class="clay-btn clay-btn-secondary" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none; padding: 10px; font-size: 0.9rem;">
                    <svg style="width:18px; height:18px;" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v3.9h6.6c-.28 1.5-1.11 2.76-2.39 3.62v3h3.86c2.26-2.09 3.67-5.17 3.67-8.45z"/>
                        <path fill="#34A853" d="M12 24c3.24 0 5.97-1.08 7.96-2.91l-3.86-3c-1.08.72-2.45 1.16-4.1 1.16-3.14 0-5.8-2.11-6.75-4.96H1.31v3.1c1.97 3.92 6.01 6.61 10.69 6.61z"/>
                        <path fill="#FBBC05" d="M5.25 14.29c-.25-.72-.39-1.49-.39-2.29s.14-1.57.39-2.29V6.6H1.31C.47 8.27 0 10.08 0 12s.47 3.73 1.31 5.4l3.94-3.11z"/>
                        <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.31 0 3.27 2.69 1.31 6.6l3.94 3.11c.95-2.85 3.61-4.96 6.75-4.96z"/>
                    </svg>
                    <span>Google</span>
                </a>
                <a href="{{ route('auth.redirect', 'github') }}" class="clay-btn clay-btn-secondary" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none; padding: 10px; font-size: 0.9rem;">
                    <svg style="width:18px; height:18px;" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M12 0C5.37 0 0 5.37 0 12c0 5.3 3.438 9.8 8.205 11.385.6.11.82-.26.82-.577v-2.234c-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22v3.293c0 .319.22.694.825.576C20.565 21.795 24 17.3 24 12c0-6.63-5.37-12-12-12z"/>
                    </svg>
                    <span>GitHub</span>
                </a>
            </div>

            <div class="text-center mt-4" style="color: var(--text-muted); font-size: 0.9rem;">
                Already have an account? 
                <a href="{{ route('login') }}" class="auth-link">Log In</a>
            </div>

            <div class="text-center mt-3" style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5;">
                By signing up, you agree to our <a href="{{ route('terms') }}" style="color: #4a88ff; text-decoration: none; font-weight: 500;">Terms of Service</a> & <a href="{{ route('privacy') }}" style="color: #4a88ff; text-decoration: none; font-weight: 500;">Privacy Policy</a>.
            </div>

            <!-- Floating Theme Toggle -->
            <div style="position: absolute; top: 15px; right: 15px;">
                <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" class="clay-btn clay-btn-secondary" style="border-radius: 50%; width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;">
                    <i x-show="!darkMode" class="bi bi-moon-stars-fill"></i>
                    <i x-show="darkMode" class="bi bi-sun-fill"></i>
                </button>
            </div>
        </div>
    </div>
</body>
</html>
