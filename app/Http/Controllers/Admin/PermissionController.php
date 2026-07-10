<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions.
     */
    public function index(Request $request)
    {
        $query = Permission::withCount('roles');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $permissions = $query->orderBy('name')->paginate(20);

        return view('admin.permissions.index', compact('permissions'));
    }

    /**
     * Store a newly created permission in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80', 'unique:permissions,name', 'regex:/^[a-z0-9\-_\.]+$/i'],
            'description' => ['nullable', 'string', 'max:255'],
        ], [
            'name.regex' => 'Permission name must only contain alphanumeric characters, hyphens, and underscores (e.g., manage-billing or view-reports).'
        ]);

        $permission = Permission::create([
            'name' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
        ]);

        // Automatically assign new permission to Super Admin role
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->permissions()->attach($permission->id);
        }

        ActivityLog::log(
            'create_permission',
            "Created System Permission: {$permission->name}",
            Auth::id()
        );

        return redirect()->route('admin.permissions.index')
            ->with('success', "Permission '{$permission->name}' created and assigned to Super Admin successfully.");
    }

    /**
     * Update the specified permission.
     */
    public function update(Request $request, Permission $permission)
    {
        // Protect core default permissions
        $corePerms = [
            'manage-users', 'manage-ai', 'manage-prompts', 'manage-kb',
            'view-analytics', 'manage-settings', 'view-logs', 'human-handoff'
        ];

        $rules = [
            'description' => ['nullable', 'string', 'max:255'],
        ];

        if (!in_array($permission->name, $corePerms)) {
            $rules['name'] = ['required', 'string', 'max:80', "unique:permissions,name,{$permission->id}", 'regex:/^[a-z0-9\-_\.]+$/i'];
        }

        $validated = $request->validate($rules);

        if (isset($validated['name']) && !in_array($permission->name, $corePerms)) {
            $permission->name = Str::slug($validated['name']);
        }
        $permission->description = $validated['description'] ?? $permission->description;
        $permission->save();

        ActivityLog::log(
            'update_permission',
            "Updated Permission: {$permission->name}",
            Auth::id()
        );

        return back()->with('success', "Permission '{$permission->name}' updated successfully.");
    }

    /**
     * Remove the specified permission from storage.
     */
    public function destroy(Permission $permission)
    {
        $corePerms = [
            'manage-users', 'manage-ai', 'manage-prompts', 'manage-kb',
            'view-analytics', 'manage-settings', 'view-logs', 'human-handoff'
        ];

        if (in_array($permission->name, $corePerms)) {
            return back()->with('error', "The '{$permission->name}' permission is a core system requirement and cannot be deleted.");
        }

        $name = $permission->name;
        $permission->delete();

        ActivityLog::log(
            'delete_permission',
            "Deleted custom Permission: {$name}",
            Auth::id()
        );

        return redirect()->route('admin.permissions.index')
            ->with('success', "Permission '{$name}' deleted successfully.");
    }
}
