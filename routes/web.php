<?php

use App\Http\Controllers\AuthController;

use App\Livewire\Colors;
use App\Livewire\Dashboard;

use App\Livewire\Projects\NewProject;
use App\Livewire\Projects\Projects;

use App\Livewire\Projects\Archive\Overview as ProjectArchiveOverview;

use App\Livewire\Projects\Backlog\Overview as ProjectBacklogOverview;

use App\Livewire\Projects\Board\Board as ProjectBoard;

use App\Livewire\Projects\Dashboard\Dashboard as ProjectDashboard;

use App\Livewire\Projects\Settings\Admin as ProjectSettingsAdmin;
use App\Livewire\Projects\Settings\General as ProjectSettingsGeneral;
use App\Livewire\Projects\Settings\Members as ProjectSettingsMembers;
use App\Livewire\Projects\Settings\ProjectColumns as ProjectSettingsColumns;
use App\Livewire\Projects\Settings\Columns\NewColumn as ProjectSettingsColumnsNewColumn;
use App\Livewire\Projects\Settings\Columns\EditColumn as ProjectSettingsColumnsEditColumn;

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
    Route::get('/register', fn() => view('auth.register'))
        ->middleware('check-registration')
        ->name('register');
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware(['check-registration', 'throttle:5,1'])
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

        Route::get('/{uuid}/archive', ProjectArchiveOverview::class)->name('projects.archive.render');

        Route::get('/{uuid}/backlog', ProjectBacklogOverview::class)->name('projects.backlog.render');

        Route::get('/{uuid}/board/{sprintUuid}', ProjectBoard::class)
            ->middleware('sprint-started')
            ->name('projects.board.render');

        Route::get('/{uuid}/sprints', SprintsOverview::class)->name('projects.sprints.render');
        Route::get('/{uuid}/sprints/new', NewSprint::class)->name('projects.sprints.new.render');

        Route::get('/{uuid}/settings/general', ProjectSettingsGeneral::class)->name('projects.settings.general.render');
        Route::get('/{uuid}/settings/members', ProjectSettingsMembers::class)->name('projects.settings.members.render');
        Route::get('/{uuid}/settings/columns', ProjectSettingsColumns::class)->name('projects.settings.columns.render');
        Route::get('/{uuid}/settings/columns/new', ProjectSettingsColumnsNewColumn::class)->name('projects.settings.columns.new.render');
        Route::get('/{uuid}/settings/columns/{columnId}/edit', ProjectSettingsColumnsEditColumn::class)->name('projects.settings.columns.edit.render');
        Route::get('/{uuid}/settings/admin', ProjectSettingsAdmin::class)
            ->middleware('project-owner')
            ->name('projects.settings.admin.render');
    });
});

// ? Colors (Replacement for the old tailwind safelist, this page is not visitable for users, it is used for loading the tailwind colors)
Route::middleware(['auth', 'role:Superadmin'])->group(function() {
    Route::get('/colors', Colors::class)->name('colors.render');
});