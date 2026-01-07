<?php

use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('tasks',TaskController::class)
    ->missing(function (){
        return response()->json(['message' => 'Not found!'],404);
    });
