<?php

use App\Http\Controllers\InsertedUser;
use App\Models\Clinic;
use App\Models\Hospital;
use App\Models\Lab;
use Helpers\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// load routes
$routesPath = __DIR__ . DIRECTORY_SEPARATOR . "routes" . DIRECTORY_SEPARATOR;
$excludes = [".", ".."];
foreach (scandir($routesPath) as $file){
    if (!in_array($file, $excludes)){
        include_once $routesPath . $file;
    }
}

Route::put("/", function() {
    Utils::truncateAllTables();
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
});
