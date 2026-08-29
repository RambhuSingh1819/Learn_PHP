<?php

namespace App\Services;

use App\Models\User;
use App\Models\Task;

class UserAvailabilityService
{
    /**
     * Get availability and workload details of a specific user.
     */
    public function getAvailabilityMetrics(User $user): array
    {
        // Count active tasks (tasks not completed)
        $activeTasksCount = Task::withoutGlobalScope('tenant')
            ->where('assigned_to', $user->id)
            ->whereNotIn('status', ['completed'])
            ->count();

        // Calculate workload classification
        $workload = 'low';
        $workloadColor = 'emerald'; // Green status indicator

        if ($activeTasksCount >= 5) {
            $workload = 'overloaded';
            $workloadColor = 'rose'; // Red status indicator
        } elseif ($activeTasksCount >= 3) {
            $workload = 'heavy';
            $workloadColor = 'amber'; // Orange status indicator
        } elseif ($activeTasksCount >= 1) {
            $workload = 'moderate';
            $workloadColor = 'blue'; // Blue status indicator
        }

        return [
            'active_tasks_count' => $activeTasksCount,
            'workload' => $workload,
            'workload_color' => $workloadColor,
            'status' => $user->status, // e.g. active, suspended
            'availability_status' => $user->availability_status ?? 'active', // active, on_leave, away
        ];
    }

    /**
     * Compute task-user suitability score based on user skills matching task keywords.
     */
    public function getSuitabilityScore(User $user, string $taskText): array
    {
        $skills = $user->skills ?? [];
        if (empty($skills)) {
            return [
                'score' => 0,
                'matched_skills' => [],
                'recommendation' => 'No matching skills listed.'
            ];
        }

        $matchedSkills = [];
        $taskTextLower = strtolower($taskText);

        foreach ($skills as $skill) {
            if (str_contains($taskTextLower, strtolower($skill))) {
                $matchedSkills[] = $skill;
            }
        }

        $matchCount = count($matchedSkills);
        $totalSkills = count($skills);
        $score = $totalSkills > 0 ? round(($matchCount / $totalSkills) * 100) : 0;

        $recommendation = 'General match';
        if ($score >= 60) {
            $recommendation = 'Highly Recommended (Expert match)';
        } elseif ($score >= 30) {
            $recommendation = 'Recommended';
        }

        return [
            'score' => $score,
            'matched_skills' => $matchedSkills,
            'recommendation' => $recommendation
        ];
    }

    /**
     * Decorate a collection of users with workload, availability, and skills.
     */
    public function getDecoratedUsersForOrganization(string $orgId): \Illuminate\Support\Collection
    {
        $users = User::withoutGlobalScope('tenant')
            ->where('organization_id', $orgId)
            ->where('status', 'active')
            ->get();

        return $users->map(function (User $user) {
            $metrics = $this->getAvailabilityMetrics($user);
            
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'skills' => $user->skills ?? [],
                'availability_status' => $metrics['availability_status'],
                'workload' => $metrics['workload'],
                'workload_color' => $metrics['workload_color'],
                'active_tasks_count' => $metrics['active_tasks_count'],
            ];
        });
    }
}
