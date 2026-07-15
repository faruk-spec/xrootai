<?php

namespace Tests\Feature;

use App\Models\Attachment;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\AIQuotaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AIQuotaSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        Storage::fake('local');
        Storage::fake('public');
    }

    public function test_concurrent_stream_lock_blocks_second_stream(): void
    {
        $user = User::factory()->create(['role' => 'User']);
        SystemSetting::set('plans_concurrent_streams_limit', 1);

        $lock1 = AIQuotaService::acquireConcurrentStreamLock($user, null);
        $this->assertTrue($lock1['allowed']);

        $lock2 = AIQuotaService::acquireConcurrentStreamLock($user, null);
        $this->assertFalse($lock2['allowed']);
        $this->assertStringContainsString('Concurrent stream limit reached', $lock2['message']);

        AIQuotaService::releaseConcurrentStreamLock($user, null);
        $lock3 = AIQuotaService::acquireConcurrentStreamLock($user, null);
        $this->assertTrue($lock3['allowed']);
    }

    public function test_super_admin_bypasses_concurrent_stream_lock(): void
    {
        $superAdmin = User::factory()->create(['role' => 'Super Admin']);
        SystemSetting::set('plans_concurrent_streams_limit', 1);

        $lock1 = AIQuotaService::acquireConcurrentStreamLock($superAdmin, null);
        $lock2 = AIQuotaService::acquireConcurrentStreamLock($superAdmin, null);
        $lock3 = AIQuotaService::acquireConcurrentStreamLock($superAdmin, null);

        $this->assertTrue($lock1['allowed']);
        $this->assertTrue($lock2['allowed']);
        $this->assertTrue($lock3['allowed']);
    }

    public function test_guest_message_quota_enforced(): void
    {
        SystemSetting::set('plans_guest_messages_per_session', 2);
        $token = 'test-session-123';

        AIQuotaService::recordUsage(null, $token);
        $check1 = AIQuotaService::checkCanStream(null, $token);
        $this->assertTrue($check1['allowed']);

        AIQuotaService::recordUsage(null, $token);
        $check2 = AIQuotaService::checkCanStream(null, $token);
        $this->assertFalse($check2['allowed']);
        $this->assertStringContainsString('Guest limit reached', $check2['message']);
    }

    public function test_free_user_daily_message_quota_enforced(): void
    {
        $user = User::factory()->create(['role' => 'User']);
        SystemSetting::set('plans_free_message_limit', 2);

        AIQuotaService::recordUsage($user, null, 100);
        $check1 = AIQuotaService::checkCanStream($user, null);
        $this->assertTrue($check1['allowed']);

        AIQuotaService::recordUsage($user, null, 100);
        $check2 = AIQuotaService::checkCanStream($user, null);
        $this->assertFalse($check2['allowed']);
        $this->assertStringContainsString('Daily message limit reached', $check2['message']);
    }

    public function test_pro_user_has_higher_concurrent_stream_limit(): void
    {
        $proUser = User::factory()->create(['role' => 'Pro']);
        SystemSetting::set('plans_concurrent_streams_limit', 1);

        $lock1 = AIQuotaService::acquireConcurrentStreamLock($proUser, null);
        $lock2 = AIQuotaService::acquireConcurrentStreamLock($proUser, null);

        $this->assertTrue($lock1['allowed']);
        $this->assertTrue($lock2['allowed']);
    }

    public function test_dangerous_file_upload_blocked(): void
    {
        $user = User::factory()->create(['role' => 'User']);
        SystemSetting::set('toggle_file_upload', true);
        SystemSetting::set('plans_pro_file_upload', true);

        $file = UploadedFile::fake()->create('malicious.php', 10, 'application/x-httpd-php');

        $response = $this->actingAs($user)->postJson(route('attachments.upload'), [
            'file' => $file
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('success', false);
        $response->assertJsonPath('message', 'Security check failed: Executable or dangerous file types are strictly prohibited.');
    }

    public function test_unauthorized_user_cannot_download_private_attachment(): void
    {
        $owner = User::factory()->create(['role' => 'User']);
        $attacker = User::factory()->create(['role' => 'User']);

        $conversation = Conversation::create([
            'uuid' => 'conv-123',
            'user_id' => $owner->id,
            'title' => 'Secret Talk',
            'model' => 'mock-default'
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => 'Attached secret document.'
        ]);

        Storage::disk('local')->put('attachments/secret.txt', 'CONFIDENTIAL DATA');

        $attachment = Attachment::create([
            'message_id' => $message->id,
            'file_path' => 'attachments/secret.txt',
            'file_name' => 'secret.txt',
            'mime_type' => 'text/plain',
            'file_size' => 17
        ]);

        $response = $this->actingAs($attacker)->get(route('attachments.download', $attachment));
        $response->assertStatus(403);
    }
}
