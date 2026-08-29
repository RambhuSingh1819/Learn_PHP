<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminTest extends TestCase
{
    use RefreshDatabase;

    protected $superAdmin;
    protected $normalUser;
    protected $plan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plan = Plan::create([
            'name' => 'Pro Plan',
            'slug' => 'pro',
            'price' => 3999,
            'features' => ['max_users' => 10]
        ]);

        $this->superAdmin = User::create([
            'name' => 'SaaS Super Admin',
            'email' => 'superadmin@saas.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        $org = Organization::create([
            'name' => 'Acme Corp',
            'slug' => 'acme',
            'plan_id' => $this->plan->id,
            'status' => 'active',
        ]);

        $this->normalUser = User::create([
            'organization_id' => $org->id,
            'name' => 'John Executive',
            'email' => 'john@acme.com',
            'password' => bcrypt('password'),
            'role' => 'executive',
            'status' => 'active',
        ]);
    }

    public function test_super_admin_can_create_new_tenant()
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('super.organizations.store'), [
                'name' => 'Wayne Enterprises',
                'slug' => 'wayne',
                'plan_id' => $this->plan->id,
                'admin_name' => 'Bruce Wayne',
                'admin_email' => 'bruce@wayne.com',
                'admin_password' => 'password123',
            ]);

        $response->assertRedirect();
        
        // Assert Organization was created
        $this->assertDatabaseHas('organizations', [
            'name' => 'Wayne Enterprises',
            'slug' => 'wayne',
        ]);

        // Retrieve Org ID
        $org = Organization::where('slug', 'wayne')->first();

        // Assert primary admin user was created and linked to organization
        $this->assertDatabaseHas('users', [
            'organization_id' => $org->id,
            'email' => 'bruce@wayne.com',
            'role' => 'org_admin',
        ]);
    }

    public function test_non_super_admin_cannot_create_tenant()
    {
        $response = $this->actingAs($this->normalUser)
            ->post(route('super.organizations.store'), [
                'name' => 'Wayne Enterprises',
                'slug' => 'wayne',
                'plan_id' => $this->plan->id,
                'admin_name' => 'Bruce Wayne',
                'admin_email' => 'bruce@wayne.com',
                'admin_password' => 'password123',
            ]);

        $response->assertStatus(403);
    }
}
