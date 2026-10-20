<?php

use App\Http\Controllers\ClaimController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => to_route('claims.index'));

Route::resource('claims', ClaimController::class)->except('destroy');
