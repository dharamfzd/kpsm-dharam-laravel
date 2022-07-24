<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\FileUploadController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);

Route::middleware('auth:api')->group(function () {
  Route::post('get-user', [UserController::class, 'getUser']);
  Route::post('upload', [FileUploadController::class, 'FileUpload']);
  Route::post('all-files', [FileUploadController::class, 'allFiles']);
  Route::post('logout', [UserController::class, 'logout']);
});
