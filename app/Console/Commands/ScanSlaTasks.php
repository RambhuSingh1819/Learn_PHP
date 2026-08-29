<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ScanSlaTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:scan-sla';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan tasks for SLA breaches and escalate automatically';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Scanning for SLA breaches...');

        // Query all incomplete and non-escalated tasks where due_date is in the past
        // We use withoutGlobalScope('tenant') to scan across all organizations
        $breachedTasks = Task::withoutGlobalScope('tenant')
            ->whereNotIn('status', ['completed', 'escalated'])
            ->where('due_date', '<', now())
            ->get();

        if ($breachedTasks->isEmpty()) {
            $this->info('No breached tasks found.');
            return 0;
        }

        foreach ($breachedTasks as $task) {
            $this->info("Breach detected on Task ID: {$task->id} ('{$task->title}')");

            $org = $task->organization;
            $plan = $org->plan ?? null;
            $maxEscalationDepth = $plan ? ($plan->features['escalation_depth'] ?? 1) : 1;

            if ($task->escalation_level >= $maxEscalationDepth) {
                $this->warn("Task has reached max escalation depth ({$maxEscalationDepth}). Alerting Org Admin directly.");
                // Log and mark as escalated but do not increment level beyond plan limits
                $task->update([
                    'status' => 'escalated',
                    'sla_breached_at' => now(),
                ]);
                
                $this->notifyAuthority($task, 'org_admin');
                continue;
            }

            // Perform auto escalation
            $task->increment('escalation_level');
            $task->update([
                'status' => 'escalated',
                'sla_breached_at' => now(),
            ]);

            // Determine next authority based on current assignee role
            $assignee = User::withoutGlobalScope('tenant')->find($task->assigned_to);
            $nextRole = 'manager';

            if ($assignee && $assignee->role === 'manager') {
                $nextRole = 'org_admin';
            } elseif ($assignee && $assignee->role === 'org_admin') {
                $nextRole = 'super_admin';
            }

            $this->notifyAuthority($task, $nextRole);
        }

        $this->info('SLA scan complete.');
        return 0;
    }

    /**
     * Send alerts/notifications to the specified authority role.
     */
    protected function notifyAuthority(Task $task, string $role)
    {
        // Fetch users in the same organization who have the target escalation role
        $authorities = User::withoutGlobalScope('tenant')
            ->where('organization_id', $task->organization_id)
            ->where('role', $role)
            ->where('status', 'active')
            ->get();

        if ($authorities->isEmpty()) {
            // Fallback to Org Admins
            $authorities = User::withoutGlobalScope('tenant')
                ->where('organization_id', $task->organization_id)
                ->where('role', 'org_admin')
                ->get();
        }

        foreach ($authorities as $authority) {
            Log::info("ALERT: SLA Breach notification sent to {$authority->name} ({$authority->role}) for task '{$task->title}'");

            // ==========================================
            // API SPACE: AUTOMATED SMS/WHATSAPP ESCALATION API CALL
            // ==========================================
            // TODO: Connect to Twilio/WhatsApp API or SendGrid Email API
            // 1. Initialize API request payload
            // 2. Dispatch warning message to $authority->phone or $authority->email
            // 3. Log API success/failure response
            // ==========================================
        }
    }
}
