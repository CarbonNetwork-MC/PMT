<?php

use App\Http\Middleware\CheckPermission;
use App\Livewire\Admin\AppSettings;
use App\Livewire\Dashboard;
use App\Livewire\Utils\BugReport;
use App\Livewire\Projects\Projects;
use App\Livewire\Admin\ManageUsers;
use App\Livewire\Admin\ManageStaff;
use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\ManageProjects;
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
    Route::get('/projects/{uuid}/backlog/{backlogId?}', Backlog::class)->name('projects.backlog.render');

    // Project - Settings
    Route::get('/projects/{uuid}/settings/overall', OverallSettings::class)->name('projects.settings.overall.render');
    Route::get('/projects/{uuid}/settings/members', MembersSettings::class)->name('projects.settings.members.render');
    Route::get('/projects/{uuid}/settings/admin', AdminSettings::class)->name('projects.settings.admin.render');

    // Utils
    Route::get('/report-a-bug', BugReport::class)->name('utils.bug-report.render');

    // Admin
    Route::get('/admin/manage-projects', ManageProjects::class)->middleware('permission:view_other_projects')->name('admin.manage-projects.render');
    Route::get('/admin/manage-users', ManageUsers::class)->middleware('permission:manage_users')->name('admin.manage-users.render');
    Route::get('/admin/manage-staff', ManageStaff::class)->middleware('permission:manage_staff')->name('admin.manage-staff.render');
    Route::get('/admin/app-settings', AppSettings::class)->middleware('permission:manage_app_settings')->name('admin.app-settings.render');
});
