<?php

use App\Http\Controllers\UserController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::controller(UserController::class)
    ->prefix('/v1/pendo')
    ->as('pendo')
    ->group(function () {
        Route::post('/registerUser', 'registerUser')->name('registerUser');
        Route::post('/loginUser', 'loginUser')->name('loginUser');
        Route::post('/logoutUser', 'logoutUser')->name('logoutUser');
        Route::post('/resetPassword', 'resetPassword')->name('resetPassword');

    });
