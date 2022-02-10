<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
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
    Route::get('/packages',function (){ return view('master.packages'); });
});