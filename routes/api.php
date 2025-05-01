<?php

use Illuminate\Support\Facades\Route;


Route::apiResource('posts', \App\Http\Controllers\PostController::class);
