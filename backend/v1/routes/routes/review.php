<?php

use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::post("/review/created", [ReviewController::class, "postCreatedReviews"]);

Route::post("/review/personal", [ReviewController::class, "postReviews"]);

Route::post("/review", [ReviewController::class, "postReview"]);

Route::post("/review/created/search", [ReviewController::class, "searchCreatedReviews"]);

Route::post("/review/personal/search", [ReviewController::class, "searchReviews"]);

Route::post("/review", [ReviewController::class, "create"]);

Route::patch("/review", [ReviewController::class, "update"]);

Route::delete("/review", [ReviewController::class, "delete"]);

Route::patch("/review/rating", [ReviewController::class, "changeRating"]);

Route::post("/review/rating", [ReviewController::class, "postRating"]);