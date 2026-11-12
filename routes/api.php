<?php

use App\Http\Controllers\Api\LanguageController;
use App\Http\Controllers\Api\UserAppDeviceController;
use App\Http\Controllers\Api\GoogleAnalyticsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/lang', [LanguageController::class, 'index']);
Route::get('/img', [LanguageController::class, 'getImages']);
Route::get('/audio', [LanguageController::class, 'getAudio']);
Route::post('/init-device', [UserAppDeviceController::class, 'init']);
Route::post('/send-update-notification', [UserAppDeviceController::class, 'sendUpdateNotification']);
Route::get('/google-analytics-report', [GoogleAnalyticsController::class, 'index']);
