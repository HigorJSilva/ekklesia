<?php

use App\Http\Controllers\Condomino\CondominoController;
use App\Http\Controllers\Morador\MoradorController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'auth'], function () {
    Route::group(['middleware' => 'role:morador', 'prefix' => 'morador', 'as' => 'morador.'], function () {
        Route::resource('morador', MoradorController::class);
    });
    Route::group(['middleware' => 'role:condomino', 'prefix' => 'condomino', 'as' => 'condomino.'], function () {
        Route::resource('condomino', CondominoController::class);
    });
    Route::group(['middleware' => 'role:admin', 'prefix' => 'admin', 'as' => 'admin.'], function () {
        Route::resource('admin', AdminController::class);
    });
});
