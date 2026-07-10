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
                    <th>Status</th>
                    <th>Verification</th>
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
                            <div class="d-flex flex-wrap gap-1">
                                <span class="badge {{ in_array($user->role, ['admin', 'Super Admin']) ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-primary-subtle text-primary border border-primary-subtle' }}">
                                    {{ $user->role }}
                                </span>
                                @foreach($user->roles as $assignedRole)
                                    @if($assignedRole->name !== $user->role)
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle font-monospace" style="font-size: 0.72rem;">
                                            + {{ $assignedRole->name }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </td>
                        <td>
                            @if($user->status === 'suspended')
                                <span class="badge bg-danger-subtle text-danger"><i class="bi bi-slash-circle me-1"></i> Suspended</span>
                            @elseif(!$user->is_approved)
                                <span class="badge bg-warning-subtle text-warning-emphasis"><i class="bi bi-clock me-1"></i> Pending Approval</span>
                            @else
                                <span class="badge bg-success-subtle text-success"><i class="bi bi-check-circle-fill me-1"></i> Active</span>
                            @endif
                        </td>
                        <td>
                            @if($user->isVerified())
                                <span class="badge bg-success-subtle text-success" title="Verified on {{ $user->email_verified_at?->format('M d, Y H:i') ?? $user->otp_verified_at?->format('M d, Y H:i') }}">
                                    <i class="bi bi-shield-check me-1"></i> Verified
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary">
                                    <i class="bi bi-exclamation-circle me-1"></i> Unverified
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-bold">{{ $user->conversations_count }}</span> conversations
                        </td>
                        <td>
                            <div style="font-size:0.85rem;">{{ $user->created_at->format('M d, Y') }}</div>
                            <div class="text-muted" style="font-size:0.75rem;">{{ $user->created_at->format('H:i') }}</div>
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-secondary py-1 px-2 d-flex align-items-center gap-1">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>

                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle py-1 px-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="font-size: 0.85rem;">
                                        @if(!$user->isVerified())
                                            <li>
                                                <form action="{{ route('admin.users.verify-manually', $user->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-success d-flex align-items-center gap-2">
                                                        <i class="bi bi-check-circle-fill"></i> Mark as Verified
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.users.resend-verification', $user->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item d-flex align-items-center gap-2">
                                                        <i class="bi bi-envelope-check"></i> Resend Verification Mail
                                                    </button>
                                                </form>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                        @endif

                                        @if(!$user->is_approved || $user->status !== 'active')
                                            <li>
                                                <form action="{{ route('admin.users.approve', $user->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-primary d-flex align-items-center gap-2">
                                                        <i class="bi bi-person-check-fill"></i> Approve User Account
                                                    </button>
                                                </form>
                                            </li>
                                        @endif

                                        @if(auth()->id() !== $user->id && $user->status !== 'suspended')
                                            <li>
                                                <form action="{{ route('admin.users.suspend', $user->id) }}" method="POST" onsubmit="return confirm('Suspend user {{ $user->name }}? They will be blocked from logging in.')">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-warning d-flex align-items-center gap-2">
                                                        <i class="bi bi-slash-circle"></i> Suspend Account
                                                    </button>
                                                </form>
                                            </li>
                                        @endif

                                        @if(auth()->id() !== $user->id)
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" onsubmit="return confirm('Permanently delete {{ $user->name }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2">
                                                        <i class="bi bi-trash-fill"></i> Delete Permanently
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
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
