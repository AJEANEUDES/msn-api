<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('inscription', [AuthController::class, 'register']);
Route::post('connexion', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function() {

    // utilisateurs

    Route::get('users/{id}', [UserController::class, 'show']);
    Route::put('users/{id}', [UserController::class, 'update']);
    
    // utilisateurs
   
    Route::post('deconnexion', [AuthController::class, 'logout']);
    Route::get('inside-mware', function() {
        return response()->json('Sucess', 200);
    });



});
