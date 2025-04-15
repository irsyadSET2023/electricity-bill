<?php

use App\Http\Controllers\Bill\BillController;
use App\Http\Controllers\Building\BuildingController;
use App\Http\Controllers\Building\SimpleBuildingController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/', function () {
//     return Inertia::render('Welcome');
// })->name('home');



Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    Route::group(['prefix' => 'users'], function () {
        Route::get('/', [UserController::class, 'index'])->name('users');
    });
    Route::group(['prefix' => 'buildings'], function () {
        Route::get('/', [BuildingController::class, 'index'])->name('buildings');
        Route::get('/simple', SimpleBuildingController::class)->name('buildings.simple');
    });
    Route::group(['prefix' => 'bills'], function () {
        Route::get('/', [BillController::class, 'index'])->name('bills');
        Route::post('/', [BillController::class, 'store'])->name('bills.store');
    });

});




require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
