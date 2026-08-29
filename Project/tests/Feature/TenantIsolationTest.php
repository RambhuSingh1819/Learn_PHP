<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Organization;
use App\Models\User;
use App\Models\Task;
use App\Tenant\TenantManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_isolation_prevents_cross_tenant_access()
    {
        // 1. Create a Plan
        $plan = Plan::create([
            'name' => 'Standard Plan',
            'slug' => 'standard',
            'price' => 20.00,
            'features' => ['max_users' => 10],
        ]);

        // 2. Create Tenant 1 (Acme) & User
        $acme = Organization::create([
            'name' => 'Acme Corp',
            'slug' => 'acme',
            'plan_id' => $plan->id,
            'status' => 'active',
        ]);

        // Set context to seed Acme user/task
        TenantManager::setTenantId($acme->id);
        
        $acmeUser = User::create([
            'name' => 'Acme User',
            'email' => 'acme@example.com',
            'password' => bcrypt('password'),
            'role' => 'executive',
            'status' => 'active',
        ]);

        $acmeTask = Task::create([
            'title' => 'Acme Internal Task',
            'assigned_to' => $acmeUser->id,
            'created_by' => $acmeUser->id,
            'priority' => 'medium',
            'due_date' => now()->addDays(2),
        ]);

        // 3. Create Tenant 2 (Stark) & User
        TenantManager::setTenantId(null); // Reset
        
        $stark = Organization::create([
            'name' => 'Stark Industries',
            'slug' => 'stark',
            'plan_id' => $plan->id,
            'status' => 'active',
        ]);

        TenantManager::setTenantId($stark->id);
        
        $starkUser = User::create([
            'name' => 'Tony Stark',
            'email' => 'tony@example.com',
            'password' => bcrypt('password'),
            'role' => 'org_admin',
            'status' => 'active',
        ]);

        $starkTask = Task::create([
            'title' => 'Stark Secret Task',
            'assigned_to' => $starkUser->id,
            'created_by' => $starkUser->id,
            'priority' => 'high',
            'due_date' => now()->addDays(1),
        ]);

        // Reset tenant manager context
        TenantManager::setTenantId(null);

        // 4. Act: Log in as Acme User
        $this->actingAs($acmeUser);

        // Under Acme session, TenantScopeMiddleware will set TenantManager::setTenantId($acme->id)
        TenantManager::setTenantId($acme->id);

        // 5. Assert: Acme User can see Acme task
        $this->assertNotNull(Task::find($acmeTask->id));

        // 6. Assert: Acme User CANNOT see Stark task (should return null due to tenant scope)
        $this->assertNull(Task::find($starkTask->id));
    }
}
