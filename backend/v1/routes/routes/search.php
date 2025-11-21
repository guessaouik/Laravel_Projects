<?php

use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::post("/search", [SearchController::class, "search"]);

Route::post("filter/search", [SearchController::class, "filterSearch"]);

Route::post("filters", [SearchController::class, "filters"]);