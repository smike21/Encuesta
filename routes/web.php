<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\SurveyorController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SurveyController::class, 'index'])->name('surveys.index');
Route::get('/encuestas/{survey}', [SurveyController::class, 'show'])->name('surveys.show');
Route::post('/encuestas/{survey}', [SurveyController::class, 'submit'])->name('surveys.submit');
Route::get('/admin/login', [AdminController::class, 'loginForm'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
Route::get('/admin/encuestadores', [AdminController::class, 'surveyors'])->name('admin.surveyors');
Route::get('/admin/encuestadores/crear', [AdminController::class, 'createSurveyor'])->name('admin.surveyors.create');
Route::post('/admin/encuestadores', [AdminController::class, 'storeSurveyor'])->name('admin.surveyors.store');
Route::get('/admin/encuestadores/{user}/permisos', [AdminController::class, 'surveyorAccess'])->name('admin.surveyors.access');
Route::put('/admin/encuestadores/{user}/permisos', [AdminController::class, 'updateSurveyorAccess'])->name('admin.surveyors.access.update');
Route::patch('/admin/encuestadores/{user}/estado', [AdminController::class, 'toggleSurveyor'])->name('admin.surveyors.toggle');
Route::get('/admin/encuestas/crear', [AdminController::class, 'create'])->name('admin.create');
Route::post('/admin/encuestas', [AdminController::class, 'store'])->name('admin.store');
Route::get('/admin/encuestas/{survey}/editar', [AdminController::class, 'edit'])->name('admin.edit');
Route::put('/admin/encuestas/{survey}', [AdminController::class, 'update'])->name('admin.update');
Route::get('/admin/encuestas/{survey}/resultados', [AdminController::class, 'results'])->name('admin.results');
Route::get('/admin/encuestas/{survey}/exportar', [AdminController::class, 'export'])->name('admin.export');
Route::patch('/admin/encuestas/{survey}/estado', [AdminController::class, 'toggle'])->name('admin.toggle');
Route::delete('/admin/encuestas/{survey}', [AdminController::class, 'destroy'])->name('admin.destroy');
Route::get('/admin/setup', [AdminController::class, 'setup'])->name('admin.setup');
Route::get('/encuestador', [SurveyorController::class, 'dashboard'])->name('surveyor.dashboard');
Route::get('/encuestador/encuestas/{survey}/resultados', [SurveyorController::class, 'results'])->name('surveyor.results');
