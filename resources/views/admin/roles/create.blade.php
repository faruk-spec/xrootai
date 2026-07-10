@extends('layouts.admin')

@section('title', 'Create New Role')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.users') }}">User Management</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles & Permissions</a></li>
    <li class="breadcrumb-item active" aria-current="page">Create Role</li>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-4 rounded-4">
            <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
                <div>
                    <h4 class="fw-bold mb-1">Create Custom Access Role</h4>
                    <p class="text-muted small mb-0">Define a new authorization group and attach system capabilities.</p>
                </div>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left"></i> Back to Roles
                </a>
            </div>

            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="form-label fw-semibold">Role Display Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="e.g. Billing Administrator or Support Supervisor" maxlength="100">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text small">Must be a unique descriptive identifier across the system.</div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Description & Responsibilities</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Explain what responsibilities and scope of access this group covers...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-semibold mb-0">Assign Permissions (`permissions[]`)</label>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllPermsBtn">
                            <i class="bi bi-check-all me-1"></i> Select All
                        </button>
                    </div>
                    <div class="border rounded-3 p-3 bg-light">
                        <p class="text-muted small mb-3">Check all endpoints and system functions this role should be permitted to execute:</p>
                        
                        <div class="row g-2">
                            @foreach($permissions as $perm)
                                <div class="col-md-6">
                                    <div class="form-check border bg-white rounded-3 p-3 ps-4 d-flex align-items-start gap-2 shadow-sm">
                                        <input class="form-check-input perm-checkbox mt-1" type="checkbox" name="permissions[]" value="{{ $perm->id }}" id="perm_{{ $perm->id }}" {{ (is_array(old('permissions')) && in_array($perm->id, old('permissions'))) ? 'checked' : '' }}>
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

                <div class="d-flex justify-content-end gap-2 border-top pt-3">
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-light border px-4">Cancel</a>
                    <button type="submit" class="btn btn-primary fw-bold px-4 d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill"></i> Save Role & Permissions
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Help & Guidelines -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-dark text-white">
            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2 text-warning">
                <i class="bi bi-lightbulb-fill fs-5"></i> Best Practices for Roles
            </h6>
            <p class="small mb-3" style="opacity: 0.9;">
                Follow principle of least privilege when designing new roles:
            </p>
            <ul class="small ps-3 mb-0" style="opacity: 0.9; line-height: 1.6;">
                <li><strong>Granular Scope:</strong> Only attach permissions required for daily tasks.</li>
                <li><strong>Custom Rules:</strong> Need a new permission key? Create it first in the <a href="{{ route('admin.permissions.index') }}" class="text-info text-decoration-underline">System Permissions</a> panel.</li>
                <li><strong>Dynamic Evaluation:</strong> Users assigned multiple roles will inherit the union of all checked permissions.</li>
            </ul>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('selectAllPermsBtn');
    let allChecked = false;
    btn.addEventListener('click', function() {
        allChecked = !allChecked;
        document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = allChecked);
        btn.innerHTML = allChecked ? '<i class="bi bi-x-circle me-1"></i> Deselect All' : '<i class="bi bi-check-all me-1"></i> Select All';
    });
});
</script>
@endsection
