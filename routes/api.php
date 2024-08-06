<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\UserTransactionController;
use Illuminate\Support\Facades\Route;


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
    });
    Route::prefix('/booking')->controller(BookingController::class)->name('booking,')->group(function (){
       Route::post('/generateToken', 'bookRoom')->name('generateToken'); // tolong tambahin di body POST, "side" = "client" biar dapet response json
    });
})->middleware("auth:sanctum");
