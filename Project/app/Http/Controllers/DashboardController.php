<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, \App\Services\UserAvailabilityService $availabilityService)
    {
        $user = auth()->user();
        
        // If Super Admin, show a mock Super Admin tenant list metrics dashboard
        if ($user->isSuperAdmin()) {
            return $this->superAdminDashboard();
        }

        // Standard tenant dashboard
        $org = $user->organization;

        // Tasks query (automatically scoped by tenant)
        $tasksQuery = Task::query();
        $totalTasks = (clone $tasksQuery)->count();
        $pendingTasks = (clone $tasksQuery)->whereIn('status', ['pending', 'in_progress', 'on_hold'])->count();
        $completedTasks = (clone $tasksQuery)->where('status', 'completed')->count();
        $escalatedTasks = (clone $tasksQuery)->where('status', 'escalated')->count();
        
        $overdueTasks = (clone $tasksQuery)
            ->where('due_date', '<', now())
            ->whereNotIn('status', ['completed'])
            ->count();

        // Team workload (tasks per user)
        $teamWorkload = User::withCount(['tasks' => function ($query) {
            $query->whereNotIn('status', ['completed']);
        }])->get()->map(function ($member) use ($availabilityService) {
            $metrics = $availabilityService->getAvailabilityMetrics($member);
            $member->workload = $metrics['workload'];
            $member->workload_color = $metrics['workload_color'];
            $member->availability_status = $metrics['availability_status'];
            return $member;
        });

        // SLA Compliance calculation
        $breachedCount = (clone $tasksQuery)->whereNotNull('sla_breached_at')->count();
        $slaCompliance = $totalTasks > 0 
            ? round((($totalTasks - $breachedCount) / $totalTasks) * 100) 
            : 100;

        // Recent Activity Logs
        $recentLogs = ActivityLog::with(['user', 'task'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent Tasks
        $tasksList = Task::with('assignee')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // All active users for the task assignment select box
        $orgUsers = User::where('status', 'active')->get()->map(function ($member) use ($availabilityService) {
            $metrics = $availabilityService->getAvailabilityMetrics($member);
            $member->active_tasks_count = $metrics['active_tasks_count'];
            $member->workload = $metrics['workload'];
            $member->workload_color = $metrics['workload_color'];
            $member->availability_status = $metrics['availability_status'];
            return $member;
        });

        // All users in this organization (active + suspended) for the team management panel
        $teamMembers = User::orderBy('name', 'asc')->get()->map(function ($member) use ($availabilityService) {
            $metrics = $availabilityService->getAvailabilityMetrics($member);
            $member->active_tasks_count = $metrics['active_tasks_count'];
            $member->workload = $metrics['workload'];
            $member->workload_color = $metrics['workload_color'];
            $member->availability_status = $metrics['availability_status'];
            return $member;
        });

        return view('dashboard', compact(
            'org',
            'totalTasks',
            'pendingTasks',
            'completedTasks',
            'escalatedTasks',
            'overdueTasks',
            'teamWorkload',
            'slaCompliance',
            'recentLogs',
            'tasksList',
            'orgUsers',
            'teamMembers'
        ));
    }

    protected function superAdminDashboard()
    {
        // Fetch stats across all tenants (requires bypassing global scope or querying org model)
        $organizations = \App\Models\Organization::with('plan')->get();
        $totalTenants = $organizations->count();
        $activeTenants = $organizations->where('status', 'active')->count();
        $suspendedTenants = $organizations->where('status', 'suspended')->count();
        $plans = \App\Models\Plan::all();

        return view('super_dashboard', compact(
            'organizations',
            'totalTenants',
            'activeTenants',
            'suspendedTenants',
            'plans'
        ));
    }

    public function toggleTenantStatus(\App\Models\Organization $organization)
    {
        if ($organization->status === 'active') {
            $organization->update(['status' => 'suspended']);
        } else {
            $organization->update(['status' => 'active']);
        }

        return redirect()->back()->with('success', 'Tenant status updated successfully.');
    }

    public function overrideTenantPlan(\App\Models\Organization $organization)
    {
        $plans = \App\Models\Plan::orderBy('price', 'asc')->get();
        if ($plans->isEmpty()) {
            return redirect()->back()->with('error', 'No plans available.');
        }

        // Cycle to the next plan
        $currentPlanIndex = $plans->pluck('id')->search($organization->plan_id);
        
        if ($currentPlanIndex === false || $currentPlanIndex >= $plans->count() - 1) {
            $nextPlanId = $plans->first()->id;
        } else {
            $nextPlanId = $plans->get($currentPlanIndex + 1)->id;
        }

        $organization->update(['plan_id' => $nextPlanId]);

        return redirect()->back()->with('success', 'Tenant plan overridden successfully.');
    }

    public function storeTenant(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:organizations,slug',
            'plan_id' => 'required|exists:plans,id',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255|unique:users,email',
            'admin_password' => 'required|string|min:8',
        ]);

        $org = \App\Models\Organization::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'plan_id' => $request->plan_id,
            'status' => 'active',
            'settings' => [
                'sla_hours_high' => 4,
                'sla_hours_medium' => 12,
                'sla_hours_low' => 24,
            ]
        ]);

        \App\Tenant\TenantManager::setTenantId($org->id);

        \App\Models\User::create([
            'organization_id' => $org->id,
            'name' => $request->admin_name,
            'email' => $request->admin_email,
            'password' => bcrypt($request->admin_password),
            'role' => 'org_admin',
            'status' => 'active',
        ]);

        \App\Tenant\TenantManager::setTenantId(null);

        return redirect()->back()->with('success', 'New tenant created successfully.');
    }
}
