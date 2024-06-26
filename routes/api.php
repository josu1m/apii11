<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::post('register', [AuthController::class, 'register']);
Route::post("login",[AuthController::class,"login"]);

Route::group(["middleware"=>['auth:sanctum']], function(){
    Route::get('userProfile',[AuthController::class,'userProfile']);
    Route::post('logout',[AuthController::class,'logout']);
    Route::put('updateUser/{id}',[AuthController::class,'updateUser']); // Actualizar perfil de usuario
    Route::delete('deleteUser/{id}', [AuthController::class, 'deleteUser']);




});

