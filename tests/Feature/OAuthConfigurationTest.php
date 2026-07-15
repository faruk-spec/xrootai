<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\OAuthProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OAuthConfigurationTest extends TestCase
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

        // Run migrations and seed providers
        $this->artisan('migrate');
    }

    public function test_admin_can_view_oauth_configuration_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.oauth.index'));
        $response->assertStatus(200);
        $response->assertSee('OAuth configurations');
        $response->assertSee('Google');
        $response->assertSee('GitHub');
    }

    public function test_non_admin_cannot_view_oauth_configuration_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.oauth.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_update_oauth_configuration(): void
    {
        $provider = OAuthProvider::where('provider_slug', 'google')->first();

        $response = $this->actingAs($this->admin)->put(route('admin.oauth.update', $provider->id), [
            'provider_slug' => 'google',
            'client_id' => 'google-client-id-test',
            'client_secret' => 'google-client-secret-test',
            'scopes' => 'openid, profile, email',
            'is_active' => 'on',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        $provider = $provider->fresh();
        $this->assertEquals('google-client-id-test', $provider->client_id);
        $this->assertEquals('google-client-secret-test', $provider->client_secret); // Decrypted automatically on retrieval
        $this->assertEquals(['openid', 'profile', 'email'], $provider->scopes);
        $this->assertTrue($provider->is_active);
    }

    public function test_admin_can_reset_oauth_configuration(): void
    {
        $provider = OAuthProvider::where('provider_slug', 'github')->first();
        $provider->update([
            'client_id' => 'temp-id',
            'client_secret' => 'temp-secret',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.oauth.reset', $provider->id));
        $response->assertRedirect();

        $provider = $provider->fresh();
        $this->assertNull($provider->client_id);
        $this->assertNull($provider->client_secret);
        $this->assertFalse($provider->is_active);
    }

    public function test_oauth_connection_test_requires_credentials(): void
    {
        $provider = OAuthProvider::where('provider_slug', 'github')->first();
        
        // Initially empty
        $response = $this->actingAs($this->admin)->post(route('admin.oauth.test', $provider->id));
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'success' => false,
        ]);

        // Add credentials
        $provider->update([
            'client_id' => 'valid-id',
            'client_secret' => 'valid-secret',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.oauth.test', $provider->id));
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'success' => true,
        ]);
    }
}
