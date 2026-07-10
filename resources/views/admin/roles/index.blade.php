@extends('layouts.admin')

@section('title', 'Roles & RBAC Management')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.users') }}">User Management</a></li>
    <li class="breadcrumb-item active" aria-current="page">Roles & Permissions</li>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-12">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 p-3 d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-4 p-3 d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        <div class="card border-0 shadow-sm p-4 rounded-4 mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 pb-3 border-bottom">
                <div>
                    <h4 class="fw-bold mb-1">Role-Based Access Control (RBAC)</h4>
                    <p class="text-muted small mb-0">Define custom roles, assign granular permissions, and control access privileges across all SaaS modules.</p>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <form method="GET" action="{{ route('admin.roles.index') }}" class="d-flex gap-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Search roles..." value="{{ request('search') }}">
                            @if(request('search'))
                                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a>
                            @endif
                        </div>
                    </form>
                    <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary btn-sm fw-semibold d-flex align-items-center gap-1">
                        <i class="bi bi-shield-lock"></i> System Permissions
                    </a>
                    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm fw-semibold d-flex align-items-center gap-1">
                        <i class="bi bi-plus-lg"></i> Create New Role
                    </a>
                </div>
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col" class="py-3 px-3 text-uppercase text-muted small fw-bold" style="width: 22%;">Role Name</th>
                            <th scope="col" class="py-3 px-3 text-uppercase text-muted small fw-bold" style="width: 48%;">Assigned Permissions Matrix</th>
                            <th scope="col" class="py-3 px-3 text-uppercase text-muted small fw-bold text-center" style="width: 15%;">Assigned Users</th>
                            <th scope="col" class="py-3 px-3 text-uppercase text-muted small fw-bold text-end" style="width: 15%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                            <tr>
                                <td class="px-3 py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle p-2 d-flex align-items-center justify-content-center {{ $role->name === 'Super Admin' ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary' }}" style="width: 38px; height: 38px;">
                                            <i class="bi {{ $role->name === 'Super Admin' ? 'bi-shield-check' : 'bi-person-badge' }}"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark d-flex align-items-center gap-1">
                                                {{ $role->name }}
                                                @if(in_array($role->name, ['Super Admin', 'Admin', 'User']))
                                                    <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.65rem;">CORE</span>
                                                @endif
                                            </div>
                                            <div class="text-muted small">{{ Str::limit($role->description ?? 'No description provided.', 60) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="d-flex flex-wrap gap-1">
                                        @if($role->name === 'Super Admin')
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fw-semibold">
                                                <i class="bi bi-star-fill me-1"></i> ALL SYSTEM PERMISSIONS (UNRESTRICTED)
                                            </span>
                                        @else
                                            @forelse($role->permissions as $perm)
                                                <span class="badge bg-light text-dark border px-2 py-1 font-monospace" style="font-size: 0.75rem;">
                                                    {{ $perm->name }}
                                                </span>
                                            @empty
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                                    No permissions attached
                                                </span>
                                            @endforelse
                                        @endif
                                    </div>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <a href="{{ route('admin.users') }}?role={{ urlencode($role->name) }}" class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2 rounded-pill fw-bold text-decoration-none">
                                        <i class="bi bi-people-fill me-1"></i> {{ $role->users_count }} {{ Str::plural('User', $role->users_count) }}
                                    </a>
                                </td>
                                <td class="px-3 py-3 text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-outline-primary" title="Configure Role Permissions">
                                            <i class="bi bi-sliders"></i> Edit
                                        </a>
                                        @if(!in_array($role->name, ['Super Admin', 'Admin', 'User']))
                                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteRoleModal_{{ $role->id }}" title="Delete Role">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>

                                    @if(!in_array($role->name, ['Super Admin', 'Admin', 'User']))
                                        <!-- Delete Role Modal -->
                                        <div class="modal fade text-start" id="deleteRoleModal_{{ $role->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                                    <div class="modal-header bg-danger text-white border-0 px-4 py-3">
                                                        <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Delete Role: {{ $role->name }}</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <div class="modal-body p-4">
                                                            <p class="mb-2">Are you sure you want to delete the <strong>{{ $role->name }}</strong> role?</p>
                                                            <div class="alert alert-warning border-0 rounded-3 small mb-0">
                                                                <i class="bi bi-info-circle me-1"></i> This action will remove the role definition and strip any assigned permissions from this group.
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light px-4 py-3 border-0">
                                                            <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-danger fw-bold px-4">Delete Role</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="text-muted my-3">
                                        <i class="bi bi-shield-slash fs-1 d-block mb-2"></i>
                                        <h5>No Roles Found</h5>
                                        <p class="small mb-0">Get started by creating your first custom authorization role.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-4">
                {{ $roles->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
