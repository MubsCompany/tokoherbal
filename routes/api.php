<?php

use App\Http\Controllers\AlamatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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

// membuat routing register yang akan menjalankan fungsi registerUser yang ada di UserController
Route::post('register', [UserController::class, 'registerUser']);

Route::post('login', [UserController::class, 'loginUser']);

// middleware adalah jembatan dari route ke controller
Route::post('logout', [UserController::class, 'logoutUser'])->middleware('auth:sanctum');

Route::get('user/all', [UserController::class, 'getAllUsers']);

Route::get('user/{id}', [UserController::class, 'getUser']);

Route::post('user/{id}', [UserController::class, 'updateUser']);

Route::delete('user/{id}', [UserController::class, 'deleteUser']);

Route::get('user', function () {
    return response()->json('', 403);
});

// rute untuk mendapatkan alamat berdasarkan user_id
Route::get('user/{user_id}/alamat', [AlamatController::class, 'getAlamat']);

// rute create dan update alamat berdasarakan user_id
Route::post('user/{user_id}/alamat', [AlamatController::class, 'createAlamat']);

// rute hapus alamat berdasarakan user_id
Route::delete('user/{user_id}/alamat', [AlamatController::class, 'hapusAlamat']);
