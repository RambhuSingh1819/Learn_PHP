<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Middleware\TenantScopeMiddleware;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified', TenantScopeMiddleware::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Web APIs for task interactions via Alpine.js / Fetch
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.status');
    Route::post('/tasks/{task}/close', [TaskController::class, 'close'])->name('tasks.close');
    Route::post('/tasks/{task}/escalate', [TaskController::class, 'escalate'])->name('tasks.escalate');
    Route::post('/tasks/{task}/comments', [TaskController::class, 'addComment'])->name('tasks.comment');
    Route::post('/tasks/check-suitability', [TaskController::class, 'checkSuitability'])->name('tasks.check-suitability');

    // Super Admin Control Center routes
    Route::post('/super/organizations', [DashboardController::class, 'storeTenant'])->name('super.organizations.store');
    Route::post('/super/organizations/{organization}/toggle-status', [DashboardController::class, 'toggleTenantStatus'])->name('super.organizations.toggle-status');
    Route::post('/super/organizations/{organization}/override-plan', [DashboardController::class, 'overrideTenantPlan'])->name('super.organizations.override-plan');

    // Team Management routes
    Route::post('/team', [\App\Http\Controllers\TeamController::class, 'store'])->name('team.store');
    Route::post('/team/{user}/toggle-status', [\App\Http\Controllers\TeamController::class, 'toggleStatus'])->name('team.toggle-status');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
