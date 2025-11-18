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
use App\Http\Controllers\SportsController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sport/{sport}', [SportsController::class, 'show'])->name('sport.show');
Route::get('/sport/{sport}/about', [SportsController::class, 'about'])->name('sport.about');
Route::get('/sport/{sport}/camps', [SportsController::class, 'camps'])->name('sport.camps');
Route::get('/sport/{sport}/faqs', [SportsController::class, 'faqs'])->name('sport.faqs');

// Temporarily change this:
Route::get('/user-profile', function () {
    return view('user-profile');
})->middleware(['auth'])->name('user-profile');  // Removed 'verified' temporarily

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

    Route::get('/teams-display', [CoachController::class, 'showTeamsDisplay'])
        ->name('teams-display');

    Route::get('/download-teams-excel', [CoachController::class, 'downloadTeamsExcel'])
        ->name('download-teams-excel');

    Route::get('/camp-registrations', [CoachDashboardController::class, 'campRegistrations'])
        ->name('camp-registrations');

    Route::get('/create-camp', [CoachController::class, 'showCreateCampForm'])
        ->name('create-camp');

    Route::get('/edit-camp', [CoachController::class, 'showEditCampForm'])
        ->name('edit-camp');
    Route::get('/edit-camp/{id}/data', [CoachController::class, 'getCampData'])
        ->name('edit-camp.data');
    Route::put('/edit-camp/{id}', [CoachController::class, 'updateCamp'])
        ->name('edit-camp.update');

    Route::post('/create-camp', [CoachController::class, 'storeCamp'])
        ->name('store-camp');
});

// Protected routes for admin users only
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])
        ->name('admin.dashboard');

    Route::get('/finances', [App\Http\Controllers\AdminController::class, 'finances'])
        ->name('admin.finances');

    Route::post('/finances/export', [App\Http\Controllers\AdminController::class, 'exportFinances'])
        ->name('admin.finances.export');

    Route::get('/invite-coach', [App\Http\Controllers\AdminController::class, 'inviteCoach'])
        ->name('admin.invite-coach');

    Route::get('/manage-coaches', [App\Http\Controllers\AdminController::class, 'manageCoaches'])
        ->name('admin.manage-coaches');

    Route::get('/manage-coaches/{id}/edit', [App\Http\Controllers\AdminController::class, 'editCoach'])
        ->name('admin.edit-coach');

    Route::put('/manage-coaches/{id}', [App\Http\Controllers\AdminController::class, 'updateCoach'])
        ->name('admin.update-coach');

    Route::delete('/manage-coaches/{id}', [App\Http\Controllers\AdminController::class, 'deleteCoach'])
        ->name('admin.delete-coach');

    // Sports management routes
    Route::get('/sports', [App\Http\Controllers\Admin\AdminSportsController::class, 'index'])
        ->name('admin.manage-sports');
    Route::post('/sports', [App\Http\Controllers\Admin\AdminSportsController::class, 'store'])
        ->name('admin.sports.store');
    Route::get('/sports/{id}/data', [App\Http\Controllers\Admin\AdminSportsController::class, 'show'])
        ->name('admin.sports.show');
    Route::put('/sports/{id}', [App\Http\Controllers\Admin\AdminSportsController::class, 'update'])
        ->name('admin.sports.update');
    Route::delete('/sports/{id}', [App\Http\Controllers\Admin\AdminSportsController::class, 'destroy'])
        ->name('admin.sports.destroy');
});

// Regular authenticated user routes
Route::middleware(['auth'])->group(function () {
    Route::get('/registration', [RegistrationController::class, 'show'])
        ->middleware('auth')
        ->name('registration.form');
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/parent/store', [App\Http\Controllers\ParentController::class, 'store'])
        ->name('parent.store');

    Route::post('/profile/update-ajax', [ProfileController::class, 'updateAjax'])
        ->name('profile.update.ajax');

    Route::post('/player/update-ajax', [PlayerController::class, 'updateAjax'])
        ->name('player.update.ajax');

    Route::post('/player/delete-ajax', [PlayerController::class, 'deleteAjax'])
        ->name('player.delete.ajax');

    Route::post('/player/add-ajax', [PlayerController::class, 'addAjax'])
        ->name('player.add.ajax');
});

require __DIR__ . '/auth.php';
