<?php

use App\Controllers\ProjectsController;
use Core\Router\Route;

// Create
Route::get('/projects/new', [ProjectsController::class, 'new'])->name('projects.new');
Route::post('/projects', [ProjectsController::class, 'create'])->name('projects.create');

// Retrieve
Route::get('/', [ProjectsController::class, 'index'])->name('root');
Route::get('/projects', [ProjectsController::class, 'index'])->name('projects.index');
Route::get('/projects/{id}', [ProjectsController::class, 'show'])->name('projects.show');

// Update
Route::get('/projects/{id}/edit', [ProjectsController::class, 'edit'])->name('projects.edit');
Route::put('/projects/{id}', [ProjectsController::class, 'update'])->name('projects.update');

// Delete
Route::delete('/projects/{id}', [ProjectsController::class, 'destroy'])->name('projects.destroy');
