<?php

use Illuminate\Support\Facades\Route;
use Webkul\TimeTracker\Http\Controllers\TimeTrackerController;

Route::prefix('timetracker')->group(function () {
    Route::get('', [TimeTrackerController::class, 'index'])->name('admin.timetracker.index');
});
