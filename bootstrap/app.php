<?php

use App\Http\Middleware\CheckIfRegistrationIsAllowed;
use App\Http\Middleware\EnsureSprintIsArchived;
use App\Http\Middleware\EnsureSprintIsStarted;
use App\Http\Middleware\EnsureUserCanViewProject;
use App\Http\Middleware\EnsureUserIsProjectOwner;
use App\Http\Middleware\EnsureUserIsProjectOwnerOrAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,

            'check-registration' => CheckIfRegistrationIsAllowed::class,

            'project-view' => EnsureUserCanViewProject::class,
            'project-owner-or-admin' => EnsureUserIsProjectOwnerOrAdmin::class,
            'project-owner' => EnsureUserIsProjectOwner::class,

            'sprint-started' => EnsureSprintIsStarted::class,
            'sprint-archived' => EnsureSprintIsArchived::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
