<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - XrootAI</title>
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
            <!-- Brand Logo -->
            <div style="display: flex; justify-content: center; margin-bottom: 20px;">
                <div class="app-brand">
                    <div class="app-brand-icon">X</div>
                    <span>XrootAI</span>
                </div>
            </div>

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

                <!-- Submit Button -->
                <button type="submit" class="clay-btn clay-btn-primary" style="width: 100%; padding: 14px; margin-top: 10px;">
                    Create Account
                </button>
            </form>

            <div class="text-center mt-4" style="color: var(--text-muted); font-size: 0.9rem;">
                Already have an account? 
                <a href="{{ route('login') }}" class="auth-link">Log In</a>
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
