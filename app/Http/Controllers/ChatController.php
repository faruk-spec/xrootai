<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Attachment;
use App\Models\UserSetting;
use App\Services\AI\AIProviderManager;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    protected AIProviderManager $aiManager;

    public function __construct(AIProviderManager $aiManager)
    {
        $this->aiManager = $aiManager;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        
        // Ensure user settings exist
        $settings = $user->settings ?: UserSetting::create([
            'user_id' => $user->id,
            'theme' => 'system',
            'default_model' => 'mock',
            'system_prompt' => 'You are XrootAI, a helpful, advanced AI coding and conversation assistant.',
        ]);

        $conversations = $user->conversations()
            ->orderByRaw('pinned_at IS NULL')
            ->orderBy('pinned_at', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();

        $models = $this->aiManager->getAllModels();
        $apiKeys = $user->apiKeys()->pluck('encrypted_key', 'provider')->all();

        return view('chat', [
            'settings' => $settings,
            'conversations' => $conversations,
            'models' => $models,
            'apiKeys' => $apiKeys,
            'activeConversation' => null,
            'messages' => [],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'model' => ['required', 'string'],
        ]);

        $conversation = Conversation::create([
            'user_id' => $request->user()->id,
            'uuid' => (string) Str::uuid(),
            'title' => 'New Chat',
            'model' => $request->model,
        ]);

        return redirect()->route('chats.show', ['conversation' => $conversation->uuid]);
    }

    public function show(Request $request, Conversation $conversation)
    {
        // Ensure conversation belongs to user
        if ($conversation->user_id !== $request->user()->id) {
            abort(403);
        }

        $user = $request->user();
        $settings = $user->settings ?: UserSetting::create([
            'user_id' => $user->id,
            'theme' => 'system',
            'default_model' => 'mock',
            'system_prompt' => 'You are XrootAI, a helpful, advanced AI coding and conversation assistant.',
        ]);

        $conversations = $user->conversations()
            ->orderByRaw('pinned_at IS NULL')
            ->orderBy('pinned_at', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();

        $models = $this->aiManager->getAllModels();
        $apiKeys = $user->apiKeys()->pluck('encrypted_key', 'provider')->all();
        
        $messages = $conversation->messages()
            ->with('attachments')
            ->get();

        return view('chat', [
            'settings' => $settings,
            'conversations' => $conversations,
            'models' => $models,
            'apiKeys' => $apiKeys,
            'activeConversation' => $conversation,
            'messages' => $messages,
        ]);
    }

    public function destroy(Request $request, Conversation $conversation)
    {
        if ($conversation->user_id !== $request->user()->id) {
            abort(403);
        }

        $conversation->delete();

        return response()->json(['success' => true]);
    }

    public function rename(Request $request, Conversation $conversation)
    {
        if ($conversation->user_id !== $request->user()->id) {
            abort(403);
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $conversation->update([
            'title' => $request->title,
        ]);

        return response()->json(['success' => true, 'title' => $conversation->title]);
    }

    public function pin(Request $request, Conversation $conversation)
    {
        if ($conversation->user_id !== $request->user()->id) {
            abort(403);
        }

        $conversation->update([
            'pinned_at' => $conversation->pinned_at ? null : now(),
        ]);

        return response()->json([
            'success' => true,
            'pinned' => $conversation->pinned_at !== null
        ]);
    }

    public function uploadAttachment(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:5120'], // Max 5MB
        ]);

        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        
        // Save file in public disk so it is accessible via the storage symlink
        $path = $file->store('attachments', 'public');

        return response()->json([
            'success' => true,
            'file_path' => $path,
            'file_name' => $fileName,
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
        ]);
    }
}
