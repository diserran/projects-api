<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\TaskController;

Route::prefix('v1')->group(function () {
		Route::apiResource('projects', ProjectController::class);
		Route::apiResource('projects.tasks', TaskController::class)->shallow();
});
