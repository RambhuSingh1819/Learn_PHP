<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use App\Models\Plan;
use App\Models\Organization;
use App\Services\UserAvailabilityService;
use App\Tenant\TenantManager;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserAvailabilityServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $service;
    protected $org;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->service = new UserAvailabilityService();

        $plan = Plan::create([
            'name' => 'Standard Plan',
            'slug' => 'standard',
            'price' => 10.00,
            'features' => ['max_users' => 10],
        ]);

        $this->org = Organization::create([
            'name' => 'Acme Corp',
            'slug' => 'acme',
            'plan_id' => $plan->id,
            'status' => 'active',
        ]);

        TenantManager::setTenantId($this->org->id);

        $this->user = User::create([
            'name' => 'John Dev',
            'email' => 'john@acme.com',
            'password' => bcrypt('password'),
            'role' => 'executive',
            'status' => 'active',
            'skills' => ['Laravel', 'Vue', 'Docker'],
            'availability_status' => 'active',
        ]);
        
        TenantManager::setTenantId(null);
    }

    public function test_get_availability_metrics_returns_correct_workload()
    {
        // 1. Initial workload check (0 tasks -> low workload)
        $metrics = $this->service->getAvailabilityMetrics($this->user);
        $this->assertEquals(0, $metrics['active_tasks_count']);
        $this->assertEquals('low', $metrics['workload']);
        $this->assertEquals('emerald', $metrics['workload_color']);
        $this->assertEquals('active', $metrics['availability_status']);

        // 2. Add 2 tasks -> moderate workload
        TenantManager::setTenantId($this->org->id);
        Task::create([
            'title' => 'Task 1',
            'assigned_to' => $this->user->id,
            'created_by' => $this->user->id,
            'due_date' => now()->addDays(2),
        ]);
        Task::create([
            'title' => 'Task 2',
            'assigned_to' => $this->user->id,
            'created_by' => $this->user->id,
            'due_date' => now()->addDays(2),
        ]);
        TenantManager::setTenantId(null);

        $metrics = $this->service->getAvailabilityMetrics($this->user);
        $this->assertEquals(2, $metrics['active_tasks_count']);
        $this->assertEquals('moderate', $metrics['workload']);
        $this->assertEquals('blue', $metrics['workload_color']);
    }

    public function test_get_suitability_score_matches_keywords()
    {
        // 1. High match
        $result = $this->service->getSuitabilityScore($this->user, 'Build a Laravel API with Docker support');
        $this->assertEquals(67, $result['score']); // 2 out of 3 matches (Laravel, Docker)
        $this->assertCount(2, $result['matched_skills']);
        $this->assertContains('Laravel', $result['matched_skills']);
        $this->assertContains('Docker', $result['matched_skills']);
        $this->assertEquals('Highly Recommended (Expert match)', $result['recommendation']);

        // 2. Low match
        $result = $this->service->getSuitabilityScore($this->user, 'Fix a CSS bug in Vue frontend');
        $this->assertEquals(33, $result['score']); // 1 out of 3 matches (Vue)
        $this->assertCount(1, $result['matched_skills']);
        $this->assertEquals('Recommended', $result['recommendation']);
    }

    public function test_get_decorated_users_returns_correct_structure()
    {
        $decorated = $this->service->getDecoratedUsersForOrganization($this->org->id);
        
        $this->assertCount(1, $decorated);
        $this->assertEquals($this->user->id, $decorated->first()['id']);
        $this->assertEquals('active', $decorated->first()['availability_status']);
        $this->assertEquals('low', $decorated->first()['workload']);
        $this->assertContains('Laravel', $decorated->first()['skills']);
    }
}
