<?php

use App\Http\Controllers\ArticleController;
use Illuminate\Support\Facades\Route;

Route::post("/article/get", [ArticleController::class, "get"]);

Route::patch("/article", [ArticleController::class, "update"]);

Route::delete("/article", [ArticleController::class, "delete"]);

Route::post("/article", [ArticleController::class, "create"]);

Route::patch("/article/rating", [ArticleController::class, "changeRating"]);

Route::post("/article/rating", [ArticleController::class, "postRating"]);

Route::post("/article/search", [ArticleController::class, "search"]);

Route::post("/show/article", [ArticleController::class, "show"]);