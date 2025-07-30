<?php

use App\Http\Controllers\AppController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [AppController::class, 'index']);
Route::post('/time-allocations', [AppController::class, 'addAllocation']);
Route::post('/send-mail', [AppController::class, 'sendMail']);

