<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/home', function () {
    return view('welcome');
})->name('home');

Route::get('/', function () {
    return view('template');
})->name('template');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/bookings', [BookingController::class, 'bookRoom'])->name('bookings.store');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::get('/bookings/pay', [BookingController::class, 'pay'])->name('bookings.pay');
    Route::get('/reports/create', [ReportController::class, 'createReport'])->name('reports.create');
});


    Route::post('midtrans/notification_handling', [NotificationController::class, 'handleMidtransNotification'])->withoutMiddleware('veri');

require __DIR__.'/auth.php';
