<?php

use App\Http\Controllers\AppController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [AppController::class, 'index']);
Route::post('/time-allocations', [AppController::class, 'addAllocation']);
Route::post('/allocations-file', [AppController::class, 'uploadAllocation']);
Route::post('/staff/update', [AppController::class, 'updateEmployee']);
Route::get('/time-import', [AppController::class, 'importAllocation']);
Route::post('/time-delete', [AppController::class, 'deleteTimeAllocation']);
Route::get('/staff/time-allocations/{id}', [AppController::class, 'StaffTimeAllocations']);
Route::post('/send-mail', [AppController::class, 'sendMail']);
