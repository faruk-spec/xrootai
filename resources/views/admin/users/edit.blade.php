@extends('layouts.admin')

@section('title', 'Edit User')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.users') }}">Users</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit User</li>
@endsection

@section('content')
<div class="card border-0 p-4" style="max-width: 600px;">
    <h4 class="fw-bold mb-4">Edit User Account: {{ $user->name }}</h4>

    <form action="{{ route('admin.users.role', $user->id) }}" method="POST">
        @csrf
        
        <div class="mb-3">
            <label class="form-label fw-medium">Full Name</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label fw-medium">Email Address</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label fw-medium">Assign Role</label>
            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ old('role', $user->role) === $role->name ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            @error('role')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3 border-top pt-3 mt-4">
            <h6 class="fw-bold mb-2">Change Password (Optional)</h6>
            <div class="form-text mb-3">Leave blank if you do not wish to modify the user's password.</div>
            
            <div class="mb-3">
                <label class="form-label fw-medium">New Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">Confirm New Password</label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                <i class="bi bi-check-circle"></i> Save Changes
            </button>
            <a href="{{ route('admin.users') }}" class="btn btn-light border">Cancel</a>
        </div>
    </form>
</div>
@endsection
