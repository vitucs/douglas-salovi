<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('/', function () {
    if (session()->has('logged_in_user')) {
        return redirect()->route('dashboard');
    } else {
        return redirect()->route('auth/login');
    }
});

Route::post('/reset-user', [UserController::class, 'reset'])->name('reset.user');
Route::post('/update-user', [UserController::class, 'update'])->name('update.user');


Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/dashboard', [DashboardController::class, 'order'])->name('store.order');
Route::post('/remove-order', [DashboardController::class, 'destroyOrder'])->name('remove.order');
Route::post('/remove-employer', [DashboardController::class, 'destroyEmployer'])->name('remove.employer');


require __DIR__.'/auth.php';
