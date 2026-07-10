@extends('layouts.admin')

@section('title', 'Email Configuration & SMTP Providers')

@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">Email Configuration</li>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-12">
        <ul class="nav nav-pills bg-light p-2 rounded-4 mb-4 flex-wrap gap-2 border shadow-sm" id="smtp-tabs" role="tablist">
            @foreach($configurations as $config)
                @php
                    $iconClass = match($config->provider_slug) {
                        'zoho' => 'bi-envelope-at-fill',
                        'gmail' => 'bi-google',
                        'outlook' => 'bi-microsoft',
                        'ses' => 'bi-cloud-arrow-up-fill',
                        'mailgun' => 'bi-send-fill',
                        'sendgrid' => 'bi-lightning-charge-fill',
                        'brevo' => 'bi-mailbox2',
                        default => 'bi-hdd-network-fill',
                    };
                @endphp
                <li class="nav-item" role="presentation">
                    <button class="nav-link btn-sm py-2 px-3 d-flex align-items-center gap-2 fw-semibold {{ $loop->first ? 'active' : '' }}" 
                            id="tab-{{ $config->provider_slug }}" 
                            data-bs-toggle="pill" 
                            data-bs-target="#panel-{{ $config->provider_slug }}" 
                            type="button" 
                            role="tab" 
                            aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                        <i class="bi {{ $iconClass }}"></i>
                        <span>{{ str_replace(' / Microsoft 365', '', $config->provider_name) }}</span>
                        @if($config->is_default)
                            <span class="badge bg-primary px-1 py-1" title="Primary Mail Provider" style="font-size: 0.55rem;">DEF</span>
                        @endif
                        <span class="badge {{ $config->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}" style="font-size: 0.65rem;">
                            {{ $config->is_active ? 'On' : 'Off' }}
                        </span>
                    </button>
                </li>
            @endforeach
        </ul>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4 d-flex align-items-center gap-2" role="alert" style="border-radius: 12px;">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4 d-flex align-items-start gap-2" role="alert" style="border-radius: 12px;">
                <i class="bi bi-exclamation-triangle-fill fs-5 mt-1"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="tab-content" id="smtp-panels">
            @foreach($configurations as $config)
                @php
                    $iconClass = match($config->provider_slug) {
                        'zoho' => 'bi-envelope-at-fill',
                        'gmail' => 'bi-google',
                        'outlook' => 'bi-microsoft',
                        'ses' => 'bi-cloud-arrow-up-fill',
                        'mailgun' => 'bi-send-fill',
                        'sendgrid' => 'bi-lightning-charge-fill',
                        'brevo' => 'bi-mailbox2',
                        default => 'bi-hdd-network-fill',
                    };
                @endphp
                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
                     id="panel-{{ $config->provider_slug }}" 
                     role="tabpanel" 
                     aria-labelledby="tab-{{ $config->provider_slug }}">
                    
                    <div class="card border-0 p-4 shadow-sm mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 border-bottom pb-3">
                            <div>
                                <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                                    <i class="bi {{ $iconClass }} text-primary"></i>
                                    {{ $config->provider_name }} Configuration
                                </h4>
                                <p class="text-muted mb-0" style="font-size:0.85rem;">Configure SMTP server connection credentials, encryption protocols, and sender identities.</p>
                            </div>
                            
                            <div class="d-flex align-items-center gap-2">
                                @if($config->is_default)
                                    <span class="badge bg-primary px-3 py-2" style="font-size: 0.8rem;">
                                        <i class="bi bi-star-fill me-1"></i> Default Mailer
                                    </span>
                                @endif
                                <span class="badge {{ $config->is_active ? 'bg-success text-white' : 'bg-secondary text-white' }} px-3 py-2" style="font-size: 0.8rem;">
                                    {{ $config->is_active ? 'Active & Enabled' : 'Disabled' }}
                                </span>
                            </div>
                        </div>

                        <!-- Connection Status Bar -->
                        <div class="p-3 mb-4 rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-2 {{ $config->connection_status === 'connected' ? 'bg-success-subtle text-success-emphasis border border-success-subtle' : ($config->connection_status === 'failed' ? 'bg-danger-subtle text-danger-emphasis border border-danger-subtle' : 'bg-light text-muted border') }}">
                            <div class="d-flex align-items-center gap-2">
                                @if($config->connection_status === 'connected')
                                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                    <div>
                                        <div class="fw-bold" style="font-size: 0.9rem;">Connection Status: Connected & Verified</div>
                                        <div style="font-size: 0.78rem;">Last verified on {{ $config->last_tested_at ? $config->last_tested_at->format('M d, Y H:i:s') : 'N/A' }}</div>
                                    </div>
                                @elseif($config->connection_status === 'failed')
                                    <i class="bi bi-x-circle-fill text-danger fs-5"></i>
                                    <div>
                                        <div class="fw-bold" style="font-size: 0.9rem;">Connection Status: Verification Failed</div>
                                        <div style="font-size: 0.78rem;">Error: {{ $config->last_error ?? 'Unknown connection error' }}</div>
                                    </div>
                                @else
                                    <i class="bi bi-question-circle-fill text-secondary fs-5"></i>
                                    <div>
                                        <div class="fw-bold" style="font-size: 0.9rem;">Connection Status: Untested</div>
                                        <div style="font-size: 0.78rem;">Click 'Test SMTP Connection' to verify server credentials.</div>
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex gap-2">
                                <form action="{{ route('admin.email-config.test', $config) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1">
                                        <i class="bi bi-activity"></i> Test SMTP Connection
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-outline-success d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#sendTestEmailModal-{{ $config->id }}">
                                    <i class="bi bi-send"></i> Send Test Email
                                </button>
                            </div>
                        </div>

                        <!-- Configuration Form -->
                        <form action="{{ route('admin.email-config.update', $config) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Section 1: General Toggles -->
                            <h6 class="fw-bold text-muted text-uppercase mb-3" style="font-size:0.75rem; letter-spacing: 0.5px;">1. General Settings</h6>
                            <div class="row g-3 mb-4 p-3 bg-light rounded-3 border">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Provider Name</label>
                                    <input type="text" name="provider_name" class="form-control" value="{{ $config->provider_name }}" required>
                                </div>
                                <div class="col-md-4 d-flex align-items-center pt-3">
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" role="switch" id="is_active_{{ $config->id }}" name="is_active" value="1" {{ $config->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold ms-1" for="is_active_{{ $config->id }}">Enable Provider</label>
                                        <div class="form-text" style="font-size: 0.75rem;">Allow emails to be sent through this driver.</div>
                                    </div>
                                </div>
                                <div class="col-md-4 d-flex align-items-center pt-3">
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" role="switch" id="is_default_{{ $config->id }}" name="is_default" value="1" {{ $config->is_default ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold ms-1 text-primary" for="is_default_{{ $config->id }}">Default Mail Provider</label>
                                        <div class="form-text" style="font-size: 0.75rem;">Set as primary system mailer for all notifications.</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: SMTP Settings -->
                            <h6 class="fw-bold text-muted text-uppercase mb-3" style="font-size:0.75rem; letter-spacing: 0.5px;">2. SMTP Server Credentials</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">SMTP Host</label>
                                    <input type="text" name="host" class="form-control" value="{{ $config->host }}" placeholder="e.g. smtp.gmail.com or smtp.zoho.com">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">SMTP Port</label>
                                    <input type="number" name="port" class="form-control" value="{{ $config->port }}" required min="1" max="65535">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Encryption (SSL/TLS)</label>
                                    <select name="encryption" class="form-select">
                                        <option value="tls" {{ $config->encryption === 'tls' ? 'selected' : '' }}>TLS (Port 587)</option>
                                        <option value="ssl" {{ $config->encryption === 'ssl' ? 'selected' : '' }}>SSL (Port 465)</option>
                                        <option value="null" {{ $config->encryption === 'null' || empty($config->encryption) ? 'selected' : '' }}>None (Unencrypted)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Username / API Identifier</label>
                                    <input type="text" name="username" class="form-control" value="{{ $config->username }}" placeholder="e.g. your-email@domain.com or API Key">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Password / App Secret (Encrypted)</label>
                                    <input type="password" name="password" class="form-control" placeholder="{{ !empty($config->password) ? '•••••••••••• (Leave blank to keep existing)' : 'Enter SMTP password or app secret' }}">
                                    <div class="form-text" style="font-size: 0.75rem;">Stored using AES-256-CBC encryption in your database.</div>
                                </div>
                            </div>

                            <!-- Section 3: Sender Identity -->
                            <h6 class="fw-bold text-muted text-uppercase mb-3" style="font-size:0.75rem; letter-spacing: 0.5px;">3. Sender Identity & Headers</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">From Name</label>
                                    <input type="text" name="from_name" class="form-control" value="{{ $config->from_name }}" required placeholder="e.g. {{ \App\Models\SystemSetting::get('general_chatbot_name', 'App') }} Support">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">From Email</label>
                                    <input type="email" name="from_email" class="form-control" value="{{ $config->from_email }}" placeholder="e.g. support@domain.com">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Reply-To Email</label>
                                    <input type="email" name="reply_to" class="form-control" value="{{ $config->reply_to }}" placeholder="e.g. support@xrootai.com">
                                </div>
                            </div>

                            <hr class="my-4 border-light">

                            <div class="d-flex justify-content-between align-items-center">
                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#resetModal-{{ $config->id }}">
                                    <i class="bi bi-arrow-counterclockwise"></i> Reset Configuration
                                </button>
                                
                                <button type="submit" class="btn btn-primary px-4 d-flex align-items-center gap-2">
                                    <i class="bi bi-check2-circle"></i> Save Configuration
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Send Test Email Modal -->
                    <div class="modal fade" id="sendTestEmailModal-{{ $config->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                                        <i class="bi bi-send-fill"></i> Send Test Email — {{ $config->provider_name }}
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('admin.email-config.send-test', $config) }}" method="POST">
                                    @csrf
                                    <div class="modal-body p-4">
                                        <p class="text-muted mb-3" style="font-size: 0.9rem;">
                                            Send a sample verification message to confirm that your SMTP server connection, encryption, and sender identities work end-to-end.
                                        </p>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Recipient Email Address</label>
                                            <input type="email" name="test_email" class="form-control" required placeholder="admin@domain.com" value="{{ auth()->user()->email ?? '' }}">
                                            <div class="form-text">We will dispatch an HTML test message immediately using the current saved credentials.</div>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary btn-sm px-3 d-flex align-items-center gap-1">
                                            <i class="bi bi-send"></i> Dispatch Test Email
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Reset Confirmation Modal -->
                    <div class="modal fade" id="resetModal-{{ $config->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                                        <i class="bi bi-exclamation-triangle-fill"></i> Confirm Reset
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <p class="mb-0">Are you sure you want to reset all SMTP configuration settings for <strong>{{ $config->provider_name }}</strong>? This will clear the stored username, password, and disable the provider.</p>
                                </div>
                                <div class="modal-footer bg-light">
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                    <form action="{{ route('admin.email-config.reset', $config) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm px-3">Confirm Reset</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
