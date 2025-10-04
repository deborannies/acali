<?php

use App\Controllers\ProjectsController;
use Core\Router\Route;

Route::get('/', [ProjectsController::class, 'index'])->name('root');
Route::get('/projects', [ProjectsController::class, 'index'])->name('projects.index');
Route::get('/projects/new', [ProjectsController::class, 'new']) ->name('projects.new');
