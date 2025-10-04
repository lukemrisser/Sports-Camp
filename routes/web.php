<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\CoachController;
use App\Http\Controllers\CoachDashBoardController;

use App\Http\Controllers\Auth\RegisteredUserController;


Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/registration', function (){
    return view('registration');
})->name('registration');

Route::get('coach-register', [RegisteredUserController::class, 'create'])
    ->name('coach-register')
    ->middleware('guest');

Route::post('coach-register', [RegisteredUserController::class, 'store'])
    ->middleware('guest');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/coach_dashboard', [CoachDashboardController::class, 'coachDashboard'])->name('coach_dashboard');

Route::post('/players', [PlayerController::class, 'store'])->name('players.store');

Route::post('/upload-spreadsheet', [CoachController::class, 'uploadSpreadsheet'])->name('upload-spreadsheet');
Route::post('/select-camp', [CoachController::class, 'selectCamp'])->name('select-camp');


// Protected routes for coaches only
Route::middleware(['auth', 'coach'])->group(function () {
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

Route::post('/home', [CoachController::class, 'uploadSpreadsheet'])->name('coach.uploadSpreadsheet');
Route::post('/coach/organize-teams', [CoachController::class, 'selectCamp'])->name('coach.selectCamp');
require __DIR__.'/auth.php';
