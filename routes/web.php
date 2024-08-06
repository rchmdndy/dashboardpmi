<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserTransactionController;
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
    Route::post('/bookings', [BookingController::class, 'bookRoom'])->name('bookings.store');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::get('/bookings/pay', [BookingController::class, 'pay'])->name('bookings.pay');
    Route::get('/reports/create', [ReportController::class, 'createReport'])->name('reports.create');
});

Route::prefix('/booking')->controller(BookingController::class)->name('booking')->group(function(){
    Route::post('/generateToken', 'bookRoom')->name('bookRoom');
})->withoutMiddleware("auth:sanctum");

Route::prefix('/user_transaction')->controller(UserTransactionController::class)->name('user_transaction.')->group(function(){
   Route::get('/history', 'getTransaction')->name('history');
});



    Route::post('midtrans/notification_handling', [NotificationController::class, 'handleMidtransNotification'])->withoutMiddleware('veri');

require __DIR__.'/auth.php';
