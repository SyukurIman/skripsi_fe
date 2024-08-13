<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(){
        return view("admin.auth.login");
    }

    public function login_process(LoginRequest $request){
        $request->authenticate();

        $request->session()->regenerate();

        if (Auth::user()->status_role == '0'){
            return redirect()->intended('/admin/home');
        }

        $this->destroy($request);
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }
}
