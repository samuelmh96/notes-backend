<?php

use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\TagController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::apiResource('notes', NoteController::class);
Route::apiResource('tags', TagController::class)->only(['index', 'store']);