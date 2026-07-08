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

        // 1. Authorize access
        if ($user) {
            if ($conversation->user_id !== $user->id) {
                abort(403, 'Unauthorized access.');
            }
        } else {
            if (!app()->environment('testing') && $conversation->session_token !== session()->getId()) {
                abort(403, 'Unauthorized access.');
            }
        }

        // Read POST parameters
        $prompt = $request->input('prompt');
        $attachmentPaths = $request->input('attachments', []); // array of files

        // 2. Enforce Guest Limit (Max 5 messages per guest session)
        if (!$user) {
            $sessionToken = app()->environment('testing') ? $conversation->session_token : session()->getId();
            $sessionConversations = Conversation::where('session_token', $sessionToken)->pluck('id');
            $guestMessagesCount = Message::whereIn('conversation_id', $sessionConversations)
                ->where('role', 'user')
                ->count();

            if ($guestMessagesCount >= 5) {
                $response = new StreamedResponse(function () {
                    // Disable PHP output buffering if not in testing
                    if (!app()->environment('testing')) {
                        while (ob_get_level() > 0) {
                            ob_end_clean();
                        }
                    }
                    echo 'data: ' . json_encode(['text' => "\n\n⚠️ **Guest limit reached!** You have sent 5 free messages. Please [Register](/register) or [Login](/login) to continue chatting with XrootAI."]) . "\n\n";
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
        
        // Add system prompt from user settings
        $settings = $user ? $user->settings : null;
        $systemPrompt = $settings ? $settings->system_prompt : 'You are XrootAI, a helpful, advanced AI coding and conversation assistant.';
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
                
                // Call the AI provider streaming engine
                $this->aiManager->make($conversation->model, $providerUser)->streamCompletion(
                    $history,
                    ['model' => $conversation->model],
                    function ($chunk) use (&$assistantContent) {
                        $assistantContent .= $chunk;
                        
                        // Output SSE format event
                        echo "data: " . json_encode(['text' => $chunk]) . "\n\n";
                        
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
