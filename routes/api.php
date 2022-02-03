<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\TesteController;
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


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::apiResource('/users', AdminController::class);

Route::group(['middleware' => ['auth:sanctum', 'role:morador']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::group(['middleware' => 'role:3,2'], function () {
        Route::get('/rota-mista', [AdminController::class, 'rotaMista']);
    });

    Route::group(['middleware' => 'role:1'], function () {
        Route::get('/admin', [AdminController::class, 'admin']);
    });

    Route::group(['middleware' => 'role:2'], function () {
        Route::get('/role2', [AdminController::class, 'role2']);
        Route::apiResource('/teste', TesteController::class);
    });

    Route::group(['middleware' => 'role:3'], function () {
        Route::get('/role3', [AdminController::class, 'role3']);
    });
});
