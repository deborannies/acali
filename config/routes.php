<?php

use App\Controllers\ProjectsController;
use App\Controllers\AuthenticationsController;
use App\Controllers\ArquivosController;
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

// Processar o UPLOAD
Route::post('/projects/{id}/upload', [ArquivosController::class, 'store'])->name('arquivos.store');

// DELETAR um arquiVO
Route::delete('/arquivos/{id}', [ArquivosController::class, 'destroy'])->name('arquivos.destroy');

// Adicionar membro
Route::post('/projects/{id}/members', [ProjectsController::class, 'addMember'])->name('projects.addMember');

// Remover membro
Route::delete('/projects/{id}/members', [ProjectsController::class, 'removeMember'])->name('projects.removeMember');

// MOSTRAR o formulário de login
Route::get('/login', [AuthenticationsController::class, 'new'])->name('login.form');

// PROCESSAR a tentativa de login
Route::post('/login', [AuthenticationsController::class, 'authenticate'])->name('login.authenticate');

// SAIR do sistema (logout)
Route::get('/logout', [AuthenticationsController::class, 'destroy'])->name('logout');
