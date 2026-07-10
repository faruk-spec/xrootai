@extends('layouts.admin')

@section('title', 'Edit Role: ' . $role->name)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.users') }}">User Management</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles & Permissions</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit Role</li>
@endsection

@section('content')
<div class="row g-4">
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

        <div class="card border-0 shadow-sm p-4 rounded-4">
            <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
                <div>
                    <h4 class="fw-bold mb-1">Edit Access Role: {{ $role->name }}</h4>
                    <p class="text-muted small mb-0">Modify role details and update assigned system capabilities (`role_permission`).</p>
                </div>
                <div>
                    @if(in_array($role->name, ['Super Admin', 'Admin', 'User']))
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2 rounded-pill fw-semibold">
                            <i class="bi bi-shield-lock-fill me-1"></i> Core System Role
                        </span>
                    @else
                        <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2 rounded-pill fw-semibold">
                            <i class="bi bi-sliders me-1"></i> Custom Role
                        </span>
                    @endif
                </div>
            </div>

            <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label class="form-label fw-semibold">Role Display Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $role->name) }}" {{ $role->name === 'Super Admin' ? 'readonly style=background-color:#e2e8f0;' : 'required' }} maxlength="100">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if($role->name === 'Super Admin')
                        <div class="form-text text-danger small">The Super Admin role name cannot be modified to protect against lockout vulnerabilities.</div>
                    @else
                        <div class="form-text small">Must be a unique descriptive identifier across the system.</div>
                    @endif
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Description & Responsibilities</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Explain what responsibilities and scope of access this group covers...">{{ old('description', $role->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-semibold mb-0">Assigned Permissions Matrix (`permissions[]`)</label>
                        @if($role->name !== 'Super Admin')
                            <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllPermsBtn">
                                <i class="bi bi-check-all me-1"></i> Toggle All
                            </button>
                        @endif
                    </div>
                    <div class="border rounded-3 p-3 bg-light">
                        @if($role->name === 'Super Admin')
                            <div class="alert alert-danger border-0 rounded-3 mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-shield-fill-check fs-4"></i>
                                <div><strong>Super Admin Safeguard Active:</strong> All existing and future system permissions are automatically attached to Super Admin at runtime. Individual unchecking is disabled.</div>
                            </div>
                        @else
                            <p class="text-muted small mb-3">Check all endpoints and system functions this role should be permitted to execute:</p>
                        @endif
                        
                        <div class="row g-2">
                            @foreach($permissions as $perm)
                                <div class="col-md-6">
                                    <div class="form-check border bg-white rounded-3 p-3 ps-4 d-flex align-items-start gap-2 shadow-sm {{ $role->name === 'Super Admin' ? 'opacity-75' : '' }}">
                                        <input class="form-check-input perm-checkbox mt-1" type="checkbox" name="permissions[]" value="{{ $perm->id }}" id="perm_{{ $perm->id }}" {{ $role->name === 'Super Admin' || (is_array(old('permissions')) && in_array($perm->id, old('permissions'))) || in_array($perm->id, $rolePermissions) ? 'checked' : '' }} {{ $role->name === 'Super Admin' ? 'disabled' : '' }}>
                                        <label class="form-check-label d-block" for="perm_{{ $perm->id }}" style="cursor: pointer;">
                                            <div class="fw-bold text-dark font-monospace small">{{ $perm->name }}</div>
                                            <div class="text-muted" style="font-size: 0.78rem;">{{ $perm->description ?? 'Standard system capability.' }}</div>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center border-top pt-3">
                    <span class="text-muted small">Role ID: #{{ $role->id }} | Updated: {{ $role->updated_at?->format('M d, Y H:i') }}</span>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-light border px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary fw-bold px-4 d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill"></i> Update Role Matrix
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Stats Card -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 rounded-4 mb-4 bg-light">
            <h6 class="fw-bold text-uppercase text-muted small mb-3">Role Impact Summary</h6>

            <div class="d-flex flex-column gap-3">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                    <span class="text-muted">Assigned Users</span>
                    <span class="badge bg-primary rounded-pill px-3 py-1 fs-6">{{ $role->users()->count() }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                    <span class="text-muted">Active Permissions</span>
                    <span class="badge bg-success rounded-pill px-3 py-1 fs-6">{{ $role->name === 'Super Admin' ? 'ALL' : count($rolePermissions) }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">Created On</span>
                    <span class="fw-semibold small">{{ $role->created_at?->format('M d, Y') }}</span>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('admin.users') }}?role={{ urlencode($role->name) }}" class="btn btn-outline-primary w-100 btn-sm">
                    <i class="bi bi-people me-1"></i> View All Assigned Users
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('selectAllPermsBtn');
    if (!btn) return;
    let allChecked = false;
    btn.addEventListener('click', function() {
        allChecked = !allChecked;
        document.querySelectorAll('.perm-checkbox').forEach(cb => {
            if (!cb.disabled) cb.checked = allChecked;
        });
        btn.innerHTML = allChecked ? '<i class="bi bi-x-circle me-1"></i> Deselect All' : '<i class="bi bi-check-all me-1"></i> Toggle All';
    });
});
</script>
@endsection
