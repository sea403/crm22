<?php

use App\Http\Controllers\GoogleAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/connect/gmail', [GoogleAuthController::class, 'redirectToGoogle']);
Route::get('/oauth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);


Route::get('/', function () {
    return view('welcome');
});
