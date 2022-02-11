<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KomponenKeterampilanController;
use App\Http\Controllers\KomponenSikapController;
use App\Http\Controllers\PackagesController;
use App\Http\Controllers\ScheduleController;
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
        Route::group(['prefix' => 'packages'],function (){
            Route::any('/',[PackagesController::class,'crud']);
            Route::group(['prefix' => 'komponen'],function (){
                Route::any('/keterampilan',[KomponenKeterampilanController::class,'crud']);
                Route::any('/sikap',[KomponenSikapController::class,'crud']);
            });
        });
        Route::group(['prefix' => 'schedules'],function (){
            Route::any('/',[ScheduleController::class,'crud']);
        });
    });
});