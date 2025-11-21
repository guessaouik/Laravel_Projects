<?php

use App\Http\Controllers\ProfileActionController;
use Illuminate\Support\Facades\Route;

Route::post("/profile/save", [ProfileActionController::class, "saveProfiles"]);

Route::delete("/profile/unsave", [ProfileActionController::class, "unsaveProfiles"]);

Route::post("/post/save", [ProfileActionController::class, "savePosts"]);

Route::delete("/post/unsave", [ProfileActionController::class, "unsavePosts"]);

Route::post("/article/save", [ProfileActionController::class, "saveArticles"]);

Route::delete("/article/unsave", [ProfileActionController::class, "unsaveArticles"]);

Route::post("/saved/article", [ProfileActionController::class, "getSavedArticles"]);

Route::post("/saved/post", [ProfileActionController::class, "getSavedPosts"]);

Route::post("/saved/profile", [ProfileActionController::class, "getSavedProfiles"]);

Route::post("/saved/profile/search", [ProfileActionController::class, "searchSavedProfiles"]);

Route::post("/saved/article/search", [ProfileActionController::class, "searchSavedArticles"]);

Route::post("/saved/post/search", [ProfileActionController::class, "searchSavedPosts"]);