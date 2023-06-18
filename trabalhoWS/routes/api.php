<?php

// use App\Http\Controllers\AuthController;
use App\Http\Controllers\api\ClienteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\ApiPassportAuthController;

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

Route::post('register', [ApiPassportAuthController::class, 'register']);
Route::post('login', [ApiPassportAuthController::class, 'login']);
Route::post('logout', [ApiPassportAuthController::class, 'logout'])->middleware('auth:api');
Route::post('user', [ApiPassportAuthController::class, 'register'])->middleware('auth:api');

Route::middleware("localization")->group(function () {
        Route::get('clientes', [ClienteController::class, 'index']);
        Route::get('clientes/{id}', [ClienteController::class, 'show']);
        Route::post('clientes', [ClienteController::class, 'store']);
        Route::put('clientes/{id}', [ClienteController::class, 'update']);
        Route::delete('clientes/{id}', [ClienteController::class, 'destroy']);
        
});


// Route::post('login', [AuthController::class, 'login']);
