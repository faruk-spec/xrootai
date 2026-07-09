@extends('layouts.admin')

@section('title', 'AI Models')

@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">AI Models</li>
@endsection

@section('content')
<div class="card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">AI Models</h4>
            <p class="text-muted mb-0" style="font-size:0.85rem;">Manage individual model identifiers, parameters, capabilities, and API request token costs.</p>
        </div>
        <a href="{{ route('admin.models.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="bi bi-plus-circle-fill"></i> Add Model
        </a>
    </div>

    <!-- Filters -->
    <form action="{{ route('admin.models.index') }}" method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search models name/identifier..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="provider" class="form-select">
                <option value="">All Providers</option>
                @foreach($providers as $provider)
                    <option value="{{ $provider->id }}" {{ request('provider') == $provider->id ? 'selected' : '' }}>
                        {{ $provider->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-light border w-100"><i class="bi bi-filter"></i> Apply</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.models.index') }}" class="btn btn-link text-decoration-none pt-2 d-inline-block">Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Model</th>
                    <th>Provider</th>
                    <th>Status</th>
                    <th>Context Limit</th>
                    <th>Cost Input/Output (Per M)</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($models as $model)
                    <tr>
                        <td>
                            <div>
                                <span class="fw-semibold">{{ $model->name }}</span>
                                <div class="text-muted" style="font-size:0.75rem; font-family:monospace;">{{ $model->model_identifier }}</div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary">{{ $model->provider->name }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $model->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                {{ $model->is_active ? 'Active' : 'Disabled' }}
                            </span>
                        </td>
                        <td>
                            {{ number_format($model->context_window) }} tokens
                        </td>
                        <td>
                            @if($model->cost_per_million_input == 0 && $model->cost_per_million_output == 0)
                                <span class="text-success fw-semibold">Free</span>
                            @else
                                <span style="font-size:0.8rem;">
                                    In: <strong>${{ number_format($model->cost_per_million_input, 2) }}</strong> / Out: <strong>${{ number_format($model->cost_per_million_output, 2) }}</strong>
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.models.edit', $model->id) }}" class="btn btn-sm btn-outline-secondary py-1 px-2">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <form action="{{ route('admin.models.delete', $model->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this model configuration? This will impact routing references.')">
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
                        <td colspan="6" class="text-center py-5 text-muted">No models configured yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $models->links() }}
    </div>
</div>
@endsection
