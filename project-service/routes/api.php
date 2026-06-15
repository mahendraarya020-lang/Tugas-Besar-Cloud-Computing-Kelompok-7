<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::middleware('jwt.verify')->group(function () {
    // Client CRUD routes
    Route::apiResource('clients', ClientController::class);

    // Project CRUD routes
    Route::apiResource('projects', ProjectController::class);

    // Nested Milestone routes (index and store per project)
    Route::get('projects/{project}/milestones', [MilestoneController::class, 'index']);
    Route::post('projects/{project}/milestones', [MilestoneController::class, 'store']);
    
    // Independent Milestone CRUD (show, update, delete)
    Route::get('milestones/{milestone}', [MilestoneController::class, 'show']);
    Route::put('milestones/{milestone}', [MilestoneController::class, 'update']);
    Route::delete('milestones/{milestone}', [MilestoneController::class, 'destroy']);

    // Nested Invoice routes (index and store per project)
    Route::get('projects/{project}/invoices', [InvoiceController::class, 'index']);
    Route::post('projects/{project}/invoices', [InvoiceController::class, 'store']);

    // Independent Invoice CRUD (show, update, delete, and download)
    Route::get('invoices/{invoice}', [InvoiceController::class, 'show']);
    Route::put('invoices/{invoice}', [InvoiceController::class, 'update']);
    Route::delete('invoices/{invoice}', [InvoiceController::class, 'destroy']);
    Route::get('invoices/{invoice}/download', [InvoiceController::class, 'download']);
});
