<?php

use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RoomAssetsPrintController;
use App\Http\Controllers\UserTransactionController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/home', function () {
    return view('welcome');
})->name('home');

Route::get('/', function () {
    return redirect('/admin/login');
})->name('template');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/bookings', [BookingController::class, 'bookRoom'])->name('bookings.store');
Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
Route::get('/bookings/pay', [BookingController::class, 'pay'])->name('bookings.pay');
Route::get('/reports/create', [ReportController::class, 'createReport'])->name('reports.create');
Route::get('/reports/print', [ReportController::class, 'printReport'])->name('reports.print');
Route::get('/transactions/print', [UserTransactionController::class, 'printTransaction'])->name('transactions.print');
Route::get('/booking/print', [BookingController::class, 'printBooking'])->name('bookings.print');
Route::get('/RoomAssets/print', RoomAssetsPrintController::class)->name(name: 'RoomAssets.print');
Route::get('/reviews/print', [ReviewController::class, 'printReview'])->name(name: 'reviews.print');
Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});

Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
    ->name('password.reset');

Route::post('reset-password', [NewPasswordController::class, 'store'])
    ->name('password.store');


require __DIR__.'/auth.php';
