<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
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

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Admin Dashboard');
    }

    public function test_admin_can_view_users_list(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users'));
        $response->assertStatus(200);
        $response->assertSee('user@example.com');
        $response->assertSee('admin@example.com');
    }

    public function test_admin_can_change_user_role(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.role', $this->user), [
            'role' => 'admin',
        ]);

        $response->assertRedirect();
        $this->assertEquals('admin', $this->user->fresh()->role);
    }

    public function test_admin_cannot_change_own_role(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.role', $this->admin), [
            'role' => 'user',
        ]);

        $response->assertRedirect();
        $this->assertEquals('admin', $this->admin->fresh()->role);
    }

    public function test_admin_can_configure_model_with_allowed_roles(): void
    {
        $provider = \App\Models\AIProvider::firstOrCreate(
            ['slug' => 'openai'],
            [
                'name' => 'OpenAI',
                'base_url' => 'https://api.openai.com/v1',
                'is_active' => true,
            ]
        );

        $response = $this->actingAs($this->admin)->post(route('admin.models.store'), [
            'provider_id' => $provider->id,
            'name' => 'GPT-4 Pro Only',
            'model_identifier' => 'gpt-4-pro',
            'type' => 'chat',
            'context_window' => 8192,
            'max_tokens' => 4096,
            'cost_per_million_input' => 10.0,
            'cost_per_million_output' => 30.0,
            'allowed_roles' => ['pro', 'admin'],
            'is_active' => 'on',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $model = \App\Models\AIModel::where('model_identifier', 'gpt-4-pro')->first();
        $this->assertNotNull($model);
        $this->assertEquals(['pro', 'admin'], $model->allowed_roles);
    }
}
