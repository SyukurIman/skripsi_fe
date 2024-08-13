<?php

namespace App\Http\Middleware;

use App\Models\Notifikasi;
use App\Models\User;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = JWTAuth::parseToken()->getPayload();
            
            $check = User::where('id', $user->get('sub'))->get();
            if (count($check) == 0) {
                return response()->json(['Tidak di temukan akun anda !'], 404);
            }

            if ($check[0]->status_role != 2){
                return response()->json(['status' => 'Anda tidak memiliki akses!']);
            }

            $notif  = Notifikasi::where('user_id', $user->get('sub'))->where('status', 'belum_dibaca')->orderBy('created_at', 'desc')->get();    
            $data = ['token' => $request->bearerToken(), 'Notifikasi'=>$notif];
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json(['status' => 'Token anda tidak valid']);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                try {
                    $refreshed  = JWTAuth::refresh($request->bearerToken());
                    JWTAuth::setToken($refreshed)->toUser();

                    $request->headers->set('Authorization','Bearer '.$refreshed);
                    $data = ['token' => $refreshed, 'status_token' => 'Diperbarui'];
                } catch (\Throwable $th) {
                    return response()->json(['status' => $th->getMessage()]);
                }
            }else{
                return response()->json(['status' => "Token anda tidak terdaftar!"]);
            }
        }
        $response = $next($request);
        $response->setContent(json_encode(array_merge(
            json_decode($response->getContent(), true),
            $data
        )));

        return $response;
    }
}
