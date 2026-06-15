<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware([\Illuminate\Cookie\Middleware\EncryptCookies::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/clients', [DashboardController::class, 'storeClient'])->name('clients.store');
    Route::post('/projects', [DashboardController::class, 'storeProject'])->name('projects.store');
    Route::delete('/projects/{id}', [DashboardController::class, 'deleteProject'])->name('projects.destroy');
    Route::post('/projects/{project}/milestones', [DashboardController::class, 'storeMilestone'])->name('milestones.store');
    Route::post('/projects/{project}/invoices', [DashboardController::class, 'storeInvoice'])->name('invoices.store');
    Route::post('/projects/{id}/status', [DashboardController::class, 'updateProjectStatus'])->name('projects.status');
    Route::get('/invoices/{id}/download', [DashboardController::class, 'downloadInvoice'])->name('invoices.download');
});

