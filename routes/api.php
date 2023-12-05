<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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


Route::prefix('users')->group(function () {
    Route::post('/', [UserController::class, 'create']);
    Route::get('/', [UserController::class, 'list']);
    Route::post('/auth', [UserController::class, 'authenticate']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::middleware('auth:sanctum')->put('/{user}', [UserController::class, 'update']);
        Route::middleware('auth:sanctum')->delete('/{user}', [UserController::class, 'delete']);
    });
});
