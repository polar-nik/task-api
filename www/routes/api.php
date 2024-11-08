<?php

use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(TaskController::class)->prefix('/task')->name('task.')->group(function () {
    Route::post('/store', 'store')->middleware('auth:sanctum')->name('store');
    Route::get('/', 'index')->name('list');
    Route::get('/{task}', 'show')->name('show');
    Route::patch('/{task}', 'update')->name('update');
    Route::patch('/{task}/status', 'updateStatus')->name('update-status');
    Route::delete('/{task}', 'destroy')->name('destroy');
    Route::get('/search', 'search')->name('search');
    Route::post('/{taskId}/revive', 'revive')->name('revive');
});
