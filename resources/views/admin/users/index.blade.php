@extends('layouts.admin')

@section('title', 'Users Management')

@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">Users</li>
@endsection

@section('content')
<div class="card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Users Directory</h4>
            <p class="text-muted mb-0" style="font-size:0.85rem;">Manage dynamic SaaS roles, user information, and inspect activity logs.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="bi bi-person-plus-fill"></i> Add User
        </a>
    </div>

    <!-- Search & Filters -->
    <form action="{{ route('admin.users') }}" method="GET" class="row g-3 mb-4 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Search by name or email..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="role" class="form-select">
                <option value="">Filter by Role (All)</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-light border w-100 d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-filter"></i> Apply
            </button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.users') }}" class="btn btn-link text-decoration-none w-100">Reset</a>
        </div>
    </form>

    <!-- Users Table -->
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Conversations</th>
                    <th>Registered</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-secondary-subtle d-flex align-items-center justify-content-center fw-bold" style="width:40px; height:40px;">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $user->name }}</div>
                                    <div class="text-muted" style="font-size:0.8rem;">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $user->role === 'admin' ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary' }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td>
                            <span class="fw-bold">{{ $user->conversations_count }}</span> conversations
                        </td>
                        <td>
                            {{ $user->created_at->format('M d, Y H:i') }}
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-secondary py-1 px-2">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                @if(auth()->id() !== $user->id)
                                    <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete user {{ $user->name }}? This action is irreversible.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 d-block mb-3 text-secondary"></i>
                            No users found matching the criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
@endsection
