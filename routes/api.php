<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PasswordController;
use App\Http\Controllers\Api\ProfileController;
use App\Models\Santri;
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

// Get santri by ID (for QR scan)
Route::get('/santri/{id}', function ($id) {
    $santri = Santri::find($id);
    if (! $santri) {
        return response()->json(['error' => 'Santri tidak ditemukan'], 404);
    }

    return response()->json(['name' => $santri->name]);
});

Route::group(['middleware' => 'api', 'prefix' => 'v1'], function ($router) {
    // Autentikasi
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);

    // Ubah Password
    Route::post('password', [PasswordController::class, 'update']);

    // Profil
    Route::get('profile', [ProfileController::class, 'show']);
    Route::post('profile', [ProfileController::class, 'update']);
});
