<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NilaiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::any('/login',[AuthController::class,'login'])->name('login');
Route::any('/logout',[AuthController::class,'logout'])->middleware('auth')->name('logout');

Route::get('/',function (){ return view('home'); });
Route::group(['prefix' => 'master'],function (){
    Route::group(['prefix' => 'packages'],function (){
        Route::get('/',function (){ return view('master.packages'); });
        Route::get('/{packages}/pengetahuan',function (){ return view('master.packages.pengetahuan'); });
        Route::get('/{packages}/keterampilan', function (){ return view('master.packages.keterampilan'); });
        Route::get('/{packages}/sikap', function (){ return view('master.packages.sikap'); });
    });
    Route::group(['prefix' => 'schedules'],function (){
        Route::get('/', function (){ return view('master.schedules'); });
        Route::get('/{schedules}/peserta', function (){ return view('master.peserta'); });
    });
});
Route::get('/nilai/download/{id}',[NilaiController::class,'downloadNilai']);
Route::get('/nilai/cetak/{id}',[NilaiController::class,'cetakLembarNilai']);
Route::get('/nilai/{schedules}',function (){ return view('nilai'); });
Route::get('/mulai-ujian/{schedules}', function(){ return view('mulai-ujian'); });