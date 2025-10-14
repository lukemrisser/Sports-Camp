<?php
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\CoachController;
use App\Http\Controllers\CoachDashboardController;
use App\Http\Controllers\PaymentController;

use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/registration', function (){
    return view('registration');
})->name('registration.form');

Route::get('coach-register', [RegisteredUserController::class, 'create'])
    ->name('coach-register')
    ->middleware('guest');

Route::post('coach-register', [RegisteredUserController::class, 'store'])
    ->middleware('guest');

// Remove 'verified' from dashboard if you want unverified users to access it
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');  // Removed 'verified'

Route::get('/coach_dashboard', [CoachDashboardController::class, 'coachDashboard'])->name('coach_dashboard');

Route::post('/players', [PlayerController::class, 'store'])->name('players.store');

// Payment routes
Route::get('/payment/{player}', [PaymentController::class, 'show'])->name('payment.show');
Route::post('/payment/process', [PaymentController::class, 'process'])->name('payment.process');
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/cancelled', [PaymentController::class, 'cancelled'])->name('payment.cancelled');
Route::post('/stripe/webhook', [PaymentController::class, 'webhook'])->name('stripe.webhook');

Route::post('/upload-spreadsheet', [CoachController::class, 'uploadSpreadsheet'])->name('upload-spreadsheet');
Route::post('/select-camp', [CoachController::class, 'selectCamp'])->name('select-camp');

// Protected routes for coaches only - removed 'verified'
Route::middleware(['auth', 'verified', 'coach'])->group(function () {  // Removed 'verified'
    // Main coach dashboard with optional camp_id parameter
    Route::get('/coach-dashboard', [CoachDashboardController::class, 'dashboard'])
        ->name('coach-dashboard');

    // Other coach routes
    Route::get('/organize-teams', [CoachController::class, 'getCampsForCoach'])
        ->name('organize-teams');

    Route::post('/upload-spreadsheet', [CoachController::class, 'uploadSpreadsheet'])
        ->name('upload-spreadsheet');

    Route::post('/select-camp', [CoachController::class, 'selectCamp'])
        ->name('select-camp');

    Route::get('/camp-registrations', [CoachDashboardController::class, 'campRegistrations'])
        ->name('camp-registrations');
});

// Regular authenticated user routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Add profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/organize-teams', [CoachController::class, 'getCampsForCoach'])
    ->middleware('auth')
    ->name('organize-teams');

// Routes used by the organize-teams view forms
Route::post('/coach/upload-spreadsheet', [CoachController::class, 'uploadSpreadsheet'])->name('coach.uploadSpreadsheet');
Route::post('/coach/select-camp', [CoachController::class, 'selectCamp'])->name('coach.selectCamp');

// Email Verification Routes (these handle the verification process)
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/coach-dashboard')->with('success', 'Email successfully verified!');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Verification link sent!');
    })->middleware('throttle:6,1')->name('verification.send');
});

require __DIR__.'/auth.php';
