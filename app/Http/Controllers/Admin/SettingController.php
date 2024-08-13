<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    public $data = [
        "parent" => "setting",
    ];

    function index(){
        $this->data['position'] = "Setting Admin";
        $this->data['data_user'] = User::where('id', Auth::user()->id)->first();

        return view('admin.setting.index', $this->data);
    }

    function update(Request $request){
        $data = User::where('id', Auth::user()->id)->first();
        $data->name = $request->input('name');
        $data->email = $request->input('email');
        $data->no_telpon = $request->input('no_telpon');

        if ($request->password != "") {
            $data->password = Hash::make($request->password);
        }
        if($data->update()){
            return redirect()->back()->with('status','Updated Successfully');
        } else {
            return redirect()->back()->with('status','Updated Failed');
        }
    }
}
