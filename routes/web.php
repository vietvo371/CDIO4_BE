<?php

use App\Http\Controllers\LoginGoogleController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/anime',[TestController::class, 'test']);

// Route::controller(LoginGoogleController::class)->group(function () {
//     Route::get('auth/google', 'getGoogleSignInUrl');
//     Route::get('auth/google/callback', 'loginCallback');
// });
