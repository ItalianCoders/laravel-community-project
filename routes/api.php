<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ThreadController;
use App\Http\Controllers\Api\V1\Thread\CommentController;

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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::apiResource('v1/threads', ThreadController::class);

Route::apiResource('v1/threads/{thread}/comments', CommentController::class);
