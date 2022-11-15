<?php

use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1'], function () {
    Route::group(['prefix' => 'tasks'], function () {
        Route::post('/', [TaskController::class, 'store']);
        Route::get('/', [TaskController::class, 'get']);
    });
    Route::group(['prefix' => 'task'], function () {
        Route::delete('/{task}', [TaskController::class, 'delete']);
        Route::patch('/{task}', [TaskController::class, 'update']);
    });
});
