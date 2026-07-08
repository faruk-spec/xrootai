<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserSetting;
use App\Models\ApiKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_can_update_settings(): void
    {
        $response = $this->actingAs($this->user)->post('/settings', [
            'theme' => 'dark',
            'default_model' => 'gpt-4o',
            'system_prompt' => 'Custom system prompt',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('user_settings', [
            'user_id' => $this->user->id,
            'theme' => 'dark',
            'default_model' => 'gpt-4o',
            'system_prompt' => 'Custom system prompt',
        ]);
    }

    public function test_user_cannot_update_settings_with_invalid_data(): void
    {
        $response = $this->actingAs($this->user)->post('/settings', [
            'theme' => 'invalid-theme',
            'default_model' => '',
            'system_prompt' => 'Custom system prompt',
        ]);

        $response->assertSessionHasErrors(['theme', 'default_model']);
    }

    public function test_user_can_save_and_update_api_keys(): void
    {
        $response = $this->actingAs($this->user)->post('/settings/keys', [
            'keys' => [
                'openai' => 'sk-openai-key-test',
                'gemini' => 'AIzaSy-gemini-key-test',
            ]
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('api_keys', [
            'user_id' => $this->user->id,
            'provider' => 'openai',
            'is_active' => true,
        ]);
        
        $this->assertDatabaseHas('api_keys', [
            'user_id' => $this->user->id,
            'provider' => 'gemini',
            'is_active' => true,
        ]);

        // Verify decryption matches original input
        $openaiKeyRecord = ApiKey::where('user_id', $this->user->id)->where('provider', 'openai')->first();
        $this->assertEquals('sk-openai-key-test', $openaiKeyRecord->encrypted_key);
    }

    public function test_user_can_delete_api_keys_by_submitting_empty_values(): void
    {
        // Seed an API Key first
        ApiKey::create([
            'user_id' => $this->user->id,
            'provider' => 'openai',
            'encrypted_key' => 'old-key',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->post('/settings/keys', [
            'keys' => [
                'openai' => '',
            ]
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseMissing('api_keys', [
            'user_id' => $this->user->id,
            'provider' => 'openai',
        ]);
    }
}
