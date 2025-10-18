<?php

use App\Http\Controllers\ArquivoController;
use App\Http\Controllers\ConsultaController;
use App\Http\Controllers\EnderecoController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\TesteController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/teste', [TesteController::class, 'index']);

Route::apiResource('users', UserController::class);
Route::post('/users/login', [UserController::class, 'login']);

Route::apiResource('enderecos', EnderecoController::class);
Route::apiResource('pets', PetController::class);
Route::apiResource('arquivos', ArquivoController::class);
Route::apiResource('consultas', ConsultaController::class);

Route::post('users/{user}/pets/{pet}', [UserController::class, 'addPet']);
Route::delete('users/{user}/pets/{pet}', [UserController::class, 'removePet']);


