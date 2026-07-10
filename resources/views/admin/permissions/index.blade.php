@extends('layouts.admin')

@section('title', 'System Permissions Management')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.users') }}">User Management</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles & Permissions</a></li>
    <li class="breadcrumb-item active" aria-current="page">System Permissions</li>
@endsection

@section('content')
<div class="row g-4">
    <!-- Left Column: Permissions Table -->
    <div class="col-lg-8">
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
        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-4 p-3 mb-4" role="alert">
                <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Please check the input errors:</div>
                <ul class="mb-0 small">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card border-0 shadow-sm p-4 rounded-4 mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 pb-3 border-bottom">
                <div>
                    <h4 class="fw-bold mb-1">Granular System Permissions</h4>
                    <p class="text-muted small mb-0">Fine-grained capabilities checkable via <code>$user->hasPermission('key')</code> or middleware.</p>
                </div>

                <form method="GET" action="{{ route('admin.permissions.index') }}" class="d-flex gap-2">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search permissions..." value="{{ request('search') }}">
                        @if(request('search'))
                            <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col" class="py-3 px-3 text-uppercase text-muted small fw-bold" style="width: 32%;">Permission Slug / Key</th>
                            <th scope="col" class="py-3 px-3 text-uppercase text-muted small fw-bold" style="width: 45%;">Description</th>
                            <th scope="col" class="py-3 px-3 text-uppercase text-muted small fw-bold text-center" style="width: 13%;">Attached Roles</th>
                            <th scope="col" class="py-3 px-3 text-uppercase text-muted small fw-bold text-end" style="width: 10%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $corePerms = [
                                'manage-users', 'manage-ai', 'manage-prompts', 'manage-kb',
                                'view-analytics', 'manage-settings', 'view-logs', 'human-handoff'
                            ];
                        @endphp
                        @forelse($permissions as $perm)
                            <tr>
                                <td class="px-3 py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <code class="badge bg-dark border border-secondary text-info font-monospace p-2" style="font-size: 0.8rem;">
                                            {{ $perm->name }}
                                        </code>
                                        @if(in_array($perm->name, $corePerms))
                                            <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.62rem;">CORE</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="text-dark small">{{ $perm->description ?? 'No specific description.' }}</div>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill">
                                        {{ $perm->roles_count }} {{ Str::plural('Role', $perm->roles_count) }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-end">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editPermModal_{{ $perm->id }}" title="Edit Description">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        @if(!in_array($perm->name, $corePerms))
                                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deletePermModal_{{ $perm->id }}" title="Delete Permission">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>

                                    <!-- Edit Permission Modal -->
                                    <div class="modal fade text-start" id="editPermModal_{{ $perm->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                                <div class="modal-header bg-light border-bottom px-4 py-3">
                                                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Permission: {{ $perm->name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('admin.permissions.update', $perm) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body p-4">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold small">Permission Key <span class="text-danger">*</span></label>
                                                            <input type="text" name="name" class="form-control font-monospace small" value="{{ $perm->name }}" {{ in_array($perm->name, $corePerms) ? 'readonly style=background-color:#e2e8f0;' : 'required' }}>
                                                            @if(in_array($perm->name, $corePerms))
                                                                <div class="form-text text-muted small">Core permission key cannot be renamed.</div>
                                                            @endif
                                                        </div>
                                                        <div class="mb-0">
                                                            <label class="form-label fw-semibold small">Description</label>
                                                            <textarea name="description" class="form-control small" rows="3" required>{{ $perm->description }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light px-4 py-3 border-top">
                                                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary fw-bold px-4">Update Permission</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    @if(!in_array($perm->name, $corePerms))
                                        <!-- Delete Permission Modal -->
                                        <div class="modal fade text-start" id="deletePermModal_{{ $perm->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                                    <div class="modal-header bg-danger text-white border-0 px-4 py-3">
                                                        <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Delete Permission: {{ $perm->name }}</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('admin.permissions.destroy', $perm) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <div class="modal-body p-4">
                                                            <p class="mb-2">Are you sure you want to delete the <strong>{{ $perm->name }}</strong> permission?</p>
                                                            <div class="alert alert-warning border-0 rounded-3 small mb-0">
                                                                <i class="bi bi-info-circle me-1"></i> This action will remove this key from all assigned roles. Any routes or controllers protected by <code>$user->hasPermission('{{ $perm->name }}')</code> may become restricted or unreachable.
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light px-4 py-3 border-0">
                                                            <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-danger fw-bold px-4">Delete Permission</button>
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
                                        <i class="bi bi-key-fill fs-1 d-block mb-2"></i>
                                        <h5>No Permissions Found</h5>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-4">
                {{ $permissions->links() }}
            </div>
        </div>
    </div>

    <!-- Right Column: Add New Permission Card -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 rounded-4 mb-4">
            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle-fill text-primary fs-5"></i> Create New Permission
            </h6>
            <p class="text-muted small mb-3">Define a custom permission slug and description to attach to roles.</p>

            <form action="{{ route('admin.permissions.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Permission Slug / Key <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control font-monospace small @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="e.g. manage-billing or export-reports" pattern="^[a-zA-Z0-9\-_]+$">
                    <div class="form-text small">Use lowercase words separated by hyphens (e.g., <code>manage-billing</code>).</div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold small">Description & Purpose <span class="text-danger">*</span></label>
                    <textarea name="description" class="form-control small @error('description') is-invalid @enderror" rows="3" required placeholder="Explain what capability this permission unlocks...">{{ old('description') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary fw-bold w-100 py-2 d-flex justify-content-center align-items-center gap-2">
                    <i class="bi bi-plus-lg"></i> Create System Permission
                </button>
            </form>
        </div>

        <div class="card border-0 shadow-sm p-4 rounded-4 bg-light">
            <h6 class="fw-bold text-dark small mb-2"><i class="bi bi-code-slash me-1"></i> Developer Code Usage</h6>
            <p class="text-muted small mb-2">Check permissions in your Laravel controllers or blade templates using:</p>
            
            <div class="bg-dark text-light p-3 rounded-3 font-monospace mb-3" style="font-size: 0.78rem; overflow-x: auto;">
                // In Controller or Route Middleware:<br>
                if (!auth()->user()->hasPermission('manage-users')) {<br>
                &nbsp;&nbsp;&nbsp;&nbsp;abort(403);<br>
                }<br><br>
                // Or via Route Middleware:<br>
                Route::get('...', [...])->middleware('permission:manage-users');<br><br>
                // In Blade Templates:<br>
                &#64;if(auth()->user()->hasPermission('manage-users'))<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&lt;button&gt;Delete User&lt;/button&gt;<br>
                &#64;endif
            </div>
            <p class="text-muted small mb-0"><strong>Note:</strong> Super Admin role automatically inherits all created permissions.</p>
        </div>
    </div>
</div>
@endsection
