<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>SaaS Task & Escalation Platform</title>

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Tailwind CSS Play CDN to ensure instantaneous and bulletproof rendering -->
        <script src="https://cdn.tailwindcss.com"></script>
        
        <!-- Custom Tailwind Configuration for Premium Theme -->
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Inter', 'sans-serif'],
                            display: ['Plus Jakarta Sans', 'sans-serif'],
                        },
                        colors: {
                            brand: {
                                50: '#f0f3ff',
                                100: '#e1e7ff',
                                200: '#c8d3ff',
                                300: '#a1b4ff',
                                400: '#708bff',
                                500: '#435eff',
                                600: '#2d3eff',
                                700: '#222df2',
                                800: '#1b23c2',
                                900: '#1c2299',
                                950: '#10135c',
                            },
                        }
                    }
                }
            }
        </script>

        <style>
            .glassmorphism {
                background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border: 1px solid rgba(226, 232, 240, 0.8);
            }
            .grid-bg {
                background-color: #ffffff;
                background-image: radial-gradient(rgba(0, 0, 0, 0.04) 1px, transparent 0);
                background-size: 24px 24px;
            }
        </style>
    </head>
    <body class="bg-white text-slate-800 font-sans antialiased min-h-screen relative overflow-x-hidden grid-bg">

        <!-- Navigation Bar -->
        <nav class="sticky top-0 z-50 w-full glassmorphism border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center gap-3">
                        <div>
                            <span class="font-display font-bold text-xl tracking-tight text-slate-900">FlowEscalate</span>
                            <span class="text-xs block text-slate-500 font-medium leading-none">Multi-Tenant SaaS</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="px-4 py-2 bg-brand-600 hover:bg-brand-500 text-white font-medium rounded-lg text-sm transition-all duration-200 shadow-md shadow-brand-600/10">
                                    Go to Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="text-slate-600 hover:text-slate-950 font-medium text-sm transition-colors duration-200">
                                    Sign In
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white border border-slate-800 font-medium rounded-lg text-sm transition-all duration-200">
                                        Register Tenant
                                    </a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <header class="max-w-5xl mx-auto px-4 pt-16 pb-12 text-center relative z-10">
            <div class="inline-flex items-center gap-2 bg-brand-50 text-brand-700 border border-brand-100 px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider mb-6">
                <span class="w-2 h-2 rounded-full bg-brand-600 animate-pulse"></span>
                Ready for Evaluation
            </div>
            
            <h1 class="font-display font-extrabold text-4xl sm:text-5xl lg:text-6xl tracking-tight text-slate-900 mb-6">
                Multi-Tenant Task & <br class="hidden sm:inline" />
                <span class="bg-gradient-to-r from-brand-600 to-indigo-600 bg-clip-text text-transparent">
                    SLA Escalation Engine
                </span>
            </h1>
            
            <p class="max-w-2xl mx-auto text-lg text-slate-600 leading-relaxed mb-8">
                An industrial-grade Laravel task workflow platform designed for corporate environments. Features robust tenant data isolation, automated SLA deadline monitors, and role-based multi-tier auto-escalation paths.
            </p>

            <div class="flex flex-wrap items-center justify-center gap-4">
                <a href="#test-accounts" class="px-6 py-3 bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-lg shadow-lg shadow-brand-500/20 hover:shadow-brand-500/30 transition-all duration-200">
                    Explore Test Credentials
                </a>
                <a href="#workflow" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 font-semibold rounded-lg transition-all duration-200">
                    See How It Works
                </a>
            </div>
        </header>

        <!-- Quick Platform Stats / Overview Cards -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 grid grid-cols-1 md:grid-cols-3 gap-6 relative z-10">
            <!-- Card 1: Tenant Isolation -->
            <div class="p-6 bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-all duration-200">
                <div class="w-12 h-12 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h3 class="font-display font-semibold text-lg text-slate-900 mb-2">Tenant Data Isolation</h3>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Data isolation is enforced at the database layer via Global Scope filters and UUID-scoped request middleware context. Org members cannot view other tenants' details.
                </p>
            </div>

            <!-- Card 2: SLA Escalation -->
            <div class="p-6 bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-all duration-200">
                <div class="w-12 h-12 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="font-display font-semibold text-lg text-slate-900 mb-2">Automated SLA Monitor</h3>
                <p class="text-sm text-slate-600 leading-relaxed">
                    A background daemon monitors tasks every minute. If a deadline breaches, it escalates the task status, increments the level, and shifts responsibility up the role hierarchy.
                </p>
            </div>

            <!-- Card 3: Immutability -->
            <div class="p-6 bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-all duration-200">
                <div class="w-12 h-12 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="font-display font-semibold text-lg text-slate-900 mb-2">Immutable Audit Logging</h3>
                <p class="text-sm text-slate-600 leading-relaxed">
                    All updates (status transitions, assignees, SLA flags) are monitored and logged to an immutable log table automatically. Once tasks are closed, they are locked from editing.
                </p>
            </div>
        </section>

        <!-- Seeded Credentials Section -->
        <section id="test-accounts" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 relative z-10">
            <div class="text-center mb-10">
                <h2 class="font-display font-bold text-3xl text-slate-900 mb-3">Pre-Configured Test Credentials</h2>
                <p class="text-slate-600 max-w-xl mx-auto">Use these seeded accounts to test cross-tenant isolation and user permissions.</p>
            </div>

            <!-- Credentials Box -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 lg:p-8 shadow-sm max-w-4xl mx-auto">
                
                <!-- Notice Banner -->
                <div class="bg-brand-50 border border-brand-100 text-brand-800 p-4 rounded-xl mb-8 flex items-start gap-3">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm">
                        <span class="font-semibold block mb-0.5 text-brand-900">Quick Login Details</span>
                        All seeded accounts listed below share the same testing password: <code class="bg-brand-100 px-2 py-0.5 rounded text-brand-950 font-mono border border-brand-200/40">password</code>
                    </div>
                </div>

                <!-- Accordion/Grid for tenants -->
                <div class="space-y-6">
                    
                    <!-- Tenant 1: Acme Corp -->
                    <div class="border border-slate-200 bg-slate-50/50 rounded-xl overflow-hidden">
                        <div class="bg-slate-50 px-5 py-4 border-b border-slate-200 flex justify-between items-center">
                            <div>
                                <h3 class="font-display font-bold text-slate-900 flex items-center gap-2 text-md">
                                    🏢 Tenant A: Acme Corp
                                </h3>
                                <span class="text-xs text-slate-500">Subdomain/Slug: <code class="bg-slate-200/80 px-1.5 py-0.5 rounded text-slate-800">acme</code></span>
                            </div>
                            <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 text-xs font-semibold rounded-full border border-indigo-100">Pro Plan (Cap: 20)</span>
                        </div>
                        <div class="p-5 overflow-x-auto">
                            <table class="w-full text-left text-sm text-slate-700 min-w-[500px]">
                                <thead>
                                    <tr class="text-slate-500 border-b border-slate-200 text-xs uppercase font-semibold">
                                        <th class="pb-3">User Role</th>
                                        <th class="pb-3">Email Address</th>
                                        <th class="pb-3">System Permissions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr>
                                        <td class="py-3.5 font-medium text-slate-900">Org Admin</td>
                                        <td class="py-3.5 font-mono text-brand-600 font-medium">admin@acme.com</td>
                                        <td class="py-3.5 text-slate-500">Settings, User invitations, full tenant view</td>
                                    </tr>
                                    <tr>
                                        <td class="py-3.5 font-medium text-slate-900">Manager</td>
                                        <td class="py-3.5 font-mono text-brand-600 font-medium">manager@acme.com</td>
                                        <td class="py-3.5 text-slate-500">Task creations, assignments, manual escalations</td>
                                    </tr>
                                    <tr>
                                        <td class="py-3.5 font-medium text-slate-900">Executive 1</td>
                                        <td class="py-3.5 font-mono text-brand-600 font-medium">john@acme.com</td>
                                        <td class="py-3.5 text-slate-500">View tasks assigned to John, closure logging</td>
                                    </tr>
                                    <tr>
                                        <td class="py-3.5 font-medium text-slate-900">Executive 2</td>
                                        <td class="py-3.5 font-mono text-brand-600 font-medium">jane@acme.com</td>
                                        <td class="py-3.5 text-slate-500">View tasks assigned to Jane, closure logging</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tenant 2: Stark Industries -->
                    <div class="border border-slate-200 bg-slate-50/50 rounded-xl overflow-hidden">
                        <div class="bg-slate-50 px-5 py-4 border-b border-slate-200 flex justify-between items-center">
                            <div>
                                <h3 class="font-display font-bold text-slate-900 flex items-center gap-2 text-md">
                                    🛡️ Tenant B: Stark Industries
                                </h3>
                                <span class="text-xs text-slate-500">Subdomain/Slug: <code class="bg-slate-200/80 px-1.5 py-0.5 rounded text-slate-800">stark</code></span>
                            </div>
                            <span class="px-2.5 py-1 bg-purple-50 text-purple-700 text-xs font-semibold rounded-full border border-purple-100">Enterprise Plan (Cap: Unlimited)</span>
                        </div>
                        <div class="p-5 overflow-x-auto">
                            <table class="w-full text-left text-sm text-slate-700 min-w-[500px]">
                                <thead>
                                    <tr class="text-slate-500 border-b border-slate-200 text-xs uppercase font-semibold">
                                        <th class="pb-3">User Role</th>
                                        <th class="pb-3">Email Address</th>
                                        <th class="pb-3">System Permissions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr>
                                        <td class="py-3.5 font-medium text-slate-900">Org Admin (Owner)</td>
                                        <td class="py-3.5 font-mono text-brand-600 font-medium">tony@stark.com</td>
                                        <td class="py-3.5 text-slate-500">Full control over Stark resources & users</td>
                                    </tr>
                                    <tr>
                                        <td class="py-3.5 font-medium text-slate-900">Manager</td>
                                        <td class="py-3.5 font-mono text-brand-600 font-medium">pepper@stark.com</td>
                                        <td class="py-3.5 text-slate-500">Task definitions, assignments to Happy Hogan</td>
                                    </tr>
                                    <tr>
                                        <td class="py-3.5 font-medium text-slate-900">Executive</td>
                                        <td class="py-3.5 font-mono text-brand-600 font-medium">happy@stark.com</td>
                                        <td class="py-3.5 text-slate-500">Completes tasks (e.g. Repair Armor Mark 85)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Super Admin Control Panel -->
                    <div class="border border-slate-200 bg-slate-50/50 rounded-xl overflow-hidden">
                        <div class="bg-slate-50 px-5 py-4 border-b border-slate-200 flex justify-between items-center">
                            <div>
                                <h3 class="font-display font-bold text-slate-900 flex items-center gap-2 text-md">
                                    ⚡ SaaS Super Admin Portal
                                </h3>
                                <span class="text-xs text-slate-500">Full system overview</span>
                            </div>
                            <span class="px-2.5 py-1 bg-amber-550/10 text-amber-700 text-xs font-semibold rounded-full border border-amber-100">System Root</span>
                        </div>
                        <div class="p-5 overflow-x-auto">
                            <table class="w-full text-left text-sm text-slate-700 min-w-[500px]">
                                <thead>
                                    <tr class="text-slate-500 border-b border-slate-200 text-xs uppercase font-semibold">
                                        <th class="pb-3">User Role</th>
                                        <th class="pb-3">Email Address</th>
                                        <th class="pb-3">System Permissions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr>
                                        <td class="py-3.5 font-medium text-slate-900">Super Admin</td>
                                        <td class="py-3.5 font-mono text-brand-600 font-bold">superadmin@saas.com</td>
                                        <td class="py-3.5 text-slate-500">Provision new tenants, suspend tenants, override plans</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Workflow Visual Section -->
        <section id="workflow" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 border-t border-slate-200 relative z-10">
            <div class="text-center mb-12">
                <h2 class="font-display font-bold text-3xl text-slate-900 mb-3">System Execution Flow</h2>
                <p class="text-slate-600 max-w-xl mx-auto">Here is how tasks flow through completion locking and the auto-escalation daemon.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 max-w-5xl mx-auto relative">
                <!-- Step 1 -->
                <div class="bg-slate-50 border border-slate-200/60 p-6 rounded-xl relative">
                    <span class="absolute top-4 right-4 text-slate-200 font-extrabold text-5xl leading-none">01</span>
                    <h4 class="font-semibold text-slate-900 mb-2 pt-6">Task Creation</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Manager assigns a task. System automatically resolves SLA hours from Organization settings based on Priority.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="bg-slate-50 border border-slate-200/60 p-6 rounded-xl relative">
                    <span class="absolute top-4 right-4 text-slate-200 font-extrabold text-5xl leading-none">02</span>
                    <h4 class="font-semibold text-slate-900 mb-2 pt-6">Work & Logs</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Executives transition tasks through statuses. All movements are logged automatically to the immutable audit database.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="bg-slate-50 border border-slate-200/60 p-6 rounded-xl relative">
                    <span class="absolute top-4 right-4 text-slate-200 font-extrabold text-5xl leading-none">03</span>
                    <h4 class="font-semibold text-slate-900 mb-2 pt-6">SLA Scan Daemon</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Laravel Scheduler executes an hourly/minutely scan. If a task deadline is breached, it marks it `escalated`.
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="bg-slate-50 border border-slate-200/60 p-6 rounded-xl relative">
                    <span class="absolute top-4 right-4 text-slate-200 font-extrabold text-5xl leading-none">04</span>
                    <h4 class="font-semibold text-slate-900 mb-2 pt-6">Role Escalation</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Escalation level increments. Responsibility shifts: Executive &rarr; Manager &rarr; Org Admin &rarr; SaaS Super Admin.
                    </p>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-slate-200 bg-slate-50 py-8 relative z-10">
            <div class="max-w-7xl mx-auto px-4 text-center text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} FlowEscalate Multi-Tenant Platform. Built with Laravel 11 & Tailwind CSS.</p>
            </div>
        </footer>

    </body>
</html>
