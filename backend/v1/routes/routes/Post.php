<?php

use Illuminate\Http\Request;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;


Route::apiResource('/post', PostController::class);

Route::post('/post/react', [PostController::class, "react"]);

Route::put('/post/save', [PostController::class, "save_post"]);
