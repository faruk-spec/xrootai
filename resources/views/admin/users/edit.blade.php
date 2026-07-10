@extends('layouts.admin')

@section('title', 'Edit User: ' . $user->name)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.users') }}">Users</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit User</li>
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
                    <h4 class="fw-bold mb-1">Edit User Account: {{ $user->name }}</h4>
                    <p class="text-muted small mb-0">Modify user credentials, primary role classification, and assigned RBAC privileges.</p>
                </div>
                <div>
                    @if($user->isApproved())
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-semibold">
                            <i class="bi bi-check-circle-fill me-1"></i> Active Account
                        </span>
                    @else
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 rounded-pill fw-semibold">
                            <i class="bi bi-clock-fill me-1"></i> Pending Approval
                        </span>
                    @endif
                </div>
            </div>

            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Primary Role <span class="text-danger">*</span></label>
                    <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role', $user->role) === $role->name ? 'selected' : '' }}>
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
                        <p class="text-muted small mb-3">Check all roles that this user should hold simultaneously:</p>
                        <div class="row g-2">
                            @foreach($roles as $role)
                                <div class="col-md-6">
                                    <div class="form-check border bg-white rounded-3 p-2 ps-4 d-flex align-items-start gap-2 shadow-sm">
                                        <input class="form-check-input mt-1" type="checkbox" name="roles[]" value="{{ $role->id }}" id="edit_role_{{ $role->id }}" {{ (is_array(old('roles')) && in_array($role->id, old('roles'))) || (isset($userRoles) && in_array($role->id, $userRoles)) || old('role', $user->role) === $role->name ? 'checked' : '' }}>
                                        <label class="form-check-label d-block" for="edit_role_{{ $role->id }}" style="cursor: pointer;">
                                            <div class="fw-bold text-dark small">{{ $role->name }}</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">{{ Str::limit($role->description ?? 'Standard privileges.', 60) }}</div>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="border-top pt-4 mb-4">
                    <h6 class="fw-bold mb-1 d-flex align-items-center gap-2">
                        <i class="bi bi-key-fill text-warning"></i> Change User Password (Optional)
                    </h6>
                    <p class="text-muted small mb-3">Leave both password fields blank if you do not wish to modify the user's current credentials.</p>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">New Password</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" minlength="8" placeholder="••••••••">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control" minlength="8" placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center border-top pt-3">
                    <div>
                        <span class="text-muted small">User ID: #{{ $user->id }} | Created: {{ $user->created_at?->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.users') }}" class="btn btn-light border px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary fw-bold px-4 d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill"></i> Save User Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Status & Overrides Card -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 rounded-4 mb-4">
            <h6 class="fw-bold text-uppercase text-muted small mb-3">Account Overrides</h6>

            <div class="d-flex flex-column gap-2">
                @if(!$user->isApproved())
                    <form action="{{ route('admin.users.approve', $user) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success w-100 fw-bold d-flex justify-content-center align-items-center gap-2">
                            <i class="bi bi-check-lg"></i> Approve Account Now
                        </button>
                    </form>
                @else
                    <form action="{{ route('admin.users.suspend', $user) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100 fw-bold d-flex justify-content-center align-items-center gap-2">
                            <i class="bi bi-slash-circle"></i> Suspend User Account
                        </button>
                    </form>
                @endif

                <form action="{{ route('admin.users.resend-verification', $user) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary w-100 d-flex justify-content-center align-items-center gap-2">
                        <i class="bi bi-envelope"></i> Resend Verification Email
                    </button>
                </form>

                <form action="{{ route('admin.users.verify-manually', $user) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-info w-100 d-flex justify-content-center align-items-center gap-2">
                        <i class="bi bi-patch-check"></i> Manually Verify Email
                    </button>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm p-4 rounded-4 bg-light">
            <h6 class="fw-bold mb-2">Effective Permissions Summary</h6>
            <p class="text-muted small mb-3">Based on assigned roles, this user has authorization to access the following capabilities:</p>

            <div class="d-flex flex-wrap gap-1">
                @if($user->role === 'Super Admin' || $user->hasRole('Super Admin'))
                    <span class="badge bg-danger text-white px-2 py-1">ALL MODULES (UNRESTRICTED)</span>
                @else
                    @php
                        $userPerms = $user->roles->flatMap->permissions->unique('name');
                    @endphp
                    @if($userPerms->count() > 0)
                        @foreach($userPerms as $perm)
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">{{ $perm->name }}</span>
                        @endforeach
                    @else
                        <span class="text-muted small font-monospace">No administrative permissions assigned.</span>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
