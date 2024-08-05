<?php

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
})->middleware("auth:sanctum");
