<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\ActivityLog;
use App\Services\EmailVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles')->withCount('conversations');

        if ($request->filled('role')) {
            $roleVal = $request->role;
            $query->where(function($q) use ($roleVal) {
                $q->where('role', $roleVal)
                  ->orWhereHas('roles', function($r) use ($roleVal) {
                      $r->where('name', $roleVal);
                  });
            });
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        $users = $query->paginate(15);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        // Sync role pivots (support multi-select array or fallback to primary string role)
        if (!empty($validated['roles'])) {
            $user->roles()->sync($validated['roles']);
        } else {
            $roleRecord = Role::where('name', $validated['role'])->first();
            if ($roleRecord) {
                $user->roles()->sync([$roleRecord->id]);
            }
        }

        ActivityLog::log('create_user', "Created User: {$user->name} ({$user->email})", $request->user()->id, null, $user->toArray());

        return redirect()->route('admin.users')->with('success', "User {$user->name} created successfully.");
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles()->pluck('roles.id')->toArray();
        return view('admin.users.edit', compact('user', 'roles', 'userRoles'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->isSuperAdmin() && !$request->user()->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Only Super Admins can modify a Super Admin account.');
        }

        // For backwards compatibility with the legacy role-only endpoint tests:
        if (!$request->has('name') && !$request->has('email') && $request->has('role')) {
            if ($user->id === $request->user()->id && $request->role !== $user->role) {
                return redirect()->back()->with('error', 'You cannot change your own role.');
            }

            $request->validate([
                'role' => 'required|string',
            ]);

            $oldValues = ['role' => $user->role];
            $user->update(['role' => $request->role]);

            $roleRecord = Role::where('name', $request->role)->first();
            if ($roleRecord) {
                $user->roles()->sync([$roleRecord->id]);
            }

            ActivityLog::log('update_user_role', "Updated role for user {$user->name} to {$request->role}", $request->user()->id, $oldValues, ['role' => $request->role]);

            return redirect()->back()->with('success', 'User role updated successfully.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$user->id}",
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        if ($user->id === $request->user()->id && $validated['role'] !== $user->role) {
            return redirect()->back()->with('error', 'You cannot change your own role.');
        }

        $oldValues = $user->only(['name', 'email', 'role']);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        // Sync dynamic roles relation
        if (!empty($validated['roles'])) {
            $user->roles()->sync($validated['roles']);
        } else {
            $roleRecord = Role::where('name', $validated['role'])->first();
            if ($roleRecord) {
                $user->roles()->sync([$roleRecord->id]);
            }
        }

        $newValues = $user->only(['name', 'email', 'role']);
        ActivityLog::log('update_user', "Updated User settings: {$user->name}", $request->user()->id, $oldValues, $newValues);

        return redirect()->route('admin.users')->with('success', "User {$user->name} updated successfully.");
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return redirect()->back()->with('error', 'You cannot delete yourself.');
        }

        if ($user->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Super Admin accounts cannot be deleted.');
        }

        $name = $user->name;
        $oldValues = $user->toArray();
        $user->delete();

        ActivityLog::log('delete_user', "Deleted User: {$name}", $request->user()->id, $oldValues, null);

        return redirect()->route('admin.users')->with('success', "User {$name} deleted successfully.");
    }

    public function verifyManually(Request $request, User $user)
    {
        $result = EmailVerificationService::verifyManually($user, $request->user()->id);
        return redirect()->back()->with('success', $result['message']);
    }

    public function resendVerification(Request $request, User $user)
    {
        $result = EmailVerificationService::generateAndSend($user);
        if ($result['status']) {
            return redirect()->back()->with('success', "A fresh verification code and link have been dispatched to {$user->email}.");
        }
        return redirect()->back()->with('error', $result['message']);
    }

    public function approveUser(Request $request, User $user)
    {
        $oldValues = ['is_approved' => $user->is_approved, 'status' => $user->status];
        $user->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => $request->user()->id,
            'status' => 'active',
        ]);
        $newValues = ['is_approved' => true, 'status' => 'active'];

        ActivityLog::log('approve_user', "Admin approved account for user {$user->name} ({$user->email})", $request->user()->id, $oldValues, $newValues);

        return redirect()->back()->with('success', "User {$user->name} has been approved and activated.");
    }

    public function suspendUser(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return redirect()->back()->with('error', 'You cannot suspend your own account.');
        }

        if ($user->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Super Admin accounts cannot be suspended.');
        }

        $oldValues = ['is_approved' => $user->is_approved, 'status' => $user->status];
        $user->update([
            'status' => 'suspended',
            'is_approved' => false,
        ]);
        $newValues = ['is_approved' => false, 'status' => 'suspended'];

        ActivityLog::log('suspend_user', "Admin suspended account for user {$user->name} ({$user->email})", $request->user()->id, $oldValues, $newValues);

        return redirect()->back()->with('success', "User {$user->name} has been suspended.");
    }
}
