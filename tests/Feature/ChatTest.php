<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Attachment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_can_access_chat_dashboard(): void
    {
        $response = $this->actingAs($this->user)->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('chat');
        $response->assertViewHasAll(['settings', 'conversations', 'models', 'activeConversation', 'messages']);
    }

    public function test_user_can_create_new_conversation(): void
    {
        $response = $this->actingAs($this->user)->post('/chats', [
            'model' => 'mock-default'
        ]);

        $conversation = Conversation::first();

        $this->assertNotNull($conversation);
        $this->assertEquals($this->user->id, $conversation->user_id);
        $this->assertEquals('mock-default', $conversation->model);

        $response->assertRedirect(route('chats.show', ['conversation' => $conversation->uuid]));
    }

    public function test_user_can_view_conversation(): void
    {
        $conversation = Conversation::create([
            'user_id' => $this->user->id,
            'uuid' => 'test-uuid-1234',
            'title' => 'Test Conversation',
            'model' => 'mock-default',
        ]);

        $response = $this->actingAs($this->user)->get(route('chats.show', ['conversation' => $conversation->uuid]));

        $response->assertStatus(200);
        $response->assertViewIs('chat');
        $response->assertViewHas('activeConversation', $conversation);
    }

    public function test_user_cannot_view_another_users_conversation(): void
    {
        $otherUser = User::factory()->create();
        $conversation = Conversation::create([
            'user_id' => $otherUser->id,
            'uuid' => 'test-uuid-5678',
            'title' => 'Other Conversation',
            'model' => 'mock-default',
        ]);

        $response = $this->actingAs($this->user)->get(route('chats.show', ['conversation' => $conversation->uuid]));

        $response->assertStatus(403);
    }

    public function test_user_can_pin_and_unpin_conversation(): void
    {
        $conversation = Conversation::create([
            'user_id' => $this->user->id,
            'uuid' => 'test-uuid-1234',
            'title' => 'Test Conversation',
            'model' => 'mock-default',
        ]);

        $this->assertNull($conversation->pinned_at);

        $response = $this->actingAs($this->user)->post(route('chats.pin', ['conversation' => $conversation->uuid]));

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'pinned' => true]);

        $conversation->refresh();
        $this->assertNotNull($conversation->pinned_at);

        $response = $this->actingAs($this->user)->post(route('chats.pin', ['conversation' => $conversation->uuid]));

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'pinned' => false]);

        $conversation->refresh();
        $this->assertNull($conversation->pinned_at);
    }

    public function test_user_can_rename_conversation(): void
    {
        $conversation = Conversation::create([
            'user_id' => $this->user->id,
            'uuid' => 'test-uuid-1234',
            'title' => 'Test Conversation',
            'model' => 'mock-default',
        ]);

        $response = $this->actingAs($this->user)->patch(route('chats.rename', ['conversation' => $conversation->uuid]), [
            'title' => 'New Conversation Title'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'title' => 'New Conversation Title']);

        $conversation->refresh();
        $this->assertEquals('New Conversation Title', $conversation->title);
    }

    public function test_user_can_delete_conversation(): void
    {
        $conversation = Conversation::create([
            'user_id' => $this->user->id,
            'uuid' => 'test-uuid-1234',
            'title' => 'Test Conversation',
            'model' => 'mock-default',
        ]);

        $response = $this->actingAs($this->user)->delete(route('chats.destroy', ['conversation' => $conversation->uuid]));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Soft deletes
        $this->assertSoftDeleted('conversations', [
            'id' => $conversation->id
        ]);
    }

    public function test_user_can_upload_attachment(): void
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->create('document.txt', 100, 'text/plain');

        $response = $this->actingAs($this->user)->post(route('attachments.upload'), [
            'file' => $file
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'file_name' => 'document.txt',
            'mime_type' => 'text/plain',
        ]);

        $filePath = $response->json('file_path');
        Storage::disk('local')->assertExists($filePath);
    }

    public function test_user_can_stream_completions(): void
    {
        $conversation = Conversation::create([
            'user_id' => $this->user->id,
            'uuid' => 'test-uuid-1234',
            'title' => 'Test Conversation',
            'model' => 'mock-default',
        ]);

        // Act as user, request SSE stream.
        $response = $this->actingAs($this->user)->get(route('chats.stream', [
            'conversation' => $conversation->uuid,
            'prompt' => 'Hello mock AI',
        ]));

        $response->assertHeader('Content-Type', 'text/event-stream; charset=UTF-8');

        // Trigger streamed response content callback execution
        ob_start();
        $response->baseResponse->sendContent();
        if (ob_get_level() > 0) {
            ob_end_clean();
        }

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => 'Hello mock AI',
        ]);

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
        ]);

        $assistantMessage = Message::where('conversation_id', $conversation->id)->where('role', 'assistant')->first();
        $this->assertNotNull($assistantMessage);
        $this->assertStringContainsString('simulated AI assistant', $assistantMessage->content);
    }
}
