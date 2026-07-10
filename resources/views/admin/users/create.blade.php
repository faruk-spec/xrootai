@extends('layouts.admin')

@section('title', 'Add User')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.users') }}">Users</a></li>
    <li class="breadcrumb-item active" aria-current="page">Add User</li>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-4 rounded-4">
            <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
                <div>
                    <h4 class="fw-bold mb-1">Add New User Account</h4>
                    <p class="text-muted small mb-0">Create a new user and assign administrative or standard roles.</p>
                </div>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="e.g. Sarah Jenkins">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="e.g. sarah@enterprise.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Primary Role <span class="text-danger">*</span></label>
                    <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role', 'user') === $role->name ? 'selected' : '' }}>
                                {{ $role->name }} {{ $role->description ? '— ' . $role->description : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text small">This is the primary classification used for quick filtering and legacy checks.</div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold d-flex justify-content-between align-items-center">
                        <span>Assigned Roles & Access Matrix (`roles[]`)</span>
                        <span class="badge bg-primary-subtle text-primary fw-normal">Multi-Role Support</span>
                    </label>
                    <div class="border rounded-3 p-3 bg-light">
                        <p class="text-muted small mb-3">Select one or more roles to grant combined permissions across all assigned roles:</p>
                        <div class="row g-2">
                            @foreach($roles as $role)
                                <div class="col-md-6">
                                    <div class="form-check border bg-white rounded-3 p-2 ps-4 d-flex align-items-start gap-2 shadow-sm">
                                        <input class="form-check-input mt-1" type="checkbox" name="roles[]" value="{{ $role->id }}" id="create_role_{{ $role->id }}" {{ (is_array(old('roles')) && in_array($role->id, old('roles'))) || old('role', 'user') === $role->name ? 'checked' : '' }}>
                                        <label class="form-check-label d-block" for="create_role_{{ $role->id }}" style="cursor: pointer;">
                                            <div class="fw-bold text-dark small">{{ $role->name }}</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">{{ Str::limit($role->description ?? 'Standard privileges.', 60) }}</div>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required minlength="8" placeholder="••••••••">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" required minlength="8" placeholder="••••••••">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 border-top pt-3">
                    <a href="{{ route('admin.users') }}" class="btn btn-light border px-4">Cancel</a>
                    <button type="submit" class="btn btn-primary fw-bold px-4 d-flex align-items-center gap-2">
                        <i class="bi bi-person-check-fill"></i> Create & Activate User
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Help & Guidelines -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-primary text-white">
            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-shield-lock-fill fs-5"></i> Enterprise RBAC Guidance
            </h6>
            <p class="small mb-3" style="opacity: 0.9;">
                When you create a user account, assigning multiple roles allows granular access control:
            </p>
            <ul class="small ps-3 mb-0" style="opacity: 0.9; line-height: 1.6;">
                <li><strong>Super Admin:</strong> Full unrestricted access to all endpoints and security settings.</li>
                <li><strong>Admin:</strong> Manages users, AI models, prompts, and knowledge bases.</li>
                <li><strong>Manager:</strong> Oversees analytics and support logs without modifying core infrastructure.</li>
                <li><strong>Support Agent:</strong> Dedicated to live chat intervention (`human-handoff`).</li>
                <li><strong>Developer:</strong> Manages webhooks and API keys.</li>
            </ul>
        </div>
    </div>
</div>
@endsection
