<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Show settings panel with selected tab.
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'general');
        
        $settings = [];
        foreach (SystemSetting::$defaults as $key => $default) {
            $settings[$key] = SystemSetting::get($key);
        }

        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        return view('admin.settings.index', compact('settings', 'tab', 'roles', 'permissions'));
    }

    /**
     * Update settings group configuration.
     */
    public function update(Request $request)
    {
        $group = $request->input('_group', 'general');

        if ($request->hasFile('general_chatbot_logo')) {
            $file = $request->file('general_chatbot_logo');
            $path = $file->store('branding', 'public');
            SystemSetting::set('general_chatbot_logo', '/storage/' . $path, $group, 'string');
        }
        if ($request->hasFile('general_site_icon')) {
            $file = $request->file('general_site_icon');
            $path = $file->store('branding', 'public');
            SystemSetting::set('general_site_icon', '/storage/' . $path, $group, 'string');
        }

        $allSettings = $request->except(['_token', '_group', 'general_chatbot_logo', 'general_site_icon']);

        foreach ($allSettings as $key => $value) {
            if (array_key_exists($key, SystemSetting::$defaults)) {
                $defaultValue = SystemSetting::$defaults[$key];
                $type = 'string';

                if (is_bool($defaultValue)) {
                    $type = 'boolean';
                    $value = $request->has($key);
                } elseif (is_int($defaultValue)) {
                    $type = 'integer';
                    $value = (int)$value;
                } elseif (is_double($defaultValue) || is_float($defaultValue)) {
                    $type = 'double';
                    $value = (double)$value;
                } elseif (is_array($defaultValue)) {
                    $type = 'array';
                    if (is_string($value)) {
                        $decoded = json_decode($value, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $value = $decoded;
                        } else {
                            $value = array_filter(array_map('trim', explode(',', $value)));
                        }
                    }
                }

                SystemSetting::set($key, $value, $group, $type);
            }
        }

        // Handle checkboxes/boolean false values which are omitted by the browser request
        foreach (SystemSetting::$defaults as $key => $defaultValue) {
            if (is_bool($defaultValue) && str_starts_with($key, $group . '_') && !$request->has($key)) {
                SystemSetting::set($key, false, $group, 'boolean');
            }
            if ($group === 'plans' && str_starts_with($key, 'plans_') && is_bool($defaultValue) && !$request->has($key)) {
                SystemSetting::set($key, false, $group, 'boolean');
            }
            if ($group === 'behavior' && str_starts_with($key, 'behavior_') && is_bool($defaultValue) && !$request->has($key)) {
                SystemSetting::set($key, false, $group, 'boolean');
            }
            if ($group === 'model' && str_starts_with($key, 'model_') && is_bool($defaultValue) && !$request->has($key)) {
                SystemSetting::set($key, false, $group, 'boolean');
            }
            if ($group === 'kb' && str_starts_with($key, 'kb_') && is_bool($defaultValue) && !$request->has($key)) {
                SystemSetting::set($key, false, $group, 'boolean');
            }
            if ($group === 'conv' && str_starts_with($key, 'conv_') && is_bool($defaultValue) && !$request->has($key)) {
                SystemSetting::set($key, false, $group, 'boolean');
            }
            if ($group === 'handoff' && str_starts_with($key, 'handoff_') && is_bool($defaultValue) && !$request->has($key)) {
                SystemSetting::set($key, false, $group, 'boolean');
            }
            if ($group === 'ux' && str_starts_with($key, 'ux_') && is_bool($defaultValue) && !$request->has($key)) {
                SystemSetting::set($key, false, $group, 'boolean');
            }
            if ($group === 'lang' && str_starts_with($key, 'lang_') && is_bool($defaultValue) && !$request->has($key)) {
                SystemSetting::set($key, false, $group, 'boolean');
            }
            if ($group === 'notif' && str_starts_with($key, 'notif_') && is_bool($defaultValue) && !$request->has($key)) {
                SystemSetting::set($key, false, $group, 'boolean');
            }
            if ($group === 'security' && str_starts_with($key, 'security_') && is_bool($defaultValue) && !$request->has($key)) {
                SystemSetting::set($key, false, $group, 'boolean');
            }
            if ($group === 'privacy' && str_starts_with($key, 'privacy_') && is_bool($defaultValue) && !$request->has($key)) {
                SystemSetting::set($key, false, $group, 'boolean');
            }
            if ($group === 'moderation' && str_starts_with($key, 'moderation_') && is_bool($defaultValue) && !$request->has($key)) {
                SystemSetting::set($key, false, $group, 'boolean');
            }
            if ($group === 'logging' && str_starts_with($key, 'logging_') && is_bool($defaultValue) && !$request->has($key)) {
                SystemSetting::set($key, false, $group, 'boolean');
            }
            if ($group === 'backup' && str_starts_with($key, 'backup_') && is_bool($defaultValue) && !$request->has($key)) {
                SystemSetting::set($key, false, $group, 'boolean');
            }
            if ($group === 'developer' && str_starts_with($key, 'developer_') && is_bool($defaultValue) && !$request->has($key)) {
                SystemSetting::set($key, false, $group, 'boolean');
            }
            if ($group === 'branding' && str_starts_with($key, 'branding_') && is_bool($defaultValue) && !$request->has($key)) {
                SystemSetting::set($key, false, $group, 'boolean');
            }
            if ($group === 'toggle' && str_starts_with($key, 'toggle_') && is_bool($defaultValue) && !$request->has($key)) {
                SystemSetting::set($key, false, $group, 'boolean');
            }
        }

        ActivityLog::log('update_settings', "Updated settings group: {$group}");

        return redirect()->route('admin.settings', ['tab' => $group])->with('success', 'Settings updated successfully.');
    }

    /**
     * Update role permission mappings dynamically.
     */
    public function updatePermissions(Request $request)
    {
        $validated = $request->validate([
            'roles' => 'required|array',
        ]);

        foreach ($validated['roles'] as $roleId => $permissionIds) {
            $role = Role::find($roleId);
            if ($role) {
                $role->permissions()->sync($permissionIds);
            }
        }

        ActivityLog::log('update_roles_permissions', 'Modified role and permission mappings.');

        return redirect()->route('admin.settings', ['tab' => 'roles'])->with('success', 'Roles and Permissions mapping updated successfully.');
    }
}
