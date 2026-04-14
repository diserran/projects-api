<?php

use Illuminate\Support\Facades\Route;
use Webkul\ManagementPlan\Http\Controllers\ManagementPlanController;

Route::prefix(config('app.admin_path'))
    ->middleware(['web', 'admin_locale', 'user'])
    ->name('admin.management_plans.')
    ->group(function () {
        Route::controller(ManagementPlanController::class)->group(function () {
            Route::get('management-plans/time-entries', 'timeEntriesIndex')->name('time_entries.index');
            Route::post('management-plans/time-entries', 'storeTimeEntry')->name('time_entries.store');

            Route::get('management-plans/global', 'globalIndex')->name('global.index');

            Route::get('management-plans/plans', 'plansIndex')->name('plans.index');
            Route::post('management-plans/plans', 'storePlan')->name('plans.store');
            Route::put('management-plans/plans/{id}', 'updatePlan')->name('plans.update');
            Route::delete('management-plans/plans/{id}', 'destroyPlan')->name('plans.destroy');

            Route::post('management-plans/assignments', 'storeAssignment')->name('assignments.store');
            Route::delete('management-plans/assignments/{id}', 'destroyAssignment')->name('assignments.destroy');
        });
    });
