<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PackagesController;
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

Route::group(['prefix' => 'auth', 'middleware' => 'auth:api'],function (){
    Route::get('/me',[AuthController::class,'me']);
    Route::group(['prefix' => 'master'],function (){
        Route::any('/packages',[PackagesController::class,'crud']);
    });
});