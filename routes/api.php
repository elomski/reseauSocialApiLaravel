<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1.0.0')->group(function () {

    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('otpcode', [AuthController::class, 'checkOtpCode']);


    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('users', [UserController::class, 'index']);
        Route::get('logout', [AuthController::class, 'logout']);
        Route::post('admin', [GroupController::class, 'createAdministrator']);
        Route::post('group', [GroupController::class, 'registerGroup']);
        Route::post('/groups/{groupId}/members', [GroupController::class, 'addMember']);
        Route::post('/groups/{groupId}/invite', [GroupController::class, 'invite']);
        Route::post('/groups/{group_id}/upload',[GroupController::class, 'uploadFile']);
        Route::get('allmember', [UserController::class, 'index']);
    });

});
