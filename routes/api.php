<?php

use App\Http\Controllers\Api\ArtikelApiController;
use App\Http\Controllers\Api\Auth;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\DataDokterController;
use App\Http\Controllers\Api\DokterController;
use App\Http\Controllers\Api\GeneralDataController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\Api\MeetingApiController;
use App\Http\Controllers\Api\MidtransApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\PaketApiController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\SesiApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\MeetingController;
use App\Models\Artikel;
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

Route::group(['prefix' => 'v1'], function () {
    Route::post('/auth/register', [AuthApiController::class, 'register'])->name('register');
    Route::post('/auth/login', [AuthApiController::class, 'login'])->name('login');

    Route::group(['middleware' => 'user.auth', 'prefix' => 'user'], function(){
        Route::get('/token/update' , [UserApiController::class, 'update_token']);

        Route::get('/profile' , [UserApiController::class, 'get_user']);
        Route::post('/profile' , [UserApiController::class, 'update_user']);

        Route::post('/order' , [OrderApiController::class, 'create']);
        Route::get('/order_data/{id}' , [OrderApiController::class, 'get_order_detail']);
        Route::get('/order_user' , [OrderApiController::class, 'get_order_user']);

        Route::get('/sesi', [SesiApiController::class, 'check_sesi']);
        Route::post('/sesi', [SesiApiController::class, 'use_sesi']);
        Route::get('/sesi/layanan', [SesiApiController::class, 'layanan_order']);
        Route::post('/sesi/layanan', [SesiApiController::class, 'get_available_sesi']);


    });

    Route::group(['middleware' => 'dokter.auth', 'prefix' => 'dokter'], function(){
        Route::get('/profile' , [UserApiController::class, 'get_user']);
        Route::post('/profile' , [UserApiController::class, 'update_user']);

        Route::get('/layanan', [SesiApiController::class, 'get_layanan']);
        Route::get('/sesi', [SesiApiController::class, 'get_sesi_dokter']);
        Route::post('/sesi', [SesiApiController::class, 'create_sesi']);
        Route::post('/sesi/{data_sesi}', [SesiApiController::class, 'update_sesi']);
    });

    Route::group(['middleware' => ['general.auth']], function() {
        Route::get('/image_profile' , [UserApiController::class, 'get_image_url']);
        Route::post('/room/validasi', [MeetingApiController::class, 'join_room']);
        Route::post('/room/image', [MeetingApiController::class, 'image_room']);

        Route::post('/auth/logout', [AuthApiController::class, 'logout']);

        Route::post('/chat',[ChatController::class, 'index']);
        Route::post('/chat/send',[ChatController::class, 'sendMessage']);
        Route::get('/chat/linkkey/{id}',[ChatController::class, 'getLinkKey']);
        Route::get('/user/all',[ChatController::class, 'user']);
    });

    Route::get('/artikel' , [ArtikelApiController::class, 'get_artikel']);
    Route::get('/artikel/{id}' , [ArtikelApiController::class, 'get_detail_artikel']);
    Route::get('/paket' , [PaketApiController::class, 'get_paket']);
    Route::get('/dokter' , [GeneralDataController::class, 'get_data_dokter']);


    Route::post('/callback' , [MidtransApiController::class, 'callback']);
    Route::post('show/order/{id}' , [OrderApiController::class, 'tes_midtrans']);

    Route::get("/meeting", [MeetingController::class, 'meeting_post'])->name('meet');
});
