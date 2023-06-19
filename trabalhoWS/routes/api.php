<?php

use App\Http\Controllers\api\ClienteController;
use App\Http\Controllers\ReservaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\PassportAuthController;
use App\Http\Controllers\Api\HotelController;


Route::post('register', [PassportAuthController::class, 'register']);
Route::post('login', [PassportAuthController::class, 'login']);
Route::post('logout', [PassportAuthController::class, 'logout'])->middleware('auth:api');
Route::post('user', [PassportAuthController::class, 'clienteInfo'])->middleware('auth:api');

Route::middleware('localization')->group(function () {

    //rotas CRUD Hotel
    route::apiResource('hoteis', HotelController::class);
    Route::get('/hoteis/{idHotel}/reservas', [ReservaController::class, 'index']);
    Route::post('/hoteis/{idHotel}/reservas', [ReservaController::class, 'store']);
    Route::get('/hoteis/{idHotel}/reservas/{idReserva}', [ReservaController::class, 'show']);
    Route::put('/hoteis/{idHotel}/reservas/{idReserva}', [ReservaController::class, 'update']);
    Route::delete('/hoteis/{idHotel}/reservas/{idReserva}', [ReservaController::class, 'destroy']);

    route::apiResource('clientes', ClienteController::class)->middleware('auth:api');
});
