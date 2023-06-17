<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HotelController;

use App\Http\Controllers\ReservaController;


Route::middleware("localization")->group(function () {
    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

    //rotas CRUD Hotel
    route::apiResource('hotel', HotelController::class);
    Route::get('/hoteis/{idHotel}/reservas', [ReservaController::class, 'index']);
    Route::post('/hoteis/{idHotel}/reservas', [ReservaController::class, 'store']);
    Route::get('/hoteis/{idHotel}/reservas/{idReserva}', [ReservaController::class, 'show']);
    Route::put('/hoteis/{idHotel}/reservas/{idReserva}', [ReservaController::class, 'update']);
    Route::delete('/hoteis/{idHotel}/reservas/{idReserva}', [ReservaController::class, 'destroy']);
});
