<?php

use App\Livewire\Dashboard;
use App\Livewire\Projects\Sprints\Overview as SprintsOverview;
use App\Livewire\Projects\Backlog;
use App\Livewire\Projects\Settings;
use App\Livewire\Projects\Overview;
use App\Livewire\Projects\CreateProject;
use App\Livewire\Projects\Board as ProjectDashboard;
use Illuminate\Support\Facades\Route;

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
    Route::get('/projects/{uuid}/settings', Settings::class)->name('projects.settings.render');
});
