<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\UserSetting;
use App\Services\AI\AIProviderManager;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    protected AIProviderManager $aiManager;

    public function __construct(AIProviderManager $aiManager)
    {
        $this->aiManager = $aiManager;
    }

    protected function authorizeAccess(Request $request, Conversation $conversation)
    {
        $user = $request->user();
        if ($user) {
            if ($conversation->user_id !== $user->id) {
                abort(403, 'Unauthorized access.');
            }
        } else {
            if (!app()->environment('testing') && $conversation->session_token !== session()->getId()) {
                abort(403, 'Unauthorized access.');
            }
        }
    }

    public function index(Request $request)
    {
        $availabilityResponse = $this->checkChatbotAvailability($request);
        if ($availabilityResponse) {
            return $availabilityResponse;
        }

        $user = $request->user();
        
        if ($user) {
            // Ensure user settings exist
            $settings = $user->settings ?: UserSetting::create([
                'user_id' => $user->id,
                'theme' => 'system',
                'default_model' => \App\Models\SystemSetting::get('model_default', 'mock'),
                'system_prompt' => \App\Models\SystemSetting::get('prompt_default', 'You are XrootAI, a helpful, advanced AI coding and conversation assistant.'),
            ]);

            $conversations = $user->conversations()
                ->orderByRaw('pinned_at IS NULL')
                ->orderBy('pinned_at', 'desc')
                ->orderBy('updated_at', 'desc')
                ->get();

            $apiKeys = $user->apiKeys()->pluck('encrypted_key', 'provider')->all();
        } else {
            // Guest mode
            $settings = new UserSetting([
                'theme' => 'system',
                'default_model' => \App\Models\SystemSetting::get('model_default', 'mock'),
                'system_prompt' => \App\Models\SystemSetting::get('prompt_default', 'You are XrootAI, a helpful, advanced AI coding and conversation assistant.'),
            ]);

            $conversations = Conversation::where('session_token', session()->getId())
                ->orderByRaw('pinned_at IS NULL')
                ->orderBy('pinned_at', 'desc')
                ->orderBy('updated_at', 'desc')
                ->get();

            $apiKeys = [];
        }

        $allowedModels = \App\Models\SystemSetting::get('model_available', ['mock']);
        $models = array_values(array_filter($this->aiManager->getAllModels(), function ($model) use ($allowedModels) {
            return in_array($model['id'], $allowedModels);
        }));

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
        $availabilityResponse = $this->checkChatbotAvailability($request);
        if ($availabilityResponse) {
            return $availabilityResponse;
        }

        $request->validate([
            'model' => ['required', 'string'],
        ]);

        $user = $request->user();

        $conversation = Conversation::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'user_id' => $user ? $user->id : null,
            'session_token' => $user ? null : session()->getId(),
            'title' => 'New Chat',
            'model' => $request->model,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'uuid' => $conversation->uuid,
                'title' => $conversation->title,
                'model' => $conversation->model,
            ]);
        }

        return redirect()->route('chats.show', $conversation->uuid);
    }

    public function show(Request $request, $uuid)
    {
        $availabilityResponse = $this->checkChatbotAvailability($request);
        if ($availabilityResponse) {
            return $availabilityResponse;
        }

        $user = $request->user();
        
        $conversation = Conversation::where('uuid', $uuid)->firstOrFail();
        $this->authorizeAccess($request, $conversation);

        if ($user) {
            $settings = $user->settings ?: UserSetting::create([
                'user_id' => $user->id,
                'theme' => 'system',
                'default_model' => \App\Models\SystemSetting::get('model_default', 'mock'),
                'system_prompt' => \App\Models\SystemSetting::get('prompt_default', 'You are XrootAI, a helpful, advanced AI coding and conversation assistant.'),
            ]);

            $conversations = $user->conversations()
                ->orderByRaw('pinned_at IS NULL')
                ->orderBy('pinned_at', 'desc')
                ->orderBy('updated_at', 'desc')
                ->get();

            $apiKeys = $user->apiKeys()->pluck('encrypted_key', 'provider')->all();
        } else {
            $settings = new UserSetting([
                'theme' => 'system',
                'default_model' => \App\Models\SystemSetting::get('model_default', 'mock'),
                'system_prompt' => \App\Models\SystemSetting::get('prompt_default', 'You are XrootAI, a helpful, advanced AI coding and conversation assistant.'),
            ]);

            $conversations = Conversation::where('session_token', session()->getId())
                ->orderByRaw('pinned_at IS NULL')
                ->orderBy('pinned_at', 'desc')
                ->orderBy('updated_at', 'desc')
                ->get();

            $apiKeys = [];
        }

        $allowedModels = \App\Models\SystemSetting::get('model_available', ['mock']);
        $models = array_values(array_filter($this->aiManager->getAllModels(), function ($model) use ($allowedModels) {
            return in_array($model['id'], $allowedModels);
        }));
        
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

    protected function checkChatbotAvailability(Request $request)
    {
        $user = $request->user();
        if ($user && $user->role === 'admin') {
            return null;
        }

        if (\App\Models\SystemSetting::get('general_maintenance_mode', false)) {
            return response()->view('errors.maintenance', [], 503);
        }

        if (!\App\Models\SystemSetting::get('general_enable_chatbot', true)) {
            abort(503, \App\Models\SystemSetting::get('general_error_message', 'Chatbot is currently disabled.'));
        }

        return null;
    }

    public function destroy(Request $request, Conversation $conversation)
    {
        $this->authorizeAccess($request, $conversation);
        $conversation->delete();

        return response()->json(['success' => true]);
    }

    public function rename(Request $request, Conversation $conversation)
    {
        $this->authorizeAccess($request, $conversation);

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
        $this->authorizeAccess($request, $conversation);

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
