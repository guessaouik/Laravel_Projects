<?php

use App\Http\Controllers\AppointmentController;
use Illuminate\Support\Facades\Route;

Route::post("/appointment/schedule", [AppointmentController::class, "createBookingsInfo"]);

Route::patch("/appointment/schedule", [AppointmentController::class, "updateBookingsInfo"]);

Route::delete("/appointment/schedule", [AppointmentController::class, "deleteBookingsInfo"]);

Route::post("/appointment/schedule/dates", [AppointmentController::class, "getDates"]);

Route::post("/appointment/schedule/sessions", [AppointmentController::class, "getSessions"]);

Route::post("/appointment", [AppointmentController::class, "get"]);

Route::patch("/appointment", [AppointmentController::class, "update"]);

Route::delete("/appointment", [AppointmentController::class, "delete"]);

Route::post("/appointment", [AppointmentController::class, "book"]);
