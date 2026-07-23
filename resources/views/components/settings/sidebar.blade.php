<!-- Settings Sidebar Component -->
<aside class="settings-sidebar" x-data="{}">
    <!-- User Identity Card -->
    <div class="d-flex align-items-center gap-3 p-3 mb-4 rounded-4" style="background: var(--clay-input-bg); border: 1px solid var(--clay-card-border); box-shadow: var(--clay-input-shadow);">
        <div style="width: 44px; height: 44px; border-radius: 50%; background: #4a88ff; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.2rem; flex-shrink: 0;">
            {{ substr(Auth::user()->name, 0, 1) }}
        </div>
        <div style="overflow: hidden;">
            <div class="fw-bold text-truncate" style="font-size: 0.95rem; color: var(--text-primary);">{{ Auth::user()->name }}</div>
            <div class="small text-truncate" style="color: var(--text-muted);">{{ Auth::user()->email }}</div>
        </div>
    </div>

    <div class="text-uppercase fw-bold mb-2 px-2" style="font-size: 0.72rem; letter-spacing: 0.06em; color: var(--text-muted);">Navigation</div>

    <div class="settings-nav-group">
        <button type="button" class="nav-tab-btn" :class="{ 'active': activeTab === 'general' }" @click="activeTab = 'general'">
            <i class="bi bi-sliders"></i>
            <span>General &amp; AI</span>
        </button>
        <button type="button" class="nav-tab-btn" :class="{ 'active': activeTab === 'theme' }" @click="activeTab = 'theme'">
            <i class="bi bi-palette"></i>
            <span>Appearance &amp; Theme</span>
        </button>
        <button type="button" class="nav-tab-btn" :class="{ 'active': activeTab === 'keys' }" @click="activeTab = 'keys'">
            <i class="bi bi-key"></i>
            <span>API Keys (BYOK)</span>
        </button>
        <button type="button" class="nav-tab-btn" :class="{ 'active': activeTab === 'security' }" @click="activeTab = 'security'">
            <i class="bi bi-shield-lock"></i>
            <span>Security &amp; 2FA</span>
        </button>
    </div>

    <hr class="border-secondary border-opacity-25 my-3">

    <!-- Sign Out -->
    <form action="{{ route('logout') }}" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to sign out?');">
        @csrf
        <button type="submit" class="nav-tab-btn text-danger">
            <i class="bi bi-box-arrow-right"></i>
            <span>Sign Out</span>
        </button>
    </form>
</aside>
