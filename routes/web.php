<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\DbCheckController;

// Public DB Diagnostic Route
Route::get('check-db', [DbCheckController::class, 'index'])->name('check-db');
Route::get('check-db/reset-admin', [DbCheckController::class, 'resetAdmin'])->name('check-db.reset-admin');

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/', function () { return redirect()->route('dashboard'); });
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('profile', function() { return view('profile.index'); })->name('profile');

    // Shared lead routes
    Route::get('leads', [LeadController::class, 'index'])->name('leads.index');
    Route::post('leads/bulk-assign', [LeadController::class, 'bulkAssignOperator'])->name('leads.bulk-assign');
    Route::get('leads/{id}', [LeadController::class, 'show'])->name('leads.show')->where('id', '[0-9]+');
    Route::put('leads/{id}', [LeadController::class, 'update'])->name('leads.update');
    Route::post('leads/{id}/note', [LeadController::class, 'addNote'])->name('leads.add-note');
    Route::post('leads/{id}/note-alias', [LeadController::class, 'addNote'])->name('leads.note.add');
    Route::post('leads/{id}/assign-operator', [LeadController::class, 'assignOperator'])->name('leads.assign-operator');
    Route::post('leads/{id}/assign-alias', [LeadController::class, 'assignOperator'])->name('leads.assign');
    Route::get('leads/{id}/logs', [LeadController::class, 'getLeadLogs'])->name('leads.logs');
    Route::get('leads/{id}/data', [LeadController::class, 'getLeadData'])->name('leads.data');
    
    Route::get('tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('tasks/{id}/status', [TaskController::class, 'updateStatus'])->name('tasks.status.update');

    // Admin Only Routes
    Route::middleware('role:super_admin')->group(function () {
        Route::post('leads', [LeadController::class, 'store'])->name('leads.store');
        Route::delete('leads/{id}', [LeadController::class, 'destroy'])->name('leads.destroy');

        Route::prefix('import')->name('import.')->group(function () {
            Route::get('/', [ImportController::class, 'index'])->name('index');
            Route::get('upload', function() { return redirect()->route('import.index'); });
            Route::post('upload', [ImportController::class, 'upload'])->name('upload');
            Route::get('preview', function() { return redirect()->route('import.index'); });
            Route::get('process', function() { return redirect()->route('import.index'); });
            Route::post('process', [ImportController::class, 'process'])->name('process');
        });

        Route::get('operators', [OperatorController::class, 'index'])->name('operators.index');
        Route::post('operators', [OperatorController::class, 'store'])->name('operators.store');
        Route::put('operators/{id}', [OperatorController::class, 'update'])->name('operators.update');
        Route::get('operators/{id}', [OperatorController::class, 'show'])->name('operators.show');
        Route::post('operators/{id}/toggle', [OperatorController::class, 'toggleActive'])->name('operators.toggle');

        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingController::class, 'index'])->name('index');
            Route::put('statuses', [SettingController::class, 'updateStatuses'])->name('statuses.update');
            Route::put('fraud-types', [SettingController::class, 'updateFraudTypes'])->name('fraud-types.update');
            Route::put('loss-ranges', [SettingController::class, 'updateLossRanges'])->name('loss-ranges.update');
            Route::put('wallet-types', [SettingController::class, 'updateWalletTypes'])->name('wallet-types.update');
        });
    });
});
