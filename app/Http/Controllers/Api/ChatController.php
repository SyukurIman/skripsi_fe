<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Events\MessageSendEvent;
use App\Models\Dokter;
use App\Models\Key_Link;
use App\Models\Sesi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ChatController extends Controller
{
    /**
     * Get messages between the authenticated user and the specified user.
     *
     * @param int $user_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // dd($request->number_key);
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

            $role_user     = '';

            if ($role == 1) {
                $dokter     = Dokter::where('id_user', $user_id)->first();
                $role_user     = $dokter->jenis_dokter;
                $cek_akses  = Key_Link::where('id_dokter', $dokter->id)->where('number_key', $request->number_key)->first();
                if (!isset($cek_akses)) {
                    return response()->json([
                        'status' => 'Gagal',
                        'pesan'  => 'Anda tidak memiliki akses !',
                    ], 422);
                }
            } else {
                $role_user     = "Pelanggan";
                $cek_akses  = Key_Link::where('id_user', $user_id)->where('number_key', $request->number_key)->first();
                if (!isset($cek_akses)) {
                    return response()->json([
                        'status' => 'Gagal',
                        'pesan'  => 'Anda tidak memiliki akses !',
                    ], 422);
                }
            }

            $akses      = Key_Link::where('number_key', $request->number_key)->first();
            $idUserDokter = Dokter::where('id',$akses->id_dokter)->first();
            $sesi       = Sesi::find($akses->id_sesi);
            $date_now   = explode(' ', now());
            $time       = $date_now[1];

            if ($sesi->tanggal == $date_now[0]){
                $sender_id = $user_id;
                $receiver_id = $role_user == "Pelanggan" ? $idUserDokter->id_user : $akses->id_user;
                if ($sesi->waktu_mulai <= $time && $sesi->waktu_selesai >= $time) {

                    // dd($receiver_id);

                    $messages = Message::where(function($query) use ($sender_id, $receiver_id, $akses) {
                        $query->where('sender_id', $sender_id)
                              ->where('receiver_id', $receiver_id)
                              ->where('number_key', $akses->number_key);
                    })->orWhere(function($query) use ($sender_id, $receiver_id, $akses) {
                        $query->where('sender_id', $receiver_id)
                              ->where('receiver_id', $sender_id)
                              ->where('number_key', $akses->number_key);
                    })
                    ->with('sender:id,name', 'receiver:id,name')
                    ->get();

                    $receiver = $role_user == "Pelanggan" ?  User::find($idUserDokter->id_user) : User::find($receiver_id);

                    return response()->json([
                        'status' => 'Berhasil',
                        'messages' => $messages,
                        'sender' => Auth::guard('api')->user(),
                        'receiver' => $receiver,
                        'role_user' => $role_user,
                        'sesi' => $sesi,
                    ], 200);
                } else {
                    $alert = ($sesi->waktu_mulai >= $time) ? 'Sesi Belum Dimulai' : 'Sesi Telah Selesai';

                    // Retrieve messages regardless of session status
                    $messages = Message::where(function($query) use ($sender_id, $receiver_id, $akses) {
                        $query->where('sender_id', $sender_id)
                              ->where('receiver_id', $receiver_id)
                              ->where('number_key', $akses->number_key);
                    })->orWhere(function($query) use ($sender_id, $receiver_id, $akses) {
                        $query->where('sender_id', $receiver_id)
                              ->where('receiver_id', $sender_id)
                              ->where('number_key', $akses->number_key);
                    })
                    ->with('sender:id,name', 'receiver:id,name')
                    ->get();

                    $receiver = $role_user == "Pelanggan" ?  User::find($idUserDokter->id_user) : User::find($receiver_id);

                    return response()->json([
                        'status'    => 'Gagal',
                        'pesan'     => $alert,
                        'messages'  => $messages,
                        'receiver'  => $receiver,
                        'tanggal'   => $sesi->tanggal,
                        'waktu_mulai'   => $sesi->waktu_mulai,
                        'waktu_selesai' => $sesi->waktu_selesai,
                        'saat_ini'      => $date_now
                    ], 200);

                }
            }
        }catch (Throwable $e) {
            return response()->json([
                'status' => 'Gagal',
                'pesan'  => $e->getMessage(),
            ], 422);
        }
    }

    public function getLinkKey($id){
        try {
            if(Auth::guard('api')->user() == null){
                return response()->json([
                    'status' => 'Gagal',
                    'pesan'  => 'Anda Tidak Mempunyai Akses',
                ]);
            }
            $linkKey = Key_Link::with(['sesi','dokter.user', 'user'])->where('id', $id)->first();
            $sesi       = Sesi::find($linkKey->id_sesi);
            $date_now   = explode(' ', now());
            $time       = $date_now[1];
            
            return response()->json([
                'status' => 'Berhasil',
                'linkKey' => $linkKey,
                'sender' => Auth::guard('api')->user(),
            ], 200);
        } catch (Throwable $e){
            return response()->json([
                'status' => 'Gagal',
                'pesan'  => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Send a message from the authenticated user to the specified user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
            'number_key' => 'required|string',
        ]);

        $akses      = Key_Link::where('number_key', $request->number_key)->first();
        $sesi       = Sesi::find($akses->id_sesi);
        $date_now   = explode(' ', now());
        $time       = $date_now[1];

        if ($sesi->tanggal == $date_now[0]){
            if ($sesi->waktu_mulai <= $time && $sesi->waktu_selesai >= $time) {
                $sender_id = Auth::guard('api')->user()->id;

                $chatMessage = new Message();
                $chatMessage->sender_id = $sender_id;
                $chatMessage->receiver_id = $request->receiver_id;
                $chatMessage->number_key = $request->number_key;
                $chatMessage->message = $request->message;
                $chatMessage->save();

                $chatMessage->load(['sender:id,name', 'receiver:id,name']);

                return response()->json([
                    'status' => 'Berhasil',
                    'data' => $chatMessage
                ], 200);
            } else {
                $alert = ($sesi->waktu_mulai >= $time) ? 'Sesi Belum Dimulai' : 'Sesi Telah Selesai';
                return response()->json([
                    'status'    => 'Gagal',
                    'pesan'     => $alert,
                    'tanggal'   => $sesi->tanggal,
                    'waktu_mulai'   => $sesi->waktu_mulai,
                    'waktu_selesai' => $sesi->waktu_selesai,
                    'saat_ini'      => $date_now
                ], 200);
            }
        }

    }

    public function user() {
        $authUserId = Auth::guard('api')->user()->id;
        $users = User::where('id', '!=', $authUserId)->get();

        return response()->json([
            'success' => true,
            'users' => $users
        ], 200);
    }

}
