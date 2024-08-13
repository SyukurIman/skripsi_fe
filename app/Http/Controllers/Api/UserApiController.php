<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Throwable;

class UserApiController extends Controller
{
    function get_user(){
        try {
            $user_id    = Auth::guard('api')->user()['id'];
            $user       = User::find($user_id);

            return response()->json([
                'status' => 'Berhasil',
                'user'   => $user,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'Gagal',
                'pesan'  => $e,
            ], 422);
        }

    }

    function update_user(Request $request){
        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:255',
            'no_telpon'     => 'required|string|max:15',
            'email'         => 'required|email|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Gagal Data Belum Lengkap',
                'pesan'  => $validator->errors()
            ], 422);
        }

        try {
            $user_id    = Auth::guard('api')->user()['id'];
            $user       = User::find($user_id);

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $upload_data    = $this->upload_profile($request, $user);
            // if ($upload_data) {
            //     return response()->json([
            //         'status' => 'Berhasil',
            //         'user'   => $upload_data->original['image_profile'],
            //     ], 200);
            // }
            $user->image_profile    = $upload_data->original['image_profile'];
            $user->name             = $request->name;
            $user->no_telpon        = $request->no_telpon;
            $user->email            = $request->email;

            if ($user->save()) {
                return response()->json([
                    'status' => 'Berhasil',
                    'user'   => $user,
                ], 200);
            }
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'Gagal',
                'pesan'  => $e,
            ], 422);
        }
    }

    public function upload_profile(Request $request, User $user){
        if ($request->hasFile('image_profile')) {
            $newImage = $user->uploadImage($request->file('image_profile'), 'profile');
        }

        if (@$newImage && $user->image_profile && $user->image_profile != 'storage/default.jpg') {
            $user->deleteImage($user->image_profile);
        }

        if (isset($newImage)) {
            return response()->json([
                'pesan'         => 'Berhasil',
                'image_profile' => $newImage->path,
            ], 200);
        } else {
            return response()->json([
                'pesan'         => 'Data gambar profile tidak ditemukan',
                'image_profile' => $user->image_profile,
            ], 200);
        }
    }

    function get_image_url(){
        if (Auth::guard('api')->user() == null) {
            return response()->json([
                'pesan'         => 'Tidak memiliki akses!!',
                'status' => 'Gagal',
            ], 200);
        }
        $path = Auth::guard('api')->user()['image_profile'];
        $path = explode('/', $path)[1];

        if (!Storage::disk('profile')->exists($path)) {
            $fileContents   = Storage::disk('public')->get('default.jpg');
            return response()->json([
                'status'         => 'Berhasil',
                'image_profile' => $this->convert_image_to_base64($fileContents, 'webp'),
            ], 200);
        }

        $fileContents = Storage::disk('profile')->get($path);
        return response()->json([
            'status'         => 'Berhasil',
            'image_profile' => $this->convert_image_to_base64($fileContents, 'webp'),
        ], 200);
    }

    public function convert_image_to_base64($fileContents, $extension){
        $base64 = base64_encode($fileContents);
        return 'data:image/' . $extension . ';base64,' . $base64;
    }
}
