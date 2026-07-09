@extends('layouts.admin')

@section('title', 'System Audit Logs')

@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">Audit Logs</li>
@endsection

@section('content')
<div class="card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">System Audit Trail</h4>
            <p class="text-muted mb-0" style="font-size:0.85rem;">Detailed logs of admin panel configurations updates, settings changes, and user roles updates.</p>
        </div>
    </div>

    <!-- Search filter -->
    <form action="{{ route('admin.logs.index') }}" method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search log description..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="action" class="form-select">
                <option value="">All Actions</option>
                @foreach($actions as $action)
                    <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>
                        {{ str_replace('_', ' ', $action) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-light border w-100">Filter</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.logs.index') }}" class="btn btn-link text-decoration-none pt-2 d-inline-block">Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>IP Address</th>
                    <th>User Agent</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td style="font-size:0.85rem;">
                            {{ $log->created_at->format('Y-m-d H:i:s') }}
                        </td>
                        <td>
                            @if($log->user)
                                <span class="fw-semibold">{{ $log->user->name }}</span>
                                <div class="text-muted" style="font-size:0.75rem;">{{ $log->user->email }}</div>
                            @else
                                <span class="text-muted">System</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border" style="text-transform: uppercase; font-size:0.75rem;">
                                {{ str_replace('_', ' ', $log->action) }}
                            </span>
                        </td>
                        <td style="max-width:350px; overflow:hidden; text-overflow:ellipsis;">
                            {{ $log->description }}
                        </td>
                        <td style="font-family:monospace; font-size:0.8rem;">
                            {{ $log->ip_address }}
                        </td>
                        <td style="font-size:0.75rem; max-width:200px; overflow:hidden; text-overflow:ellipsis;" title="{{ $log->user_agent }}">
                            {{ $log->user_agent }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">No audit logs recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>
@endsection
