<?php

use App\Http\Controllers\Building\BuildingController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('buildings', [BuildingController::class, 'index'])->name('buildings');
});
