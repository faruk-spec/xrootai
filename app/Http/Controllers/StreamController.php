<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Attachment;
use App\Models\User;
use App\Services\AI\AIProviderManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StreamController extends Controller
{
    protected AIProviderManager $aiManager;

    public function __construct(AIProviderManager $aiManager)
    {
        $this->aiManager = $aiManager;
    }

    public function stream(Request $request, Conversation $conversation)
    {
        $user = $request->user();

        // Check availability
        if (\App\Models\SystemSetting::get('general_maintenance_mode', false)) {
            if (!$user || $user->role !== 'admin') {
                $response = new StreamedResponse(function() {
                    echo 'data: ' . json_encode(['text' => "\n\n⚠️ **System under maintenance.** " . \App\Models\SystemSetting::get('general_maintenance_message', 'XrootAI is currently undergoing scheduled maintenance. Please check back later.')]) . "\n\n";
                    echo "data: [DONE]\n\n";
                    flush();
                });
                $response->headers->set('Content-Type', 'text/event-stream');
                return $response;
            }
        }

        if (!\App\Models\SystemSetting::get('general_enable_chatbot', true)) {
            if (!$user || $user->role !== 'admin') {
                $response = new StreamedResponse(function() {
                    echo 'data: ' . json_encode(['text' => "\n\n⚠️ " . \App\Models\SystemSetting::get('general_error_message', 'Chatbot is currently disabled.')]) . "\n\n";
                    echo "data: [DONE]\n\n";
                    flush();
                });
                $response->headers->set('Content-Type', 'text/event-stream');
                return $response;
            }
        }

        // Read POST parameters
        $prompt = $request->input('prompt');
        $attachmentPaths = $request->input('attachments', []); // array of files
        $requestedModel = $request->input('model');
        if (!empty($requestedModel) && $requestedModel !== $conversation->model) {
            $conversation->model = $requestedModel;
            $conversation->save();
        }

        // Check if file upload is enabled
        $fileUploadAllowed = \App\Models\SystemSetting::get('toggle_file_upload', true);
        if ($fileUploadAllowed && !$user) {
            $fileUploadAllowed = \App\Models\SystemSetting::get('plans_guest_file_upload', true);
        } elseif ($fileUploadAllowed && $user && $user->role === 'user') {
            // Free user inherits Pro/general uploads or plan restrictions
            $fileUploadAllowed = \App\Models\SystemSetting::get('plans_pro_file_upload', true);
        }

        if (!$fileUploadAllowed && !empty($attachmentPaths)) {
            $response = new StreamedResponse(function() {
                echo 'data: ' . json_encode(['text' => "\n\n⚠️ **File upload permission denied.** File uploads are disabled for your account plan."]) . "\n\n";
                echo "data: [DONE]\n\n";
                flush();
            });
            $response->headers->set('Content-Type', 'text/event-stream');
            return $response;
        }

        // 2. Enforce Guest Limit (Dynamic)
        $userRole = $user ? $user->role : 'guest';
        if (\Illuminate\Support\Facades\Schema::hasTable('ai_models')) {
            $dbModel = \App\Models\AIModel::with('provider')
                ->where('model_identifier', $conversation->model)
                ->where('is_active', true)
                ->whereHas('provider', function ($p) {
                    $p->where('is_active', true);
                })
                ->first();

            if ($dbModel && !empty($dbModel->allowed_roles) && !in_array(strtolower($userRole), ['admin', 'super admin'])) {
                $rolesList = array_map('strtolower', $dbModel->allowed_roles);
                if (!in_array(strtolower($userRole), $rolesList)) {
                    $response = new StreamedResponse(function() {
                        echo 'data: ' . json_encode(['text' => "\n\n⚠️ **Model access denied.** You do not have access to this model under your plan."]) . "\n\n";
                        echo "data: [DONE]\n\n";
                        flush();
                    });
                    $response->headers->set('Content-Type', 'text/event-stream');
                    return $response;
                }
            }
        }

        // Role-based limits (Input prompt length & Output max tokens)
        $userRole = $user ? strtolower($user->role) : 'guest';
        $isAdminOrSuper = in_array($userRole, ['admin', 'super admin', 'pro', 'enterprise']);

        if (!$isAdminOrSuper) {
            $promptLength = mb_strlen($prompt ?? '');
            if ($userRole === 'guest') {
                $maxPromptChars = (int) \App\Models\SystemSetting::get('ai_guest_max_prompt_chars', 1000);
                if ($promptLength > $maxPromptChars) {
                    $response = new StreamedResponse(function() use ($maxPromptChars, $promptLength) {
                        if (!app()->environment('testing')) {
                            while (ob_get_level() > 0) { ob_end_clean(); }
                        }
                        echo 'data: ' . json_encode(['text' => "\n\n⚠️ **Prompt too long for Guest Plan.** Guests can send up to {$maxPromptChars} characters per prompt (yours is {$promptLength} characters). Please [Register](/register) or [Login](/login) to submit larger prompts and generate complete code!"]) . "\n\n";
                        echo "data: [DONE]\n\n";
                        if (!app()->environment('testing') && ob_get_length()) { ob_flush(); }
                        flush();
                    });
                    $response->headers->set('Content-Type', 'text/event-stream');
                    return $response;
                }
            } elseif ($userRole === 'user') {
                $maxPromptChars = (int) \App\Models\SystemSetting::get('ai_user_max_prompt_chars', 4000);
                if ($promptLength > $maxPromptChars) {
                    $response = new StreamedResponse(function() use ($maxPromptChars, $promptLength) {
                        if (!app()->environment('testing')) {
                            while (ob_get_level() > 0) { ob_end_clean(); }
                        }
                        echo 'data: ' . json_encode(['text' => "\n\n⚠️ **Prompt too long for Free Plan.** Free accounts can send up to {$maxPromptChars} characters per prompt (yours is {$promptLength} characters). Upgrade to Pro for unlimited prompt sizes!"]) . "\n\n";
                        echo "data: [DONE]\n\n";
                        if (!app()->environment('testing') && ob_get_length()) { ob_flush(); }
                        flush();
                    });
                    $response->headers->set('Content-Type', 'text/event-stream');
                    return $response;
                }
            }
        }

        if (!$user) {
            $sessionToken = app()->environment('testing') ? $conversation->session_token : session()->getId();
            $sessionConversations = Conversation::where('session_token', $sessionToken)->pluck('id');
            $guestMessagesCount = Message::whereIn('conversation_id', $sessionConversations)
                ->where('role', 'user')
                ->count();

            $guestLimit = (int) \App\Models\SystemSetting::get('plans_guest_messages_per_session', 5);
            if ($guestMessagesCount >= $guestLimit) {
                $response = new StreamedResponse(function () use ($guestLimit) {
                    // Disable PHP output buffering if not in testing
                    if (!app()->environment('testing')) {
                        while (ob_get_level() > 0) {
                            ob_end_clean();
                        }
                    }
                    echo 'data: ' . json_encode(['text' => "\n\n⚠️ **Guest limit reached!** You have sent {$guestLimit} free messages. Please [Register](/register) or [Login](/login) to continue chatting."]) . "\n\n";
                    echo "data: [DONE]\n\n";
                    if (!app()->environment('testing') && ob_get_length()) {
                        ob_flush();
                    }
                    flush();
                });
                $response->headers->set('Content-Type', 'text/event-stream');
                $response->headers->set('Cache-Control', 'no-cache, must-revalidate');
                $response->headers->set('Connection', 'keep-alive');
                $response->headers->set('X-Accel-Buffering', 'no');
                return $response;
            }
        } else if ($user->role === 'user') {
            // Free user daily message limit check
            $freeLimit = (int) \App\Models\SystemSetting::get('plans_free_message_limit', 50);
            if ($freeLimit > 0) {
                $userMessagesCount = Message::whereHas('conversation', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->where('role', 'user')
                    ->where('created_at', '>=', now()->startOfDay())
                    ->count();

                if ($userMessagesCount >= $freeLimit) {
                    $response = new StreamedResponse(function () use ($freeLimit) {
                        if (!app()->environment('testing')) {
                            while (ob_get_level() > 0) {
                                ob_end_clean();
                            }
                        }
                        echo 'data: ' . json_encode(['text' => "\n\n⚠️ **Daily message limit reached!** You have sent {$freeLimit} messages today on your Free plan. Please upgrade to Pro for unlimited usage."]) . "\n\n";
                        echo "data: [DONE]\n\n";
                        if (!app()->environment('testing') && ob_get_length()) {
                            ob_flush();
                        }
                        flush();
                    });
                    $response->headers->set('Content-Type', 'text/event-stream');
                    $response->headers->set('Cache-Control', 'no-cache, must-revalidate');
                    $response->headers->set('Connection', 'keep-alive');
                    $response->headers->set('X-Accel-Buffering', 'no');
                    return $response;
                }
            }
        }

        // 3. Save user message to database
        $userMessage = Message::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $prompt ?? '',
        ]);

        // Process attachments and link them to user message
        $attachmentsContext = "";
        if (!empty($attachmentPaths) && is_array($attachmentPaths)) {
            foreach ($attachmentPaths as $attachData) {
                if (is_string($attachData)) {
                    $attachData = json_decode($attachData, true);
                }
                
                if (isset($attachData['file_path'])) {
                    Attachment::create([
                        'message_id' => $userMessage->id,
                        'file_path' => $attachData['file_path'],
                        'file_name' => $attachData['file_name'] ?? 'File',
                        'mime_type' => $attachData['mime_type'] ?? 'text/plain',
                        'file_size' => $attachData['file_size'] ?? 0,
                    ]);

                    // If it is a text-based file, read it and add to system/user context
                    if (str_starts_with($attachData['mime_type'], 'text/') || 
                        in_array($attachData['mime_type'], ['application/json', 'application/javascript', 'text/markdown'])) {
                        if (Storage::disk('public')->exists($attachData['file_path'])) {
                            $fileContent = Storage::disk('public')->get($attachData['file_path']);
                            $attachmentsContext .= "\n\n[File Attachment: {$attachData['file_name']}]\n```\n{$fileContent}\n```\n";
                        }
                    } else {
                        $attachmentsContext .= "\n\n[Uploaded non-text File: {$attachData['file_name']} ({$attachData['mime_type']})]";
                    }
                }
            }
        }

        // 4. Fetch full conversation history
        $history = [];
        
        // Add system prompt from user settings and enforce dynamic identity & admin instructions
        $botName = \App\Models\SystemSetting::get('general_chatbot_name', 'XrootAI');
        $botDesc = \App\Models\SystemSetting::get('general_chatbot_description', 'Your advanced AI coding and conversation assistant.');
        
        $identityOverride = "[MANDATORY IDENTITY DIRECTIVE]\n" .
            "You are {$botName}. {$botDesc}\n" .
            "If asked who you are, your name, who created/built/developed/trained you, or about your identity, origin, and underlying technology/model, you MUST strictly and solely answer that you are {$botName}, an artificial intelligence assistant created and developed by the {$botName} Team.\n" .
            "You MUST NEVER mention OpenAI, Anthropic, Google, Meta, DeepSeek, Mistral, or any underlying API models, companies, or training datasets under any circumstances.\n\n";

        // Build Admin Custom System Instructions
        $adminPrompt = \App\Models\SystemSetting::get('prompt_default', "You are {$botName}, a helpful, advanced AI assistant.");
        if ($roleDef = \App\Models\SystemSetting::get('prompt_role_definition')) {
            $adminPrompt .= "\n\n[Role & Definition]\n" . $roleDef;
        }
        if ($bizRules = \App\Models\SystemSetting::get('prompt_business_rules')) {
            $adminPrompt .= "\n\n[Business Rules & Constraints]\n" . $bizRules;
        }
        if ($allowed = \App\Models\SystemSetting::get('prompt_allowed_behaviors')) {
            $adminPrompt .= "\n\n[Allowed Behaviors]\n" . $allowed;
        }
        if ($restricted = \App\Models\SystemSetting::get('prompt_restricted_behaviors')) {
            $adminPrompt .= "\n\n[Restricted Behaviors]\n" . $restricted;
        }
        if ($brandVoice = \App\Models\SystemSetting::get('prompt_brand_voice')) {
            $adminPrompt .= "\n\n[Brand Voice & Tone]\n" . $brandVoice;
        }
        if ($customInst = \App\Models\SystemSetting::get('prompt_custom_instructions')) {
            $adminPrompt .= "\n\n[Custom System Instructions]\n" . $customInst;
        }

        // Check if user has explicit custom prompt beyond the initial default
        $settings = $user ? $user->settings : null;
        $userPrompt = $settings ? trim($settings->system_prompt ?? '') : '';
        $oldDefault = 'You are XrootAI, a helpful, advanced AI coding and conversation assistant.';
        $currentDefault = trim(\App\Models\SystemSetting::get('prompt_default', ''));
        
        if (!empty($userPrompt) && $userPrompt !== $oldDefault && $userPrompt !== $currentDefault && $userPrompt !== trim($adminPrompt)) {
            $basePrompt = $adminPrompt . "\n\n[User Custom Preferences]\n" . $userPrompt;
        } else {
            $basePrompt = $adminPrompt;
        }

        // Ensure any static occurrences of XrootAI inside prompt_default or custom instructions are replaced with the custom botName
        if ($botName !== 'XrootAI') {
            $basePrompt = str_ireplace('XrootAI', $botName, $basePrompt);
        }
        $systemPrompt = $identityOverride . $basePrompt;
        
        // Enrich prompt with AI behavior settings
        $personality = \App\Models\SystemSetting::get('behavior_personality', 'Friendly');
        $style = \App\Models\SystemSetting::get('behavior_response_style', 'Detailed');
        $customRules = \App\Models\SystemSetting::get('behavior_custom_memory_rules', '');
        
        $behaviorGuidelines = "\n\n[System Guidelines]\n- Personality Tone: {$personality}\n- Response Layout/Style: {$style}";
        if (!empty($customRules)) {
            $behaviorGuidelines .= "\n- Additional Behavior Directives: {$customRules}";
        }
        
        $systemPrompt .= $behaviorGuidelines;
        $history[] = ['role' => 'system', 'content' => $systemPrompt];

        // Add previous messages (excluding the new user message we just created)
        $previousMessages = $conversation->messages()
            ->where('id', '!=', $userMessage->id)
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($previousMessages as $msg) {
            $history[] = [
                'role' => $msg->role,
                'content' => $msg->content,
            ];
        }

        // Add the current user prompt (combining user prompt + text files uploaded as context)
        $history[] = [
            'role' => 'user',
            'content' => ($prompt ?? '') . $attachmentsContext,
        ];

        // 5. Create the empty assistant message in the database
        $assistantMessage = Message::create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => '', // Filled by the stream response
        ]);

        // 6. Return SSE Streamed Response
        $response = new StreamedResponse(function () use ($conversation, $history, $assistantMessage, $user) {
            // Disable PHP output buffering
            if (connection_aborted()) {
                return;
            }

            // Ensure output buffering is turned off if not in testing
            if (!app()->environment('testing')) {
                while (ob_get_level() > 0) {
                    ob_end_clean();
                }
            }

            $assistantContent = '';

            try {
                // Pass mock User object if guest to satisfy registry interface
                $providerUser = $user ?: new User();
                
                // Determine output max_tokens based on role
                $maxOutputTokens = 4096;
                $maxOutputChars = -1; // -1 means unlimited
                $currentRole = $user ? strtolower($user->role) : 'guest';
                $isAdminOrSuperRole = in_array($currentRole, ['admin', 'super admin', 'pro', 'enterprise']);

                if ($currentRole === 'guest') {
                    $maxOutputTokens = (int) \App\Models\SystemSetting::get('ai_guest_max_tokens', 400);
                    $maxOutputChars = (int) ($maxOutputTokens * 4.5);
                } elseif ($currentRole === 'user') {
                    $maxOutputTokens = (int) \App\Models\SystemSetting::get('ai_user_max_tokens', 1500);
                    $maxOutputChars = (int) ($maxOutputTokens * 4.5);
                } elseif ($isAdminOrSuperRole) {
                    $maxOutputTokens = (int) \App\Models\SystemSetting::get('model_max_tokens', 8192);
                }

                $options = [
                    'model' => $conversation->model,
                    'max_tokens' => $maxOutputTokens,
                ];

                $truncated = false;

                // Call the AI provider streaming engine
                $this->aiManager->make($conversation->model, $providerUser)->streamCompletion(
                    $history,
                    $options,
                    function ($chunk) use (&$assistantContent, &$truncated, $maxOutputChars, $currentRole, $maxOutputTokens) {
                        if ($truncated) {
                            return;
                        }

                        $assistantContent .= $chunk;
                        
                        // Output SSE format event
                        echo "data: " . json_encode(['text' => $chunk]) . "\n\n";
                        
                        if ($maxOutputChars > 0 && mb_strlen($assistantContent) >= $maxOutputChars) {
                            $truncated = true;
                            $truncMsg = $currentRole === 'guest'
                                ? "\n\n---\n🔒 *Response reached the maximum output length for Guest accounts (~{$maxOutputTokens} tokens). [Register or Login](/register) for full, detailed AI responses and complete code generation!*"
                                : "\n\n---\n🔒 *Response reached the maximum output length for Free accounts (~{$maxOutputTokens} tokens). Upgrade to Pro for unlimited AI response lengths and massive code generation!*";
                            
                            $assistantContent .= $truncMsg;
                            echo "data: " . json_encode(['text' => $truncMsg]) . "\n\n";
                        }

                        // Flush the output buffer to the client
                        if (ob_get_length()) {
                            ob_flush();
                        }
                        flush();
                    }
                );
            } catch (\Exception $e) {
                $errorMsg = "[Streaming Error: " . $e->getMessage() . "]";
                $assistantContent .= $errorMsg;
                echo "data: " . json_encode(['text' => $errorMsg]) . "\n\n";
                if (ob_get_length()) {
                    ob_flush();
                }
                flush();
            }

            // Save final content to message
            $assistantMessage->update([
                'content' => $assistantContent,
            ]);

            // Touch conversation to update timestamps
            $assistantMessage->conversation->touch();
            
            echo "data: [DONE]\n\n";
            if (ob_get_length()) {
                ob_flush();
            }
            flush();
        });

        // Set streaming headers
        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache, must-revalidate');
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('X-Accel-Buffering', 'no'); // Prevent Nginx proxy buffering

        return $response;
    }
}
