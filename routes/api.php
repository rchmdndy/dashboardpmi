<?php

use App\Http\Controllers\Auth\JWTAUTHController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\UserTransactionController;
use App\Http\Controllers\Auth\VerificationController;


Route::prefix('v1')->group(function (){
    Route::prefix('room_type')->controller(RoomTypeController::class)->name('room_type.')->group(function (){
        Route::get('/getAll', 'getAll')->name('getAll');
        Route::get('/getDetail', 'getDetail')->name('getDetail');
        Route::get('/getRoomType', 'getRoomType')->name('getRoomType');
    });
    Route::prefix('room')->controller(RoomController::class)->name('room.')->group(function(){
        Route::get('/getAll', 'getAll')->name('getAll');
        Route::get('/getDetail', 'getDetail')->name('getDetail');
        Route::get('/getByRoomType', 'getByRoomType')->name('getByRoomType');
    });
    Route::prefix('/user_transaction')->controller(UserTransactionController::class)->name('user_transaction.')->group(function (){
        Route::get('/getUserTransaction', 'getUserTransaction')->name('getUserTransaction');
        Route::get('/getUserTransactionID', 'getUserTransactionByOrderID')->name('getUserTransactionByOrderID');
        Route::get('/RefreshTransactionStatus', 'refreshTransaction')->name('RefreshTransactionStatus');
        Route::get('/getSnapToken', 'getSnapToken')->name('getSnapToken');
    });
    Route::prefix('/booking')->controller(BookingController::class)->name('booking,')->group(function (){
       Route::post('/generateToken', 'bookRoom')->name('generateToken'); // tolong tambahin di body POST, "side" = "client" biar dapet response json
    });

    Route::middleware('jwt.auth')->group(function (){
        Route::post('/logout', [JWTAUTHController::class, 'logout'])->name('logout');
        Route::post('/refresh', [JWTAUTHController::class, 'refresh'])->middleware('jwt.refresh')->name('refresh');
        Route::get('/me', [JWTAUTHController::class, 'me'])->name('me');
        Route::put('/updatePassword', [JWTAUTHController::class, 'updatePassword'])->middleware('jwt.refresh')->name('updatePassword');
        Route::put('/updateProfile', [JWTAUTHController::class, 'updateProfile'])->name('updateProfile');
        Route::post('/sendEmailVerif', [JWTAUTHController::class, 'sendEmailVerificationNotification'])->name('sendEmailVerif');
    });

    Route::post('login', [JWTAUTHController::class, 'login'])->middleware('throttle:10');
    Route::post('register', [JWTAUTHController::class, 'register'])->middleware('throttle:10');
    Route::post('/forgotPassword', [JWTAUTHController::class, 'forgotPassword'])->name('forgotPassword');
    //Signature URL Email Verification
    Route::get('/email/verify/{email}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');

});
