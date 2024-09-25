<?php

use App\Http\Controllers\Auth\JWTAUTHController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\UserTransactionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('room_type')->controller(RoomTypeController::class)->name('room_type.')->group(function () {
        Route::get('/getAll', 'getAll')->name('getAll');
        Route::get('/getAllPackage', 'getAllPackage')->name('getAllPackage');
        Route::get('/getDetailPackage', 'getDetailPackage')->name('getDetailPackage');
        Route::get('/getDetail', 'getDetail')->name('getDetail');
        Route::get('/getRoomType', 'getRoomType')->name('getRoomType');
    });
    Route::prefix('room')->controller(RoomController::class)->name('room.')->group(function () {
        Route::get('/getAll', 'getAll')->name('getAll');
        Route::get('/getDetail', 'getDetail')->name('getDetail');
        Route::get('/getByRoomType', 'getByRoomType')->name('getByRoomType');
    });
    Route::prefix('/user_transaction')->controller(UserTransactionController::class)->name('user_transaction.')->group(function () {
        Route::get('/getUserTransaction', 'getUserTransaction')->name('getUserTransaction');
        Route::get('/getUserTransactionID', 'getUserTransactionByOrderID')->name('getUserTransactionByOrderID');
        Route::get('/RefreshTransactionStatus', 'refreshTransaction')->name('RefreshTransactionStatus');
        Route::get('/getSnapToken', 'getSnapToken')->name('getSnapToken');
        Route::get('/detail', 'detail')->name('getUserTransactionDetail');
    });
    Route::prefix('/booking')->controller(BookingController::class)->name('booking.')->group(function () {
        Route::post('/packageToken', 'bookPackage')->name('packageToken');
        Route::post('/generateToken', 'bookRoom')->name('generateToken'); // tolong tambahin di body POST, "side" = "client" biar dapet response json
        Route::get('/availableRoomOnDate', 'getAvailableRoomOnDate')->name('availableRoomOnDate');
    });
    Route::prefix('/packages')->controller(PackageController::class)->name('packages.')->group(function (){
        Route::get('/getAll', 'getAll')->name('getAll');
        Route::get('/getDetail', 'getDetail')->name('getDetail');
    });

    Route::prefix('/midtrans')->controller(NotificationController::class)->name('midtrans.')->group(function(){
        Route::post('/notification_handling', 'handleMidtransNotification');
    });

    Route::prefix('/report')->controller(ReportController::class)->name('report.')->group(function () {
        Route::get('/generate', 'generateMonthly')->name('generateMonthly');
    });

    Route::prefix('/review')->controller(ReviewController::class)->name("review.")->group(function(){
        Route::post('/postReview', 'postReview')->name("postReview");
        Route::get("/getReviewForCurrentTransaction", "getReviewForCurrentTransaction")->name("getReviewForCurrentTransaction");
        Route::get('/getTopReview', 'getTopReview')->name("getTopReview");
        Route::get('/getCurrentRoomTypeReview', 'getCurrentRoomTypeReview')->name("getCurrentRoomTypeReview");
    });

    // MUST LOGIN/REGISTER FIRST
    Route::middleware(['jwt.auth'])->group(function () {

        Route::controller(JWTAUTHController::class)->group(function () {
            Route::post('/logout', 'logout')->name('logout');
            Route::post('/refresh', 'refresh')->middleware('jwt.refresh')->name('refresh');
            Route::get('/me', 'me')->name('me');
            Route::put('/updatePassword', 'updatePassword')->middleware('jwt.refresh')->middleware('throttle:10')->name('updatePassword');
            Route::put('/updateProfile', 'updateProfile')->name('updateProfile');
            Route::post('/sendEmailVerif', 'sendEmailVerificationNotification')->middleware('throttle:5')->name('sendEmailVerif');
        });

    });

    Route::middleware('throttle:5')->controller(JWTAUTHController::class)->group(function () {
        Route::post('/login', 'login')->name('login');
        Route::post('/register', 'register')->name('register');
        Route::post('/registerSocial', 'registerSocial')->name('registerSocial'); //untuk google & twitter
        Route::post('/forgotPassword', 'forgotPassword')->name('forgotPassword');
    });

    //Signature URL Email Verification
    Route::get('/email/verify/{email}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');

    Route::get('/try', [VerificationController::class, 'try'])->name('verification.try');

});

Route::prefix("v2")->middleware(['jwt.auth'])->group(function(){
    Route::prefix('/booking')->controller(BookingController::class)->name('bookingv2.')->group(function () {
        Route::post('/packageToken', 'bookPackage')->name('packageTokenv2');
        Route::post('/generateToken', 'bookRoom')->name('generateTokenv2'); // tolong tambahin di body POST, "side" = "client" biar dapet response json
    });
    Route::prefix('/user_transaction')->controller(UserTransactionController::class)->name('user_transactionv2.')->group(function () {
        Route::get('/getUserTransaction', 'getUserTransaction')->name('getUserTransactionv2');
        Route::get('/detail', 'detail')->name('getUserTransactionDetailv2');
    });
});
