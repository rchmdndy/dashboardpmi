<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('/api/v1')->name('api.')->group(function (){
   Route::prefix('room_type')->controller(\App\Http\Controllers\RoomTypeController::class)->name('room_type.')->group(function (){
     Route::get('/getAll', 'getAll')->name('getAll');
     Route::get('/getDetail', 'getDetail')->name('getDetail');
   });
});

require __DIR__.'/auth.php';
