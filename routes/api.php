<?php


// routes/api.php
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\GameController;
use App\Http\Controllers\API\LobbyController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::post('/lobbies', [LobbyController::class, 'create']);
    Route::post('/lobbies/{lobby}/join', [LobbyController::class, 'join']);
    Route::post('/lobbies/{lobby}/start', [LobbyController::class, 'start']);
    Route::get('/lobbies/{lobby}/question', [GameController::class, 'getCurrentQuestion']);
    Route::post('/lobbies/{lobby}/answer', [GameController::class, 'submitAnswer']);
    Route::get('/lobbies/{lobby}/state', [GameController::class, 'getGameState']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
