@extends('layouts.admin')

@section('title', 'Prompt Templates')

@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">Prompt Templates</li>
@endsection

@section('content')
<div class="card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Prompt Templates</h4>
            <p class="text-muted mb-0" style="font-size:0.85rem;">Manage reusable structured system or assistant prompt templates containing custom variables.</p>
        </div>
        <a href="{{ route('admin.prompts.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="bi bi-plus-circle-fill"></i> Add Template
        </a>
    </div>

    <!-- Search filter -->
    <form action="{{ route('admin.prompts.index') }}" method="GET" class="row g-3 mb-4">
        <div class="col-md-5">
            <input type="text" name="search" class="form-control" placeholder="Search templates name or description..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-light border w-100">Search</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Template</th>
                    <th>Slug Key</th>
                    <th>Custom Variables</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($prompts as $prompt)
                    <tr>
                        <td>
                            <div>
                                <span class="fw-semibold">{{ $prompt->name }}</span>
                                <div class="text-muted text-truncate" style="max-width:350px; font-size:0.8rem;">{{ $prompt->description ?: 'No description provided.' }}</div>
                            </div>
                        </td>
                        <td>
                            <code style="font-size:0.85rem;">{{ $prompt->slug }}</code>
                        </td>
                        <td>
                            @forelse($prompt->variables ?? [] as $var)
                                <span class="badge bg-light text-dark border" style="font-family:monospace; font-size:0.75rem;">{{ $var }}</span>
                            @empty
                                <span class="text-muted" style="font-size:0.8rem;">None</span>
                            @endforelse
                        </td>
                        <td>
                            <span class="badge {{ $prompt->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                {{ $prompt->is_active ? 'Active' : 'Disabled' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.prompts.edit', $prompt->id) }}" class="btn btn-sm btn-outline-secondary py-1 px-2">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <form action="{{ route('admin.prompts.delete', $prompt->id) }}" method="POST" onsubmit="return confirm('Delete this prompt template?')">
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
                        <td colspan="5" class="text-center py-5 text-muted">No prompt templates registered.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $prompts->links() }}
    </div>
</div>
@endsection
