<?php

use App\Http\Controllers\ProfileActionController;
use App\Http\Controllers\ProfileUpdateController;
use App\Http\Controllers\ProfileViewController;
use Illuminate\Support\Facades\Route;

Route::patch("/profile", [ProfileUpdateController::class, "updateProfile"]);

Route::post("/profile", [ProfileViewController::class, "get"]);

Route::post("/profile/schedule", [ProfileViewController::class, "getSchedule"]);

