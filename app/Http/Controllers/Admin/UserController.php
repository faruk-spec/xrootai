<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount('conversations');

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
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        // Sync role pivots
        $roleRecord = Role::where('name', $validated['role'])->first();
        if ($roleRecord) {
            $user->roles()->sync([$roleRecord->id]);
        }

        ActivityLog::log('create_user', "Created User: {$user->name} ({$user->email})");

        return redirect()->route('admin.users')->with('success', "User {$user->name} created successfully.");
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        // For backwards compatibility with the legacy role-only endpoint tests:
        if (!$request->has('name') && !$request->has('email') && $request->has('role')) {
            if ($user->id === $request->user()->id) {
                return redirect()->back()->with('error', 'You cannot change your own role.');
            }

            $request->validate([
                'role' => 'required|string|in:admin,user,Super Admin',
            ]);

            $user->update(['role' => $request->role]);

            $roleRecord = Role::where('name', $request->role)->first();
            if ($roleRecord) {
                $user->roles()->sync([$roleRecord->id]);
            }

            ActivityLog::log('update_user_role', "Updated role for user {$user->name} to {$request->role}");

            return redirect()->back()->with('success', 'User role updated successfully.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$user->id}",
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string',
        ]);

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
        $roleRecord = Role::where('name', $validated['role'])->first();
        if ($roleRecord) {
            $user->roles()->sync([$roleRecord->id]);
        }

        ActivityLog::log('update_user', "Updated User settings: {$user->name}");

        return redirect()->route('admin.users')->with('success', "User {$user->name} updated successfully.");
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return redirect()->back()->with('error', 'You cannot delete yourself.');
        }

        $name = $user->name;
        $user->delete();

        ActivityLog::log('delete_user', "Deleted User: {$name}");

        return redirect()->route('admin.users')->with('success', "User {$name} deleted successfully.");
    }
}
