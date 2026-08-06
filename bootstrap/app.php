<?php

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
        $middleware->trustProxies(at: '*');
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'locale' => \App\Http\Middleware\SetLocale::class,

            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,

            'check-registration' => \App\Http\Middleware\CheckIfRegistrationIsAllowed::class,

            'project-view' => \App\Http\Middleware\EnsureUserCanViewProject::class,
            'project-owner-or-admin' => \App\Http\Middleware\EnsureUserIsProjectOwnerOrAdmin::class,
            'project-owner' => \App\Http\Middleware\EnsureUserIsProjectOwner::class,

            'sprint-started' => \App\Http\Middleware\EnsureSprintIsStarted::class,
            'sprint-archived' => \App\Http\Middleware\EnsureSprintIsArchived::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
