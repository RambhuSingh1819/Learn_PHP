<?php

namespace App\Observers;

use App\Models\Task;
use App\Models\ActivityLog;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        ActivityLog::create([
            'organization_id' => $task->organization_id,
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'action' => 'task_created',
            'details' => "Task '{$task->title}' was created by " . (auth()->user()->name ?? 'System') . ".",
        ]);
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        $dirty = $task->getDirty();

        // 1. Log Status Updates
        if (array_key_exists('status', $dirty)) {
            $oldStatus = strtoupper($task->getOriginal('status'));
            $newStatus = strtoupper($task->status);
            
            $action = 'status_updated';
            if ($newStatus === 'ESCALATED') {
                $action = 'escalated';
            }

            ActivityLog::create([
                'organization_id' => $task->organization_id,
                'task_id' => $task->id,
                'user_id' => auth()->id(),
                'action' => $action,
                'details' => "Task status changed from {$oldStatus} to {$newStatus}.",
            ]);
        }

        // 2. Log Reassignments
        if (array_key_exists('assigned_to', $dirty)) {
            $oldAssignee = \App\Models\User::find($task->getOriginal('assigned_to'))->name ?? 'Unknown';
            $newAssignee = $task->assignee->name ?? 'Unknown';

            ActivityLog::create([
                'organization_id' => $task->organization_id,
                'task_id' => $task->id,
                'user_id' => auth()->id(),
                'action' => 'reassigned',
                'details' => "Task reassigned from {$oldAssignee} to {$newAssignee}.",
            ]);
        }

        // 3. Log SLA Breach specifically if flagged
        if (array_key_exists('sla_breached_at', $dirty) && !is_null($task->sla_breached_at)) {
            ActivityLog::create([
                'organization_id' => $task->organization_id,
                'task_id' => $task->id,
                'user_id' => null, // Cron/System action
                'action' => 'sla_breached',
                'details' => "Task SLA breached. Automated escalation triggered.",
            ]);
        }
    }
}
