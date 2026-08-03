<?php

use App\Http\Controllers\Consultant\DailyWorkController;
use App\Http\Controllers\Consultant\DashboardController;
use App\Http\Controllers\HR\ConsultantController;
use App\Http\Controllers\HR\SiteController;
use App\Http\Controllers\HR\TaskController;
use App\Http\Controllers\Reports\PerformanceReportController;
use App\Http\Controllers\Reports\SiteActivityReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // ==========================================
    // 1. HR Routes (مسارات إدارة الموارد البشرية)
    // ==========================================
    Route::middleware(['role:hr'])->prefix('hr')->name('hr.')->group(function () {
        
        // إدارة الاستشاريين
        Route::get('/consultants', [ConsultantController::class, 'index'])->name('consultants.index');
        Route::get('/consultants/{id}', [ConsultantController::class, 'show'])->name('consultants.show');
        Route::put('/consultants/{id}', [ConsultantController::class, 'update'])->name('consultants.update');
        Route::patch('/consultants/{id}/status', [ConsultantController::class, 'changeStatus'])->name('consultants.change-status');

        // إدارة المواقع
        Route::get('/sites', [SiteController::class, 'index'])->name('sites.index');
        Route::post('/sites', [SiteController::class, 'store'])->name('sites.store');
        Route::put('/sites/{id}', [SiteController::class, 'update'])->name('sites.update');

        // Task Builder - إدارة المهام
        Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
        Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
        Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
        Route::get('/tasks/{id}/preview', [TaskController::class, 'preview'])->name('tasks.preview');
    });

    // ==========================================
    // 2. Consultant Routes (مسارات الاستشاريين)
    // ==========================================
    Route::middleware(['role:consultant'])->prefix('consultant')->name('consultant.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'show'])->name('dashboard');
        
        // دورة العمل الميداني
        Route::get('/sites', [DailyWorkController::class, 'getSites'])->name('sites');
        Route::post('/visit/start', [DailyWorkController::class, 'startVisit'])->name('visit.start');
        Route::get('/visit/{visitId}/tasks', [DailyWorkController::class, 'getTasks'])->name('visit.tasks');
        Route::post('/visit/submit', [DailyWorkController::class, 'submitVisit'])->name('visit.submit');
    });

    // ==========================================
    // 3. Reports Routes (مسارات التقارير)
    // ==========================================
    Route::middleware(['permission:view_reports'])->prefix('reports')->name('reports.')->group(function () {
        Route::get('/performance', [PerformanceReportController::class, 'index'])->name('performance');
        Route::get('/site-activity', [SiteActivityReportController::class, 'index'])->name('site-activity');
    });
});
