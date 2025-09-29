<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CoachController;
use App\Http\Controllers\CoachDashboardController;


Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/registration', function (){
    return view('registration');
})->name('registration');

Route::get('/coach-dashboard', [CoachDashboardController::class, 'coachDashboard'])
    ->middleware('auth')
    ->name('coach-dashboard');
    
Route::post('/upload-spreadsheet', [CoachController::class, 'uploadSpreadsheet'])->name('upload-spreadsheet');
Route::post('/select-camp', [CoachController::class, 'selectCamp'])->name('select-camp');

Route::middleware('auth')->group(function () {


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
