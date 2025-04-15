<?php

use App\Http\Controllers\Bill\BillController;
use App\Http\Controllers\Building\BuildingController;
use App\Http\Controllers\Building\SimpleBuildingController;
use App\Http\Controllers\User\SimpleUserController;
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
        Route::get('/simple', SimpleUserController::class)->name('users.simple');
    });
    Route::group(['prefix' => 'buildings'], function () {
        Route::get('/', [BuildingController::class, 'index'])->name('buildings');
        Route::get('/simple', SimpleBuildingController::class)->name('buildings.simple');
        Route::post('/', [BuildingController::class, 'store'])->name('buildings.store');
        Route::put('/{building}', [BuildingController::class, 'update'])->name('buildings.update');
        Route::delete('/{building}', [BuildingController::class, 'destroy'])->name('buildings.destroy');
    });
    Route::group(['prefix' => 'bills'], function () {
        Route::get('/', [BillController::class, 'index'])->name('bills');
        Route::post('/', [BillController::class, 'store'])->name('bills.store');
        Route::put('/{bill}', [BillController::class, 'update'])->name('bills.update');
        Route::delete('/{bill}', [BillController::class, 'destroy'])->name('bills.destroy');
    });

});




require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
