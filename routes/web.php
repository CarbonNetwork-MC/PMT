<?php

use App\Http\Controllers\AuthController;

use App\Livewire\Dashboard;

use App\Livewire\Projects\Projects;
use App\Livewire\Projects\Dashboard\Dashboard as ProjectDashboard;

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
        Route::get('/{uuid}/dashboard', ProjectDashboard::class)->name('projects.dashboard.render');
    });
});