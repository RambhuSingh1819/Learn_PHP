<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Tenant\TenantManager;

class TeamManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $org;
    protected $orgAdmin;
    protected $executive;

    protected function setUp(): void
    {
        parent::setUp();
        TenantManager::setTenantId(null);

        $plan = Plan::create([
            'name' => 'Pro Plan',
            'slug' => 'pro',
            'price' => 3999,
            'features' => ['max_users' => 10]
        ]);

        $this->org = Organization::create([
            'name' => 'Stark Industries',
            'slug' => 'stark',
            'plan_id' => $plan->id,
            'status' => 'active'
        ]);

        $this->orgAdmin = User::create([
            'organization_id' => $this->org->id,
            'name' => 'Pepper Potts',
            'email' => 'pepper@stark.com',
            'password' => bcrypt('password'),
            'role' => 'org_admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->executive = User::create([
            'organization_id' => $this->org->id,
            'name' => 'Happy Hogan',
            'email' => 'happy@stark.com',
            'password' => bcrypt('password'),
            'role' => 'executive',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
    }

    public function test_org_admin_can_add_team_member()
    {
        $response = $this->actingAs($this->orgAdmin)
            ->postJson(route('team.store'), [
                'name' => 'Tony Stark',
                'email' => 'tony@stark.com',
                'password' => 'password123',
                'role' => 'manager'
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => 'tony@stark.com',
            'role' => 'manager',
            'organization_id' => $this->org->id
        ]);
    }

    public function test_org_admin_cannot_add_duplicate_email()
    {
        $response = $this->actingAs($this->orgAdmin)
            ->postJson(route('team.store'), [
                'name' => 'Happy Hogan Duplicate',
                'email' => 'happy@stark.com',
                'password' => 'password123',
                'role' => 'executive'
            ]);

        $response->assertStatus(422);
    }

    public function test_org_admin_can_toggle_member_status()
    {
        $response = $this->actingAs($this->orgAdmin)
            ->postJson(route('team.toggle-status', $this->executive->id));

        $response->assertStatus(200);
        $this->assertEquals('suspended', $this->executive->fresh()->status);

        // Toggle back to active
        $response = $this->actingAs($this->orgAdmin)
            ->postJson(route('team.toggle-status', $this->executive->id));

        $response->assertStatus(200);
        $this->assertEquals('active', $this->executive->fresh()->status);
    }

    public function test_org_admin_cannot_suspend_themselves()
    {
        $response = $this->actingAs($this->orgAdmin)
            ->postJson(route('team.toggle-status', $this->orgAdmin->id));

        $response->assertStatus(400);
        $this->assertEquals('active', $this->orgAdmin->fresh()->status);
    }

    public function test_executive_cannot_add_team_members()
    {
        $response = $this->actingAs($this->executive)
            ->postJson(route('team.store'), [
                'name' => 'Dummy User',
                'email' => 'dummy@stark.com',
                'password' => 'password123',
                'role' => 'executive'
            ]);

        $response->assertStatus(403);
    }

    public function test_tenant_isolation_prevents_modifying_other_tenant_users()
    {
        // Create Tenant B
        $otherOrg = Organization::create([
            'name' => 'Acme Corp',
            'slug' => 'acme',
            'status' => 'active'
        ]);

        $otherUser = User::create([
            'organization_id' => $otherOrg->id,
            'name' => 'John Doe',
            'email' => 'john@acme.com',
            'password' => bcrypt('password'),
            'role' => 'executive',
            'status' => 'active'
        ]);

        $response = $this->actingAs($this->orgAdmin)
            ->postJson(route('team.toggle-status', $otherUser->id));

        $response->assertStatus(404);
    }
}
