<?php

Route::controller(\App\Http\Controllers\AuthController::class)->group(function () {
    Route::post('/sign-up', 'signUp')->name('sign-up');
    Route::post('/sign-in', 'signIn')->name('sign-in');
    Route::post('/sign-out', 'signOut')->name('sign-out');
});
