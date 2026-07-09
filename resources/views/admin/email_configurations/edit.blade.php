@extends('layouts.admin')

@section('title', 'Edit Email Configuration: ' . $emailConfiguration->provider_name)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.email-config.index') }}">Email Configuration</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $emailConfiguration->provider_name }}</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 p-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                <h4 class="fw-bold mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-envelope-at-fill text-primary"></i>
                    Edit {{ $emailConfiguration->provider_name }}
                </h4>
                <a href="{{ route('admin.email-config.index') }}" class="btn btn-outline-secondary btn-sm">
                    ← Back to Providers
                </a>
            </div>

            <form action="{{ route('admin.email-config.update', $emailConfiguration) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3 mb-4 p-3 bg-light rounded-3 border">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Provider Name</label>
                        <input type="text" name="provider_name" class="form-control" value="{{ $emailConfiguration->provider_name }}" required>
                    </div>
                    <div class="col-md-3 d-flex align-items-center pt-3">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ $emailConfiguration->is_active ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold ms-1" for="is_active">Enable Provider</label>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-center pt-3">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_default" name="is_default" value="1" {{ $emailConfiguration->is_default ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold ms-1 text-primary" for="is_default">Default Mailer</label>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold text-muted text-uppercase mb-3" style="font-size:0.75rem;">SMTP Server Credentials</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">SMTP Host</label>
                        <input type="text" name="host" class="form-control" value="{{ $emailConfiguration->host }}" placeholder="smtp.provider.com">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">SMTP Port</label>
                        <input type="number" name="port" class="form-control" value="{{ $emailConfiguration->port }}" required min="1" max="65535">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Encryption</label>
                        <select name="encryption" class="form-select">
                            <option value="tls" {{ $emailConfiguration->encryption === 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ $emailConfiguration->encryption === 'ssl' ? 'selected' : '' }}>SSL</option>
                            <option value="null" {{ $emailConfiguration->encryption === 'null' || empty($emailConfiguration->encryption) ? 'selected' : '' }}>None</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Username / API Key</label>
                        <input type="text" name="username" class="form-control" value="{{ $emailConfiguration->username }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Password / Secret (Encrypted)</label>
                        <input type="password" name="password" class="form-control" placeholder="{{ !empty($emailConfiguration->password) ? '•••••••••••• (Leave blank to keep current)' : 'Enter password' }}">
                    </div>
                </div>

                <h6 class="fw-bold text-muted text-uppercase mb-3" style="font-size:0.75rem;">Sender Identity</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">From Name</label>
                        <input type="text" name="from_name" class="form-control" value="{{ $emailConfiguration->from_name }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">From Email</label>
                        <input type="email" name="from_email" class="form-control" value="{{ $emailConfiguration->from_email }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Reply-To Email</label>
                        <input type="email" name="reply_to" class="form-control" value="{{ $emailConfiguration->reply_to }}">
                    </div>
                </div>

                <hr class="my-4 border-light">

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.email-config.index') }}" class="btn btn-secondary px-3">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 d-flex align-items-center gap-2">
                        <i class="bi bi-check2-circle"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
