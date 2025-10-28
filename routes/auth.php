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
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('coach-register', [RegisteredUserController::class, 'create'])
        ->name('coach-register');

    Route::post('coach-register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');

    // Resend verification routes
    Route::get('resend-verification', [RegisteredUserController::class, 'showResendForm'])
        ->name('verification.resend.form');

    Route::post('resend-verification', [RegisteredUserController::class, 'resendVerification'])
        ->name('verification.resend');

    // UPDATED: Handle already-verified users to prevent redirect loops
    Route::get('verify-email', function () {
        // Check if user is logged in and already verified
        if (Auth::check() && Auth::user()->hasVerifiedEmail()) {
            // Redirect verified users to their appropriate dashboard
            if (Auth::user()->isCoach()) {
                return redirect('/coach-dashboard');
            }
            return redirect('/dashboard');
        }

        // Show verification page for unverified or guest users
        return view('auth.verify-email');
    })->name('verification.notice');

    // Verification for pending registrations
    Route::get('/verify-registration/{token}', [RegisteredUserController::class, 'verifyRegistration'])
        ->name('registration.verify');
});

Route::middleware('auth')->group(function () {
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
