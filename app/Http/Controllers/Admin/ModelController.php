<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AIModel;
use App\Models\AIProvider;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ModelController extends Controller
{
    public function index(Request $request)
    {
        $query = AIModel::with('provider');

        if ($request->filled('provider')) {
            $query->where('provider_id', $request->provider);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('model_identifier', 'like', '%' . $request->search . '%');
        }

        $models = $query->paginate(15);
        $providers = AIProvider::all();

        return view('admin.models.index', compact('models', 'providers'));
    }

    public function create()
    {
        $providers = AIProvider::all();
        $roles = ['guest', 'user', 'pro', 'admin', 'Super Admin'];
        if (\Illuminate\Support\Facades\Schema::hasTable('roles')) {
            $dbRoles = \App\Models\Role::pluck('name')->toArray();
            $roles = array_values(array_unique(array_merge(['guest', 'user', 'pro'], $dbRoles)));
        }
        return view('admin.models.create', compact('providers', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'provider_id' => 'required|exists:ai_providers,id',
            'name' => 'required|string|max:255',
            'model_identifier' => 'required|string|max:255',
            'type' => 'required|string|in:chat,embedding,image,speech',
            'context_window' => 'required|integer|min:1',
            'max_tokens' => 'required|integer|min:1',
            'cost_per_million_input' => 'required|numeric|min:0',
            'cost_per_million_output' => 'required|numeric|min:0',
            'capabilities' => 'nullable|array',
            'allowed_roles' => 'nullable|array',
            'is_active' => 'sometimes',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['capabilities'] = [
            'vision' => $request->has('capabilities.vision'),
            'function_calling' => $request->has('capabilities.function_calling'),
            'image_gen' => $request->has('capabilities.image_gen'),
            'audio' => $request->has('capabilities.audio'),
        ];
        $validated['allowed_roles'] = array_values(array_unique(array_map('strtolower', $request->input('allowed_roles', []))));

        $model = AIModel::create($validated);

        ActivityLog::log('create_model', "Created new AI Model: {$model->name} ({$model->model_identifier})");

        return redirect()->route('admin.models.index')->with('success', "Model {$model->name} created successfully.");
    }

    public function edit(AIModel $model)
    {
        $providers = AIProvider::all();
        $roles = ['guest', 'user', 'pro', 'admin', 'super admin'];
        if (\Illuminate\Support\Facades\Schema::hasTable('roles')) {
            $dbRoles = \App\Models\Role::pluck('name')->toArray();
            $roles = array_values(array_unique(array_map('strtolower', array_merge(['guest', 'user', 'pro', 'admin', 'super admin'], $dbRoles))));
        }
        return view('admin.models.edit', compact('model', 'providers', 'roles'));
    }

    public function update(Request $request, AIModel $model)
    {
        $validated = $request->validate([
            'provider_id' => 'required|exists:ai_providers,id',
            'name' => 'required|string|max:255',
            'model_identifier' => 'required|string|max:255',
            'type' => 'required|string|in:chat,embedding,image,speech',
            'context_window' => 'required|integer|min:1',
            'max_tokens' => 'required|integer|min:1',
            'cost_per_million_input' => 'required|numeric|min:0',
            'cost_per_million_output' => 'required|numeric|min:0',
            'capabilities' => 'nullable|array',
            'allowed_roles' => 'nullable|array',
            'is_active' => 'sometimes',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['capabilities'] = [
            'vision' => $request->has('capabilities.vision'),
            'function_calling' => $request->has('capabilities.function_calling'),
            'image_gen' => $request->has('capabilities.image_gen'),
            'audio' => $request->has('capabilities.audio'),
        ];
        $validated['allowed_roles'] = array_values(array_unique(array_map('strtolower', $request->input('allowed_roles', []))));

        $model->update($validated);

        ActivityLog::log('update_model', "Updated AI Model: {$model->name}");

        return redirect()->route('admin.models.index')->with('success', "Model {$model->name} updated successfully.");
    }

    public function destroy(AIModel $model)
    {
        $name = $model->name;
        $model->delete();

        ActivityLog::log('delete_model', "Deleted AI Model: {$name}");

        return redirect()->route('admin.models.index')->with('success', "Model {$name} deleted successfully.");
    }
}
