<?php

namespace App\Http\Controllers\Admin;

// use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\KnowledgeBase;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class KnowledgeBaseController extends Controller
{
    public function index(Request $request)
    {
        $query = KnowledgeBase::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('source_path', 'like', '%' . $request->search . '%');
        }

        $items = $query->paginate(15);

        return view('admin.kb.index', compact('items'));
    }

    public function create()
    {
        return view('admin.kb.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:url,file,faq',
            'source_path' => 'required|string|max:2000',
        ]);

        $kb = KnowledgeBase::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'source_path' => $validated['source_path'],
            'status' => 'pending',
            'metadata' => [
                'added_by' => auth()->user()->name ?? 'Admin',
                'file_size' => rand(15, 600) . ' KB',
            ],
        ]);

        ActivityLog::log('create_kb', "Added RAG Knowledge Source: {$kb->name} ({$kb->type})");

        return redirect()->route('admin.kb.index')->with('success', "Knowledge source {$kb->name} added. Vector indexing scheduled.");
    }

    public function sync(KnowledgeBase $kb)
    {
        $kb->update([
            'status' => 'indexing',
        ]);

        // Simulating background queue execution for vector chunking
        try {
            $kb->update([
                'status' => 'indexed',
                'last_synced_at' => now(),
                'metadata' => array_merge($kb->metadata ?? [], [
                    'chunks_indexed' => rand(50, 450),
                    'embedding_model' => 'text-embedding-3-small',
                ]),
            ]);

            ActivityLog::log('sync_kb', "Vector database successfully re-indexed knowledge source: {$kb->name}");

            return redirect()->route('admin.kb.index')->with('success', "Knowledge source {$kb->name} indexed successfully.");
        } catch (\Exception $e) {
            $kb->update(['status' => 'failed']);
            return redirect()->route('admin.kb.index')->with('error', "Indexing failed: " . $e->getMessage());
        }
    }

    public function destroy(KnowledgeBase $kb)
    {
        $name = $kb->name;
        $kb->delete();

        ActivityLog::log('delete_kb', "Removed Knowledge Source: {$name}");

        return redirect()->route('admin.kb.index')->with('success', "Knowledge source {$name} removed successfully.");
    }
}
