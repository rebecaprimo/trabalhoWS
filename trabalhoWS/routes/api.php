<?php

// use App\Http\Controllers\AuthController;
use App\Http\Controllers\api\ClienteController;
use App\Http\Controllers\api\ReservaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\PassportAuthController;
use App\Http\Controller\Api\HotelController;

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
Route::post('register', [PassportAuthController::class, 'register']);
Route::post('login', [PassportAuthController::class, 'login']);
Route::post('logout', [PassportAuthController::class, 'logout'])->middleware('auth:api');
Route::post('user', [PassportAuthController::class, 'clienteInfo'])->middleware('auth:api');

Route::middleware('localization')->group(function ( ) {
    // Route::middleware('auth:api')->get('/user', function(Request $request) {
    //     return $request->user( );
    // });

    // route::apiResource('clientes', ClienteController::class);
    route::apiResource('reservas', ReservaController::class);

    route::apiResource('clientes', ClienteController::class)->middleware('auth:api');
    // route::get('clientes', [ClienteController::class, 'index'])->middleware('auth:api');
});


// Route::post('login', [AuthController::class, 'login']);

// Route::group(['middleware' => ['auth:api', 'api.version:v1']], function () {
//     Route::get('clientes', [ClienteController::class, 'index']);
//     Route::get('clientes/{id}', [ClienteController::class, 'show']);
//     Route::post('clientes', [ClienteController::class, 'store']);
//     Route::put('clientes/{id}', [ClienteController::class, 'update']);
//     Route::delete('clientes/{id}', [ClienteController::class, 'destroy']);
//     // Route::post('logout', [AuthController::class, 'logout']);
//     // Route::post('refresh', [AuthController::class, 'refresh']);
// });
//rotas CRUD Hotel
route::apiResource('hotel', HotelController::class);
