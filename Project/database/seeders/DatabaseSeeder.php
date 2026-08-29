<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Organization;
use App\Models\User;
use App\Models\Task;
use App\Models\Comment;
use App\Tenant\TenantManager;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Plans
        $basicPlan = Plan::create([
            'name' => 'Basic Plan',
            'slug' => 'basic',
            'price' => 1499.00,
            'features' => [
                'max_users' => 5,
                'escalation_depth' => 1,
                'sla_config' => false,
                'reports_export' => false,
                'api_access' => false,
            ],
            'stripe_price_id' => 'price_basic_mock',
        ]);

        $proPlan = Plan::create([
            'name' => 'Pro Plan',
            'slug' => 'pro',
            'price' => 3999.00,
            'features' => [
                'max_users' => 20,
                'escalation_depth' => 3,
                'sla_config' => true,
                'reports_export' => true,
                'api_access' => false,
            ],
            'stripe_price_id' => 'price_pro_mock',
        ]);

        $entPlan = Plan::create([
            'name' => 'Enterprise Plan',
            'slug' => 'enterprise',
            'price' => 12999.00,
            'features' => [
                'max_users' => 9999,
                'escalation_depth' => 5,
                'sla_config' => true,
                'reports_export' => true,
                'api_access' => true,
            ],
            'stripe_price_id' => 'price_enterprise_mock',
        ]);

        // 2. Seed Super Admin (No tenant id)
        User::create([
            'name' => 'SaaS Super Admin',
            'email' => 'superadmin@saas.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        // 3. Seed Org 1: Acme Corp
        $acme = Organization::create([
            'name' => 'Acme Corp',
            'slug' => 'acme',
            'plan_id' => $proPlan->id,
            'status' => 'active',
            'settings' => [
                'sla_hours_high' => 4,
                'sla_hours_medium' => 12,
                'sla_hours_low' => 24,
            ]
        ]);

        // Set tenant context for seeding Org 1 models
        TenantManager::setTenantId($acme->id);

        $acmeAdmin = User::create([
            'name' => 'Acme Admin',
            'email' => 'admin@acme.com',
            'password' => bcrypt('password'),
            'role' => 'org_admin',
            'status' => 'active',
        ]);

        $acmeManager = User::create([
            'name' => 'Acme Manager',
            'email' => 'manager@acme.com',
            'password' => bcrypt('password'),
            'role' => 'manager',
            'status' => 'active',
        ]);

        $acmeExec1 = User::create([
            'name' => 'John Executive',
            'email' => 'john@acme.com',
            'password' => bcrypt('password'),
            'role' => 'executive',
            'status' => 'active',
            'skills' => ['Payment', 'Backend', 'Database', 'API'],
            'availability_status' => 'active',
        ]);

        $acmeExec2 = User::create([
            'name' => 'Jane Executive',
            'email' => 'jane@acme.com',
            'password' => bcrypt('password'),
            'role' => 'executive',
            'status' => 'active',
            'skills' => ['Design', 'Frontend', 'CSS', 'Figma'],
            'availability_status' => 'on_leave',
        ]);

        // Seed some tasks for Acme
        $task1 = Task::create([
            'title' => 'Fix Payment Gateway Bug',
            'description' => 'Fix the checkout failure issue that happens during high concurrent traffic.',
            'assigned_to' => $acmeExec1->id,
            'created_by' => $acmeManager->id,
            'priority' => 'high',
            'status' => 'in_progress',
            'due_date' => now()->addHours(6),
            'sla_hours' => 4,
        ]);

        $task2 = Task::create([
            'title' => 'Design Marketing Landing Page',
            'description' => 'Draft a mock design for the upcoming summer marketing launch campaign.',
            'assigned_to' => $acmeExec2->id,
            'created_by' => $acmeManager->id,
            'priority' => 'medium',
            'status' => 'pending',
            'due_date' => now()->addDays(2),
            'sla_hours' => 12,
        ]);

        // An overdue/escalated task for cron testing
        $task3 = Task::create([
            'title' => 'Database Optimization',
            'description' => 'Create indexes on the audit logs table to speed up searches.',
            'assigned_to' => $acmeExec1->id,
            'created_by' => $acmeAdmin->id,
            'priority' => 'high',
            'status' => 'pending',
            'due_date' => now()->subHours(2), // Already overdue!
            'sla_hours' => 4,
        ]);

        Comment::create([
            'task_id' => $task1->id,
            'user_id' => $acmeExec1->id,
            'content' => 'Investigating Stripe token exchange code.',
        ]);


        // 4. Seed Org 2: Stark Industries
        $stark = Organization::create([
            'name' => 'Stark Industries',
            'slug' => 'stark',
            'plan_id' => $entPlan->id,
            'status' => 'active',
            'settings' => [
                'sla_hours_high' => 2,
                'sla_hours_medium' => 8,
                'sla_hours_low' => 16,
            ]
        ]);

        // Set tenant context for seeding Org 2 models
        TenantManager::setTenantId($stark->id);

        $starkAdmin = User::create([
            'name' => 'Tony Stark',
            'email' => 'tony@stark.com',
            'password' => bcrypt('password'),
            'role' => 'org_admin',
            'status' => 'active',
        ]);

        $starkManager = User::create([
            'name' => 'Pepper Potts',
            'email' => 'pepper@stark.com',
            'password' => bcrypt('password'),
            'role' => 'manager',
            'status' => 'active',
        ]);

        $starkExec = User::create([
            'name' => 'Happy Hogan',
            'email' => 'happy@stark.com',
            'password' => bcrypt('password'),
            'role' => 'executive',
            'status' => 'active',
            'skills' => ['Armor', 'Security', 'Hardware', 'Repair'],
            'availability_status' => 'active',
        ]);

        Task::create([
            'title' => 'Repair Mark 85 Armor',
            'description' => 'Fix the thrusters and replace damaged nanotech plating on the left leg.',
            'assigned_to' => $starkExec->id,
            'created_by' => $starkManager->id,
            'priority' => 'high',
            'status' => 'pending',
            'due_date' => now()->addHours(1),
            'sla_hours' => 2,
        ]);

        // Reset tenant manager
        TenantManager::setTenantId(null);
    }
}
