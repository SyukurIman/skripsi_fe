<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthApiController extends Controller
{
    function login(Request $request){
        //set validation
        $validator = Validator::make($request->all(), [
            'email'     => 'required',
            'password'  => 'required'
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //get credentials from request
        $credentials = $request->only('email', 'password');

        //if auth failed
        if(!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json([
                'status' => 'Gagal',
                'pesan'  => 'Email atau Password Anda salah'
            ], 401);
        }

        $role = 'User Biasa';

        if(Auth::guard('api')->user()->status_role == 1){
            $role = 'Dokter';
        }

        //if auth success
        return response()->json([
            'status'    => 'Berhasil',
            'role'      => $role,
            'token'     => $token
        ], 200);
    }

    function update_token(Request $request){
        $refreshed  = JWTAuth::refresh($request->bearerToken());

        JWTAuth::setToken($refreshed)->toUser();
        $request->headers->set('Authorization','Bearer '.$refreshed);
        return response()->json([
            'status'  => 'Berhasil',
            'token'    => $refreshed,
        ], 201);
    }

    function register(Request $request){
        //set validation
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'email'     => 'required|email|unique:users',
            'password'  => 'required|min:8|confirmed',
            'no_telpon' => 'required',
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //create user
        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => bcrypt($request->password),
            'no_telpon'     => $request->no_telpon,
            'status_role'   => '2',
            'image_profile' => 'storage/default.jpg'
        ]);

        //return response JSON user is created
        if($user) {
            return response()->json([
                'status'  => 'Berhasil',
                'user'    => $user,
            ], 201);
        }

        //return JSON process insert failed
        return response()->json([
            'success' => false,
        ], 409);
    }

    function logout(){
        if(JWTAuth::invalidate(JWTAuth::getToken())) {
            return response()->json([
                'status' => 'Berhasil',
                'pesan'  => 'Logout Berhasil!',
            ]);
        }
    }
}
