<?php

use App\Http\Controllers\GeneralController;
use Illuminate\Support\Facades\Route;

Route::get("/specialty", [GeneralController::class, "getSpecialty"]);