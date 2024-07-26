<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('room_type')->controller(\App\Http\Controllers\RoomTypeController::class)->name('room_type.')->group(function (){
    Route::get('/getAll', 'getAll')->name('getAll');
    Route::get('/getDetail', 'getDetail')->name('getDetail');
})->middleware('auth:sanctum');
