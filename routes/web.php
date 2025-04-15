<?php

use App\Http\Controllers\Bill\BillController;
use App\Http\Controllers\Building\BuildingController;
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

    Route::get('bills', [BillController::class, 'index'])->name('bills');

    Route::get('buildings', [BuildingController::class, 'index'])->name('buildings');

    Route::get('users', [UserController::class, 'index'])->name('users');
});




require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
