<?php

use App\Events\MessageSendEvent;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ArtikelController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\LayananController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaketController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\MeetingController;
use App\Models\User;
use Illuminate\Http\Request;
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

Route::get('/', function () {
    // broadcast(new MessageSendEvent('tes'));
    return view('welcome');
})->name('home');


Route::get('/tes_midtrans/{id}', [OrderApiController::class, 'tes_midtrans']);

Route::get('/chat_ui/{id}', function(Request $request, $id) {
    $token = $request->bearerToken();
    return view('chat.chat-component', ['id' => $id, 'token' => $token]);
})->name('chat_ui');


Route::get('/chat', function(){
    return view('chat');
})->name('chat_dashboard');

// Auth Admin
Route::get('/admin/login', [AuthController::class, 'login'])->middleware('guest')->name('login_admin');
Route::post('/admin/login', [AuthController::class, 'login_process'])->middleware('guest');
Route::get('/admin/logout', [AuthController::class, 'destroy'])->middleware('is_admin')->name('logout_admin');

Route::middleware('is_admin')->group(function () {
    Route::get('/admin/home', [AdminController::class, 'home_admin'])->name('home_admin');

    Route::get('/admin/user_management', [UserController::class, 'index'])->name('user_management_admin');
    Route::post('/admin/user_management/get_all_data', [UserController::class, 'get_all_data'])->name('all_user_data_admin');
    Route::get('/admin/user_management/create', [UserController::class, 'from_create_user'])->name('user_management_create_admin');
    Route::post('/admin/user_management/create', [UserController::class, 'save_create_user'])->name('user_management_create_admin');
    Route::get('/admin/user_management/edit/{id}', [UserController::class, 'from_update_user'])->name('user_management_update_admin');
    Route::post('/admin/user_management/edit/{id}', [UserController::class, 'save_update_user'])->name('user_management_update_admin');
    Route::post('/admin/user_management/delete/', [UserController::class, 'delete_user'])->name('admin.user_management.delete');

    Route::get('/admin/layanan', [LayananController::class, 'index'])->name('layanan_admin');
    Route::post('/admin/layanan/get_all_data', [LayananController::class, 'get_all_data'])->name('all_layanan_data');
    Route::get('/admin/layanan/create', [LayananController::class, 'from_create'])->name('layanan_create_admin');
    Route::post('/admin/layanan/create', [LayananController::class, 'save_create'])->name('layanan_create_admin');
    Route::get('/admin/layanan/edit/{id}', [LayananController::class, 'from_update'])->name('layanan_update_admin');
    Route::post('/admin/layanan/edit/{data}', [LayananController::class, 'save_update'])->name('layanan_update_admin');
    Route::post('/admin/layanan/delete/', [LayananController::class, 'delete'])->name('admin.layanan.delete');

    Route::get('/admin/sesi/{id_layanan}', [LayananController::class, 'index_sesi'])->name('layanan_sesi');
    Route::post('/admin/data_sesi/{id_layanan}', [LayananController::class, 'get_all_sesi'])->name('all_sesi_data');

    Route::prefix('artikel')->name('artikel.')->group(function(){
        Route::get('/',[ArtikelController::class,'artikel'])->name('index');
        Route::post('/table', [ArtikelController::class, 'table'])->name('table');
        Route::get('/create',[ArtikelController::class,'create'])->name('create');
        Route::get('/update/{id}',[ArtikelController::class,'update'])->name('update');
        Route::post('/updateform',[ArtikelController::class,'updateform'])->name('updateform');
        Route::post('/createform',[ArtikelController::class,'createform'])->name('createform');
        Route::post('/deleteform',[ArtikelController::class,'deleteform'])->name('deleteform');
    });

    Route::prefix('paket')->name('paket.')->group(function(){
        Route::get('/',[PaketController::class,'paket'])->name('index');
        Route::post('/table', [PaketController::class, 'table'])->name('table');
        Route::get('/create',[PaketController::class,'create'])->name('create');
        Route::get('/update/{id}',[PaketController::class,'update'])->name('update');
        Route::post('/updateform',[PaketController::class,'updateform'])->name('updateform');
        Route::post('/createform',[PaketController::class,'createform'])->name('createform');
        Route::post('/deleteform',[PaketController::class,'deleteform'])->name('deleteform');
    });

    Route::prefix('order')->name('order.')->group(function(){
        Route::get('/',[OrderController::class,'order'])->name('index');
        Route::post('/table', [OrderController::class, 'table'])->name('table');
        Route::get('/invoice/{invoice}',[OrderController::class,'invoice'])->name('invoice');
        Route::post('/invoice/{invoice}',[OrderController::class,'update_invoice'])->name('update_invoice');
        Route::post('/deleteform',[OrderController::class,'deleteform'])->name('deleteform');
    });

    Route::get('/setting', [SettingController::class, 'index'])->name('setting');
    Route::post('/setting', [SettingController::class, 'update'])->name('setting');
});

Route::view('/api-doc', 'api-documentation');
Route::get("/meeting/{number_key}", [MeetingController::class, 'meeting'])->name('meet');

Route::get("/chatormeet", function() {
    return view('toChatOrMeet');
})->name('chatormeet');
