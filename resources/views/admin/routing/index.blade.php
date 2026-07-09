@extends('layouts.admin')

@section('title', 'AI Routing Rules')

@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">AI Routing Rules</li>
@endsection

@section('content')
<div class="card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">AI Routing Rules</h4>
            <p class="text-muted mb-0" style="font-size:0.85rem;">Control dynamic orchestration by routing matching user prompts or system states to specified LLM models.</p>
        </div>
        <a href="{{ route('admin.routing.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="bi bi-plus-circle-fill"></i> Add Routing Rule
        </a>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Rule Name</th>
                    <th>Trigger</th>
                    <th>Pattern / Filter</th>
                    <th>Target Model</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rules as $rule)
                    <tr>
                        <td>
                            <span class="fw-semibold">{{ $rule->name }}</span>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ strtoupper($rule->trigger_type) }}</span>
                        </td>
                        <td style="font-family:monospace; font-size:0.85rem;">
                            {{ $rule->pattern ?: 'N/A' }}
                        </td>
                        <td>
                            {{ $rule->targetModel->name ?? 'N/A' }}
                            <span class="text-muted text-uppercase" style="font-size:0.75rem;">({{ $rule->targetModel->provider->slug ?? '' }})</span>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $rule->priority }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $rule->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                {{ $rule->is_active ? 'Enabled' : 'Disabled' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.routing.edit', $rule->id) }}" class="btn btn-sm btn-outline-secondary py-1 px-2">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <form action="{{ route('admin.routing.delete', $rule->id) }}" method="POST" onsubmit="return confirm('Delete this routing rule?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">No model routing rules configured. Default system logic will apply.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
