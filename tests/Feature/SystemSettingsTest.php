<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SystemSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->user = User::create([
            'name' => 'Standard User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);
    }

    public function test_admin_can_view_settings_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.settings'));
        $response->assertStatus(200);
        $response->assertSee('System Settings');
        $response->assertSee('General Settings');
    }

    public function test_non_admin_cannot_access_settings_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.settings'));
        $response->assertStatus(403);
    }

    public function test_admin_can_update_settings(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), [
            '_group' => 'general',
            'general_chatbot_name' => 'CustomBot',
            'general_welcome_message' => 'Custom Welcome!',
            'general_enable_chatbot' => 'on',
        ]);

        $response->assertRedirect();
        $this->assertEquals('CustomBot', SystemSetting::get('general_chatbot_name'));
        $this->assertEquals('Custom Welcome!', SystemSetting::get('general_welcome_message'));
        $this->assertTrue(SystemSetting::get('general_enable_chatbot'));
    }

    public function test_maintenance_mode_blocks_guests(): void
    {
        SystemSetting::set('general_maintenance_mode', true, 'general', 'boolean');
        SystemSetting::set('general_maintenance_message', 'Under Scheduled Maintenance', 'general', 'string');

        $response = $this->get(route('chat'));
        $response->assertStatus(503);
        $response->assertSee('Under Scheduled Maintenance');
    }

    public function test_maintenance_mode_blocks_free_users(): void
    {
        SystemSetting::set('general_maintenance_mode', true, 'general', 'boolean');
        SystemSetting::set('general_maintenance_message', 'Under Scheduled Maintenance', 'general', 'string');

        $response = $this->actingAs($this->user)->get(route('chat'));
        $response->assertStatus(503);
        $response->assertSee('Under Scheduled Maintenance');
    }

    public function test_maintenance_mode_allows_admins(): void
    {
        SystemSetting::set('general_maintenance_mode', true, 'general', 'boolean');

        $response = $this->actingAs($this->admin)->get(route('chat'));
        $response->assertStatus(200);
    }

    public function test_ai_provider_manager_falls_back_to_global_keys(): void
    {
        SystemSetting::set('model_openai_key', 'global-openai-key-test', 'model', 'string');

        $manager = app(\App\Services\AI\AIProviderManager::class);
        $provider = $manager->make('gpt-4o', $this->user);

        $this->assertInstanceOf(\App\Services\AI\Providers\OpenAIProvider::class, $provider);
        $this->assertEquals('global-openai-key-test', $provider->getApiKey());
    }

    public function test_admin_can_update_settings_for_other_groups(): void
    {
        // Test KB settings update
        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), [
            '_group' => 'kb',
            'kb_chunk_size' => 1000,
            'kb_auto_sync' => 'on',
        ]);
        $response->assertRedirect();
        $this->assertEquals(1000, SystemSetting::get('kb_chunk_size'));
        $this->assertTrue(SystemSetting::get('kb_auto_sync'));

        // Test Feature Toggle settings update
        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), [
            '_group' => 'toggle',
            'toggle_vision' => 'on',
        ]);
        $response->assertRedirect();
        $this->assertTrue(SystemSetting::get('toggle_vision'));
        $this->assertFalse(SystemSetting::get('toggle_voice')); // omitted boolean should be cast to false

        // Test System Prompt settings update
        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), [
            '_group' => 'prompt',
            'prompt_brand_voice' => 'Empathetic and Warm',
        ]);
        $response->assertRedirect();
        $this->assertEquals('Empathetic and Warm', SystemSetting::get('prompt_brand_voice'));
    }
}
