<?php

use App\Controllers\ProjectsController;
use App\Controllers\AuthenticationsController;
use App\Controllers\ArquivosController; // <-- ADICIONADO
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

// Rota para processar o UPLOAD (Item 3.2)
Route::post('/projects/{id}/upload', [ArquivosController::class, 'store'])->name('arquivos.store');

// Rota para DELETAR um arquivo (Item 3.3)
Route::delete('/arquivos/{id}', [ArquivosController::class, 'destroy'])->name('arquivos.destroy');


// Authentication
// Rota para MOSTRAR o formulário de login
Route::get('/login', [AuthenticationsController::class, 'new'])->name('login.form');

// Rota para PROCESSAR a tentativa de login
Route::post('/login', [AuthenticationsController::class, 'authenticate'])->name('login.authenticate');

// Rota para SAIR do sistema (logout)
Route::get('/logout', [AuthenticationsController::class, 'destroy'])->name('logout');
