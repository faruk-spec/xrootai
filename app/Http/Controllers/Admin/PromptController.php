<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromptTemplate;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PromptController extends Controller
{
    public function index(Request $request)
    {
        $query = PromptTemplate::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        $prompts = $query->paginate(15);

        return view('admin.prompts.index', compact('prompts'));
    }

    public function create()
    {
        return view('admin.prompts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'description' => 'nullable|string|max:1000',
            'variables' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        
        // Parse variables string into json array (comma separated -> array)
        if (!empty($validated['variables'])) {
            $validated['variables'] = array_filter(array_map('trim', explode(',', $validated['variables'])));
        } else {
            $validated['variables'] = [];
        }

        $prompt = PromptTemplate::create($validated);

        ActivityLog::log('create_prompt_template', "Created Prompt Template: {$prompt->name}");

        return redirect()->route('admin.prompts.index')->with('success', "Prompt Template {$prompt->name} created successfully.");
    }

    public function edit(PromptTemplate $prompt)
    {
        return view('admin.prompts.edit', compact('prompt'));
    }

    public function update(Request $request, PromptTemplate $prompt)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'description' => 'nullable|string|max:1000',
            'variables' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        if (isset($validated['variables'])) {
            $validated['variables'] = array_filter(array_map('trim', explode(',', $validated['variables'])));
        } else {
            $validated['variables'] = [];
        }

        $prompt->update($validated);

        ActivityLog::log('update_prompt_template', "Updated Prompt Template: {$prompt->name}");

        return redirect()->route('admin.prompts.index')->with('success', "Prompt Template {$prompt->name} updated successfully.");
    }

    public function destroy(PromptTemplate $prompt)
    {
        $name = $prompt->name;
        $prompt->delete();

        ActivityLog::log('delete_prompt_template', "Deleted Prompt Template: {$name}");

        return redirect()->route('admin.prompts.index')->with('success', "Prompt Template {$name} deleted successfully.");
    }
}
