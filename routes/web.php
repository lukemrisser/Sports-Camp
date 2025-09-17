<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/registration', function () {
    return view('registration');
})->name('registration');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/youth-sports-registration', function () {
    return view('registrations.youth-sports');
})->name('youth-sports-registration');

Route::get('/adult-fitness-registration', function () {
    return view('registrations.adult-fitness');
})->name('adult-fitness-registration');

Route::get('/team-registration', function () {
    return view('registrations.team');
})->name('team-registration');

Route::get('/coach-application', function () {
    return view('registrations.coach-application');
})->name('coach-application');

Route::get('/registration', function (){
    return view('registration');
})->name('registration');

Route::get('/coachdashboard', function (){
    return view('coachdashboard');
})->name('coachdashboard');

require __DIR__.'/auth.php';
