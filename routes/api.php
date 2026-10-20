<?php

use App\Http\Controllers\Api\ClaimController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::apiResource('claims', ClaimController::class)->only(['index', 'show']);
});
