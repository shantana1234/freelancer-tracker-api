<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

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

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TimeLogController;
use App\Http\Controllers\UserController;
use App\Notifications\LoggedEightHours;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/profile', [UserController::class, 'update']);
    Route::apiResource('clients', ClientController::class);
    Route::apiResource('projects', ProjectController::class);
    Route::get('/time-logs/grouped', [TimeLogController::class, 'grouped']);
    Route::get('/reports', [TimeLogController::class, 'report']);
    Route::get('/exports', [TimeLogController::class, 'exportPdf']);

    Route::apiResource('time-logs', TimeLogController::class);
   

});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// Route::get('/test-email', function () {
//     $user = \App\Models\User::first();
//     $user->notify(new LoggedEightHours('2024-05-27', 8.25));
//     return 'Notification triggered';
// });


Route::get('/test-mailtrap', function () {
    Mail::raw('Hello! This is a test email ,.', function ($message) {
        $message->to('you@example.com')  
                ->subject('Laravel Test Email via Mailtrap');
    });

    return 'Mailtrap test email sent successfully.';
});
