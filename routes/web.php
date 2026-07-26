<?php

use App\Http\Controllers\AuthController;
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
    Route::get('/dashboard', \App\Livewire\Dashboard::class)->name('dashboard.render');

    // ? Profile
    Route::prefix('/profile')->group(function() {
        Route::get('/', \App\Livewire\Profile\Overview::class)->name('profile.overview.render');
    });

    // ? Projects
    Route::prefix('/projects')->group(function() {
        Route::get('/', \App\Livewire\Projects\Projects::class)->name('projects.render');
        Route::get('/new', \App\Livewire\Projects\NewProject::class)->name('projects.new.render');
    
        Route::middleware(['project-view'])->group(function() {
            // ? Dashboard
            Route::get('/{uuid}/dashboard', \App\Livewire\Projects\Dashboard\Dashboard::class)
                ->name('projects.dashboard.render');

            // ? Archive
            Route::get('/{uuid}/archive', \App\Livewire\Projects\Archive\Overview::class)
                ->name('projects.archive.render');

            Route::get('/{uuid}/archive/{sprintUuid}', \App\Livewire\Projects\Archive\Board::class)
                ->middleware('sprint-archived')
                ->name('projects.archive.board.render');

            // ? Backlog
            Route::get('/{uuid}/backlog', \App\Livewire\Projects\Backlog\Overview::class)
                ->name('projects.backlog.render');

            // ? Board
            Route::get('/{uuid}/board/{sprintUuid}', \App\Livewire\Projects\Board\Board::class)
                ->middleware('sprint-started')
                ->name('projects.board.render');

            // ? Sprints
            Route::get('/{uuid}/sprints', \App\Livewire\Projects\Sprints\Overview::class)
                ->name('projects.sprints.render');

            Route::get('/{uuid}/sprints/new', \App\Livewire\Projects\Sprints\NewSprint::class)
                ->name('projects.sprints.new.render');

            // ? Settings
            Route::get('/{uuid}/settings/general', \App\Livewire\Projects\Settings\General::class)
                ->name('projects.settings.general.render');

            Route::get('/{uuid}/settings/members', \App\Livewire\Projects\Settings\Members::class)
                ->name('projects.settings.members.render');

            Route::get('/{uuid}/settings/columns', \App\Livewire\Projects\Settings\ProjectColumns::class)
                ->middleware('project-owner-or-admin')
                ->name('projects.settings.columns.render');

            Route::get('/{uuid}/settings/columns/new', \App\Livewire\Projects\Settings\Columns\NewColumn::class)
                ->middleware('project-owner-or-admin')
                ->name('projects.settings.columns.new.render');

            Route::get('/{uuid}/settings/columns/{columnId}/edit', \App\Livewire\Projects\Settings\Columns\EditColumn::class)
                ->middleware('project-owner-or-admin')
                ->name('projects.settings.columns.edit.render');

            Route::get('/{uuid}/settings/admin', \App\Livewire\Projects\Settings\Admin::class)
                ->middleware('project-owner')
                ->name('projects.settings.admin.render');
        });
    });

    // ? Admin
    Route::prefix('/admin')->group(function() {
        Route::get('/', \App\Livewire\Admin\Dashboard::class)
            ->middleware(['role:Superadmin|Admin'])
            ->name('admin.dashboard.render');

        // ? Users
        Route::get('/users', \App\Livewire\Admin\Users\Overview::class)
            ->middleware(['role:Superadmin|Admin', 'permission:manage-users'])
            ->name('admin.users.render');

        Route::get('/users/edit/{uuid}', \App\Livewire\Admin\Users\EditUser::class)
            ->middleware(['role:Superadmin|Admin', 'permission:manage-users'])
            ->name('admin.users.edit.render');
        
        // ? Roles and Permissions
        Route::get('/roles-and-permissions', \App\Livewire\Admin\RolesAndPermissions\Overview::class)
            ->middleware(['role:Superadmin|Admin', 'permission:manage-permissions'])
            ->name('admin.roles-and-permissions.render');

        Route::get('/roles-and-permissions/create-permission', \App\Livewire\Admin\RolesAndPermissions\CreatePermission::class)
            ->middleware(['role:Superadmin|Admin', 'permission:manage-permissions'])
            ->name('admin.roles-and-permissions.create-permission.render');

        Route::get('/roles-and-permissions/edit-permission/{uuid}', \App\Livewire\Admin\RolesAndPermissions\EditPermission::class)
            ->middleware(['role:Superadmin|Admin', 'permission:manage-permissions'])
            ->name('admin.roles-and-permissions.edit-permission.render');

        Route::get('/roles-and-permissions/create-role', \App\Livewire\Admin\RolesAndPermissions\CreateRole::class)
            ->middleware(['role:Superadmin|Admin', 'permission:manage-roles'])
            ->name('admin.roles-and-permissions.create-role.render');

        Route::get('/roles-and-permissions/edit-role/{uuid}', \App\Livewire\Admin\RolesAndPermissions\EditRole::class)
            ->middleware(['role:Superadmin|Admin', 'permission:manage-roles'])
            ->name('admin.roles-and-permissions.edit-role.render');

        // ? Invite Codes
        Route::get('/invite-codes', \App\Livewire\Admin\InviteCodes\Overview::class)
            ->middleware(['role:Superadmin|Admin', 'permission:manage-users'])
            ->name('admin.invite-codes.render');
    });
});

// ? Colors (Replacement for the old tailwind safelist, this page is not visitable for users, it is used for loading the tailwind colors)
Route::middleware(['auth', 'role:Superadmin'])->group(function() {
    Route::get('/colors', \App\Livewire\Colors::class)
        ->name('colors.render');
});