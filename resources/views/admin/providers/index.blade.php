@extends('layouts.admin')

@section('title', 'AI Providers')

@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">AI Providers</li>
@endsection

@section('content')
<div class="card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">AI Providers</h4>
            <p class="text-muted mb-0" style="font-size:0.85rem;">Manage backend Large Language Model (LLM) APIs, connection settings, and credentials.</p>
        </div>
    </div>

    <!-- Alert placeholder for live JS tests -->
    <div id="testAlert" class="alert d-none shadow-sm mb-4" style="border-radius:12px;"></div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Provider</th>
                    <th>Status</th>
                    <th>Base Endpoint URL</th>
                    <th>Configured Models</th>
                    <th>Testing</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($providers as $provider)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold" style="width:40px; height:40px;">
                                    <i class="bi bi-cpu"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $provider->name }}</div>
                                    <div class="text-muted" style="font-size:0.8rem; text-transform:uppercase;">{{ $provider->slug }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $provider->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                {{ $provider->is_active ? 'Active' : 'Disabled' }}
                            </span>
                        </td>
                        <td style="font-family:monospace; font-size:0.8rem; max-width:300px; overflow:hidden; text-overflow:ellipsis;">
                            {{ $provider->base_url ?: 'N/A' }}
                        </td>
                        <td>
                            <span class="fw-bold">{{ $provider->models_count }}</span> models
                        </td>
                        <td>
                            <button onclick="testConnection('{{ $provider->id }}', this)" class="btn btn-sm btn-outline-info py-1 px-2 d-flex align-items-center gap-1">
                                <i class="bi bi-broadcast"></i> Test Credentials
                            </button>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.providers.edit', $provider->id) }}" class="btn btn-sm btn-outline-secondary py-1 px-2">
                                <i class="bi bi-pencil-square"></i> Configure
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">No providers registered.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function testConnection(providerId, btn) {
        const testAlert = document.getElementById('testAlert');
        testAlert.className = 'alert d-none'; // reset classes
        
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status"></span> Testing...`;

        fetch(`{{ url('admin/providers') }}/${providerId}/test`, {
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
            
            testAlert.classList.remove('d-none');
            if (data.success) {
                testAlert.classList.add('alert-success');
                testAlert.innerHTML = `<i class="bi bi-check-circle-fill me-2"></i> ${data.message}`;
            } else {
                testAlert.classList.add('alert-danger');
                testAlert.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2"></i> ${data.message}`;
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            testAlert.classList.remove('d-none');
            testAlert.classList.add('alert-danger');
            testAlert.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2"></i> Network error occurred while testing connection.`;
        });
    }
</script>
@endsection
