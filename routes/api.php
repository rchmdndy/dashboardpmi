<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomTypeController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function (){
    Route::prefix('room_type')->controller(RoomTypeController::class)->name('room_type.')->group(function (){
        Route::get('/getAll', 'getAll')->name('getAll');
        Route::get('/getDetail', 'getDetail')->name('getDetail');
    });
    Route::prefix('room')->controller(RoomController::class)->name('room.')->group(function(){
        Route::get('/getAll', 'getAll')->name('getAll');
        Route::get('/getDetail', 'getDetail')->name('getDetail');
        Route::get('/getByRoomType', 'getByRoomType')->name('getByRoomType');
    });

});


Route::get('/getBookings', [BookingController::class, 'getBookings'])->name('bookings.get');
Route::post('/bookings', [BookingController::class, 'bookRoom'])->name('bookings.store');
Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');


Route::post('login', [AuthenticatedSessionController::class, 'login']);
Route::post('register', [AuthenticatedSessionController::class, 'register']);
Route::post('/logout', [AuthenticatedSessionController::class, 'logout'])->middleware('jwt.auth')->name('logout');
Route::post('/refresh', [AuthenticatedSessionController::class, 'refresh'])->middleware('jwt.refresh')->middleware('jwt.auth')->name('refresh');
Route::post('/me', [AuthenticatedSessionController::class, 'me'])->middleware('jwt.auth')->name('me');
Route::put('/updatePassword', [AuthenticatedSessionController::class, 'updatePassword'])->middleware('jwt.auth')->middleware('jwt.refresh')->name('updatePassword');
Route::put('/updateProfile', [AuthenticatedSessionController::class, 'updateProfile'])->middleware('jwt.auth')->name('updateProfile');
Route::post('/sendEmailVerif', [AuthenticatedSessionController::class, 'sendEmailVerificationNotification'])->middleware('jwt.auth')->name('sendEmailVerif');
Route::post('/forgotPassword', [AuthenticatedSessionController::class, 'forgotPassword'])->name('forgotPassword');

Route::get('/email/verify/{email}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');