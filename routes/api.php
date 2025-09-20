<?php

use App\Http\Controllers\PetController;
use App\Http\Controllers\TesteController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/teste', [TesteController::class, 'index']);

Route::apiResource('pets', PetController::class);
