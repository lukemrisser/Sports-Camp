<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CoachController;


Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/registration', function (){
    return view('registration');
})->name('registration');

Route::middleware('auth')->group(function () {
    Route::get('/coach-dashboard', [CoachController::class, 'dashboard'])->name('coach-dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__.'/auth.php';
