<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Conversation;
use App\Models\SystemSetting;
use App\Models\UserSetting;
use App\Services\AI\AIProviderManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    protected AIProviderManager $aiManager;

    public function __construct(AIProviderManager $aiManager)
    {
        $this->aiManager = $aiManager;
    }

    protected function getAvailableModels($userRole): array
    {
        if (\Illuminate\Support\Facades\Schema::hasTable('ai_models')) {
            $dbModels = \App\Models\AIModel::with('provider')
                ->where('is_active', true)
                ->whereHas('provider', function ($query) {
                    $query->where('is_active', true);
                })
                ->orderBy('id', 'asc')
                ->get();

            if ($dbModels->isNotEmpty()) {
                $models = [];
                foreach ($dbModels as $model) {
                    $isAllowed = true;
                    if (!empty($model->allowed_roles) && !in_array(strtolower($userRole), ['admin', 'super admin'])) {
                        $rolesList = array_map('strtolower', $model->allowed_roles);
                        if (!in_array(strtolower($userRole), $rolesList)) {
                            $isAllowed = false;
                        }
                    }
                    $models[] = [
                        'id' => $model->model_identifier,
                        'name' => $model->name . (!$isAllowed ? ' 🔒 (Upgrade Plan)' : ''),
                        'context' => $model->context_window,
                        'provider' => $model->provider ? $model->provider->slug : 'mock',
                        'is_allowed' => $isAllowed,
                    ];
                }
                if (!empty($models)) {
                    return $models;
                }
            }
        }

        return $this->aiManager->getAllModels();
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

        $userRole = $user ? $user->role : 'guest';
        $models = $this->getAvailableModels($userRole);

        if (!empty($models) && !in_array($settings->default_model, array_column($models, 'id'))) {
            $settings->default_model = $models[0]['id'];
        }

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
        $userRole = $user ? $user->role : 'guest';
        $models = $this->getAvailableModels($userRole);
        $modelToSave = $request->model;
        if (!empty($models) && !in_array($modelToSave, array_column($models, 'id'))) {
            $modelToSave = $models[0]['id'];
        }

        $conversation = Conversation::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'user_id' => $user ? $user->id : null,
            'session_token' => $user ? null : session()->getId(),
            'title' => 'New Chat',
            'model' => $modelToSave,
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

        $userRole = $user ? $user->role : 'guest';
        $models = $this->getAvailableModels($userRole);
        
        if (!empty($models) && !in_array($settings->default_model, array_column($models, 'id'))) {
            $settings->default_model = $models[0]['id'];
        }
        if (!empty($models) && !in_array($conversation->model, array_column($models, 'id'))) {
            $conversation->model = $models[0]['id'];
        }

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
        if ($user && ($user->isSuperAdmin() || in_array(strtolower($user->role), ['admin', 'super admin']))) {
            return null;
        }

        if (SystemSetting::get('general_maintenance_mode', false)) {
            return response()->view('errors.maintenance', [], 503);
        }

        if (!SystemSetting::get('general_enable_chatbot', true)) {
            abort(503, SystemSetting::get('general_error_message', 'Chatbot is currently disabled.'));
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
        $user = $request->user();
        $userRole = $user ? strtolower($user->role) : 'guest';
        $isAdminOrSuper = $user && ($user->isSuperAdmin() || in_array($userRole, ['admin', 'super admin', 'pro', 'enterprise']));

        if (!$isAdminOrSuper) {
            $fileUploadAllowed = SystemSetting::get('toggle_file_upload', true);
            if ($fileUploadAllowed && !$user) {
                $fileUploadAllowed = SystemSetting::get('plans_guest_file_upload', true);
            } elseif ($fileUploadAllowed && $user && $userRole === 'user') {
                $fileUploadAllowed = SystemSetting::get('plans_pro_file_upload', true);
            }

            if (!$fileUploadAllowed) {
                return response()->json(['success' => false, 'message' => 'File uploads are disabled for your account plan.'], 403);
            }
        }

        $request->validate([
            'file' => ['required', 'file', 'max:10240'], // Max 10MB
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        $blockedExtensions = ['php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'phps', 'phar', 'js', 'mjs', 'exe', 'bat', 'cmd', 'sh', 'bash', 'bin', 'dll', 'svg', 'html', 'htm', 'htaccess'];

        if (in_array($extension, $blockedExtensions)) {
            return response()->json(['success' => false, 'message' => 'Security check failed: Executable or dangerous file types are strictly prohibited.'], 422);
        }

        $fileName = $file->getClientOriginalName();
        
        // Save file in local (private) storage instead of public for security/privacy
        $path = $file->store('attachments', 'local');

        return response()->json([
            'success' => true,
            'file_path' => $path,
            'file_name' => $fileName,
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
        ]);
    }

    public function downloadAttachment(Request $request, Attachment $attachment)
    {
        $conversation = $attachment->message?->conversation;
        if (!$conversation) {
            abort(404, 'Attachment not found.');
        }

        $user = $request->user();
        if ($user) {
            if (!$user->isSuperAdmin() && !in_array(strtolower($user->role), ['admin', 'super admin']) && $conversation->user_id !== $user->id) {
                abort(403, 'Unauthorized access to file attachment.');
            }
        } else {
            if (!app()->environment('testing') && $conversation->session_token !== session()->getId()) {
                abort(403, 'Unauthorized access to file attachment.');
            }
        }

        $disk = Storage::disk('local');
        if (!$disk->exists($attachment->file_path)) {
            $disk = Storage::disk('public');
            if (!$disk->exists($attachment->file_path)) {
                abort(404, 'File not found on server.');
            }
        }

        return $disk->download($attachment->file_path, $attachment->file_name);
    }
}
