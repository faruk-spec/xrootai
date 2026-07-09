@extends('layouts.admin')

@section('title', 'Configure AI Provider')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.providers.index') }}">AI Providers</a></li>
    <li class="breadcrumb-item active" aria-current="page">Configure</li>
@endsection

@section('content')
<div class="card border-0 p-4" style="max-width: 600px;">
    <h4 class="fw-bold mb-4">Configure AI Provider: {{ $provider->name }}</h4>

    <form action="{{ route('admin.providers.update', $provider->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label fw-medium">Display Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $provider->name) }}" required>
            <div class="form-text">The label shown in provider menus.</div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-medium">Base Endpoint URL</label>
            <input type="url" name="base_url" class="form-control" value="{{ old('base_url', $provider->base_url) }}">
            <div class="form-text">API root endpoint (leave blank to use provider default SDK configs).</div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-medium">API Credential Key</label>
            <input type="password" name="api_key" class="form-control" placeholder="••••••••••••••••">
            <div class="form-text">Leave blank to keep current key. Credentials are securely encrypted at rest.</div>
        </div>

        <div class="mb-4">
            <div class="form-check form-switch">
                <input type="checkbox" name="is_active" class="form-check-input" id="isActive" {{ old('is_active', $provider->is_active) ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="isActive">Enable AI Provider</label>
            </div>
            <div class="form-text">When disabled, all routed model prompts will trigger default backup providers instead.</div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                <i class="bi bi-check-circle"></i> Save Settings
            </button>
            <a href="{{ route('admin.providers.index') }}" class="btn btn-light border">Cancel</a>
        </div>
    </form>
</div>
@endsection
