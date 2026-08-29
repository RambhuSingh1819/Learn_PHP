<x-app-layout>
    <!-- Use Google Fonts (Plus Jakarta Sans & Inter) to make it premium -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <div class="py-6 font-sans text-slate-800 antialiased" x-data="superAdminData()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Title Header -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 border-l-4 border-l-brand-600">
                <h1 class="text-2xl font-extrabold text-slate-950 font-display tracking-tight flex items-center gap-2">
                    SaaS Super Admin Control Center
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Manage all global platform organizations, subscriptions, and MRR.
                </p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                
                <div class="bg-gradient-to-br from-indigo-50/50 via-white to-white p-5 rounded-2xl border border-indigo-100/60 shadow-sm hover:shadow-md transition-all duration-300">
                    <span class="text-xs uppercase font-bold text-slate-400 tracking-wider">Total Tenants</span>
                    <h3 class="text-3.5xl font-black text-brand-600 mt-2 font-display">{{ $totalTenants }}</h3>
                    <p class="text-xs text-slate-500 mt-2">Registered businesses</p>
                </div>

                <div class="bg-gradient-to-br from-emerald-50/50 via-white to-white p-5 rounded-2xl border border-emerald-100/60 shadow-sm hover:shadow-md transition-all duration-300">
                    <span class="text-xs uppercase font-bold text-slate-400 tracking-wider">Active</span>
                    <h3 class="text-3.5xl font-black text-emerald-600 mt-2 font-display">{{ $activeTenants }}</h3>
                    <p class="text-xs text-slate-500 mt-2">Active subscriptions</p>
                </div>

                <div class="bg-gradient-to-br from-rose-50/50 via-white to-white p-5 rounded-2xl border border-rose-100/60 shadow-sm hover:shadow-md transition-all duration-300">
                    <span class="text-xs uppercase font-bold text-slate-400 tracking-wider">Suspended</span>
                    <h3 class="text-3.5xl font-black text-rose-600 mt-2 font-display">{{ $suspendedTenants }}</h3>
                    <p class="text-xs text-slate-500 mt-2">Trial ended / unpaid</p>
                </div>

                <div class="bg-gradient-to-br from-brand-500 via-brand-600 to-indigo-700 p-5 rounded-2xl text-white shadow-lg shadow-brand-500/15 hover:shadow-xl transition-all duration-300">
                    <span class="text-xs uppercase font-semibold text-brand-100 tracking-wider">Estimated MRR</span>
                    <h3 class="text-3.5xl font-black mt-2 font-display">₹16,998</h3>
                    <p class="text-xs text-brand-100 mt-2">Monthly recurring revenue</p>
                </div>

            </div>

            <!-- Tenants Management Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/40 flex justify-between items-center">
                    <h2 class="font-extrabold text-slate-900 font-display text-lg">All Tenant Organizations</h2>
                    <button @click="showAddTenantModal = true" class="bg-brand-600 hover:bg-brand-500 text-white text-xs px-4 py-2.5 rounded-xl transition duration-150 font-semibold shadow-md shadow-brand-600/10 cursor-pointer">
                        + Add Tenant
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 text-xs uppercase font-bold text-slate-400 tracking-wider bg-slate-50/20">
                                <th class="p-4.5 pl-6">Company Name</th>
                                <th class="p-4.5">Subdomain / Slug</th>
                                <th class="p-4.5">Current Plan</th>
                                <th class="p-4.5">Registration Date</th>
                                <th class="p-4.5">Status</th>
                                <th class="p-4.5 pr-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($organizations as $org)
                                <tr class="hover:bg-slate-50/30 transition text-sm text-slate-800">
                                    <td class="p-4.5 pl-6 font-bold text-slate-950">{{ $org->name }}</td>
                                    <td class="p-4.5"><code class="bg-slate-100 px-2.5 py-1 rounded-lg text-xs text-slate-600 font-mono border border-slate-200/40">{{ $org->slug }}</code></td>
                                    <td class="p-4.5">
                                        <span class="font-bold text-brand-600">
                                            {{ $org->plan->name ?? 'No active plan' }}
                                        </span>
                                    </td>
                                    <td class="p-4.5 text-slate-500">{{ $org->created_at->format('Y-M-d') }}</td>
                                    <td class="p-4.5">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold inline-block
                                            @if($org->status === 'active') bg-emerald-50 text-emerald-700 border border-emerald-150
                                            @elseif($org->status === 'trial') bg-blue-50 text-blue-700 border border-blue-150
                                            @else bg-rose-50 text-rose-700 border border-rose-150 @endif">
                                            {{ strtoupper($org->status) }}
                                        </span>
                                    </td>
                                    <td class="p-4.5 pr-6 text-right space-x-2 flex justify-end items-center gap-2">
                                        <!-- Plan Override Form -->
                                        <form method="POST" action="{{ route('super.organizations.override-plan', $org->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-slate-100 hover:bg-slate-200 text-xs px-3.5 py-2 rounded-xl transition text-slate-700 font-bold border border-slate-200/65 cursor-pointer">
                                                Override Plan
                                            </button>
                                        </form>

                                        <!-- Suspend / Activate Form -->
                                        <form method="POST" action="{{ route('super.organizations.toggle-status', $org->id) }}" class="inline">
                                            @csrf
                                            @if($org->status === 'active')
                                                <button type="submit" class="bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs px-3.5 py-2 rounded-xl transition font-bold border border-rose-155 cursor-pointer">
                                                    Suspend
                                                </button>
                                            @else
                                                <button type="submit" class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs px-3.5 py-2 rounded-xl transition font-bold border border-emerald-155 cursor-pointer">
                                                    Activate
                                                </button>
                                            @endif
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Add Tenant Modal -->
            <div x-show="showAddTenantModal" 
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
                 x-transition
                 style="display: none;">
                 
                <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-xl border border-slate-150 space-y-4"
                     @click.away="showAddTenantModal = false">
                    
                    <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                        <h3 class="text-lg font-bold text-slate-900 font-display">Add New Tenant Organization</h3>
                        <button @click="showAddTenantModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">
                            &times;
                        </button>
                    </div>

                    <form method="POST" action="{{ route('super.organizations.store') }}" class="space-y-4">
                        @csrf

                        <!-- Org Name -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-500 tracking-wider mb-1">Company Name</label>
                            <input type="text" name="name" required placeholder="e.g. Wayne Enterprises"
                                   class="w-full rounded-xl border-slate-200 text-sm focus:ring-brand-500 focus:border-brand-500 px-3 py-2 bg-white text-slate-800">
                        </div>

                        <!-- Slug -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-500 tracking-wider mb-1">Slug / Subdomain</label>
                            <input type="text" name="slug" required placeholder="e.g. wayne"
                                   class="w-full rounded-xl border-slate-200 text-sm focus:ring-brand-500 focus:border-brand-500 px-3 py-2 bg-white text-slate-800">
                        </div>

                        <!-- Subscription Plan -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-500 tracking-wider mb-1">Subscription Plan</label>
                            <select name="plan_id" required
                                    class="w-full rounded-xl border-slate-200 text-sm focus:ring-brand-500 focus:border-brand-500 px-3 py-2 bg-white text-slate-800">
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}">{{ $plan->name }} (₹{{ number_format($plan->price) }}/mo)</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="border-t border-slate-100 pt-3">
                            <h4 class="text-sm font-bold text-slate-900 mb-2">Primary Admin User</h4>
                        </div>

                        <!-- Admin Name -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-500 tracking-wider mb-1">Admin Full Name</label>
                            <input type="text" name="admin_name" required placeholder="e.g. Bruce Wayne"
                                   class="w-full rounded-xl border-slate-200 text-sm focus:ring-brand-500 focus:border-brand-500 px-3 py-2 bg-white text-slate-800">
                        </div>

                        <!-- Admin Email -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-500 tracking-wider mb-1">Admin Email Address</label>
                            <input type="email" name="admin_email" required placeholder="e.g. bruce@wayne.com"
                                   class="w-full rounded-xl border-slate-200 text-sm focus:ring-brand-500 focus:border-brand-500 px-3 py-2 bg-white text-slate-800">
                        </div>

                        <!-- Admin Password -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-500 tracking-wider mb-1">Admin Password</label>
                            <input type="password" name="admin_password" required placeholder="Min 8 characters"
                                   class="w-full rounded-xl border-slate-200 text-sm focus:ring-brand-500 focus:border-brand-500 px-3 py-2 bg-white text-slate-800">
                        </div>

                        <!-- Buttons -->
                        <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                            <button type="button" @click="showAddTenantModal = false"
                                    class="bg-slate-100 hover:bg-slate-200 text-xs px-4 py-2.5 rounded-xl transition text-slate-700 font-semibold cursor-pointer">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="bg-brand-600 hover:bg-brand-500 text-white text-xs px-4 py-2.5 rounded-xl transition font-semibold shadow-md shadow-brand-600/10 cursor-pointer">
                                Create Tenant
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
    
    <script>
        function superAdminData() {
            return {
                showAddTenantModal: false
            }
        }
    </script>
</x-app-layout>
