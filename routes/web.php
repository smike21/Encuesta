<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\SurveyController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SurveyController::class, 'index'])->name('surveys.index');
Route::get('/encuestas/{survey}', [SurveyController::class, 'show'])->name('surveys.show');
Route::post('/encuestas/{survey}', [SurveyController::class, 'submit'])->name('surveys.submit');
Route::get('/admin/login', [AdminController::class, 'loginForm'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
Route::get('/admin/encuestas/crear', [AdminController::class, 'create'])->name('admin.create');
Route::post('/admin/encuestas', [AdminController::class, 'store'])->name('admin.store');
Route::get('/admin/encuestas/{survey}/resultados', [AdminController::class, 'results'])->name('admin.results');
Route::get('/admin/encuestas/{survey}/exportar', [AdminController::class, 'export'])->name('admin.export');
Route::patch('/admin/encuestas/{survey}/estado', [AdminController::class, 'toggle'])->name('admin.toggle');
Route::delete('/admin/encuestas/{survey}', [AdminController::class, 'destroy'])->name('admin.destroy');
Route::get('/admin/setup', [AdminController::class, 'setup'])->name('admin.setup');
