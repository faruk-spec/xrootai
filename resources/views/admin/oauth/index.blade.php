@extends('layouts.admin')

@section('title', 'OAuth Configurations')

@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">OAuth configurations</li>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-12">
        <ul class="nav nav-pills bg-light p-2 rounded-4 mb-4 flex-wrap gap-2 border shadow-sm" id="oauth-tabs" role="tablist">
            @foreach($providers as $prov)
                <li class="nav-item" role="presentation">
                    <button class="nav-link btn-sm py-2 px-3 d-flex align-items-center gap-2 fw-semibold {{ $loop->first ? 'active' : '' }}" 
                            id="tab-{{ $prov->provider_slug }}" 
                            data-bs-toggle="pill" 
                            data-bs-target="#panel-{{ $prov->provider_slug }}" 
                            type="button" 
                            role="tab" 
                            aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                        <i class="bi bi-{{ $prov->provider_slug === 'twitter' ? 'twitter-x' : ($prov->provider_slug === 'custom' ? 'shield-lock' : $prov->provider_slug) }}"></i>
                        <span>{{ str_replace('(Twitter)', '', $prov->provider_name) }}</span>
                        <span class="badge {{ $prov->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}" style="font-size: 0.65rem;">
                            {{ $prov->is_active ? 'On' : 'Off' }}
                        </span>
                    </button>
                </li>
            @endforeach
        </ul>

        <div id="connectionAlert" class="alert d-none shadow-sm mb-4" style="border-radius:12px;"></div>

        <div class="tab-content" id="oauth-panels">
            @foreach($providers as $prov)
                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
                     id="panel-{{ $prov->provider_slug }}" 
                     role="tabpanel" 
                     aria-labelledby="tab-{{ $prov->provider_slug }}">
                    
                    <div class="card border-0 p-4 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                            <div>
                                <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                                    <i class="bi bi-{{ $prov->provider_slug === 'twitter' ? 'twitter-x' : ($prov->provider_slug === 'custom' ? 'shield-lock' : $prov->provider_slug) }} text-primary"></i>
                                    {{ $prov->provider_name }} Configuration
                                </h4>
                                <p class="text-muted mb-0" style="font-size:0.85rem;">Setup third-party SSO login credentials and authentication scope parameters.</p>
                            </div>
                            
                            <span class="badge {{ $prov->is_active ? 'bg-success text-white' : 'bg-secondary text-white' }} px-3 py-2" style="font-size: 0.8rem;">
                                {{ $prov->is_active ? 'Active & Enabled' : 'Disabled' }}
                            </span>
                        </div>

                        <!-- 1. Collapsible Setup Guide -->
                        <div class="accordion mb-4" id="guideAccordion-{{ $prov->provider_slug }}">
                            <div class="accordion-item border border-light-subtle rounded-3">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed py-2 px-3 fw-semibold text-primary bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#guide-{{ $prov->provider_slug }}">
                                        <i class="bi bi-info-circle-fill me-2"></i> {{ $prov->provider_name }} Developer Setup Guide
                                    </button>
                                </h2>
                                <div id="guide-{{ $prov->provider_slug }}" class="accordion-collapse collapse" data-bs-parent="#guideAccordion-{{ $prov->provider_slug }}">
                                    <div class="accordion-body bg-light-subtle text-dark" style="font-size:0.85rem;">
                                        <ol class="mb-0">
                                            <li class="mb-1">Navigate to the <strong>{{ $prov->provider_name }} Developer Console</strong>.</li>
                                            <li class="mb-1">Create a new <strong>OAuth 2.0 Web Application</strong> project.</li>
                                            <li class="mb-1">Set the <strong>Authorized Redirect URI / Callback URL</strong> to: <code class="bg-body-secondary px-2 py-0.5 rounded">{{ url("/auth/{$prov->provider_slug}/callback") }}</code>.</li>
                                            <li class="mb-1">Obtain the generated <strong>Client ID</strong> and <strong>Client Secret</strong>.</li>
                                            <li class="mb-1">Paste them in the configuration fields below and toggle the Provider state to <strong>Enabled</strong>.</li>
                                            <li>Test connection and click <strong>Save Settings</strong> to deploy live authentication callbacks.</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Configuration Form -->
                        <form action="{{ route('admin.oauth.update', $prov->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="provider_slug" value="{{ $prov->provider_slug }}">

                            <!-- Toggle Activation -->
                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="active-{{ $prov->provider_slug }}" {{ $prov->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="active-{{ $prov->provider_slug }}">Enable {{ $prov->provider_name }} Authentication</label>
                                </div>
                                <div class="form-text">Enable dynamic user logins and registrations through this OAuth provider callback.</div>
                            </div>

                            <hr class="my-4 border-light-subtle">

                            <!-- Client Credentials -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Client ID</label>
                                    <input type="text" name="client_id" class="form-control" value="{{ old('client_id', $prov->client_id) }}" required placeholder="e.g. 109283726-abcde.apps.googleusercontent.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Client Secret</label>
                                    <input type="password" name="client_secret" class="form-control" placeholder="••••••••••••••••••••••••••••••••">
                                    <div class="form-text">Leave blank to retain current encrypted key.</div>
                                </div>
                            </div>

                            <!-- Callback URL (Read-Only) -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Authorized Redirect Callback URL</label>
                                <div class="input-group">
                                    <input type="text" class="form-control bg-light" value="{{ url("/auth/{$prov->provider_slug}/callback") }}" readonly id="url-{{ $prov->provider_slug }}">
                                    <button class="btn btn-outline-secondary" type="button" onclick="copyCallbackUrl('{{ $prov->provider_slug }}', this)">
                                        <i class="bi bi-clipboard"></i> Copy URL
                                    </button>
                                </div>
                                <div class="form-text">Add this callback URL inside the developer console redirect mapping.</div>
                            </div>

                            <!-- Custom Endpoint Overrides (Only shown for custom OAuth provider) -->
                            @if($prov->provider_slug === 'custom')
                                <div class="row g-3 mb-3 border-top pt-3 border-light-subtle mt-4">
                                    <h6 class="fw-bold mb-1">Generic Custom Endpoint Overrides</h6>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Authorization Endpoint</label>
                                        <input type="url" name="auth_url" class="form-control" value="{{ old('auth_url', $prov->auth_url) }}" placeholder="https://oauth.provider.com/authorize">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Token Exchange Endpoint</label>
                                        <input type="url" name="token_url" class="form-control" value="{{ old('token_url', $prov->token_url) }}" placeholder="https://oauth.provider.com/token">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">User Info Profile Endpoint</label>
                                        <input type="url" name="user_info_url" class="form-control" value="{{ old('user_info_url', $prov->user_info_url) }}" placeholder="https://oauth.provider.com/user">
                                    </div>
                                </div>
                            @endif

                            <!-- OAuth Scopes selection -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Scopes (Comma Separated)</label>
                                <input type="text" name="scopes" class="form-control" 
                                       value="{{ old('scopes', is_array($prov->scopes) ? implode(', ', $prov->scopes) : '') }}" 
                                       placeholder="e.g. openid, profile, email">
                                <div class="form-text">List OAuth parameters requested during login consent. Separated by comma.</div>
                            </div>

                            <!-- Additional JSON Params -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Additional Parameters (JSON String)</label>
                                <textarea name="additional_params" class="form-control" rows="3" style="font-family:monospace;" placeholder='{"prompt": "consent", "access_type": "offline"}'>{{ old('additional_params', !empty($prov->additional_params) ? json_encode($prov->additional_params, JSON_PRETTY_PRINT) : '') }}</textarea>
                                <div class="form-text">Must be a valid raw JSON object. Empty for default configs.</div>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex gap-2 flex-wrap border-top pt-3 border-light-subtle">
                                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                                    <i class="bi bi-save2-fill"></i> Save Settings
                                </button>
                                
                                <button type="button" onclick="testOAuthConnection('{{ $prov->id }}', this)" class="btn btn-outline-info d-flex align-items-center gap-2">
                                    <i class="bi bi-broadcast"></i> Test Config
                                </button>
                                
                                <button type="button" onclick="resetOAuthConnection('{{ $prov->id }}', '{{ $prov->provider_name }}')" class="btn btn-outline-danger d-flex align-items-center gap-2 ms-auto">
                                    <i class="bi bi-trash"></i> Reset Provider
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Reset Config Hidden Form -->
<form id="resetForm" method="POST" action="" class="d-none">
    @csrf
</form>
@endsection

@section('scripts')
<script>
    function copyCallbackUrl(slug, btn) {
        const input = document.getElementById(`url-${slug}`);
        input.select();
        input.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(input.value);
        
        const originalHtml = btn.innerHTML;
        btn.innerHTML = `<i class="bi bi-check2"></i> Copied!`;
        btn.className = "btn btn-success";
        
        setTimeout(() => {
            btn.innerHTML = originalHtml;
            btn.className = "btn btn-outline-secondary";
        }, 2000);
    }

    function testOAuthConnection(providerId, btn) {
        const alertBox = document.getElementById('connectionAlert');
        alertBox.className = 'alert d-none';
        
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status"></span> Testing...`;

        fetch(`{{ url('admin/oauth') }}/${providerId}/test`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            
            alertBox.classList.remove('d-none');
            if (data.success) {
                alertBox.classList.add('alert-success');
                alertBox.innerHTML = `<i class="bi bi-check-circle-fill me-2"></i> ${data.message}`;
            } else {
                alertBox.classList.add('alert-danger');
                alertBox.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2"></i> ${data.message}`;
            }
            window.scrollTo({ top: 0, behavior: 'smooth' });
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            alertBox.classList.remove('d-none');
            alertBox.classList.add('alert-danger');
            alertBox.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2"></i> Network error occurred while testing OAuth parameters connection.`;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    function resetOAuthConnection(providerId, name) {
        showAdminConfirmModal(`Are you sure you want to completely reset and disable credentials for ${name}? This action is irreversible.`, function() {
            const form = document.getElementById('resetForm');
            form.action = `{{ url('admin/oauth') }}/${providerId}/reset`;
            form.submit();
        });
    }
</script>
@endsection
