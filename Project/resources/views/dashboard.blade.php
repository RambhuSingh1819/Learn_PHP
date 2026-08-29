<x-app-layout>
    <div class="py-6" x-data="dashboardData()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Title & Create Task Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-6 rounded-2xl shadow-sm border border-slate-100 border-l-4 border-l-brand-600">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-950 font-display tracking-tight flex items-center gap-2">
                        {{ $org->name }} Dashboard
                    </h1>
                    <p class="text-sm text-slate-500 mt-1">
                        SaaS Plan: <span class="font-bold text-brand-600">{{ strtoupper($org->plan->name ?? 'None') }}</span>
                    </p>
                </div>
                @if(auth()->user()->hasRole(['org_admin', 'manager']))
                    <button @click="openCreateModal = true" class="mt-4 sm:mt-0 flex items-center gap-2 bg-brand-600 hover:bg-brand-500 text-white px-4 py-2.5 rounded-xl font-bold shadow-md shadow-brand-600/10 transition duration-150 cursor-pointer">
                        Create New Task
                    </button>
                @endif
            </div>

            <!-- Dashboard Analytics Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5">
                
                <!-- SLA Compliance -->
                <div class="bg-gradient-to-br from-brand-500 via-brand-600 to-indigo-700 p-5 rounded-2xl text-white shadow-md shadow-brand-600/15 relative overflow-hidden">
                    <div class="absolute -right-4 -bottom-4 opacity-10 text-8xl font-black">%</div>
                    <span class="text-xs uppercase font-semibold text-brand-100 tracking-wider">SLA Compliance</span>
                    <h3 class="text-3.5xl font-black mt-2 font-display">{{ $slaCompliance }}%</h3>
                    <p class="text-xs text-brand-100 mt-2">On-time resolutions</p>
                </div>

                <!-- Total Tasks -->
                <div class="bg-gradient-to-br from-blue-50/40 via-white to-white p-5 rounded-2xl border border-blue-100/60 shadow-sm hover:shadow-md transition-all duration-300">
                    <span class="text-xs uppercase font-bold text-slate-400 tracking-wider">Total Tasks</span>
                    <h3 class="text-3.5xl font-black text-slate-900 mt-2 font-display">{{ $totalTasks }}</h3>
                    <div class="flex items-center gap-1.5 text-xs text-slate-500 mt-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span> Active projects
                    </div>
                </div>

                <!-- Pending / In Progress -->
                <div class="bg-gradient-to-br from-amber-50/40 via-white to-white p-5 rounded-2xl border border-amber-100/60 shadow-sm hover:shadow-md transition-all duration-300">
                    <span class="text-xs uppercase font-bold text-slate-400 tracking-wider">Pending Work</span>
                    <h3 class="text-3.5xl font-black text-amber-600 mt-2 font-display">{{ $pendingTasks }}</h3>
                    <div class="flex items-center gap-1.5 text-xs text-slate-500 mt-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> In queue & progress
                    </div>
                </div>

                <!-- Escalated Tasks -->
                <div class="bg-gradient-to-br from-red-50/40 via-white to-white p-5 rounded-2xl border border-red-100/60 shadow-sm hover:shadow-md transition-all duration-300">
                    <span class="text-xs uppercase font-bold text-slate-400 tracking-wider">Escalated</span>
                    <h3 class="text-3.5xl font-black text-red-600 mt-2 font-display">{{ $escalatedTasks }}</h3>
                    <div class="flex items-center gap-1.5 text-xs text-slate-500 mt-2">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span> Critical attention
                    </div>
                </div>

                <!-- Overdue Tasks -->
                <div class="bg-gradient-to-br from-rose-50/40 via-white to-white p-5 rounded-2xl border border-rose-100/60 shadow-sm hover:shadow-md transition-all duration-300">
                    <span class="text-xs uppercase font-bold text-slate-400 tracking-wider">Overdue</span>
                    <h3 class="text-3.5xl font-black text-rose-600 mt-2 font-display">{{ $overdueTasks }}</h3>
                    <div class="flex items-center gap-1.5 text-xs text-slate-500 mt-2">
                        <span class="w-2 h-2 rounded-full bg-rose-600"></span> Missed SLA deadline
                    </div>
                </div>

            </div>

            <!-- Main Workspace Area -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left Column: Tasks Feed -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                            <h2 class="font-bold text-gray-900">Active Tasks Flow</h2>
                            <span class="text-xs bg-indigo-50 text-indigo-600 px-2.5 py-1 rounded-full font-medium">Auto SLA Tracking</span>
                        </div>
                        
                        <div class="divide-y divide-gray-100">
                            @forelse($tasksList as $task)
                                <div class="p-6 hover:bg-gray-50 transition cursor-pointer" @click="selectTask({{ json_encode($task) }})">
                                    <div class="flex justify-between items-start gap-4">
                                        <div class="space-y-1">
                                            <h4 class="font-bold text-gray-900 hover:text-indigo-600 transition">
                                                {{ $task->title }}
                                            </h4>
                                            <p class="text-sm text-gray-500 line-clamp-1">
                                                {{ $task->description ?? 'No description provided.' }}
                                            </p>
                                        </div>
                                        
                                        <!-- Priority / Status Badges -->
                                        <div class="flex gap-2 items-center flex-shrink-0">
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold @if($task->priority === 'high') bg-red-100 text-red-700 @elseif($task->priority === 'medium') bg-amber-100 text-amber-700 @else bg-blue-100 text-blue-700 @endif">
                                                {{ strtoupper($task->priority) }}
                                            </span>

                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold @if($task->status === 'completed') bg-emerald-100 text-emerald-700 @elseif($task->status === 'escalated') bg-rose-100 text-rose-700 @elseif($task->status === 'in_progress') bg-blue-100 text-blue-700 @elseif($task->status === 'on_hold') bg-purple-100 text-purple-700 @else bg-gray-100 text-gray-600 @endif">
                                                {{ str_replace('_', ' ', strtoupper($task->status)) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mt-4 flex justify-between items-center text-xs text-gray-400">
                                        <div class="flex items-center gap-2">
                                            <span class="flex items-center gap-1 bg-gray-100 text-gray-600 px-2 py-0.5 rounded">
                                                Assignee: {{ $task->assignee->name }}
                                            </span>
                                            @if($task->escalation_level > 0)
                                                <span class="text-red-500 font-semibold bg-red-50 px-2 py-0.5 rounded">
                                                    Escalated Level {{ $task->escalation_level }}
                                                </span>
                                            @endif
                                        </div>
                                        <span class="flex items-center gap-1 @if($task->due_date < now() && $task->status !== 'completed') text-red-500 font-semibold @endif">
                                            Due: {{ $task->due_date->format('M d, H:i') }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center text-gray-400">
                                    No tasks registered yet.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Right Column: Workload & Logs Feed -->
                <div class="space-y-6">

                    <!-- Workload -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <h3 class="font-bold text-gray-900 mb-4">Team Workload</h3>
                        <div class="space-y-3">
                            @foreach($teamWorkload as $member)
                                <div class="flex justify-between items-center py-2 border-b border-gray-50 last:border-b-0">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs uppercase">
                                            {{ substr($member->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-sm font-medium text-gray-800">{{ $member->name }}</span>
                                                @if($member->availability_status === 'on_leave')
                                                    <span class="px-1.5 py-0.5 rounded bg-rose-100 text-rose-600 text-[10px] font-semibold">On Leave</span>
                                                @elseif($member->availability_status === 'away')
                                                    <span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-600 text-[10px] font-semibold">Away</span>
                                                @endif
                                            </div>
                                            <span class="text-xs text-gray-400 uppercase">{{ $member->role }}</span>
                                            @if(!empty($member->skills))
                                                <span class="text-[10px] text-indigo-600 block mt-0.5">Skills: {{ implode(', ', $member->skills) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold @if($member->workload_color === 'emerald') bg-emerald-100 text-emerald-700 @elseif($member->workload_color === 'blue') bg-blue-100 text-blue-700 @elseif($member->workload_color === 'amber') bg-amber-100 text-amber-700 @else bg-rose-100 text-rose-700 @endif">
                                            {{ $member->tasks_count }} Tasks ({{ ucfirst($member->workload) }})
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Audit Timeline -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <h3 class="font-bold text-gray-900 mb-4">Activity Audit Logs</h3>
                        <div class="flow-root">
                            <ul class="-mb-8">
                                @forelse($recentLogs as $log)
                                    <li>
                                        <div class="relative pb-8">
                                            @if(!$loop->last)
                                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white @if($log->action === 'task_created') bg-blue-100 text-blue-600 @elseif($log->action === 'status_updated') bg-amber-100 text-amber-600 @elseif($log->action === 'completed') bg-emerald-100 text-emerald-600 @elseif($log->action === 'escalated' || $log->action === 'sla_breached') bg-red-100 text-red-600 @else bg-gray-100 text-gray-600 @endif">
                                                        Log
                                                    </span>
                                                </div>
                                                <div class="flex-1 min-w-0 pt-1.5">
                                                    <p class="text-xs text-gray-500 font-medium">
                                                        {{ $log->details }}
                                                    </p>
                                                    <span class="text-[10px] text-gray-400 block mt-0.5">
                                                        {{ $log->created_at->diffForHumans() }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <div class="text-center text-xs text-gray-400">No logs generated yet.</div>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                </div>

            </div>

            <!-- Manage Team Section (Only visible to org_admin and manager roles) -->
            @if(auth()->user()->hasRole(['org_admin', 'manager']))
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4 mt-6">
                    <div class="flex justify-between items-center border-b border-gray-100 pb-4">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Manage Team</h2>
                            <p class="text-xs text-gray-500 mt-1">Add, suspend, and view organization members and their access roles.</p>
                        </div>
                        <button @click="openTeamModal = true" class="bg-brand-600 hover:bg-brand-500 shadow-md shadow-brand-600/10 cursor-pointer text-white text-xs px-3.5 py-2 rounded-xl font-medium shadow-sm transition">
                            Add Team Member
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="text-gray-400 font-bold border-b border-gray-100">
                                    <th class="pb-3 font-semibold">Name</th>
                                    <th class="pb-3 font-semibold">Email</th>
                                    <th class="pb-3 font-semibold">Role</th>
                                    <th class="pb-3 font-semibold">Status</th>
                                    <th class="pb-3 text-right font-semibold pr-4">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($teamMembers as $member)
                                    <tr class="hover:bg-gray-50/50 transition duration-150">
                                        <td class="py-3.5 font-medium text-gray-900">{{ $member->name }}</td>
                                        <td class="py-3.5 text-gray-500">{{ $member->email }}</td>
                                        <td class="py-3.5">
                                            <span class="px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700">
                                                {{ strtoupper($member->role) }}
                                            </span>
                                        </td>
                                        <td class="py-3.5">
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold @if($member->status === 'active') bg-emerald-50 text-emerald-700 @else bg-rose-50 text-rose-700 @endif">
                                                {{ strtoupper($member->status) }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 text-right pr-4">
                                            @if(auth()->id() !== $member->id)
                                                <button @click="toggleMemberStatus('{{ $member->id }}')" class="text-xs px-2.5 py-1.5 rounded-lg border transition @if($member->status === 'active') border-rose-200 text-rose-600 hover:bg-rose-50 @else border-emerald-200 text-emerald-600 hover:bg-emerald-50 @endif">
                                                    {{ $member->status === 'active' ? 'Suspend' : 'Activate' }}
                                                </button>
                                            @else
                                                <span class="text-xs text-gray-400 italic">Logged In</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- Task Detail Dialog (Modal) -->
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50" x-show="showDetailModal" x-transition x-cloak>
            <div class="bg-white rounded-3xl max-w-2xl w-full p-6 space-y-6 shadow-2xl border border-gray-100 relative overflow-hidden" @click.away="showDetailModal = false">
                
                <div class="flex justify-between items-start border-b border-gray-100 pb-4">
                    <div>
                        <span class="text-xs uppercase font-black text-indigo-500 tracking-widest" x-text="selectedTask.priority + ' priority'"></span>
                        <h2 class="text-xl font-bold text-gray-900 mt-1" x-text="selectedTask.title"></h2>
                    </div>
                    <button @click="showDetailModal = false" class="text-gray-400 hover:text-gray-600 font-bold text-lg">x</button>
                </div>

                <div class="space-y-4">
                    <p class="text-sm text-gray-600" x-text="selectedTask.description || 'No description provided.'"></p>

                    <!-- Assigned Info -->
                    <div class="flex flex-wrap gap-4 text-xs bg-gray-50 p-4 rounded-2xl">
                        <div>
                            <span class="text-gray-400 block">Assignee</span>
                            <span class="font-bold text-gray-800" x-text="selectedTask.assignee ? selectedTask.assignee.name : 'Unknown'"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block">Creator</span>
                            <span class="font-semibold text-gray-800" x-text="selectedTask.creator ? selectedTask.creator.name : 'System'"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block">Escalations</span>
                            <span class="font-semibold text-red-500" x-text="'Level ' + selectedTask.escalation_level"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block">Status</span>
                            <span class="px-2 py-0.5 bg-indigo-50 text-indigo-600 font-black rounded" x-text="selectedTask.status"></span>
                        </div>
                    </div>

                    <!-- Task Status Controls (Only active for non-completed) -->
                    <template x-if="selectedTask.status !== 'completed'">
                        <div class="border-t border-gray-100 pt-4 space-y-4">
                            <h4 class="font-semibold text-sm text-gray-900">Workflow Operations</h4>
                            <div class="flex flex-wrap gap-3">
                                <button @click="changeStatus('in_progress')" class="bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold border border-blue-200/50 cursor-pointer text-xs px-3.5 py-2 rounded-xl transition shadow-sm">Set IN PROGRESS</button>
                                <button @click="changeStatus('on_hold')" class="bg-purple-50 hover:bg-purple-100 text-purple-700 font-bold border border-purple-200/50 cursor-pointer text-xs px-3.5 py-2 rounded-xl transition shadow-sm">Set ON HOLD</button>
                                <button @click="openEscalatePrompt = true" class="bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold border border-rose-200/50 cursor-pointer text-xs px-3.5 py-2 rounded-xl transition shadow-sm">Manual Escalation</button>
                                <button @click="openClosePrompt = true" class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold border border-emerald-200/50 cursor-pointer text-xs px-3.5 py-2 rounded-xl transition shadow-sm">Close & Complete Task</button>
                            </div>
                        </div>
                    </template>

                    <!-- Completed Closure comments -->
                    <template x-if="selectedTask.status === 'completed'">
                        <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-2xl">
                            <span class="text-xs text-emerald-700 font-bold block mb-1">Closure Verification Comment</span>
                            <p class="text-sm text-emerald-900" x-text="selectedTask.closure_comment"></p>
                        </div>
                    </template>

                    <!-- Interactive Manual Escalation and Close input panels -->
                    <div class="bg-red-50 p-4 rounded-2xl space-y-3" x-show="openEscalatePrompt" x-transition>
                        <label class="block text-xs font-bold text-red-700">Escalation Reason (Mandatory)</label>
                        <input type="text" x-model="escalateReason" class="w-full text-sm rounded-xl border-red-200 bg-white text-gray-850 px-3 py-2 focus:ring-red-500 focus:border-red-500" placeholder="e.g. Blocker on external API dependencies">
                        <div class="flex gap-2">
                            <button @click="submitEscalation()" class="bg-red-50 hover:bg-red-100 text-red-700 font-bold border border-red-200/50 cursor-pointer text-xs px-3 py-1.5 rounded-lg">Confirm Escalation</button>
                            <button @click="openEscalatePrompt = false" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold cursor-pointer text-xs px-3 py-1.5 rounded-lg">Cancel</button>
                        </div>
                    </div>

                    <div class="bg-emerald-50 p-4 rounded-2xl space-y-3" x-show="openClosePrompt" x-transition>
                        <label class="block text-xs font-bold text-emerald-700">Completion comment (Mandatory)</label>
                        <input type="text" x-model="closeComment" class="w-full text-sm rounded-xl border-emerald-200 bg-white text-gray-850 px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="e.g. Code successfully deployed and tested.">
                        <div class="flex gap-2">
                            <button @click="submitClosure()" class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold border border-emerald-200/50 cursor-pointer text-xs px-3 py-1.5 rounded-lg">Complete Task</button>
                            <button @click="openClosePrompt = false" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold cursor-pointer text-xs px-3 py-1.5 rounded-lg">Cancel</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Create Task Dialog (Modal) -->
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50" x-show="openCreateModal" x-transition x-cloak>
            <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-gray-100">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-lg font-bold text-gray-900">Create New Task</h3>
                    <button @click="openCreateModal = false" class="text-gray-400 hover:text-gray-600">x</button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Title</label>
                        <input type="text" x-model="newTask.title" class="w-full rounded-xl border-gray-200 bg-white text-gray-900 px-3 py-2" placeholder="Task summary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Description</label>
                        <textarea x-model="newTask.description" class="w-full rounded-xl border-gray-200 bg-white text-gray-900 px-3 py-2 h-20" placeholder="Details..."></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Assignee</label>
                        <select x-model="newTask.assigned_to" class="w-full rounded-xl border-gray-200 bg-white text-gray-900 px-3 py-2">
                            <option value="">Select Assignee</option>
                            @foreach($orgUsers as $member)
                                <option value="{{ $member->id }}">{{ $member->name }} ({{ strtoupper($member->role) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Dynamic Suitability & Availability Panel -->
                    <div x-show="suitabilityRecs.length > 0" x-transition x-cloak class="bg-indigo-50/50 border border-indigo-100 p-4 rounded-xl space-y-2.5 max-h-[180px] overflow-y-auto">
                        <span class="text-[10px] uppercase font-bold text-indigo-600 tracking-wider block">Suitability & Availability Matches:</span>
                        <div class="space-y-2">
                            <template x-for="rec in suitabilityRecs" :key="rec.id">
                                <div class="flex justify-between items-start text-xs py-1.5 border-b border-indigo-100/40 last:border-b-0">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="font-bold text-slate-800" x-text="rec.name"></span>
                                            <span class="text-[10px] text-slate-500 uppercase" x-text="rec.role"></span>
                                        </div>
                                        <div class="flex flex-wrap gap-1">
                                            <template x-for="skill in rec.skills" :key="skill">
                                                <span class="px-1.5 py-0.5 rounded bg-white text-slate-600 text-[9px] border border-slate-200/40" x-text="skill"></span>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="text-right flex flex-col items-end gap-1 shrink-0">
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold"
                                              :class="rec.workload_color === 'emerald' ? 'bg-emerald-100 text-emerald-700' : rec.workload_color === 'blue' ? 'bg-blue-100 text-blue-700' : rec.workload_color === 'amber' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700'"
                                              x-text="rec.active_tasks_count + ' active tasks'"></span>
                                        
                                        <span class="text-[10px] font-bold"
                                              :class="rec.availability_status === 'active' ? 'text-emerald-600' : 'text-rose-500'"
                                              x-text="rec.availability_status === 'active' ? 'Available' : 'On Leave'"></span>

                                        <span class="text-[10px] font-bold text-indigo-600" x-text="'Match: ' + rec.suitability_score + '%'"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Priority</label>
                            <select x-model="newTask.priority" class="w-full rounded-xl border-gray-200 bg-white text-gray-900 px-3 py-2">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Due Date</label>
                            <input type="datetime-local" x-model="newTask.due_date" :min="minDateTime" @input="validateDueDate()" class="w-full rounded-xl border-gray-200 bg-white text-gray-900 px-3 py-2">
                        </div>
                        <div class="col-span-2" x-show="dateWarning" x-cloak>
                            <p class="text-xs text-red-500 font-semibold" x-text="dateWarning"></p>
                        </div>
                    </div>

                    <button @click="submitTask()" :disabled="isSaving || dateWarning !== ''" class="w-full bg-brand-600 hover:bg-brand-500 shadow-md shadow-brand-600/10 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed text-white font-medium py-2.5 rounded-xl mt-4 shadow-sm transition">
                        <span x-text="isSaving ? 'Saving...' : 'Save Task'"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Add Team Member Dialog (Modal) -->
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50" x-show="openTeamModal" x-transition x-cloak>
            <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-gray-100">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-lg font-bold text-gray-900">Add Team Member</h3>
                    <button @click="openTeamModal = false" class="text-gray-400 hover:text-gray-600">x</button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Name</label>
                        <input type="text" x-model="newMember.name" class="w-full rounded-xl border-gray-200 bg-white text-gray-900 px-3 py-2" placeholder="Full Name">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Email</label>
                        <input type="email" x-model="newMember.email" class="w-full rounded-xl border-gray-200 bg-white text-gray-900 px-3 py-2" placeholder="email@company.com">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Password</label>
                        <input type="password" x-model="newMember.password" class="w-full rounded-xl border-gray-200 bg-white text-gray-900 px-3 py-2" placeholder="Minimum 8 characters">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Role</label>
                        <select x-model="newMember.role" class="w-full rounded-xl border-gray-200 bg-white text-gray-900 px-3 py-2">
                            <option value="executive">Executive / Employee</option>
                            <option value="manager">Manager</option>
                        </select>
                    </div>

                    <button @click="submitTeamMember()" :disabled="isSavingTeam" class="w-full bg-brand-600 hover:bg-brand-500 shadow-md shadow-brand-600/10 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed text-white font-medium py-2.5 rounded-xl mt-4 shadow-sm transition">
                        <span x-text="isSavingTeam ? 'Adding...' : 'Add Member'"></span>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- Alpine Dashboard Data Model -->
    <script>
        function dashboardData() {
            return {
                openCreateModal: false,
                showDetailModal: false,
                openEscalatePrompt: false,
                openClosePrompt: false,
                openTeamModal: false,
                isSavingTeam: false,
                selectedTask: {},
                escalateReason: '',
                closeComment: '',
                dateWarning: '',
                isSaving: false,
                suitabilityRecs: [],
                init() {
                    this.$watch('newTask.title', value => this.fetchSuitability());
                    this.$watch('newTask.description', value => this.fetchSuitability());
                    this.$watch('openCreateModal', value => {
                        if (value) {
                            this.fetchSuitability();
                        }
                    });
                },
                fetchSuitability() {
                    const text = (this.newTask.title + ' ' + this.newTask.description).trim();
                    fetch('/tasks/check-suitability', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ text: text || ' ' })
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.suitabilityRecs = data;
                    });
                },
                minDateTime: (() => {
                    const now = new Date();
                    const year = now.getFullYear();
                    const month = String(now.getMonth() + 1).padStart(2, '0');
                    const day = String(now.getDate()).padStart(2, '0');
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    return `${year}-${month}-${day}T${hours}:${minutes}`;
                })(),
                newTask: {
                    title: '',
                    description: '',
                    assigned_to: '',
                    priority: 'medium',
                    due_date: ''
                },
                newMember: {
                    name: '',
                    email: '',
                    password: '',
                    role: 'executive'
                },
                validateDueDate() {
                    if (!this.newTask.due_date) {
                        this.dateWarning = '';
                        return;
                    }
                    const selectTime = new Date(this.newTask.due_date).getTime();
                    const nowTime = new Date().getTime();
                    if (selectTime < nowTime) {
                        this.dateWarning = 'Not able to save due date before assignment date.';
                    } else {
                        this.dateWarning = '';
                    }
                },
                selectTask(task) {
                    this.selectedTask = task;
                    this.openEscalatePrompt = false;
                    this.openClosePrompt = false;
                    this.escalateReason = '';
                    this.closeComment = '';
                    this.showDetailModal = true;
                },
                changeStatus(status) {
                    fetch(`/tasks/${this.selectedTask.id}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ status })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.error) alert(data.error);
                        else window.location.reload();
                    });
                },
                submitEscalation() {
                    if (!this.escalateReason) return alert('Reason is required!');
                    fetch(`/tasks/${this.selectedTask.id}/escalate`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ reason: this.escalateReason })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.error) alert(data.error);
                        else window.location.reload();
                    });
                },
                submitClosure() {
                    if (!this.closeComment) return alert('Comment is required!');
                    fetch(`/tasks/${this.selectedTask.id}/close`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ closure_comment: this.closeComment })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.error) alert(data.error);
                        else window.location.reload();
                    });
                },
                submitTask() {
                    if (this.dateWarning) {
                        alert(this.dateWarning);
                        return;
                    }
                    this.isSaving = true;
                    fetch('/tasks', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.newTask)
                    })
                    .then(res => {
                        return res.json().then(data => {
                            if (!res.ok) {
                                throw data;
                            }
                            return data;
                        });
                    })
                    .then(data => {
                        window.location.reload();
                    })
                    .catch(err => {
                        this.isSaving = false;
                        if (err.errors) {
                            alert(Object.values(err.errors).flat().join("\n"));
                        } else if (err.message) {
                            alert(err.message);
                        } else {
                            alert("Failed to save task.");
                        }
                    });
                },
                submitTeamMember() {
                    if (!this.newMember.name || !this.newMember.email || !this.newMember.password) {
                        alert("All fields are required.");
                        return;
                    }
                    this.isSavingTeam = true;
                    fetch('/team', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.newMember)
                    })
                    .then(res => {
                        return res.json().then(data => {
                            if (!res.ok) {
                                throw data;
                            }
                            return data;
                        });
                    })
                    .then(data => {
                        window.location.reload();
                    })
                    .catch(err => {
                        this.isSavingTeam = false;
                        if (err.errors) {
                            alert(Object.values(err.errors).flat().join("\n"));
                        } else if (err.message) {
                            alert(err.message);
                        } else {
                            alert("Failed to add team member.");
                        }
                    });
                },
                toggleMemberStatus(userId) {
                    if (!confirm("Are you sure you want to change this member's status?")) return;
                    fetch(`/team/${userId}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(res => {
                        return res.json().then(data => {
                            if (!res.ok) {
                                throw data;
                            }
                            return data;
                        });
                    })
                    .then(data => {
                        window.location.reload();
                    })
                    .catch(err => {
                        alert(err.message || "Failed to update status.");
                    });
                }
            }
        }
    </script>
</x-app-layout>
