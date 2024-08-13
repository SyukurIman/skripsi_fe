<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {
            if (Auth::user()->status_role == '0'){
                return route('login_admin');
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses Anda Telah Habis'
                ], 401);;
            }
        }
    }
}
