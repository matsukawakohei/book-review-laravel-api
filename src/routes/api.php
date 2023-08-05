<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('api')->name('api.')->group(function() {
    Route::prefix('v1')->name('v1.')->group(function() {
        Route::prefix('user')
            ->controller(UserController::class)
            ->name('user.')
            ->group(function() {

            Route::post('', 'store')->name('store');
        });

        Route::prefix('auth')
            ->controller(LoginController::class)
            ->name('auth.')
            ->group(function() {

            Route::post('', 'login')->name('login');
        });

        Route::prefix('review')
            ->controller(ReviewController::class)
            ->name('review.')
            ->group(function() {
            
            Route::post('', 'store')
                ->middleware('has.access.token')
                ->name('store');
            
            Route::put('{review}', 'update')
                ->middleware('has.access.token')
                ->name('update');
            
            Route::delete('{review}', 'destroy')
                ->middleware('has.access.token')
                ->name('delete');
        });
    });
});
