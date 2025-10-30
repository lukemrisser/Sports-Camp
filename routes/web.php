<?php
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\CoachController;
use App\Http\Controllers\CoachDashboardController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\PaymentController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/registration', [RegistrationController::class, 'show'])
    ->name('registration.form');


// Temporarily change this:
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');  // Removed 'verified' temporarily

Route::post('/players', [PlayerController::class, 'store'])->name('players.store');

// Payment routes
Route::get('/payment/{player}/{camp}', [PaymentController::class, 'show'])->name('payment.show');
Route::post('/payment/process', [PaymentController::class, 'process'])->name('payment.process');
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/cancelled', [PaymentController::class, 'cancelled'])->name('payment.cancelled');
Route::post('/stripe/webhook', [PaymentController::class, 'webhook'])->name('stripe.webhook');

// Protected routes for coaches only
Route::middleware(['auth', 'coach'])->group(function () {
    // Main coach dashboard
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

    Route::get('/create-camp', [CoachController::class, 'showCreateCampForm'])
        ->name('create-camp');

    Route::post('/create-camp', [CoachController::class, 'storeCamp'])
        ->name('store-camp');
});

// Protected routes for admin users only
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])
        ->name('admin.dashboard');
    
    Route::get('/finances', [App\Http\Controllers\AdminController::class, 'finances'])
        ->name('admin.finances');
    
    Route::get('/invite-coach', [App\Http\Controllers\AdminController::class, 'inviteCoach'])
        ->name('admin.invite-coach');
    
    Route::get('/manage-coaches', [App\Http\Controllers\AdminController::class, 'manageCoaches'])
        ->name('admin.manage-coaches');
});

// Regular authenticated user routes
Route::middleware(['auth'])->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
