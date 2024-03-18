<?php

use App\Http\Controllers\UserController;
use App\Models\User;
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

Route::post('/user/auth', [UserController::class, 'authlogin']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/persons/', [UserController::class, 'listPerson']);
    Route::get('/logout', [UserController::class, 'logout']);
    Route::post('/send', [UserController::class, 'sendMessage']);
    Route::get('/messages/{to}', [UserController::class, 'getMessages']);
});

// Route::get('test', function () {
//     $data = User::orderBy('created_at')->get();
//     // $time = $data->created_at->format('H:i');
//     return response([
//         $data
//     ]);
// });
