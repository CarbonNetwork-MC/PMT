<?php

use App\Livewire\Dashboard;
use App\Livewire\Projects\Projects;
use Illuminate\Support\Facades\Route;
use App\Livewire\Projects\Boards\Board;
use App\Livewire\Projects\Backlog\Backlog;
use App\Livewire\Projects\Overview\Overview;
use App\Livewire\Projects\Settings\Admin as AdminSettings;
use App\Livewire\Projects\Settings\Overall as OverallSettings;
use App\Livewire\Projects\Settings\Members as MembersSettings;
use App\Livewire\Projects\Sprints\Overview as SprintsOverview;

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Dashboard
    Route::get('/dashboard', Dashboard::class)->name('dashboard.render');

    // Projects
    Route::get('/projects', Projects::class)->name('projects.projects.render');
    Route::get('/projects/{uuid}', Overview::class)->name('projects.overview.render');
    Route::get('/projects/boards/{uuid}', Board::class)->name('projects.board.render');
    Route::get('/projects/{uuid}/sprints', SprintsOverview::class)->name('projects.sprints.render');
    Route::get('/projects/{uuid}/backlog/{backlogId}', Backlog::class)->name('projects.backlog.render');

    // Project - Settings
    Route::get('/projects/{uuid}/settings/overall', OverallSettings::class)->name('projects.settings.overall.render');
    Route::get('/projects/{uuid}/settings/members', MembersSettings::class)->name('projects.settings.members.render');
    Route::get('/projects/{uuid}/settings/admin', AdminSettings::class)->name('projects.settings.admin.render');  
});
