<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Attachment;
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
        if ($conversation->user_id !== $request->user()->id) {
            abort(403);
        }

        $user = $request->user();
        $prompt = $request->query('prompt');
        $attachmentPaths = $request->query('attachments', []); // array of file paths

        // 1. Save user message to database
        $userMessage = Message::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $prompt,
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
                        if (Storage::exists($attachData['file_path'])) {
                            $fileContent = Storage::get($attachData['file_path']);
                            $attachmentsContext .= "\n\n[File Attachment: {$attachData['file_name']}]\n```\n{$fileContent}\n```\n";
                        }
                    } else {
                        $attachmentsContext .= "\n\n[Uploaded non-text File: {$attachData['file_name']} ({$attachData['mime_type']})]";
                    }
                }
            }
        }

        // 2. Fetch full conversation history
        $history = [];
        
        // Add system prompt from user settings
        $settings = $user->settings;
        $systemPrompt = $settings ? $settings->system_prompt : 'You are a helpful AI assistant.';
        $history[] = ['role' => 'system', 'content' => $systemPrompt];

        // Add previous messages (excluding the new user message we just created, which we will format separately with attachment context)
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
            'content' => $prompt . $attachmentsContext,
        ];

        // 3. Create the empty assistant message in the database
        $assistantMessage = Message::create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => '', // Filled by the stream response
        ]);

        // 4. Return SSE Streamed Response
        $response = new StreamedResponse(function () use ($conversation, $history, $assistantMessage, $user) {
            // Disable PHP output buffering
            if (connection_aborted()) {
                return;
            }

            // Ensure output buffering is turned off
            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            $assistantContent = '';

            try {
                // Call the AI provider streaming engine
                $this->aiManager->make($conversation->model, $user)->streamCompletion(
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
