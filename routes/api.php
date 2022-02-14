<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KomponenKeterampilanController;
use App\Http\Controllers\KomponenPengetahuanController;
use App\Http\Controllers\KomponenSikapController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\PackagesController;
use App\Http\Controllers\PesertaController;
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
Route::post('/login',[AuthController::class,'login']);
Route::group(['prefix' => 'auth', 'middleware' => 'auth:api'],function (){
    Route::get('/me',[AuthController::class,'me']);
    Route::post('/logout',[AuthController::class,'logout']);
    Route::any('/users',[AuthController::class,'users']);
    Route::group(['prefix' => 'master'],function (){
        Route::group(['prefix' => 'packages'],function (){
            Route::any('/',[PackagesController::class,'crud']);
            Route::group(['prefix' => 'komponen'],function (){
                Route::any('/keterampilan',[KomponenKeterampilanController::class,'crud']);
                Route::group(['prefix' => 'pengetahuan'],function (){
                    Route::any('/',[KomponenPengetahuanController::class,'crud']);
                    Route::any('/soal',[KomponenPengetahuanController::class,'soal']);
                });
                Route::any('/sikap',[KomponenSikapController::class,'crud']);
            });
        });
        Route::group(['prefix' => 'schedules'],function (){
            Route::any('/',[ScheduleController::class,'crud']);
            Route::any('/peserta',[PesertaController::class,'crud']);
        });
    });
    Route::any('/nilai',[NilaiController::class,'crud']);
});