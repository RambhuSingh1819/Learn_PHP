# SaaS Multi-Tenant Task Management & SLA Escaler

A high-performance, SaaS-oriented, multi-tenant task management and Service Level Agreement (SLA) compliance tracking application built using **Laravel 13** and **PHP 8.3**. 

This system allows organizations (tenants) to manage workloads, assign tasks to executives based on real-time skill matching and availability, track SLA compliance, and automatically escalate overdue tasks up the hierarchy. It also provides a centralized control center for a Super Admin to manage subscription plans and monitor tenants.

---

## 🚀 Key Features

### 1. Robust Multi-Tenant Isolation
* **Automated Scoping**: Uses a custom global query scope (`HasTenantScope` trait) and context manager (`TenantManager`) to automatically isolate database records (Users, Tasks, Comments, Activity Logs) under an `organization_id`.
* **Tenant Middleware**: Resolves tenant contexts transparently at the request level via the `TenantScopeMiddleware` after authentication.

### 2. Tiered Subscription Plans
The SaaS platform defines three levels of subscription plans that govern tenant capabilities:
* **Basic Plan**: Max 5 users, maximum 1-level escalation depth, no custom SLA configurations, no reports export, no API access.
* **Pro Plan**: Max 20 users, maximum 3-level escalation depth, custom SLA configurations, reports export, no API access.
* **Enterprise Plan**: Unlimited users, maximum 5-level escalation depth, custom SLA configurations, reports export, and API access enabled.

### 3. Role-Based Access Control (RBAC)
* **Super Admin**: System-wide access. Oversees all organizations, creates/toggles tenants, and overrides plans.
* **Org Admin**: Administrator for a specific tenant. Full command over team management, organization settings, and task overrides.
* **Manager**: Can create, assign, and update tasks, as well as manually trigger immediate task escalations.
* **Executive**: Can update assigned tasks (transition state between `pending`, `in_progress`, and `on_hold`), add comments with attachments, and request closure.

### 4. Real-Time Executive Suitability Matching
* **Skill Matching**: When assigning or inspecting tasks, managers can query the suitability API (`/tasks/check-suitability`). It runs a keyword match between the task description and user `skills` arrays.
* **Workload Metrics**: Computes availability and classifies workload status dynamically:
  * **0 Tasks**: Low workload (`emerald`/green status)
  * **1-2 Tasks**: Moderate workload (`blue`/blue status)
  * **3-4 Tasks**: Heavy workload (`amber`/orange status)
  * **5+ Tasks**: Overloaded workload (`rose`/red status)

### 5. Automated SLA Scan & Hierarchical Escalation
* **Artisan Scanner**: Running `php artisan tasks:scan-sla` scans all incomplete, non-escalated tasks whose due dates have breached the current time.
* **Plan Constraints**: The scanner respects plan constraints, halting automated escalations when the plan's `escalation_depth` is reached, and raising direct warnings to the Org Admin.
* **Hierarchical Routing**: Automatically escalates tasks step-by-step: `Executive ➡️ Manager ➡️ Org Admin ➡️ Super Admin`.

### 6. Closure Proofs & Locking
* **Proof of Work**: Closing a task requires a closure comment (minimum 5 characters) and optional document/media proof (`pdf`, `jpg`, `png`, `zip`).
* **Immutability**: Once a task is marked `completed`, it becomes permanently locked. Its state, description, and details cannot be modified.

---

## 🛠️ Technology Stack
* **Framework**: Laravel 13
* **Language**: PHP 8.3
* **Frontend**: Tailwind CSS, Alpine.js, Laravel Breeze (Blade templates)
* **Database**: SQLite (default configuration), easily switchable to MySQL / PostgreSQL
* **Tooling**: Vite (assets compiler), Concurrently (dev process runner)

---

## 📂 Project Architecture & Key Classes

* **Multi-Tenancy Architecture**:
  * [TenantManager.php](file:///Applications/XAMPP/xamppfiles/htdocs/Project/app/Tenant/TenantManager.php): Holds and resolves the current tenant context.
  * [HasTenantScope.php](file:///Applications/XAMPP/xamppfiles/htdocs/Project/app/Tenant/HasTenantScope.php): Trait applied to Eloquent models to filter queries by the tenant's `organization_id` automatically and inject the ID on record creation.
  * [TenantScopeMiddleware.php](file:///Applications/XAMPP/xamppfiles/htdocs/Project/app/Http/Middleware/TenantScopeMiddleware.php): Activates tenant scoping once a user session is authenticated.
* **Core Business Logic Services**:
  * [UserAvailabilityService.php](file:///Applications/XAMPP/xamppfiles/htdocs/Project/app/Services/UserAvailabilityService.php): Calculates availability status, workload classifications, and computes task-executive keyword suitability scores.
* **Controllers**:
  * [DashboardController.php](file:///Applications/XAMPP/xamppfiles/htdocs/Project/app/Http/Controllers/DashboardController.php): Controls the standard tenant dashboard view and the system-wide Super Admin control center dashboard.
  * [TaskController.php](file:///Applications/XAMPP/xamppfiles/htdocs/Project/app/Http/Controllers/TaskController.php): Manages task listing, creation, updates, status transitions, closures, manual escalations, and suitability recommendations.
  * [TeamController.php](file:///Applications/XAMPP/xamppfiles/htdocs/Project/app/Http/Controllers/TeamController.php): Invites/registers team members and manages their status.
* **Console Commands**:
  * [ScanSlaTasks.php](file:///Applications/XAMPP/xamppfiles/htdocs/Project/app/Console/Commands/ScanSlaTasks.php): The background scanner that detects SLA breaches, updates task escalation levels, and sends alerts to higher authorities.

---

## ⚙️ Installation & Setup

Follow these steps to run the application locally on your machine:

### 1. Run Setup Script
Run the predefined Composer setup script. This script automatically installs backend dependencies, sets up the `.env` configuration file, generates the application key, runs migrations, installs frontend assets, and builds the production assets:
```bash
composer run setup
```

### 2. Seed Mock Data
Initialize subscription plans, a Super Admin account, and two default mock tenants with pre-loaded teams and tasks:
```bash
php artisan db:seed
```

### 3. Run Development Server
Start the Laravel development server, queue listener, log tailing, and Vite compiler concurrently using:
```bash
composer run dev
```
The application will be accessible at: `http://localhost:8000` (or the port output by the Artisan CLI).

### 4. Run Automated Test Suite
Ensure the test suite compiles and runs properly. The test suite checks profile management, team management, tenant data isolation, and Super Admin actions:
```bash
composer run test
```

---

## 🔑 Default Seeded Credentials
Use the credentials below to log in and explore different dashboards:

### System Super Admin
* **Email**: `superadmin@saas.com`
* **Password**: `password`
* *Allows access to the SaaS Tenant Control Center to add tenants, toggle their status (Active/Suspended), and override their plans.*

### Tenant 1: Acme Corp (Pro Plan)
Acme Corp has a Pro Plan (SLA config active, max 3 escalation levels).
* **Org Admin**: `admin@acme.com` (password: `password`)
* **Manager**: `manager@acme.com` (password: `password`)
* **Executive (Active, Developer)**: `john@acme.com` (password: `password`, skills: `['Payment', 'Backend', 'Database', 'API']`)
* **Executive (On Leave, Designer)**: `jane@acme.com` (password: `password`, skills: `['Design', 'Frontend', 'CSS', 'Figma']`)

### Tenant 2: Stark Industries (Enterprise Plan)
Stark Industries has an Enterprise Plan (SLA config active, max 5 escalation levels).
* **Org Admin**: `tony@stark.com` (password: `password`)
* **Manager**: `pepper@stark.com` (password: `password`)
* **Executive (Active)**: `happy@stark.com` (password: `password`, skills: `['Armor', 'Security', 'Hardware', 'Repair']`)

---

## ⏱️ SLA Scanning and Escalation Command

To test the SLA automated escalation workflow, execute:
```bash
php artisan tasks:scan-sla
```
This scans for tasks that have crossed their `due_date` without being marked as `completed`.
1. It reads the assignee's role (`executive`, `manager`, etc.).
2. It increments the task's `escalation_level`.
3. It marks the status as `escalated` and sets the `sla_breached_at` timestamp.
4. It resolves the next authority up the chain (e.g., `executive` ➡️ `manager`, or `manager` ➡️ `org_admin`) and logs an alert.
5. If the plan's maximum escalation depth is breached (e.g., depth limit of 3 for Acme Corp's Pro Plan), it skips further increments and alerts the `org_admin` directly.

---

## 📡 Web API and Routing Interface
All core interactions (in [web.php](file:///Applications/XAMPP/xamppfiles/htdocs/Project/routes/web.php)) are secured under standard authentication and tenant scoping middleware:

### Task Routes
* `POST /tasks` - Create a new task (SLA hours determined by priority & tenant configuration).
* `PATCH /tasks/{task}/status` - Update status (`pending`, `in_progress`, `on_hold`) by Assignee or Managers.
* `POST /tasks/{task}/close` - Close task (requires closure comment and proof).
* `POST /tasks/{task}/escalate` - Manually trigger manager escalation.
* `POST /tasks/{task}/comments` - Append comments and attachments.
* `POST /tasks/check-suitability` - Query executive workload and keyword suitability score.

### Team Routes
* `POST /team` - Register a new team member and assign roles/skills.
* `POST /team/{user}/toggle-status` - Active / Suspend team member status.

### Super Admin Control Center
* `POST /super/organizations` - Provision a new tenant and seed an admin user.
* `POST /super/organizations/{organization}/toggle-status` - Suspend or activate a tenant.
* `POST /super/organizations/{organization}/override-plan` - Cycle a tenant's plan.
