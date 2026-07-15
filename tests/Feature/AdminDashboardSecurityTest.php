<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_shows_security_safeguards_and_audit_trail(): void
    {
        $superAdmin = User::factory()->create(['role' => 'Super Admin']);

        ActivityLog::create([
            'user_id' => $superAdmin->id,
            'action' => 'login',
            'description' => 'Super admin logged into system',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit'
        ]);

        $response = $this->actingAs($superAdmin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSeeText('Enterprise Security Posture & Safeguards');
        $response->assertSeeText('2FA REQUIREMENT');
        $response->assertSeeText('ADMIN IP ALLOWLIST');
        $response->assertSeeText('SESSION TIMEOUT');
        $response->assertSeeText('MAINTENANCE MODE');
        $response->assertSeeText('Recent Security & Audit Trail');
        $response->assertSeeText('login');
        $response->assertSeeText('127.0.0.1');
    }

    public function test_normal_user_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'User']);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }
}
