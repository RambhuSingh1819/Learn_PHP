<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Comment;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks scoped to the current tenant.
     */
    public function index(Request $request)
    {
        $query = Task::with(['assignee', 'creator']);

        // 1. Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 2. Filter by Priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // 3. Filter by Assignee
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // 4. Search Title/Description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // 5. Views: My Tasks, Team Tasks, Overdue, Escalated
        $view = $request->query('view', 'all');
        if ($view === 'my_tasks') {
            $query->where('assigned_to', auth()->id());
        } elseif ($view === 'overdue') {
            $query->where('due_date', '<', now())
                  ->whereNotIn('status', ['completed']);
        } elseif ($view === 'escalated') {
            $query->where('status', 'escalated');
        }

        $tasks = $query->orderBy('due_date', 'asc')->paginate(15);

        return response()->json($tasks);
    }

    /**
     * Create a new task under the current tenant.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'required|exists:users,id',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'required|date|after_or_equal:now',
        ], [
            'due_date.after_or_equal' => 'Not able to save due date before assignment date.',
        ]);

        $user = auth()->user();
        $org = $user->organization;

        // Determine SLA hours based on tenant settings
        $slaHours = 24; // fallback default
        if ($org && isset($org->settings)) {
            $settings = $org->settings;
            $priorityKey = 'sla_hours_' . $request->priority;
            if (isset($settings[$priorityKey])) {
                $slaHours = (int)$settings[$priorityKey];
            }
        }

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'assigned_to' => $request->assigned_to,
            'created_by' => $user->id,
            'priority' => $request->priority,
            'status' => 'pending',
            'due_date' => $request->due_date,
            'sla_hours' => $slaHours,
        ]);

        return response()->json([
            'message' => 'Task created successfully',
            'task' => $task->load(['assignee', 'creator'])
        ], 201);
    }

    /**
     * Update the specified task.
     */
    public function update(Request $request, Task $task)
    {
        // Enforce immutability post-closure
        if ($task->status === 'completed') {
            throw ValidationException::withMessages([
                'task' => 'Completed tasks are locked and cannot be edited.'
            ]);
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'sometimes|required|exists:users,id',
            'priority' => 'sometimes|required|in:low,medium,high',
            'due_date' => 'sometimes|required|date|after:now',
        ]);

        $task->update($request->only([
            'title', 'description', 'assigned_to', 'priority', 'due_date'
        ]));

        return response()->json([
            'message' => 'Task updated successfully',
            'task' => $task->load(['assignee', 'creator'])
        ]);
    }

    /**
     * Transition task workflow status.
     */
    public function updateStatus(Request $request, Task $task)
    {
        if ($task->status === 'completed') {
            throw ValidationException::withMessages([
                'task' => 'Completed tasks are locked.'
            ]);
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,on_hold',
        ]);

        $user = auth()->user();

        // Rules: Only owner/assignee updates status, unless Manager/Admin override
        $isAssignee = (int)$task->assigned_to === (int)$user->id;
        $isAuthorized = $isAssignee || $user->hasRole(['org_admin', 'manager', 'super_admin']);

        if (!$isAuthorized) {
            return response()->json([
                'error' => 'Unauthorized. Only the assignee or a manager can update status.'
            ], 403);
        }

        $task->update([
            'status' => $request->status
        ]);

        return response()->json([
            'message' => 'Status updated successfully',
            'task' => $task
        ]);
    }

    /**
     * Close the task with mandatory comment and optional proof.
     */
    public function close(Request $request, Task $task)
    {
        if ($task->status === 'completed') {
            throw ValidationException::withMessages([
                'task' => 'Task is already closed.'
            ]);
        }

        $request->validate([
            'closure_comment' => 'required|string|min:5',
            'proof' => 'nullable|file|mimes:pdf,jpg,png,zip|max:5120', // max 5MB
        ]);

        $proofPath = null;
        if ($request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store('proofs', 'public');
        }

        $task->update([
            'status' => 'completed',
            'closure_comment' => $request->closure_comment,
            'proof_attachment_path' => $proofPath,
        ]);

        return response()->json([
            'message' => 'Task closed and locked successfully.',
            'task' => $task
        ]);
    }

    /**
     * Trigger manual escalation of task.
     */
    public function escalate(Request $request, Task $task)
    {
        if ($task->status === 'completed') {
            throw ValidationException::withMessages([
                'task' => 'Completed tasks cannot be escalated.'
            ]);
        }

        $request->validate([
            'reason' => 'required|string|min:5',
        ]);

        $user = auth()->user();

        // Manual escalation can only be manager/admin triggered
        if (!$user->hasRole(['org_admin', 'manager', 'super_admin'])) {
            return response()->json([
                'error' => 'Only Managers or Admins can trigger manual escalation.'
            ], 403);
        }

        $task->increment('escalation_level');
        $task->update([
            'status' => 'escalated'
        ]);

        ActivityLog::create([
            'organization_id' => $task->organization_id,
            'task_id' => $task->id,
            'user_id' => $user->id,
            'action' => 'escalated',
            'details' => "Manual escalation triggered by {$user->name}. Reason: {$request->reason}",
        ]);

        // ==========================================
        // API SPACE: TRIGGER IMMEDIATE SMS/WHATSAPP ESCALATION ALERT
        // ==========================================
        // TODO: Integrate Twilio/WhatsApp API to alert higher authorities
        // ==========================================

        return response()->json([
            'message' => 'Task manually escalated successfully.',
            'task' => $task
        ]);
    }

    /**
     * Add comment to a task.
     */
    public function addComment(Request $request, Task $task)
    {
        $request->validate([
            'content' => 'required|string',
            'attachment' => 'nullable|file|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
        }

        $comment = Comment::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'content' => $request->content,
            'attachment_path' => $attachmentPath,
        ]);

        return response()->json([
            'message' => 'Comment added.',
            'comment' => $comment->load('user')
        ]);
    }

    /**
     * Dynamically match executives against task description/title for suitability.
     */
    public function checkSuitability(Request $request, \App\Services\UserAvailabilityService $availabilityService)
    {
        $request->validate([
            'text' => 'required|string',
        ]);

        // Automatically scoped to tenant organization
        $users = User::where('status', 'active')
            ->whereIn('role', ['manager', 'executive'])
            ->get();

        $recommendations = $users->map(function ($user) use ($availabilityService, $request) {
            $suitability = $availabilityService->getSuitabilityScore($user, $request->text);
            $metrics = $availabilityService->getAvailabilityMetrics($user);

            return [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role,
                'skills' => $user->skills ?? [],
                'availability_status' => $metrics['availability_status'],
                'workload' => $metrics['workload'],
                'workload_color' => $metrics['workload_color'],
                'active_tasks_count' => $metrics['active_tasks_count'],
                'suitability_score' => $suitability['score'],
                'matched_skills' => $suitability['matched_skills'],
                'recommendation' => $suitability['recommendation'],
            ];
        })->sortByDesc('suitability_score')->values();

        return response()->json($recommendations);
    }
}
