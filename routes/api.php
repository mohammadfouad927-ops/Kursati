<?php

use App\Http\Controllers\Api\Auth\StudentAuthController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\TrinaingCourseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function(){
    Route::apiResource('training-courses',TrinaingCourseController::class);
    Route::apiResource('groups', GroupController::class);
    Route::apiResource('branches', BranchController::class);
    Route::post('login', [StudentAuthController::class, 'login']);
    Route::post('register', [StudentAuthController::class, 'register']);
    Route::middleware('auth:student')->group(function(){
        Route::get('my-profile', [StudentAuthController::class, 'profile']);
        Route::put('my-profile', [StudentAuthController::class, 'update']);
    });
});
