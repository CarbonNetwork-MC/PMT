<?php

use App\Http\Controllers\AuthController;

use App\Livewire\Dashboard;

use App\Livewire\Projects\NewProject;
use App\Livewire\Projects\Projects;
use App\Livewire\Projects\Dashboard\Dashboard as ProjectDashboard;
use App\Livewire\Projects\Settings\Admin as ProjectSettingsAdmin;
use App\Livewire\Projects\Settings\General as ProjectSettingsGeneral;
use App\Livewire\Projects\Settings\Members as ProjectSettingsMembers;
use App\Livewire\Projects\Settings\ProjectColumns as ProjectSettingsColumns;
use App\Livewire\Projects\Sprints\NewSprint;
use App\Livewire\Projects\Sprints\Overview as SprintsOverview;

use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function() {
    // ? Login
    Route::get('/', fn() => view('auth.login'))->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])
        ->middleware('throttle:5,1')
        ->name('login.post');

    // ? Register
    Route::get('/register', fn() => view('auth.register'))->name('register');
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:5,1')
        ->name('register.post');
});

Route::middleware(['auth'])->group(function() {
    // ? Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ? Dashboard
    Route::get('/dashboard', Dashboard::class)->name('dashboard.render');

    // ? Projects
    Route::prefix('/projects')->group(function() {
        Route::get('/', Projects::class)->name('projects.render');
        Route::get('/new', NewProject::class)->name('projects.new.render');
    
        Route::get('/{uuid}/dashboard', ProjectDashboard::class)->name('projects.dashboard.render');

        Route::get('/{uuid}/sprints', SprintsOverview::class)->name('projects.sprints.render');
        Route::get('/{uuid}/sprints/new', NewSprint::class)->name('projects.sprints.new.render');

        Route::get('/{uuid}/settings/general', ProjectSettingsGeneral::class)->name('projects.settings.general.render');
        Route::get('/{uuid}/settings/members', ProjectSettingsMembers::class)->name('projects.settings.members.render');
        Route::get('/{uuid}/settings/columns', ProjectSettingsColumns::class)->name('projects.settings.columns.render');
        Route::get('/{uuid}/settings/admin', ProjectSettingsAdmin::class)->name('projects.settings.admin.render')->middleware('project-owner');
    });
});