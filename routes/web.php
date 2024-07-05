<?php

use App\Livewire\Dashboard;
use App\Livewire\Projects\Overview;
use App\Livewire\Projects\Dashboard as ProjectDashboard;
use App\Livewire\Projects\CreateProject;
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
    Route::get('/projects/view/{uuid}', ProjectDashboard::class)->name('projects.dashboard.render');

    Route::get('/projects/create', CreateProject::class)->name('projects.create');
});
