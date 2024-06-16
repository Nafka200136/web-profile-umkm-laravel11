<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Middleware\ValidateSignature;

Route::prefix('admin')->group(function(){

    Route::middleware('guest')->group(function() {

        Route::controller(RegisteredUserController::class)->group(function() {
            Route::get('register', 'create')->name('register');
            Route::post('register', 'store');
        });

        Route::controller(AuthenticatedSessionController::class)->group(function() {
            Route::get('login', 'create')->name('login');
            Route::post('login', 'store');

        });

        Route::controller(PasswordResetLinkController::class)->group(function() {
            Route::get('forgot-password','create')->name('password.request');
            Route::post('forgot-password','store')->name('password.email');
        });

        Route::controller(NewPasswordController::class)->group(function () {
            Route::get('reset-password/{token}','create')->name('password.reset');
            Route::post('reset-password','store')->name('password.store');
        });

    });

    Route::middleware(['auth', 'verified'])->group(function () {

        Route::get('/dashboard', function () {
            return view('backend.dashboard');
        })->name('dashboard');

        Route::controller(ConfirmablePasswordController::class)->group(function () {
            Route::get('confirm-password','show')->name('password.confirm');
            Route::post('confirm-password','store');
        });

        Route::controller(ProfileController::class)->group(function () {
            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        });

        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

        Route::put('password', [PasswordController::class, 'update'])->name('password.update');

        Route::get('verify-email', EmailVerificationPromptController::class)
            ->name('verification.notice');

        Route::middleware(['signed', 'throttle:6,1'])->group(function () {
            Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)->name('verification.verify');

            Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->withoutMiddleware([ValidateSignature::class])
                ->name('verification.send');
        });

    });

});




