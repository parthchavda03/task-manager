<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TaskController;
use App\Http\Controllers\LoginController;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('tasks.index');
    });

    Route::resource('tasks', TaskController::class);
    Route::patch('/tasks/{task}/toggle-status', [TaskController::class, 'toggleStatus'])->name('tasks.toggle-status');
    Route::post('/tasks/reorder', [TaskController::class, 'updateOrder'])->name('tasks.reorder');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
