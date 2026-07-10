<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    /**
     * Display a listing of roles along with user count and permission matrix.
     */
    public function index(Request $request)
    {
        $query = Role::withCount(['users', 'permissions']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $roles = $query->orderBy('id')->paginate(15);
        foreach ($roles as $role) {
            $role->users_count = \App\Models\User::where(function ($q) use ($role) {
                $q->where(\Illuminate\Support\Facades\DB::raw('LOWER(role)'), strtolower($role->name))
                  ->orWhereHas('roles', function ($r) use ($role) {
                      $r->where('roles.id', $role->id);
                  });
            })->count();
        }

        $allPermissions = Permission::orderBy('name')->get();

        return view('admin.roles.index', compact('roles', 'allPermissions'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $permissions = Permission::orderBy('name')->get();
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:roles,name'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        if (!empty($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        ActivityLog::log(
            'create_role',
            "Created Role: {$role->name} with " . count($validated['permissions'] ?? []) . " permissions",
            Auth::id()
        );

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' created and permissions assigned successfully.");
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get();
        $rolePermissions = $role->permissions()->pluck('permissions.id')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified role and its permission assignments in storage.
     */
    public function update(Request $request, Role $role)
    {
        // Protected check: Super Admin name cannot be changed to prevent lockouts
        $rules = [
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ];

        if ($role->name !== 'Super Admin') {
            $rules['name'] = ['required', 'string', 'max:100', "unique:roles,name,{$role->id}"];
        }

        $validated = $request->validate($rules);

        if (isset($validated['name']) && $role->name !== 'Super Admin') {
            $role->name = $validated['name'];
        }
        $role->description = $validated['description'] ?? $role->description;
        $role->save();

        // If this is Super Admin, enforce that all permissions are always attached
        if ($role->name === 'Super Admin') {
            $allPermIds = Permission::pluck('id')->toArray();
            $role->permissions()->sync($allPermIds);
        } else {
            $role->permissions()->sync($validated['permissions'] ?? []);
        }

        ActivityLog::log(
            'update_role',
            "Updated Role: {$role->name} permissions (" . count($validated['permissions'] ?? []) . " active)",
            Auth::id()
        );

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' updated successfully.");
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role)
    {
        // Safeguard default core roles
        $protectedRoles = ['Super Admin', 'Admin', 'User', 'user'];
        if (in_array($role->name, $protectedRoles)) {
            return back()->with('error', "The '{$role->name}' role is a core system role and cannot be deleted.");
        }

        $assignedCount = \App\Models\User::where(function ($q) use ($role) {
            $q->where(\Illuminate\Support\Facades\DB::raw('LOWER(role)'), strtolower($role->name))
              ->orWhereHas('roles', function ($r) use ($role) {
                  $r->where('roles.id', $role->id);
              });
        })->count();

        if ($assignedCount > 0) {
            return back()->with('error', "Cannot delete role '{$role->name}' because it has {$assignedCount} assigned users. Please reassign those users first.");
        }

        $name = $role->name;
        $role->delete();

        ActivityLog::log(
            'delete_role',
            "Deleted custom Role: {$name}",
            Auth::id()
        );

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$name}' deleted successfully.");
    }
}
