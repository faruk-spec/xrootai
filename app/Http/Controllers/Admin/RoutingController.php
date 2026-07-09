<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AIRoutingRule;
use App\Models\AIModel;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class RoutingController extends Controller
{
    public function index()
    {
        $rules = AIRoutingRule::with('targetModel.provider')->orderBy('priority', 'desc')->get();
        return view('admin.routing.index', compact('rules'));
    }

    public function create()
    {
        $models = AIModel::with('provider')->where('is_active', true)->get();
        return view('admin.routing.create', compact('models'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'trigger_type' => 'required|string|in:pattern,plan,fallback',
            'pattern' => 'nullable|string|max:255',
            'target_model_id' => 'required|exists:ai_models,id',
            'priority' => 'required|integer',
            'is_active' => 'sometimes|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $rule = AIRoutingRule::create($validated);

        ActivityLog::log('create_routing_rule', "Created AI Routing Rule: {$rule->name}");

        return redirect()->route('admin.routing.index')->with('success', "Routing rule {$rule->name} created successfully.");
    }

    public function edit(AIRoutingRule $routing)
    {
        $models = AIModel::with('provider')->where('is_active', true)->get();
        return view('admin.routing.edit', compact('routing', 'models'));
    }

    public function update(Request $request, AIRoutingRule $routing)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'trigger_type' => 'required|string|in:pattern,plan,fallback',
            'pattern' => 'nullable|string|max:255',
            'target_model_id' => 'required|exists:ai_models,id',
            'priority' => 'required|integer',
            'is_active' => 'sometimes|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $routing->update($validated);

        ActivityLog::log('update_routing_rule', "Updated AI Routing Rule: {$routing->name}");

        return redirect()->route('admin.routing.index')->with('success', "Routing rule {$routing->name} updated successfully.");
    }

    public function destroy(AIRoutingRule $routing)
    {
        $name = $routing->name;
        $routing->delete();

        ActivityLog::log('delete_routing_rule', "Deleted AI Routing Rule: {$name}");

        return redirect()->route('admin.routing.index')->with('success', "Routing rule {$name} deleted successfully.");
    }
}
