@extends('layouts.admin')

@section('title', 'Add User')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.users') }}">Users</a></li>
    <li class="breadcrumb-item active" aria-current="page">Add User</li>
@endsection

@section('content')
<div class="card border-0 p-4" style="max-width: 600px;">
    <h4 class="fw-bold mb-4">Add New User</h4>

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        
        <div class="mb-3">
            <label class="form-label fw-medium">Full Name</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">Enter the user's full display name.</div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-medium">Email Address</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">Must be a unique valid email address.</div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-medium">Assign Role</label>
            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            @error('role')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">Dynamic authorization privileges map to this role selection.</div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-medium">Password</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">Minimum 8 characters.</div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-medium">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                <i class="bi bi-check-circle"></i> Save User
            </button>
            <a href="{{ route('admin.users') }}" class="btn btn-light border">Cancel</a>
        </div>
    </form>
</div>
@endsection
