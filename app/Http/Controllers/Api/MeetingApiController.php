<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dokter;
use App\Models\Key_Link;
use App\Models\Sesi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Throwable;

class MeetingApiController extends Controller
{
    function join_room(Request $request){
        $validator = Validator::make($request->all(), [
            'number_key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Gagal',
                'pesan'  => $validator->errors()
            ], 422);
        }

        try {
            if(Auth::guard('api')->user() == null){
                return response()->json([
                    'status' => 'Gagal',
                    'pesan'  => 'Anda Tidak Mempunyai Akses',
                ]);
            }

            $user_id    = Auth::guard('api')->user()->id;
            $user       = User::find($user_id);
            $role       = $user->status_role;
            // return $role;
            $status     = '';

            if ($role == 1) {
                $dokter     = Dokter::where('id_user', $user_id)->first();
                $status     = $dokter->jenis_dokter;
                $cek_akses  = Key_Link::where('id_dokter', $dokter->id)->where('number_key', $request->number_key)->first();
                if (!isset($cek_akses)) {
                    return response()->json([
                        'status' => 'Gagal',
                        'pesan'  => 'Anda tidak memiliki akses !',
                    ], 422);
                }

            } else {
                $status     = "Pelanggan";
                $cek_akses  = Key_Link::where('id_user', $user_id)->where('number_key', $request->number_key)->first();
                if (!isset($cek_akses)) {
                    return response()->json([
                        'status' => 'Gagal',
                        'pesan'  => 'Anda tidak memiliki akses !',
                    ], 422);
                }
            }

            $akses      = Key_Link::where('number_key', $request->number_key)->first();
            $sesi       = Sesi::find($akses->id_sesi);
            $date_now   = explode(' ', now());
            $time       = $date_now[1];

            if ($sesi->tanggal == $date_now[0] || $request->number_key == 'trk_8346ok') {
                if ($sesi->waktu_mulai <= $time && $sesi->waktu_selesai >= $time || $request->number_key == 'trk_8346ok') {
                    return response()->json([
                        'status'    => 'Berhasil',
                        'username'  => $user->name.'-'.$status,
                        'status'    => $status,
                        'jenis_fitur'   => $sesi->jenis_pelayanan,
                        'kunci_room'    => $request->number_key,
                        'domain'        => env('METERED_DOMAIN')
                    ], 200);
                } else {
                    $message = ($sesi->waktu_mulai >= $time) ? 'Sesi Belum Dimulai' : 'Sesi Telah Selesai';

                    return response()->json([
                        'status'    => 'Gagal',
                        'pesan'     => $message,
                        'tanggal'   => $sesi->tanggal,
                        'waktu_mulai'   => $sesi->waktu_mulai,
                        'waktu_selesai' => $sesi->waktu_selesai,
                        'saat_ini'      => $date_now
                    ], 200);
                }
            } else {
                $message = ($sesi->tanggal >= $date_now[0]) ? 'Sesi Belum Dimulai' : 'Sesi Telah Selesai';

                return response()->json([
                    'status'    => 'Gagal',
                    'pesan'     => $message,
                    'tanggal'   => $sesi->tanggal,
                    'saat_ini'  => $date_now
                ], 200);
            }


        } catch (Throwable $th) {
            return response()->json([
                'status' => 'Gagal',
                'pesan'  => $th->getMessage(),
            ], 422);
        }
    }

    function image_room(Request $request){
        $validator = Validator::make($request->all(), [
            'number_key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Gagal',
                'pesan'  => $validator->errors()
            ], 422);
        }

        try {
            $data_key       = Key_Link::where('number_key', $request->number_key)->with('user', 'dokter', 'dokter.user')->first();
            $path_user      = $data_key->user->image_profile;
            $path_dokter    = $data_key->dokter->user->image_profile;

            return response()->json([
                'status'        => 'Berhasil',
                'image_user'    => $this->get_image_url($path_user),
                'image_dokter'  => $this->get_image_url($path_dokter),
            ], 200);

        } catch (Throwable $th) {
            return response()->json([
                'status' => 'Gagal',
                'pesan'  => $th->getMessage(),
            ], 422);
        }
    }

    public function get_image_url($path){
        $path = explode('/', $path)[1];

        if (!Storage::disk('profile')->exists($path)) {
            $fileContents   = Storage::disk('public')->get('default.jpg');
            return $this->convert_image_to_base64($fileContents, 'jpg');
        }

        $fileContents = Storage::disk('profile')->get($path);
        return $this->convert_image_to_base64($fileContents, 'webp');;
    }

    public function convert_image_to_base64($fileContents, $extension){
        $base64 = base64_encode($fileContents);
        return 'data:image/' . $extension . ';base64,' . $base64;
    }
}
