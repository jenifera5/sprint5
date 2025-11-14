<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LibroController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\PrestamoController;

Route::post('/register' ,[AuthController::class,'register']);
Route::post('/login' ,[AuthController::class,'login']);

Route::middleware('auth:api')->group(function() {
    Route::post('/logout' ,[AuthController::class,'logout']);
     //Endpoints de solo lectura (usuarios normales y admin)
    Route::get('/books' ,[LibroController::class,'index']);
     
      //  Endpoints solo para administradores
    Route::middleware('role:admin')->group(function () {
        Route::post('/books', [LibroController::class, 'store']);
        Route::put('/books/{id}', [LibroController::class, 'update']);
    });

});