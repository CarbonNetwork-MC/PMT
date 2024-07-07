<?php

use App\Livewire\Dashboard;
use App\Livewire\Projects\Backlog;
use App\Livewire\Projects\Overview;
use Illuminate\Support\Facades\Route;
use App\Livewire\Projects\Board as ProjectDashboard;
use App\Livewire\Projects\Settings\Admin as AdminSettings;
use App\Livewire\Projects\Settings\Overall as OverallSettings;
use App\Livewire\Projects\Settings\Members as MembersSettings;
use App\Livewire\Projects\Sprints\Overview as SprintsOverview;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Dashboard
    Route::get('/dashboard', Dashboard::class)->name('dashboard.render');

    // Projects
    Route::get('/projects', Overview::class)->name('projects.overview.render');
    Route::get('/projects/{uuid}', ProjectDashboard::class)->name('projects.board.render');
    Route::get('/projects/{uuid}/sprints', SprintsOverview::class)->name('projects.sprints.render');
    Route::get('/projects/{uuid}/backlog', Backlog::class)->name('projects.backlog.render');

    // Project - Settings
    Route::get('/projects/{uuid}/settings/overall', OverallSettings::class)->name('projects.settings.overall.render');
    Route::get('/projects/{uuid}/settings/members', MembersSettings::class)->name('projects.settings.members.render');
    Route::get('/projects/{uuid}/settings/admin', AdminSettings::class)->name('projects.settings.admin.render');
});
