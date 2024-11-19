<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\CrmActivities\CrmActivityMeetingController;
use App\Http\Controllers\Tesoreria\TesoreriaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Raffles\RaffleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('tesoreria')->group(function () {
    Route::post('/gastos', [TesoreriaController::class, 'storeUnclassifiedExpensese']);
});
Route::post('/acta/description', action: [CrmActivityMeetingController::class, 'updateMeeting']);
Route::post('/getAyudas', action: [ApiController::class, 'getayudas']);
Route::post('/updateAyudas/{id}', action: [ApiController::class, 'updateAyudas']);
Route::post('/updateMensajes', action: [ApiController::class, 'updateMensajes']);

// Rutas para la gestión de sorteos
Route::post('/assign-tickets', [RaffleController::class, 'assignRandomTickets']); // Asignar tickets aleatorios

// Rutas para la gestión de clientes
Route::post('/clients/create', [ApiController::class, 'crearCliente']); // Crear un cliente
Route::put('/clients/{id}', [ApiController::class, 'updateClientData']); // Actualizar datos del cliente
Route::post('/clients/{id}/password', [ApiController::class, 'updatePassword']); // Actualizar contraseña del cliente

// Rutas para la recuperación de contraseñas
Route::post('/password/reset/request', [ApiController::class, 'requestPasswordReset']); // Solicitar restablecimiento de contraseña
Route::post('/password/reset', [ApiController::class, 'resetPassword']); // Restablecer contraseña