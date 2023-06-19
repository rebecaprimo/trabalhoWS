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
    Route::get('/hoteis/{idHotel}/reservas', [ReservaController::class, 'index'])->middleware('auth:api');
    Route::post('/hoteis/{idHotel}/reservas', [ReservaController::class, 'store'])->middleware('auth:api');
    Route::get('/hoteis/{idHotel}/reservas/{idReserva}', [ReservaController::class, 'show'])->middleware('auth:api');
    Route::put('/hoteis/{idHotel}/reservas/{idReserva}', [ReservaController::class, 'update'])->middleware('auth:api');
    Route::delete('/hoteis/{idHotel}/reservas/{idReserva}', [ReservaController::class, 'destroy'])->middleware('auth:api');

    route::apiResource('clientes', ClienteController::class)->middleware('auth:api');
});
