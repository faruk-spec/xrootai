<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function index(Request $request)
    {
        $query = Conversation::with(['user'])->withCount('messages');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('title', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', '%' . $search . '%');
                  });
        }

        $conversations = $query->latest()->paginate(15);

        return view('admin.conversations.index', compact('conversations'));
    }

    public function show($id)
    {
        $conversation = Conversation::with(['user', 'messages'])->findOrFail($id);
        return view('admin.conversations.show', compact('conversation'));
    }

    public function destroy($id)
    {
        $conversation = Conversation::findOrFail($id);
        $title = $conversation->title;
        $conversation->delete();

        ActivityLog::log('delete_conversation', "Admin deleted conversation: {$title}");

        return redirect()->route('admin.conversations.index')->with('success', "Conversation {$title} deleted successfully.");
    }
}
