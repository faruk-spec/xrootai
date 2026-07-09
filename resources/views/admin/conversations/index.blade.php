@extends('layouts.admin')

@section('title', 'Conversations')

@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">Conversations</li>
@endsection

@section('content')
<div class="card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Conversations Audit Log</h4>
            <p class="text-muted mb-0" style="font-size:0.85rem;">Audit system chat logs, inspect assistant responses, token count usage, and manage active session details.</p>
        </div>
    </div>

    <!-- Search filter -->
    <form action="{{ route('admin.conversations.index') }}" method="GET" class="row g-3 mb-4">
        <div class="col-md-5">
            <input type="text" name="search" class="form-control" placeholder="Search conversations by title or user name..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-light border w-100">Search</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Conversation Title</th>
                    <th>User</th>
                    <th>Selected Model</th>
                    <th>Total Messages</th>
                    <th>Last Active</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($conversations as $conv)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $conv->title }}</div>
                            <div class="text-muted text-truncate" style="max-width:250px; font-size:0.75rem;">UUID: {{ $conv->uuid }}</div>
                        </td>
                        <td>
                            @if($conv->user)
                                <span>{{ $conv->user->name }}</span>
                                <div class="text-muted" style="font-size:0.75rem;">{{ $conv->user->email }}</div>
                            @else
                                <span class="text-muted">Guest User</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary">{{ $conv->model }}</span>
                        </td>
                        <td>
                            <span class="fw-bold">{{ $conv->messages_count }}</span> messages
                        </td>
                        <td>
                            {{ $conv->updated_at->diffForHumans() }}
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.conversations.show', $conv->id) }}" class="btn btn-sm btn-outline-secondary py-1 px-2">
                                    <i class="bi bi-eye"></i> View Logs
                                </a>
                                <form action="{{ route('admin.conversations.delete', $conv->id) }}" method="POST" onsubmit="return confirm('Permanently delete this conversation record?')">
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
                        <td colspan="6" class="text-center py-5 text-muted">No active conversations found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $conversations->links() }}
    </div>
</div>
@endsection
