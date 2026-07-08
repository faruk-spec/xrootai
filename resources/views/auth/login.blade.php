<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - XrootAI</title>
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
        <div class="clay-card auth-card">
            <!-- Brand Logo -->
            <div style="display: flex; justify-content: center; margin-bottom: 20px;">
                <div class="app-brand">
                    <div class="app-brand-icon">X</div>
                    <span>XrootAI</span>
                </div>
            </div>

            <h1 class="auth-title">Welcome Back</h1>
            <p class="auth-subtitle">Log in to continue your AI conversation</p>

            <form method="POST" action="{{ route('login.store') }}">
                @csrf

                <!-- Email Address -->
                <div class="clay-input-group">
                    <label for="email" class="clay-input-label">Email Address</label>
                    <input id="email" class="clay-inset clay-input" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="name@example.com">
                    @error('email')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="clay-input-group">
                    <label for="password" class="clay-input-label">Password</label>
                    <input id="password" class="clay-inset clay-input" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                    @error('password')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 24px; padding-left: 8px;">
                    <input id="remember_me" type="checkbox" name="remember" style="width: 16px; height: 16px; accent-color: #4a88ff;">
                    <label for="remember_me" class="clay-input-label" style="padding-left: 0; cursor: pointer; user-select: none;">Remember me</label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="clay-btn clay-btn-primary" style="width: 100%; padding: 14px;">
                    Log In
                </button>
            </form>

            <div class="text-center mt-4" style="color: var(--text-muted); font-size: 0.9rem;">
                Don't have an account? 
                <a href="{{ route('register') }}" class="auth-link">Sign Up</a>
            </div>

            <!-- Floating Theme Toggle -->
            <div style="position: absolute; top: 15px; right: 15px;">
                <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" class="clay-btn clay-btn-secondary" style="border-radius: 50%; width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;">
                    <span x-show="!darkMode">🌙</span>
                    <span x-show="darkMode">☀️</span>
                </button>
            </div>
        </div>
    </div>
</body>
</html>
